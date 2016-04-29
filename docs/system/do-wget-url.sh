#!/bin/sh

BASE_URL=http://YOUR_OPENCACHING_HOSTNAME/
OUTPUT_DIR=/path/to/conjobs_output/

JOB=$1
OUTPUT=$2

if [ -z "$2"
# Assume wget is in /usr/bin
/usr/bin/wget -q ${BASE_URL}$1 -O ${OUTPUT_DIR}$2

# b..dy s. wypisywane do pliku - jak si. je wypisze na stdout, to cron powiadomi
cat $OUTPUT_DIR$2
