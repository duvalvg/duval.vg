#!/bin/bash

grep -q localhost:8080 /vagrant/foro/Settings.php
[ $? -eq 0 ] && exit

set -e

patch -p 1 -d /vagrant < /vagrant/provisioning/Settings.patch 
