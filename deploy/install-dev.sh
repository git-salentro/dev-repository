#!/usr/bin/env bash

#docker run --rm -v $(pwd):/app composer/composer install && \
docker-compose -f docker-compose-dev.yml exec php bash -c "php app/console doctrine:database:create --if-not-exists" && \
docker-compose -f docker-compose-dev.yml exec php bash -c "php app/console doctrine:migrations:migrate -n"
docker-compose -f docker-compose-dev.yml exec php bash -c "php app/console assets:install --symlink --env=dev"
docker-compose -f docker-compose-dev.yml exec php bash -c "php app/console assetic:dump --env=dev"
docker-compose -f docker-compose-dev.yml exec php bash -c "php app/console cache:clear --env=dev"

result=$?

if [ $result -eq 0 ]
then
  echo "Deploy succeed!"
else
  echo "Deploy failed!" >&2
fi

exit $result