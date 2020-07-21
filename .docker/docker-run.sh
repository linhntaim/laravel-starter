#!/bin/bash
# Stop the container
docker-compose down
# Pull the required image
docker pull linhntatdsquare/uemp:latest
# Refesh database
if [[ $@ =~ "--db-refesh" ]] 
then
    docker volume rm docker_mysql
fi
# Launch the container
docker-compose up
