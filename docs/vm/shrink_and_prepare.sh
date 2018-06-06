# Use this script before publishing new version od OCPL-DEVEL VM.

echo "Checking local uncommited changes..."
if [ `sudo git status /srv/ocpl --porcelain | wc -l` -gt 0 ]
then
    echo "UNCOMMITED CHANGES DETECTED. CANCELLED."
    exit 1
fi

echo "Stopping services (until next restart)..."
sudo service cron stop
sudo service apache2 stop

echo "Removing history and preferences..."
rm -f ~/.*_history ~/.selected_editor ~/.lesshst
sudo rm -f ~/.viminfo
sudo rm -f /root/.*_history /root/.selected_editor /root/.lesshst

echo "Removing unimportant data..."
echo "delete from okapi_cache where \`key\` != 'cron_schedule'" | mysql -pubuntu ocpl

# be sure that hidden caches are removed from DB:
echo "DELETE FROM caches WHERE caches.status IN (4,5,6)" | mysql -pubuntu ocpl
echo "DELETE ca FROM caches_additions ca LEFT JOIN caches c USING (cache_id) WHERE c.cache_id IS NULL" | mysql -pubuntu ocpl
echo "DELETE ca FROM caches_attributes ca LEFT JOIN caches c USING (cache_id) WHERE c.cache_id IS NULL" | mysql -pubuntu ocpl
echo "DELETE ca FROM cache_arch ca LEFT JOIN caches c USING (cache_id) WHERE c.cache_id IS NULL" | mysql -pubuntu ocpl
echo "DELETE ca FROM cache_logs ca LEFT JOIN caches c USING (cache_id) WHERE c.cache_id IS NULL" | mysql -pubuntu ocpl
echo "DELETE ca FROM cache_moved ca LEFT JOIN caches c USING (cache_id) WHERE c.cache_id IS NULL" | mysql -pubuntu ocpl
echo "DELETE ca FROM waypoints ca LEFT JOIN caches c USING (cache_id) WHERE c.cache_id IS NULL" | mysql -pubuntu ocpl
echo "DELETE ca FROM cache_npa_areas ca LEFT JOIN caches c USING (cache_id) WHERE c.cache_id IS NULL" | mysql -pubuntu ocpl
echo "DELETE pc FROM powerTrail_caches pc LEFT JOIN caches c ON pc.cacheId = c.cache_id WHERE c.cache_id IS NULL" | mysql -pubuntu ocpl

echo "truncate sys_logins" | mysql -pubuntu ocpl
echo "truncate sys_sessions" | mysql -pubuntu ocpl
echo "truncate okapi_stats_hourly" | mysql -pubuntu ocpl
echo "truncate okapi_stats_temp" | mysql -pubuntu ocpl

sudo rm -fR /srv/ocpl-dynamic-files/okapi-db-dump*
sudo rm -f /srv/ocpl-dynamic-files/statpics/*
sudo rm -f /var/log/apache2/*.log

echo "Removing old packages..."
sudo apt-get autoremove
sudo apt-get autoclean
sudo apt-get clean

echo "Compressing MySQL tables..."
mysqladmin -uroot -ptoor flush-tables
mysqlcheck -uroot -ptoor -o ocpl
mysqladmin -uroot -ptoor flush-tables

echo "Stopping mysql service..."
sudo service mysql stop


echo "Preparing for shrink (filling unused space with zeroes)..."
cat /dev/zero | pv -s 50g > zero.fill; sync; sleep 1; sync; rm -f zero.fill

echo "Preparing for shrink (filling swap partition with zeroes)..."
if [ `cat /proc/swaps | grep "/dev/sda5" | wc -l` -eq 1 ]
then
    sudo swapoff /dev/sda5
    sudo sswap -fllvz /dev/sda5
    sudo swapon /dev/sda5
else
    echo "-> /dev/sda5 is NOT a swap partition on your system, skipping this step."
fi

echo "Done. (You're ready for 'sudo shutdown -P 0')"












