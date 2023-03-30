#!/bin/bash

sudo chown -R www-data:www-data /var/www/html && sudo service apache2 start

while sleep 1000; do :; done