#!/bin/bash
# MySQL
### Set directory's permission; without it, service cannot start
chown -R mysql:mysql /var/lib/mysql
### Start service
service mysql start
### Create database
NONE_EXISTED=$(mysql -s -N -e "SELECT schema_name FROM information_schema.schemata where schema_name = 'base';")
if [[ -z "${NONE_EXISTED}" ]]; then
	mysql -e "CREATE DATABASE base;"
	### Create new user for database
	mysql -e "CREATE USER 'base'@'localhost' IDENTIFIED BY 'base';"
	### Make sure user can handle all actions on the database
	mysql -e "GRANT ALL ON *.* TO 'base'@'localhost';" #
	### Apply the change of user permissions
	mysql -e "FLUSH PRIVILEGES;"
	### Create database structure and data by script
	mysql -e "USE base;SOURCE /dsquare/base/.docker/database.sql;"
fi
# Laravel
### Install packages
composer install --working-dir=/dsquare/base
### Replace application environment
cp /dsquare/base/.docker/.env.api /dsquare/base/.env
### Require permissions for Laravel app
chmod -R 777 /dsquare/base/bootstrap/cache
chmod -R 777 /dsquare/base/storage
### Make sure Laravel schedule is ready to run
echo "* * * * * php /dsquare/base/artisan schedule:run >> /dev/null 2>&1" >> /etc/crontab
### Create symlink of public storage
php /dsquare/base/artisan storage:link
# sendmail
### Start service
service sendmail start
# Supervisor
### Configuration to run Laravel queue
cp /dsquare/base/.docker/supervisor.conf /etc/supervisor/conf.d/base.conf
### Start service
service supervisor start
# PHP
### Start service
service php7.3-fpm start
# NGINX configuration
### Replace the configuration to run application
cp /dsquare/base/.docker/nginx.conf /etc/nginx/sites-available/default
### Make sure the NGINX run in foreground; without it, docker container will stop running
echo "daemon off;" >> /etc/nginx/nginx.conf
### Start service
service nginx start
