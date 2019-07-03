#!/bin/bash

# Compose standard cache icons (in res directory)
# to required opencaching-pl iconset


SRC="."
DST=".."
PWD_CHECK="res"
SKIP_EXISTING=1

CACHE_TYPES=$(cat __cache_types.txt | grep -vE "^#|^\s*$" | cut -f2 -d' ')

_FILENAME() {
    INPUT="$1"
    OUTPUT=$(echo ${INPUT}.* | grep -iE "png|svg" | sort -r | head -n 1)
    echo "${OUTPUT}"
}

_OVERLAY_A="-a"
_OVERLAY_D="-d"
_OVERLAY_DNF="-dnf"
_OVERLAY_FOUND="-found"
_OVERLAY_I="-i"
_OVERLAY_N="-n"
_OVERLAY_S="-s"
_OVERLAY_OWNER="-owner"

OVERLAY_A=$(_FILENAME "_overlay__${_OVERLAY_A}")
OVERLAY_D=$(_FILENAME "_overlay__${_OVERLAY_D}")
OVERLAY_DNF=$(_FILENAME "_overlay__${_OVERLAY_DNF}")
OVERLAY_FOUND=$(_FILENAME "_overlay__${_OVERLAY_FOUND}")
OVERLAY_I=$(_FILENAME "_overlay__${_OVERLAY_I}")
OVERLAY_N=$(_FILENAME "_overlay__${_OVERLAY_N}")
OVERLAY_OWNER=$(_FILENAME "_overlay__${_OVERLAY_OWNER}")
    
usage() {
    cat << EOM
    usage: make-cache-iconset.sh [options]

    OPTIONS:
    -o              overwrite existing icons (default skip)
    -h              show this message

EOM
}
    
if [ "$(basename $(pwd))" != "${PWD_CHECK}" ]; then
    echo "Error: must be called inside '${PWD_CHECK}' directory. Stop."
    echo
    exit 1
fi  

while getopts "oh" OPTS; do
    case $OPTS in
        o)
            SKIP_EXISTING=
            ;;
        h)
            usage
            exit 1
            ;;
        \?)
            echo "use $0 -h for help"
            exit
            ;;
    esac
done

# Compose icons
function composeICONS() {
    NAME="$1"
    CACHE=$(_FILENAME "_cache_type__$1")

    if [ ! -f "${SRC}/${CACHE}" ]; then
        echo "Error: file not found ${CACHE}!"
        exit 1
    fi
    rm -f "${DST}/${NAME}*"

    convert "${SRC}/${CACHE}" png32:"${DST}/${NAME}.png"

    convert "${SRC}/${CACHE}" -resize 16x16 png32:"${DST}/16x16-${NAME}.png"
    convert -alpha set -background none "${SRC}/${CACHE}" -channel a -evaluate multiply 0.6 +channel "${OVERLAY_FOUND}" -compose src-over -composite -resize 16x16 png32:"${DST}/16x16-${NAME}${_OVERLAY_FOUND}.png"
    convert -alpha set -background none "${SRC}/${CACHE}" -channel a -evaluate multiply 0.6 +channel "${OVERLAY_OWNER}" -compose src-over -composite -resize 16x16 png32:"${DST}/16x16-${NAME}${_OVERLAY_S}${_OVERLAY_OWNER}.png"

    convert -alpha set -background none "${SRC}/${CACHE}" -channel a -evaluate multiply 0.6 +channel "${OVERLAY_A}" -compose src-over -composite png32:"${DST}/${NAME}${_OVERLAY_A}.png"
    convert -alpha set -background none "${SRC}/${CACHE}" -channel a -evaluate multiply 0.6 +channel "${OVERLAY_A}" -compose src-over -composite "${OVERLAY_DNF}" -compose src-over -composite png32:"${DST}/${NAME}${_OVERLAY_A}${_OVERLAY_DNF}.png"
    convert -alpha set -background none "${SRC}/${CACHE}" -channel a -evaluate multiply 0.6 +channel "${OVERLAY_A}" -compose src-over -composite "${OVERLAY_FOUND}" -compose src-over -composite png32:"${DST}/${NAME}${_OVERLAY_A}${_OVERLAY_FOUND}.png"
    convert -alpha set -background none "${SRC}/${CACHE}" -channel a -evaluate multiply 0.6 +channel "${OVERLAY_A}" -compose src-over -composite "${OVERLAY_OWNER}" -compose src-over -composite png32:"${DST}/${NAME}${_OVERLAY_A}${_OVERLAY_OWNER}.png"

    convert -alpha set -background none "${SRC}/${CACHE}" -channel a -evaluate multiply 0.6 +channel "${OVERLAY_D}" -compose src-over -composite png32:"${DST}/${NAME}${_OVERLAY_D}.png"
    convert -alpha set -background none "${SRC}/${CACHE}" -channel a -evaluate multiply 0.6 +channel "${OVERLAY_D}" -compose src-over -composite "${OVERLAY_DNF}" -compose src-over -composite png32:"${DST}/${NAME}${_OVERLAY_D}${_OVERLAY_DNF}.png"
    convert -alpha set -background none "${SRC}/${CACHE}" -channel a -evaluate multiply 0.6 +channel "${OVERLAY_D}" -compose src-over -composite "${OVERLAY_FOUND}" -compose src-over -composite png32:"${DST}/${NAME}${_OVERLAY_D}${_OVERLAY_FOUND}.png"
    convert -alpha set -background none "${SRC}/${CACHE}" -channel a -evaluate multiply 0.6 +channel "${OVERLAY_D}" -compose src-over -composite "${OVERLAY_OWNER}" -compose src-over -composite png32:"${DST}/${NAME}${_OVERLAY_D}${_OVERLAY_OWNER}.png"

    convert -size 34x34 xc:none -alpha set -background none "${SRC}/${CACHE}" -geometry +1+1 -compose dst-over -composite "${OVERLAY_I}" -compose dst-over -composite png32:"${DST}/${NAME}${_OVERLAY_I}.png"
    convert -size 34x34 xc:none -alpha set -background none "${SRC}/${CACHE}" -geometry +1+1 -compose dst-over -composite "${OVERLAY_I}" -compose dst-over -composite -fx '(r+g+b)/3' png32:"${DST}/${NAME}${_OVERLAY_I}-bw.png"

    convert -alpha set -background none "${SRC}/${CACHE}" -channel a -evaluate multiply 0.6 +channel "${OVERLAY_N}" -compose src-over -composite png32:"${DST}/${NAME}${_OVERLAY_N}.png"
    convert -alpha set -background none "${SRC}/${CACHE}" -channel a -evaluate multiply 0.6 +channel "${OVERLAY_N}" -compose src-over -composite "${OVERLAY_DNF}" -compose src-over -composite png32:"${DST}/${NAME}${_OVERLAY_N}${_OVERLAY_DNF}.png"
    convert -alpha set -background none "${SRC}/${CACHE}" -channel a -evaluate multiply 0.6 +channel "${OVERLAY_N}" -compose src-over -composite "${OVERLAY_FOUND}" -compose src-over -composite png32:"${DST}/${NAME}${_OVERLAY_N}${_OVERLAY_FOUND}.png"
    convert -alpha set -background none "${SRC}/${CACHE}" -channel a -evaluate multiply 0.6 +channel "${OVERLAY_N}" -compose src-over -composite "${OVERLAY_OWNER}" -compose src-over -composite png32:"${DST}/${NAME}${_OVERLAY_N}${_OVERLAY_OWNER}.png"

    convert -alpha set -background none "${SRC}/${CACHE}" png32:"${DST}/${NAME}${_OVERLAY_S}.png"
    convert -alpha set -background none "${SRC}/${CACHE}" -channel a -evaluate multiply 0.6 +channel "${OVERLAY_DNF}" -compose src-over -composite png32:"${DST}/${NAME}${_OVERLAY_S}${_OVERLAY_DNF}.png"
    convert -alpha set -background none "${SRC}/${CACHE}" -channel a -evaluate multiply 0.6 +channel "${OVERLAY_FOUND}" -compose src-over -composite png32:"${DST}/${NAME}${_OVERLAY_S}${_OVERLAY_FOUND}.png"
    convert -alpha set -background none "${SRC}/${CACHE}" -channel a -evaluate multiply 0.6 +channel "${OVERLAY_OWNER}" -compose src-over -composite png32:"${DST}/${NAME}${_OVERLAY_S}${_OVERLAY_OWNER}.png"
}

# Process all cache types
for x in ${CACHE_TYPES}; do
    if [ -f "${DST}/${x}.png" -a "${SKIP_EXISTING}" == "1" ]; then
        echo "Skipping ${x} (already exists)"
        continue
    fi
    echo -n "Creating iconset for ${x}..."
    composeICONS "${x}"
    echo "done."
done


if [ -f "${DST}/preview.png" ]; then
    rm -f "${DST}/preview.png"
fi
echo "Creating preview ..."
montage "${DST}/*.png" -geometry 32x32+2+2 -tile 16x png24:${DST}/preview.png
