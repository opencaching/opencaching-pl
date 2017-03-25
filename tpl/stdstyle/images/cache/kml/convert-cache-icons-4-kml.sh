#!/bin/bash

# Convert standard cache icons (in parent directory) for better display 
# in Google Earth via KML export

SRC="../"
CACHE_TYPES="event moving multi owncache podcache quiz traditional unknown virtual webcam"

MARKER="__marker_oc.png"
MARKER_DISABLED="__marker_disabled_oc.png"
MARKER_ARCHIVED="__marker_archived_oc.png"

function convertKML() {
    CACHE="$1.png"
    KML="$1_kml.png"
    KML_DISABLED="$1_kml-disabled.png"
    KML_ARCHIVED="$1_kml-archived.png"
    if [ ! -f "${SRC}${CACHE}" ]; then
        echo "Error: file not found ${CACHE}!"
        exit 1
    fi
    rm -f "${KML}"
    rm -f "${KML_DISABLED}"
    rm -f "${KML_ARCHIVED}"
    composite -compose src-over -geometry +6+6 "${SRC}${CACHE}" "${MARKER}" "${KML}"
    composite -compose src-over -geometry +6+6 "${SRC}${CACHE}" "${MARKER_DISABLED}" "${KML_DISABLED}"
    composite -compose src-over -geometry +6+6 "${SRC}${CACHE}" "${MARKER_ARCHIVED}" "${KML_ARCHIVED}"
}

for x in ${CACHE_TYPES}; do
    echo -n "Creating KML icon for ${x}..."
    convertKML $x
    echo "done."
done
    