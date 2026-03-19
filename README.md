# Video Gallery

A Laravel web application for browsing and voting on video files. Videos are displayed as animated GIF previews that users can click to watch the full video. Includes offline face recognition to filter videos by the people in them.

## Requirements

- PHP 8.0+
- Composer
- MySQL
- Node.js & npm
- FFmpeg (must be in PATH)
- Python 3.8+ (for face recognition)

## Installation

```bash
# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Install Node dependencies
npm install

# Copy environment file and configure
cp .env.example .env
php artisan key:generate

# Run migrations
php artisan migrate --force

# Link storage
php artisan storage:link

# Symlink video directory (adjust path to your video source)
ln -s video_base_dir public/storage/video

# Set permissions (Linux)
chgrp -R www-data storage bootstrap/cache
chmod -R ug+rwx storage bootstrap/cache
```

### Database Setup

Configure your `.env` file with MySQL credentials:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=video
DB_USERNAME=video
DB_PASSWORD=video
```

### Storage Structure

Ensure the following directories exist inside `public/storage/`:

```
public/storage/
├── video/      # Source video files (.mp4)
├── gif/        # Generated GIF previews
├── css/        # Stylesheets
└── faces/      # Auto-generated face thumbnails
```

### Generate GIF Previews

The `gif.sh` script creates animated GIF previews from videos. Run it from `public/storage/`:

```bash
cd public/storage
bash gif.sh
```

For each video it extracts three 20-second clips (beginning, middle, end), combines them, and generates a sped-up GIF.

## Running the App

```bash
php artisan serve
```

Visit `http://localhost:8000`. All pages require login.

### Cron Job (production)

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

Or locally:

```bash
php artisan schedule:work
```

## Pages

| Route | Description |
|-------|-------------|
| `/` | 20 random videos |
| `/sort` | Top 20 videos by votes |
| `/all` | All videos (newest first) |
| `/view?file=<basename>` | Video player with voting |
| `/person?id=<id>` | Videos filtered by person |
| `/login` | Login page |
| `/register` | Registration page |

## Voting

Each video has a vote counter. Click the submit button on the video player page to add +1 vote.

## Face Recognition

Offline face recognition powered by Python's `face_recognition` library (uses dlib). No data is sent to any external service — everything runs locally.

### How It Works

1. Extracts frames from each video using FFmpeg
2. Detects faces in each frame using dlib's HOG-based detector
3. Encodes each face as a 128-dimensional vector
4. Compares against known faces — matches existing people or creates new ones
5. Saves relationships to the database and generates face thumbnails
6. All views show clickable person buttons to filter videos

### Python Setup

```bash
pip install cmake
pip install dlib
pip install face_recognition numpy mysql-connector-python Pillow
```

> **Note:** `dlib` requires CMake and a C++ compiler. On Windows, install
> [Visual Studio Build Tools](https://visualstudio.microsoft.com/visual-cpp-build-tools/)
> with the "Desktop development with C++" workload.

### Running a Face Scan

Via artisan:

```bash
php artisan faces:scan
```

Options:

| Option | Default | Description |
|--------|---------|-------------|
| `--tolerance` | `0.6` | Face match tolerance. Lower = stricter matching |
| `--frames` | `5` | Number of frames to extract per video |
| `--video-dir` | `public/storage/video` | Path to video directory |

Example with custom settings:

```bash
php artisan faces:scan --tolerance=0.5 --frames=8
```

Or run the Python script directly:

```bash
python scripts/scan_faces.py --video-dir public/storage/video --tolerance 0.6 --frames 5
```

### Renaming People

Detected people are unnamed by default (shown as "Person #1", etc.). Click a person button on any page, then use the rename form on the person page.

### Re-scanning

The scanner skips already-processed videos. To re-scan everything:

```sql
TRUNCATE file_person;
```

Then run `php artisan faces:scan` again.

## Database Schema

### files

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| basename | string | Full filename (e.g. `video.mp4`) |
| extension | string | File extension |
| filename | string | Filename without extension |
| gif | string | GIF preview filename |
| votes | int | Vote count (default 0) |

### people

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| name | string | Display name (nullable, set by user) |
| face_encoding | text | 128-d face vector as JSON |
| thumbnail | string | Face thumbnail filename |

### file_person

| Column | Type | Description |
|--------|------|-------------|
| file_id | bigint | FK to files |
| person_id | bigint | FK to people |

## Tips

If you run into issues:

```bash
php artisan optimize
```

To disable public registration, comment out in `config/fortify.php`:

```php
//Features::registration()
```

## Tech Stack

- **Backend:** Laravel 8, Fortify, Jetstream, Sanctum
- **Frontend:** Blade templates, Bootstrap 5, Inertia.js + Vue 3 (auth pages)
- **Database:** MySQL
- **Media:** FFmpeg, freezeframe.js
- **Face Recognition:** Python, dlib, face_recognition (fully offline)
