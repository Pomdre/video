#!/bin/bash
mkdir -p gif thumb temp

for file in video/*; do
    NAME=$(basename "${file%%.*}")
    GIF="gif/${NAME}.gif"
    THUMB="thumb/${NAME}.jpg"

    # Generate static thumbnail if missing
    if [ ! -f "$THUMB" ]; then
        echo "Generating thumbnail: $THUMB"
        videotime=$(ffprobe -i "$file" -show_format -v quiet | sed -n 's/duration=//p')
        round=$(printf "%.0f\n" $videotime)
        mid=$(expr $round / 2)
        ffmpeg -ss $mid -i "$file" -frames:v 1 -vf "scale=320:-1" -q:v 5 -y "$THUMB"
    fi

    # Generate GIF if missing
    if [ -f "$GIF" ]; then
        echo "$GIF exists."
    else
        echo "Generating GIF: ${NAME}.gif"
        TMPDIR="temp/${NAME}_map"
        mkdir -p "$TMPDIR"
        videotime=$(ffprobe -i "$file" -show_format -v quiet | sed -n 's/duration=//p')
        round=$(printf "%.0f\n" $videotime)
        mid=$(expr $round / 2)
        end=$(expr $round - 20)

        # Extract 10-second clips (shorter = smaller GIF)
        ffmpeg -n -ss 30 -t 10 -i "$file" -c copy "$TMPDIR/first.mp4"
        ffmpeg -n -ss $mid -t 10 -i "$file" -c copy "$TMPDIR/mid.mp4"
        ffmpeg -n -ss $end -t 10 -i "$file" -c copy "$TMPDIR/end.mp4"

        # Concatenate clips
        ffmpeg -i "$TMPDIR/first.mp4" -i "$TMPDIR/mid.mp4" -i "$TMPDIR/end.mp4" \
            -filter_complex "[0:0][1:0][2:0]concat=n=3:v=1:a=0" \
            "$TMPDIR/output.mkv"

        # Generate optimized GIF: 10fps instead of 30, sped up 10x instead of 20x
        ffmpeg -n -i "$TMPDIR/output.mkv" \
            -vf "setpts=PTS/10,fps=10,scale=320:-1:flags=lanczos,split[s0][s1];[s0]palettegen=max_colors=128[p];[s1][p]paletteuse=dither=bayer:bayer_scale=3" \
            -loop 0 "$GIF"

        rm -r "$TMPDIR"
    fi
done
