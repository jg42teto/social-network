#!/bin/bash

prj_root=$(dirname $0)
docker_compose_path=$prj_root/docker-compose.yml

echo "Populating database... (may take some time)"

docker-compose -f $docker_compose_path exec app php artisan db:seed
