#!/bin/bash
set -e

docker-compose down --volumes
docker rmi vuex_vue_web vuex_vue_php