#!/bin/sh
for file in `find . -name "*.png"|grep "\-undef.png"`;do
basefile=`basename $file -undef.png`
#mogrify -fill '#FFFFFF' $file
convert $basefile.png -colorspace Gray $1 $file

mogrify -channel A -evaluate divide 2 $file

done