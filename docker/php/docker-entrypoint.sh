#!/usr/bin/env bash

function info {
    printf "\033[0;36m===> \033[0;33m${1}\033[0m\n"
}

info "Run RabbitMQ"
php app/console rabbitmq:consumer -m 50

info "Add Cron Job"
#echo '* * * * * root php /var/www/app/console erp:payment:charge' >> /etc/crontab

exec "$@"