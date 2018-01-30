#!/usr/bin/env bash

function info {
    printf "\033[0;36m===> \033[0;33m${1}\033[0m\n"
}

mkdir -p /var/www/app/logs/supervisor

info "Run RabbitMQ"
php app/console rabbitmq:consumer -m 50

info "Add Cron Job"
echo '0 0 * * * root php /var/www/app/console erp:recurring-payment:check' >> /etc/crontab
echo '0 1 * * * root php /var/www/app/console erp:rent-payment:check' >> /etc/crontab

exec "$@"