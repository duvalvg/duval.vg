#!/bin/bash

dpkg -l apache2 >/dev/null 2>&1
[ $? -eq 0 ] && exit

set -e

apt-get install -y apache2
