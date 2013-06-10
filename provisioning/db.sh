#!/bin/bash

mysql -e "SHOW DATABASES LIKE 'myduval'" | grep -q myduval
[ $? -eq 0 ] && exit

set -e

mysqladmin create myduval
echo "grant all privileges on myduval.* to myduval@localhost identified by '12345678';" | mysql 

sed -e 's/www\.myduval\.com/localhost:8080/g' -e 's|/ruta/al/foro|/vagrant/foro|g' /vagrant/DB/duval_dump.sql | mysql -u myduval -p12345678 myduval

