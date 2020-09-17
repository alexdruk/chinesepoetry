#!/bin/bash

sudo /usr/local/bin/certbot-auto -c /etc/letsencrypt/cli_renewal.ini certonly --webroot -w /var/www/html/ -d chinese-poetry.ru -d www.chinese-poetry.ru
sudo /usr/local/bin/certbot-auto -c /etc/letsencrypt/cli_renewal.ini certonly --webroot -w /var/www/html/admin/ -d edit.chinese-poetry.ru 