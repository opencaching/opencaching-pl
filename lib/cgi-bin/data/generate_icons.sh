#!/bin/bash
for file in marker.svg markerown.svg markerfound.svg markernew.svg;do
	inkscape $file -e ${file%*.svg}4.png -w 8
	inkscape $file -e ${file%*.svg}5.png -w 10
	inkscape $file -e ${file%*.svg}6.png -w 10
	inkscape $file -e ${file%*.svg}7.png -w 12
	inkscape $file -e ${file%*.svg}8.png -w 14
	inkscape $file -e ${file%*.svg}9.png -w 16
	inkscape $file -e ${file%*.svg}10.png -w 18
	inkscape $file -e ${file%*.svg}11.png -w 20
	inkscape $file -e ${file%*.svg}12.png -w 22
	inkscape $file -e ${file%*.svg}13.png -w 22
	inkscape $file -e ${file%*.svg}14.png -w 24
	inkscape $file -e ${file%*.svg}15.png -w 26
	inkscape $file -e ${file%*.svg}16.png -w 28
	inkscape $file -e ${file%*.svg}17.png -w 30
	inkscape $file -e ${file%*.svg}18.png -w 33
	inkscape $file -e ${file%*.svg}19.png -w 35
done

for file in archivedmap.svg event.svg foundmap.svg moving.svg multi.svg quiz.svg redflagmap.svg traditional.svg unknown.svg virtual.svg challenge.svg podcache.svg webcam.svg;do
	inkscape $file -e ${file%*.svg}4.png -w 4
	inkscape $file -e ${file%*.svg}5.png -w 6
	inkscape $file -e ${file%*.svg}6.png -w 6
	inkscape $file -e ${file%*.svg}7.png -w 8
	inkscape $file -e ${file%*.svg}8.png -w 10
	inkscape $file -e ${file%*.svg}9.png -w 12
	inkscape $file -e ${file%*.svg}10.png -w 14
	inkscape $file -e ${file%*.svg}11.png -w 16
	inkscape $file -e ${file%*.svg}12.png -w 18
	inkscape $file -e ${file%*.svg}13.png -w 18
	inkscape $file -e ${file%*.svg}14.png -w 20
	inkscape $file -e ${file%*.svg}15.png -w 22
	inkscape $file -e ${file%*.svg}16.png -w 24
	inkscape $file -e ${file%*.svg}17.png -w 26
	inkscape $file -e ${file%*.svg}18.png -w 28
	inkscape $file -e ${file%*.svg}19.png -w 30
done
