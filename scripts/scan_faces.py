#!/usr/bin/env python3
"""
Offline face recognition scanner for video files.
Extracts frames from videos, detects faces, clusters them into people,
and writes results to the MySQL database.

Requirements:
    pip install face_recognition numpy mysql-connector-python Pillow

Usage:
    python scripts/scan_faces.py [--video-dir PATH] [--tolerance 0.6] [--frames 5]
"""

import argparse
import json
import os
import subprocess
import sys
import tempfile
from pathlib import Path

import face_recognition
import mysql.connector
import numpy as np
from PIL import Image


def get_db_connection():
    """Connect to the MySQL database using the Laravel .env settings."""
    env = {}
    env_path = Path(__file__).resolve().parent.parent / '.env'
    with open(env_path) as f:
        for line in f:
            line = line.strip()
            if '=' in line and not line.startswith('#'):
                key, _, value = line.partition('=')
                env[key.strip()] = value.strip().strip('"').strip("'")

    return mysql.connector.connect(
        host=env.get('DB_HOST', '127.0.0.1'),
        port=int(env.get('DB_PORT', 3306)),
        user=env.get('DB_USERNAME', 'video'),
        password=env.get('DB_PASSWORD', 'video'),
        database=env.get('DB_DATABASE', 'video'),
    )


def extract_frames(video_path, num_frames=5):
    """Extract evenly spaced frames from a video using FFmpeg."""
    frames = []
    with tempfile.TemporaryDirectory() as tmpdir:
        # Get video duration
        result = subprocess.run(
            ['ffprobe', '-v', 'quiet', '-show_format', '-print_format', 'json', str(video_path)],
            capture_output=True, text=True
        )
        try:
            duration = float(json.loads(result.stdout)['format']['duration'])
        except (KeyError, json.JSONDecodeError, ValueError):
            print(f"  Could not get duration for {video_path}, skipping")
            return []

        if duration < 1:
            return []

        # Extract frames at evenly spaced intervals
        interval = duration / (num_frames + 1)
        for i in range(1, num_frames + 1):
            timestamp = interval * i
            output_path = os.path.join(tmpdir, f"frame_{i}.jpg")
            subprocess.run(
                ['ffmpeg', '-ss', str(timestamp), '-i', str(video_path),
                 '-frames:v', '1', '-q:v', '2', '-y', output_path],
                capture_output=True
            )
            if os.path.exists(output_path):
                frames.append(output_path)

        # Load frames as numpy arrays
        loaded = []
        for frame_path in frames:
            try:
                img = face_recognition.load_image_file(frame_path)
                loaded.append(img)
            except Exception:
                continue

    return loaded


def save_face_thumbnail(face_image, face_location, person_id, output_dir):
    """Crop and save a face thumbnail."""
    top, right, bottom, left = face_location
    # Add some padding
    height, width = face_image.shape[:2]
    pad = int((bottom - top) * 0.3)
    top = max(0, top - pad)
    bottom = min(height, bottom + pad)
    left = max(0, left - pad)
    right = min(width, right + pad)

    face_crop = face_image[top:bottom, left:right]
    img = Image.fromarray(face_crop)
    img = img.resize((150, 150))
    filename = f"person_{person_id}.jpg"
    filepath = os.path.join(output_dir, filename)
    img.save(filepath, 'JPEG')
    return filename


def scan_videos(video_dir, tolerance=0.6, num_frames=5):
    """Main scanning logic."""
    project_root = Path(__file__).resolve().parent.parent
    thumbnail_dir = project_root / 'public' / 'storage' / 'faces'
    thumbnail_dir.mkdir(parents=True, exist_ok=True)

    conn = get_db_connection()
    cursor = conn.cursor(dictionary=True)

    # Get all files from the database
    cursor.execute("SELECT id, basename FROM files")
    db_files = cursor.fetchall()

    # Get already-processed file IDs
    cursor.execute("SELECT DISTINCT file_id FROM file_person")
    processed_ids = {row['file_id'] for row in cursor.fetchall()}

    # Get existing people and their encodings
    cursor.execute("SELECT id, face_encoding FROM people")
    existing_people = []
    for row in cursor.fetchall():
        encoding = np.array(json.loads(row['face_encoding']))
        existing_people.append({'id': row['id'], 'encoding': encoding})

    print(f"Found {len(db_files)} files in database, {len(processed_ids)} already processed")
    print(f"Found {len(existing_people)} known people")

    new_faces_found = 0
    files_processed = 0

    for db_file in db_files:
        file_id = db_file['id']
        basename = db_file['basename']

        if file_id in processed_ids:
            continue

        video_path = Path(video_dir) / basename
        if not video_path.exists():
            print(f"  Video not found: {video_path}")
            continue

        print(f"Processing: {basename} (id={file_id})")
        frames = extract_frames(video_path, num_frames)

        if not frames:
            print(f"  No frames extracted")
            continue

        # Detect faces in all frames
        all_encodings = []
        all_locations = []
        all_frame_indices = []

        for idx, frame in enumerate(frames):
            locations = face_recognition.face_locations(frame, model='hog')
            encodings = face_recognition.face_encodings(frame, locations)
            for loc, enc in zip(locations, encodings):
                all_encodings.append(enc)
                all_locations.append(loc)
                all_frame_indices.append(idx)

        if not all_encodings:
            print(f"  No faces detected")
            # Mark as processed with no people (insert a dummy we'll skip)
            continue

        print(f"  Found {len(all_encodings)} face(s) across {len(frames)} frames")

        # Match each face encoding to existing people or create new ones
        matched_person_ids = set()

        for i, encoding in enumerate(all_encodings):
            best_match_id = None

            if existing_people:
                known_encodings = [p['encoding'] for p in existing_people]
                distances = face_recognition.face_distance(known_encodings, encoding)
                min_idx = np.argmin(distances)
                if distances[min_idx] <= tolerance:
                    best_match_id = existing_people[min_idx]['id']

            if best_match_id is None:
                # New person
                encoding_json = json.dumps(encoding.tolist())
                cursor.execute(
                    "INSERT INTO people (name, face_encoding, created_at, updated_at) VALUES (%s, %s, NOW(), NOW())",
                    (None, encoding_json)
                )
                conn.commit()
                new_person_id = cursor.lastrowid

                # Save thumbnail
                frame = frames[all_frame_indices[i]]
                thumb_filename = save_face_thumbnail(
                    frame, all_locations[i], new_person_id, str(thumbnail_dir)
                )
                cursor.execute(
                    "UPDATE people SET thumbnail = %s WHERE id = %s",
                    (thumb_filename, new_person_id)
                )
                conn.commit()

                existing_people.append({'id': new_person_id, 'encoding': encoding})
                best_match_id = new_person_id
                new_faces_found += 1
                print(f"  New person created: id={new_person_id}")

            matched_person_ids.add(best_match_id)

        # Insert file_person relationships
        for person_id in matched_person_ids:
            try:
                cursor.execute(
                    "INSERT INTO file_person (file_id, person_id, created_at, updated_at) VALUES (%s, %s, NOW(), NOW())",
                    (file_id, person_id)
                )
            except mysql.connector.IntegrityError:
                pass  # Already linked

        conn.commit()
        files_processed += 1

    cursor.close()
    conn.close()

    print(f"\nDone! Processed {files_processed} new files, found {new_faces_found} new people.")
    print(f"Total known people: {len(existing_people)}")


def main():
    parser = argparse.ArgumentParser(description='Scan video files for faces')
    project_root = Path(__file__).resolve().parent.parent
    default_video_dir = project_root / 'public' / 'storage' / 'video'

    parser.add_argument('--video-dir', default=str(default_video_dir),
                        help='Path to video directory')
    parser.add_argument('--tolerance', type=float, default=0.6,
                        help='Face match tolerance (lower = stricter, default 0.6)')
    parser.add_argument('--frames', type=int, default=5,
                        help='Number of frames to extract per video (default 5)')

    args = parser.parse_args()

    print(f"Video directory: {args.video_dir}")
    print(f"Tolerance: {args.tolerance}")
    print(f"Frames per video: {args.frames}")
    print()

    scan_videos(args.video_dir, args.tolerance, args.frames)


if __name__ == '__main__':
    main()
