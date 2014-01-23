#!/bin/bash
for size in 16 18 20 32;do
    for file in *.svg;do
        inkscape $file -e ${file%*.svg}-$size.png -w $size
    done
done