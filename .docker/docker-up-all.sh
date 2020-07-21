#!/bin/bash
# Pull the required image
docker pull linhntatdsquare/uempnv:latest
# Refesh database
if [[ $@ =~ "--db-refesh" ]] 
then
    docker volume rm docker_mysql
fi
# Launch the container
docker-compose -f docker-compose-all.yml up
