# README

This is a [docker-compose](https://docs.docker.com/compose/) development environment fo this project.

As long as you have docker-compose installed on your system,
you should be able to install the project's dependencies by going into this directory in a terminal
and running `docker-compose run composer_install`.

You can then run the PHPUnit tests by running `sudo docker-compose run php_cli ./vendor/bin/phpunit`, 
and run the [behat](http://behat.org/en/latest/) feature tests by running `sudo docker-compose run php_cli ./vendor/bin/behat`.