#!/bin/bash -e

site_path=/etc/apache2/sites-enabled/duval

[ -f "$site_path" ] && exit

default_path=/etc/apache2/sites-enabled/000-default
[ -f "$default_path" ] && rm "$default_path"

# TODO copiarlo a sites-available y linkear a sites-enabled
cp /vagrant/provisioning/apache.conf "$site_path"

a2enmod rewrite

apachectl restart

