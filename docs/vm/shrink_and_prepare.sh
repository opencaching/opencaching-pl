# Use this script before publishing new version od OCPL-DEVEL VM.

echo "Removing history and preferences..."
rm -f ~/.*_history ~/.selected_editor ~/.lesshst
rm -fR ~/.subversion/auth
sudo rm -f ~/.viminfo
sudo rm -f /root/.*_history /root/.selected_editor /root/.lesshst
sudo rm -fR /root/.subversion/auth
echo "Removing unimportant data..."
echo "delete from okapi_cache where \`key\` != 'cron_schedule'" | mysql -pubuntu ocpl
sudo rm -fR /srv/ocpl-dynamic-files/okapi-dump*
sudo rm -f /srv/ocpl-dynamic-files/statpics/*
echo "Removing old packages..."
sudo apt-get clean
echo "Compressing MySQL tables..."
mysqladmin -uroot -ptoor flush-tables
mysqlcheck -uroot -ptoor -o ocpl
mysqladmin -uroot -ptoor flush-tables
echo "Preparing for shrink (filling unused space with zeroes)..."
cat /dev/zero | pv -s 50g > zero.fill; sync; sleep 1; sync; rm -f zero.fill
echo "Done."
