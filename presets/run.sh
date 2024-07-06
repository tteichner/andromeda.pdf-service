#!/bin/bash

# create log files and remove unused ones
touch /var/log/php-fpm.log
chown www-data:www-data /var/log/php-fpm.log
rm -f /var/log/php7.0-fpm.log
rm -f /var/log/fpm-php.log
rm -f /var/log/php7.4-fpm.log
rm -rf /tmp/
mkdir /tmp
chmod 777 /tmp

# special change for other host
if [[ "x$FAA_STRIP_ABI_TAG" == "x1" ]] ; then
    strip --remove-section=.note.ABI-tag /usr/lib/x86_64-linux-gnu/libQt5Core.so.5
fi

# start the server
/etc/init.d/php-fpm start

# hand over to supervisor
supervisord -c /etc/supervisord.conf
