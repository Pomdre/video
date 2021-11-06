for file in video/*; do
    echo "$(basename "${file%%.*}").gif"
    echo "$(basename "${file}")"
    ffmpeg -n -ss 30 -t 20 -i "video/$(basename "${file}")" -vf "setpts=0.2*PTS,fps=30,scale=1080:-1:flags=lanczos,split[s0][s1];[s0]palettegen[p];[s1][p]paletteuse" -loop 0 "gif/$(basename "${file%%.*}").gif"
done