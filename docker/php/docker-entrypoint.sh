#!/usr/bin/env bash

function info {
  printf "\033[0;36m===> \033[0;33m${1}\033[0m\n"
}

if [ ! -d "/var/www/app/logs/supervisor" ]; then
    mkdir -p /var/www/app/logs/supervisor
fi

if [ ! -d "/var/www/web/uploads" ]; then
    mkdir -p /var/www/web/uploads
    chown -R www-data:www-data /var/www/web/uploads
fi

chown -R www-data:www-data /var/www/app/cache
chown -R www-data:www-data /var/www/app/logs

info "Run RabbitMQ"
php app/console rabbitmq:consumer -m 50

info "Add Cron Job"
(crontab -l ; echo "0 0 * * * /var/www/site/app/console erp:property:scheduled-payment-check") | crontab -
(crontab -l ; echo "0 0 * * * /var/www/site/app/console erp:stripe:subscription:check-end-of-trial-period") | crontab -
(crontab -l ; echo "0 1 * * * /var/www/site/app/console erp:property:rent-payment-check") | crontab -

exec "$@"