#!/bin/bash 
#
# usage: dumpCleaner <file-with-sql-dump> <file-with-list-of-UNNEED-tables>
# 
#

# sql dump
SQL_FILE=$1
TABLES_TO_TRIM_FILE=$2

# result file
TRIMMED_FILE="OC_DEV_DUMP_"${SQL_FILE%.*}

# just print message
function say () {
    echo  `date +%F_%H:%M:%S`": $1"
}

# replace tricky processing breakers to peacefull chars
function cleanFile() {
    FILE=$1

    #replace double back-slashes to pipe "\\" -> "|"
    sed -E -i -e 's/\\\\/|/g' $FILE

    # replace escaped quote to colon "\'" -> ":" 
    sed -E -i -e "s/\\\'/:/g" $FILE
}

function printFileSize() {
    SIZE=`ls -lh $1 | cut -d " " -f 5`
    echo "File:" $1 "size:" $SIZE
}

# select only necessary data from sql file
function trimFile () {

    TABLES_TO_TRIM=`cat $TABLES_TO_TRIM_FILE | grep -v '^#' | grep -v '^$'`

    # prepare pattern to grep
    TRIM_PATTERN=""
    for table in $TABLES_TO_TRIM
    do
        TRIM_PATTERN=$TRIM_PATTERN"^INSERT INTO \`$table\` |"
    done

    # remove last pipe
    TRIM_PATTERN=${TRIM_PATTERN%?}

    zcat $SQL_FILE | grep -Ev "$TRIM_PATTERN" > $TRIMMED_FILE
}

# change all email addresses to DEFAULT-EMAIL - actually not used becaus user email needs to be unique!
function blureEmails () {

    # email address to set in all user rows
    DEFAULT_EMAIL="" #by default email address is empty 

    EMAIL_REGEX="[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,6}"
    
    sed -E -i $TRIMMED_FILE -e "s/,'$EMAIL_REGEX',/,'$DEFAULT_EMAIL',/g"
}

# change all cache passwords to DEFAULT-PASS
function changeCachesPassword() {

    DEFAULT_PASS="pass"         # password to use for all caches
    CACHE_PASS_FIELD_NR=24      # password column number 

    TMP_FILE="tmp_`date +%s`.sql"
    TMP2_FILE="tmp2_`date +%s`.sql"

    CACHES_PATTERN="INSERT INTO \`caches\`"
    CACHES_INSERT_LINE_MARKER="ALTER TABLE \`caches\` DISABLE KEYS"

    cat $TRIMMED_FILE | grep "$CACHES_PATTERN" > $TMP_FILE

    cleanFile $TMP_FILE

    # replace all commas inside the quotes with semicolon
    awk -F"'" -v OFS="'" '{ for (i=2; i<=NF; i+=2) gsub(",", ";", $i) } 1' $TMP_FILE > $TMP2_FILE

    mv $TMP2_FILE $TMP_FILE
    
    # split long lines to one inserted row per line
    sed -i $TMP_FILE -e 's/),(/),\n(/g'

    # change passwords
    awk 'BEGIN {OFS=FS=","} { 
        if($'$CACHE_PASS_FIELD_NR' != "'\'''\''") 
            $'$CACHE_PASS_FIELD_NR'="'\'$DEFAULT_PASS\''";  print 
        }' $TMP_FILE > $TMP2_FILE

    # join lines inside TMP_FILE
    cat $TMP2_FILE | tr '\n' ' ' > $TMP_FILE
    
    # split lines per each INSERT...
    sed -i -e 's/\; INSERT INTO/\;\nINSERT INTO/g' $TMP_FILE
    
    # remove insert lines from trimmed file
    sed -i "/$CACHES_PATTERN/d" $TRIMMED_FILE

    # print all modified lines again to trimmed file
    sed -i "/$CACHES_INSERT_LINE_MARKER/ r $TMP_FILE" $TRIMMED_FILE

    # delete tmp files
    rm $TMP_FILE $TMP2_FILE
}

# change all user passwords to default value: haslo
function changeUserPasswords() {

    #pass=haslo
    PASSWORD="ffb1b530c3116b4cd2e75b764c9ba0d647ab53053571a29532dbe8161996fe38540490fbecd45f4a4114d53d6e9bba6fb790a3b86ccda1b61c821ac02c1b53fa"
    SALT=""
    HASHING_ROUNDS=1

    PASSWORD_COL=3
    SALT_COL=4
    HASH_RND_COL=5
    EMAIL_COL=6

    USER_TABLE_PATTERN="INSERT INTO \`user\`"
    USER_INSERT_LINE_MARKER="ALTER TABLE \`user\` DISABLE KEYS"

    TMP_FILE="tmp_`date +%s`.sql"
    TMP2_FILE="tmp2_`date +%s`.sql"

    cat $TRIMMED_FILE | grep "$USER_TABLE_PATTERN" > $TMP_FILE

    cleanFile $TMP_FILE

    # replace all commas inside the quotes with semicolon
    awk -i inplace -F"'" -v OFS="'" '{ for (i=2; i<=NF; i+=2) gsub(",", ";", $i) } 1' $TMP_FILE

    # split long lines to one inserted row per line
    sed -i -e 's/),(/),\n(/g' $TMP_FILE

    # change passwords
    awk -i inplace 'BEGIN {OFS=FS=","} {
            uid=$1;
            gsub(/.*\(/,"",uid);
            $'$PASSWORD_COL'="'\'$PASSWORD\''";
            $'$SALT_COL'="'\'$SALT\''";  
            $'$HASH_RND_COL'="'\'$HASHING_ROUNDS\''";
            $'$EMAIL_COL'="'\''"uid"@localhost'\''";
            print
        }' $TMP_FILE

    # join lines inside TMP_FILE
    cat $TMP_FILE | tr '\n' ' ' > $TMP2_FILE

    mv $TMP2_FILE $TMP_FILE

    # split lines per each INSERT...
    sed -i -e 's/\; INSERT INTO/\;\nINSERT INTO/g' $TMP_FILE
    
    # remove insert lines from trimmed file
    sed -i "/$USER_TABLE_PATTERN/d" $TRIMMED_FILE

    # print all modified lines again to trimmed file
    sed -i "/$USER_INSERT_LINE_MARKER/ r $TMP_FILE" $TRIMMED_FILE

    # delete tmp files
    rm $TMP_FILE
}

# remove most of cache log texts
function removeLogTexts () {

    LOG_TEXT_COL=6
    LOG_TEXT="TFTC"

    LOGS_TABLE_PATTERN="INSERT INTO \`cache_logs\`"
    LOGS_INSERT_LINE_MARKER="ALTER TABLE \`cache_logs\` DISABLE KEYS"

    TMP_FILE="tmp_`date +%s`.sql"
    TMP2_FILE="tmp2_`date +%s`.sql"

    cat $TRIMMED_FILE | grep "$LOGS_TABLE_PATTERN" > $TMP_FILE

    cleanFile $TMP_FILE

    # replace all commas inside the quotes with semicolon
    awk -F"'" -v OFS="'" '{ for (i=2; i<=NF; i+=2) gsub(",", ";", $i) } 1' $TMP_FILE > $TMP2_FILE

    mv $TMP2_FILE $TMP_FILE

    # split long lines to one inserted row per line
    sed -i -e 's/),(/),\n(/g' $TMP_FILE

    # remove 90% of logs texts
    awk 'BEGIN {OFS=FS=","} { 
        if(NR % 10 != 0) 
            $'$LOG_TEXT_COL'="'\'$LOG_TEXT\''";  print 
        }' $TMP_FILE > $TMP2_FILE

    # join lines inside TMP_FILE
    cat $TMP2_FILE | tr '\n' ' ' > $TMP_FILE

    # split lines per each INSERT...
    sed -i -e 's/\; INSERT INTO/\;\nINSERT INTO/g' $TMP_FILE

    # remove insert lines from trimmed file
    sed -i "/$LOGS_TABLE_PATTERN/d" $TRIMMED_FILE

    # print all modified lines again to trimmed file
    sed -i "/$LOGS_INSERT_LINE_MARKER/ r $TMP_FILE" $TRIMMED_FILE

    # delete tmp files
    rm $TMP_FILE $TMP2_FILE
}

function main {
    say "SqlDumpCleaner by kojoty started!"

    say "Initial sql dump file size:"
    printFileSize $SQL_FILE

    say "Copy necessary data from gzip file"
    trimFile

    say "Change cache passwords"
    changeCachesPassword

    say "Change user passwords"
    changeUserPasswords

    say "Remove most of cache logs"
    removeLogTexts

    say "Compress result file"
    gzip -9 -c $TRIMMED_FILE > $TRIMMED_FILE.gz

    say "Final dev-dump file size"
    printFileSize $TRIMMED_FILE.gz
}
 
main


