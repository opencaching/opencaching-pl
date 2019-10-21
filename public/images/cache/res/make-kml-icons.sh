#!/bin/bash

# Convert standard cache icons (in parent directory) for better display
# in Google Earth via KML export

SRC="."
DST="../kml"
PWD_CHECK="res"
CACHE_TYPES=$(cat "__cache_types.txt" | grep -vE "^#|^\s*$" | cut -f2 -d' ')

MARKER="__kml_oc.png"
MARKER_DISABLED="__kml_disabled_oc.png"
MARKER_ARCHIVED="__kml_archived_oc.png"

if [ "$(basename $(pwd))" != "${PWD_CHECK}" ]; then
    echo "Error: must be called inside '${PWD_CHECK}' directory. Stop."
    echo
    exit 1
fi

function _FILENAME() {
    INPUT="$1"
    OUTPUT=$(echo ${INPUT}.* | grep -iE "png|svg" | sort -r | head -n 1)
    echo "${OUTPUT}"
}

function convertKML() {
    NAME="$1"
    CACHE=$(_FILENAME "_cache_type__$1")
    KML="$1_kml.png"
    KML_DISABLED="$1_kml-disabled.png"
    KML_ARCHIVED="$1_kml-archived.png"

    if [ ! -f "${CACHE}" ]; then
        echo "Error: file not found ${CACHE}!"
        exit 1
    fi

    rm -f "${DST}/${KML}"
    rm -f "${DST}/${KML_DISABLED}"
    rm -f "${DST}/${KML_ARCHIVED}"

    composite -compose src-over -geometry +6+6 "${CACHE}" "${MARKER}" png32:"${DST}/${KML}"
    composite -compose src-over -geometry +6+6 "${CACHE}" "${MARKER_DISABLED}" png32:"${DST}/${KML_DISABLED}"
    composite -compose src-over -geometry +6+6 "${CACHE}" "${MARKER_ARCHIVED}" png32:"${DST}/${KML_ARCHIVED}"
}

for x in ${CACHE_TYPES}; do
    echo -n "Creating KML icon for ${x}..."
    convertKML $x
    echo "done."
done
