#!/bin/bash
set -e

# version to start comes from external caller
git_tag="softwarefactories/pdf-service:$1"

# build image
sudo docker build -t "$git_tag" -f Dockerfile .
