#!/bin/bash

convertRectangle() {
    local name="$1"
    local base="${name}.png"
    local undef="${name}-undef.png"
    local no="${name}-no.png"
    local tmp="template-scaled.png"

    rm -f "$undef" "$no" "$tmp"

    convert "$base" \
        -colorspace Gray \
        -alpha set \
        -channel A -evaluate multiply 0.5 +channel \
        "$undef"

    size=$(identify -format "%wx%h" "$base")

    convert "template-attr-no.png" \
        -resize "${size}!" \
        "$tmp"

    composite \
        -compose over \
        "$tmp" \
        "$base" \
        "$no"

    rm -f "$tmp"

    echo "Done:"
    echo "$undef"
    echo "$no"
}

#convertRectangle "deaddrop"
#convertRectangle "detail"
#convertRectangle "drivein"
#convertRectangle "natural"
#convertRectangle "street_art"
#convertRectangle "scenic"
#convertRectangle "ruin"

#convertRectangle "mp3"

#convertRectangle "letterbox"

