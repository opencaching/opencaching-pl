# The name and location of this scripts should match the location in
# the following script:
# https://code.google.com/p/opencaching-pl/source/browse/trunk/post-commit.php

# site name (FQDN)
SITE_NAME=www.opencaching.xx
# hosting root directory
SITE_ROOT=/var/www/site_root
# user owner of the files.
# must have primary group same as webserver user
SITE_USER=oc_xx
# sudo command to run "svn up" as SITE_USER
# comment it if unused
SUDO_CMD="sudo -u ${SITE_USER}"

# set to 1 to log updates
DO_LOG=1
# define log facility and level
LOG_LEVEL="-p cron.info"

if [ "$DO_LOG" == "1" ]; then
    echo "Updating ${SITE_NAME}..."         | logger ${LOG_LEVEL}
    # run "svn up"
    ${SUDO_CMD} svn up ${SITE_ROOT}             | logger ${LOG_LEVEL}
    echo "- SVN update completed."          | logger ${LOG_LEVEL}
    echo                        | logger ${LOG_LEVEL}

    echo "Running OKAPI update scripts..."      | logger ${LOG_LEVEL}
    wget -O - -q http://${SITE_NAME}/okapi/update   | logger ${LOG_LEVEL}
    echo "--- Done."                    | logger ${LOG_LEVEL}
    echo                        | logger ${LOG_LEVEL}
else
    # run "svn up"
    ${SUDO_CMD} svn up ${SITE_ROOT}
    wget -O - -q http://${SITE_NAME}/okapi/update
fi
