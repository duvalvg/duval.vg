#!/bin/bash

grep -q duval /etc/hosts
[ $? -eq 0 ] && exit

set -e

echo www.myduval.com 127.0.0.1 >>/etc/hosts
echo myduval.com 127.0.0.1 >>/etc/hosts

