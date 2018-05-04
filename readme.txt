
/var/cron/tabs/www

*/7    *     *       *       *       cd /data/sites_php71/scripts/proxygrabber &&  /usr/local/bin/php  /data/sites_php71/scripts/proxygrabber/check.php  > /dev/null 2>&1
*/10    *     *       *       *       cd /data/sites_php71/scripts/proxygrabber &&  /usr/local/bin/php  /data/sites_php71/scripts/proxygrabber/parse.php  > /dev/null 2>&1

