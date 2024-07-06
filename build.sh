#!/bin/bash
set -e

# version to start comes from external caller
git_tag="$1/$2:$3"

# build image
docker build -t "$git_tag" -f Dockerfile .
