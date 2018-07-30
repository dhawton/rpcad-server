#!/bin/sh
# This file is when the docker container is initalized
# It checks to see if the environment variable CAD_JOB_RUNNER is set to "true"
# if so, it creates the cron job to run the task scheduler. This is for docker containers
# only.  After checking for job runner, it passes control of the container to supervisord.

if [ "$CAD_JOB_RUNNER" == "true" ]; then
  echo "*    *    *    *    *    cd /www && php artisan schedule:run >> /dev/null 2>&1" >> /etc/crontabs/application
  cd /www && php artisan migrate --force
fi

/usr/bin/supervisord --nodaemon --configuration /etc/supervisord.conf
