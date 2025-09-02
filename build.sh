#!/bin/bash
set -e

# version to start comes from external caller
app='pdf-service'
version="$1"
git_tag="softwarefactories/$app:$version"
green='\e[0;32m'
red='\e[0;31m'
endColor='\e[0m'

# build image
docker build -t "$git_tag" -f Dockerfile .

# get id of this image
id=$(docker images "$git_tag" -q)

# publish image to dockerhub, requires login before
echo "Tag and release image $app ($id) in version $version"
docker tag "$id" "softwarefactories/$app:latest"
if [[ $? -eq 0 ]]; then
    echo -e "[ ${green}OK!${endColor} ]"

    echo "Push to docker hub"
    docker push "softwarefactories/$app:$version" && docker push "softwarefactories/$app:latest"
    if [[ $? -eq 0 ]]; then
        echo -e "[ ${green}OK!${endColor} ]"
    else
        echo -e "[ ${red}FAILED!${endColor} ]"
    fi
else
    echo -e "[ ${red}FAILED!${endColor} ]"
fi