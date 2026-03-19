#!/bin/bash
for file in video/*; do
    FILE=gif/$(basename "${file%%.*}").gif
    if [ -f "$FILE" ]; then
        echo "$FILE exists."
    else 
        echo "$(basename "${file%%.*}").gif"
        echo "$(basename "${file}")"
        mkdir "temp/$(basename "${file}")_map"
        videotime=$(ffprobe -i "video/$(basename "${file}")" -show_format -v quiet | sed -n 's/duration=//p')
        echo $videotime
        round=$(printf "%.0f\n" $videotime)
        mid=$(expr $round / 2)
        end=$(expr $round - 35)
        echo $mid $end
        # #ffmpeg -ss 00:08:00 -i Video.mp4 -ss 00:01:00 -t 00:01:00 -c copy VideoClip.mp4
        ffmpeg -n -ss 30 -t 20 -i "video/$(basename "${file}")" -c copy "temp/$(basename "${file}")_map/first_$(basename "${file%%.*}").mp4"
        ffmpeg -n -ss $mid -t 20 -i "video/$(basename "${file}")" -c copy "temp/$(basename "${file}")_map/mid_$(basename "${file%%.*}").mp4"
        ffmpeg -n -ss $end -t 20 -i "video/$(basename "${file}")" -c copy "temp/$(basename "${file}")_map/end_$(basename "${file%%.*}").mp4"
        #Sett sammen filer
        ffmpeg -i "temp/$(basename "${file}")_map/first_$(basename "${file%%.*}").mp4" -i "temp/$(basename "${file}")_map/mid_$(basename "${file%%.*}").mp4" -i "temp/$(basename "${file}")_map/end_$(basename "${file%%.*}").mp4" -filter_complex "[0:0] [1:0] [2:0] concat=n=3:v=1:a=0" "temp/$(basename "${file}")_map/output_$(basename "${file%%.*}").mkv"
        #Lag GIF
        ffmpeg -n -i "temp/$(basename "${file}")_map/output_$(basename "${file%%.*}").mkv" -vf "setpts=PTS/20,fps=30,scale=320:-1:flags=lanczos,split[s0][s1];[s0]palettegen[p];[s1][p]paletteuse" -loop 0 "gif/$(basename "${file%%.*}").gif"
        #Rydd
        rm -r "temp/$(basename "${file}")_map"
    fi
done 
