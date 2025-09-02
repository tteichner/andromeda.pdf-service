#!/bin/bash
set -e

image="softwarefactories/pdf-service:1.5.6"
net_name='locale'
log_dir='/var/log/docker-images/pdf-service'
data_dir='/srv/data/storage'

if [[ -d ".git" ]] ; then
    # get the latest git version tag
    git_tag=$(git for-each-ref refs/tags/v* --sort=-taggerdate --format='%(refname)' --count=1)
    git_tag=$(echo ${git_tag} | sed -e 's/refs\/tags\/v//g')
    image="softwarefactories/pdf-service:$git_tag"
fi

for ((i=1;i<=$#;i++));
do
    if [[ ${!i} == "--tag" ]] ; then ((i++))
        # version to start comes from external caller
        image="softwarefactories/pdf-service:${!i}"
    elif [[ ${!i} == "--net" ]] ; then ((i++))
        net_name=${!i}
    elif [[ ${!i} == "--logs" ]] ; then
        log_dir=${!i}
    fi
done;

# check if container is there
tag_test=$(docker images "$image" -q)
if [[ -z ${tag_test} ]]; then
    echo "Image $image is missing, pulling..."
    docker pull ${image}
fi

# delete other containers
tag_test=$(docker ps --filter 'name=pdf-service' -q)
if [[ ! -z ${tag_test} ]]; then
    docker ps --filter 'name=pdf-service' -q | xargs docker stop
fi
tag_test=$(docker ps --filter "name=pdf-service" -q -a)
if [[ ! -z ${tag_test} ]]; then
    docker rm $(docker ps -a -q --filter "name=pdf-service")
fi

# build the log folders
mkdir -p "$log_dir/nginx"
mkdir -p "$log_dir/letsencrypt"
mkdir -p "$log_dir/app"
mkdir -p "$data_dir"

# build the log files
touch "$log_dir/nginx/error.log"
touch "$log_dir/nginx/access.log"
touch "$log_dir/syslog"
touch "$log_dir/faillog"
touch "$log_dir/messages"
chmod 777 "$log_dir" -R

# start container
echo "Starting $image"
docker run --name pdf-service.local \
       --volume="$log_dir:/var/log" \
       --volume="$data_dir:/var/www/storage" \
       -e "FAA_PDF_SERVICE_PASS=$FAA_PDF_SERVICE_PASS" \
       --net "$net_name" -d -t ${image}
