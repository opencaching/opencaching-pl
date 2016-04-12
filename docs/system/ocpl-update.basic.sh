# The name and location of this scripts should match the location in
# the following script:
# https://github.com/opencaching/opencaching-pl/blob/master/post-commit.php

echo Updating /path/to/OPENCACHING-PL ...
cd /path/to/OPENCACHING-PL

# This assumes that git has been set up properly with the project repository
# at https://github.com/opencaching/opencaching-pl.git
git stash
git pull --rebase
git stash pop

echo Running OKAPI update scripts...
wget -O - -q http://YOUR_OPENCACHING_HOSTNAME/okapi/update
