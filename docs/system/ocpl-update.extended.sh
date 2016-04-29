#!/bin/sh

# The name and location of this scripts should match the location in
# the following script:
# https://github.com/opencaching/opencaching-pl/blob/master/post-commit.php

# site name, FQDN, not URL
SITE_NAME=YOUR_OPENCACHING_HOSTNAME
# hosting root directory
SITE_ROOT=/path/to/OPENCACHING-PL
# user owner of the files.
# must have primary group same as webserver user
SITE_USER=oc_user
# sudo command to run the update as SITE_USER
# comment it if unused
SUDO_CMD="sudo -u ${SITE_USER}"

# repository
REPO=https://github.com/opencaching/opencaching-pl.git
# branch
BRANCH=master
# update command
CMD="git pull --rebase"
CMD_PRE="git stash"
CMD_POST="git stash pop"

# set to 1 to log updates
DO_LOG=1
# define log facility and level
LOG_LEVEL="-p cron.info"
# set to 1 to preserve local changes
KEEP_LOCAL=0

# Updating $SITE_NAME at $SITE_ROOT ...

if [ "$DO_LOG" == "1" ]; then 
    ( 
    cd ${SITE_ROOT}
    echo "Updating ${SITE_NAME}..."				| logger ${LOG_LEVEL}
    # run update command(s)
    if [ "$KEEP_LOCAL" == "1" ]; then
	${SUDO_CMD} ${CMD_PRE} 2>&1 > /dev/null
    fi

    # repository update
    ${SUDO_CMD} ${CMD} ${REPO} ${BRANCH} 2>&1			| logger ${LOG_LEVEL}

#    echo "- GIT status and conflicts (if any)."			| logger ${LOG_LEVEL}

    if [ "$KEEP_LOCAL" == "1" ]; then
	${SUDO_CMD} ${CMD_POST} 2>&1 > /dev/null
    fi
    echo "- GIT update completed."				| logger ${LOG_LEVEL}
    echo 							| logger ${LOG_LEVEL}

    # run OKAPI update
    echo "Running OKAPI update scripts..."			| logger ${LOG_LEVEL}
    wget -O - -q http://${SITE_NAME}/okapi/update		| logger ${LOG_LEVEL}
    echo "--- Done." 						| logger ${LOG_LEVEL}
    echo 							| logger ${LOG_LEVEL}
    )
else
    ( 
    cd ${SITE_ROOT}
    # run update command(s) (no logging)
    if [ "$KEEP_LOCAL" == "1" ]; then
	${SUDO_CMD} ${CMD_PRE} 2>&1 > /dev/null
    fi
    # repository update
    ${SUDO_CMD} ${CMD} ${REPO} ${BRANCH} 2> /dev/null 1> /dev/null
    if [ "$KEEP_LOCAL" == "1" ]; then
	${SUDO_CMD} ${CMD_POST} 2>&1 > /dev/null
    fi

    # run OKAPI update
    wget -O - -q http://${SITE_NAME}/okapi/update 2>&1 > /dev/null
    )
fi
