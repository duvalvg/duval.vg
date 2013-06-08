#!/bin/bash

dpkg -l mysql-server >/dev/null 2>&1
[ $? -eq 0 ] && exit

set -e

export DEBIAN_FRONTEND=noninteractive
apt-get update && apt-get -y install mysql-server
