# Use this script before publishing new version od OCPL-DEVEL VM.

echo "Checking local uncommited changes..."
if [ `svn st /srv/ocpl | wc -l` -gt 0 ]
then
    echo "UNCOMMITED CHANGES DETECTED. CANCELLED."
    exit 1
fi
echo "Stopping services (until next restart)..."
sudo service cron stop
sudo service apache2 stop
echo "Removing history and preferences..."
rm -f ~/.*_history ~/.selected_editor ~/.lesshst
rm -fR ~/.subversion/auth
sudo rm -f ~/.viminfo
sudo rm -f /root/.*_history /root/.selected_editor /root/.lesshst
sudo rm -fR /root/.subversion/auth
echo "Removing unimportant data..."
echo "delete from okapi_cache where \`key\` != 'cron_schedule'" | mysql -pubuntu ocpl
echo "truncate sys_logins" | mysql -pubuntu ocpl
echo "truncate sys_sessions" | mysql -pubuntu ocpl
echo "truncate okapi_stats_hourly" | mysql -pubuntu ocpl
echo "truncate okapi_stats_temp" | mysql -pubuntu ocpl
sudo rm -fR /srv/ocpl-dynamic-files/okapi-dump*
sudo rm -f /srv/ocpl-dynamic-files/statpics/*
sudo rm -f /var/log/apache2/*.log
echo "Removing old packages..."
sudo apt-get clean
echo "Compressing MySQL tables..."
mysqladmin -uroot -ptoor flush-tables
mysqlcheck -uroot -ptoor -o ocpl
mysqladmin -uroot -ptoor flush-tables
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
echo "Done. (You're ready for `sudo shutdown -P 0`)"
