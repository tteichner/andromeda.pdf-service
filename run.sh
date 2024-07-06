#!/bin/bash
set -e

# version to start comes from external caller
image="softwarefactories/pdf-service:$1"

# check if container is there
tag_test=$(docker images "$image" -q)
if [[ -z  ${tag_test} ]]; then
    echo "Image $image is missing"
    docker pull ${image}
fi

# delete other containers
tag_test=$(docker ps --filter 'name=pdf-service' -q)
if [[ ! -z  ${tag_test} ]]; then
    docker ps --filter 'name=pdf-service' -q | xargs docker stop
fi
tag_test=$(docker ps --filter "name=pdf-service" -q -a)
if [[ ! -z  ${tag_test} ]]; then
    docker rm $(docker ps -a -q --filter "name=pdf-service")
fi

# build the log folders
mkdir -p /var/log/docker-images/pdf-service/nginx
mkdir -p /var/log/docker-images/pdf-service/letsencrypt
mkdir -p /var/log/docker-images/pdf-service/app

# build the log files
touch /var/log/docker-images/pdf-service/nginx/error.log
touch /var/log/docker-images/pdf-service/nginx/access.log
touch /var/log/docker-images/pdf-service/syslog
touch /var/log/docker-images/pdf-service/faillog
touch /var/log/docker-images/pdf-service/messages
chmod 777 /var/log/docker-images/pdf-service -R

# start container
echo "Starting $image"
docker run --name pdf-service.local \
           --volume=/var/log/docker-images/pdf-service:/var/log \
           --net locale -d -t ${image}
