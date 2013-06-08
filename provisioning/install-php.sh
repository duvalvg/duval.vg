#!/bin/bash

dpkg -l php5 >/dev/null 2>&1
[ $? -eq 0 ] && exit

set -e

apt-get install -y php5 php5-mysql

