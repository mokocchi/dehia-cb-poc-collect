# DEHIA Circuit Breaker PoC Collect Service
One of the services for a [Circuit Breaker](https://docs.microsoft.com/en-us/azure/architecture/patterns/circuit-breaker) Proof-of-Concept using a DEHIA platform simplification

## Contents
- [Proof of Concept](#proof-of-concept)
- [Installation](#installation)
  - [Docker](#docker-recommended)
  - [Run locally (Linux)](#run-locally-linux)
- [Deploying to Heroku](#deploying-to-heroku)
  - [Prerequisites](#prerequisites)
  - [Deploy](#deploy)
- [Environment Variables](#environment-variables)
  - [Docker variables](#docker-variables)
  - [PHP variables](#php-variables)
- [Endpoints](#endpoints)
- [See Also](#see-also)

## Proof of Concept
The Results Service has a [Circuit Breaker](https://docs.microsoft.com/en-us/azure/architecture/patterns/circuit-breaker) that can be enabled *by user* (for testing purposes).
The Collect Service can be disabled *by user* (again, for testing purposes).

The Results Service asks the Collect Service for the last results. If the Collect Service is "down" (disabled for the user) the request fails.

If the Circuit Breaker is enabled, when the first request fails, the circuit "opens" and another path is taken: the Results Service returns the last cached results instead. For a fixed amount of time (default: 3 minutes) the Results Service doesn't attempt a new request. When the time is up, the circuit "closes" again and requests can be made again.

## Installation
You can install the gateway either in containerized version using Docker or locally (on Linux) using PHP7.4 and Apache or NGINX.
### Docker (recommended)
 1. Create an `app/.env.local` file based in `app/.env` (See [Environment Variables](#Environment-Variables))
 2. If the results service or the gateway are also run with docker, take note of the docker network.
 3. Build the image: 

 ```
 docker image build -t <image-tag> .
 ```
 4. Run the container - Only if needed: a) Expose the port you set in the `.env` file (if the gateway or the results service aren't run with Docker) b) Use a Docker network (if the gateway or the results service are run with docker). If one is run with Docker and the not the other, you will need both.
 ```
 docker run -e PORT=<container-port> --name <container-name> [-p <host-port>:<container-port>] [--network <poc-network>] <image-tag>
 ```
 5. Go to `http://localhost:<host-port>`. You should see a "Collect Index" message.
 6. Now you can add the URL to the results service and the gateway.

## Run locally (Linux)
1. Make sure you have PHP 7.4 installed
```
php --version
```
2. Instal curl
```
sudo apt install curl libcurl4 libcurl4-openssl-dev php7.4-curl
```
3. Install the following PHP extensions: `mbstring`, `xml`, `curl`, `sqlite3`
```
sudo apt-get install php7.4-mbstring php7.4-xml php7.4-curl php7.4-sqlite3
```

4. Open a terminal in the `app` directory and install [Composer](https://getcomposer.org/download/) by running this script (you can additionally move the executable to /usr/local/bin to get global access):
```
#!/bin/sh
EXPECTED_CHECKSUM="$(php -r 'copy("https://composer.github.io/installer.sig", "php://stdout");')"
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
ACTUAL_CHECKSUM="$(php -r "echo hash_file('sha384', 'composer-setup.php');")"

if [ "$EXPECTED_CHECKSUM" != "$ACTUAL_CHECKSUM" ]
then
    >&2 echo 'ERROR: Invalid installer checksum'
    rm composer-setup.php
    exit 1
fi

php composer-setup.php --quiet
RESULT=$?
rm composer-setup.php
exit $RESULT
```
5. Create an `app/.env.local` file based in `app/.env` (See [Environment Variables](#Environment-Variables))
6. Update dependencies and install the application
``` 
php composer.phar update --no-dev
```
7. Create the database 
```
bin/console doctrine:database:create
```
8. Run the migrations
```
bin/console doctrine:migrations:migrate
``` 
9. Copy the `app` repository to `/var/www`
```
sudo cp -r app /var/www
```
10. Rename the folder
```
sudo mv /var/www/app /var/www/collect
```
11. Crate a `collect.conf` file in /etc/apache2/sites-available` with the following content (set a different port if you want): 
```
# collect.conf
Listen 85

<VirtualHost *:85>
        DocumentRoot /var/www/collect/public

        ErrorLog ${APACHE_LOG_DIR}/error.log
        CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>

```
13. Enable the site for the app
```
sudo a2ensite collect && systemctl reload apache2
```
14. Go to `http://localhost:<host-port>`. You should see a "Collect Index" message.
15. Now you can add the URL to the results service and the gateway.


## Deploying to Heroku
 You can deploy the dockerized version to Heroku if you want.
 ### Prerequisites
 - Having the [Heroku CLI](https://devcenter.heroku.com/articles/heroku-cli) installed
 - Having a heroku account and room for one more app

 ### Deploy
  1. Login in to the Heroku CLI
  ```
  heroku login
  ```
  2. Create a new app
  ```
  heroku create
  ```
  3. You can now change the app name if you want at the Heroku [Dashboard](https://dashboard.heroku.com/)
  4. Set the [Environment Variables](#Environment-Variables) from the Dashboard
  5. Set the stack to `container`
  ```
  heroku stack:set container
  ```
  6. Push app to heroku
  ```
  git push heroku master
  ```
  7. Go to `https://<your-app>.herokuapp.com`. You should see a "Collect Index" message.
  8. Now you can add the URL to the results service and the gateway.

# Environment Variables
PHP variables go in the `app/.env.local` file.
## PHP variables
- **JWT_SECRET**: symmetric key for signing the internal tokens (gateway <-> services). It must be the same in the gateway and the results service.

## Endpoints
- `GET api/v1.0/collect-status`: returns `{status: "OK"}` if the Collect service is running normally or `{status: "SUSPENDED"}` if it's disabled for the current user. Secured endpoint*.
- `POST api/v1.0/switch`: disables the Collect service for the current user. Secured endpoint*.
- `DELETE api/v1.0/switch`: enables the Collect service for the current user. Secured endpoint*.


*Secured endpoint: it needs an `Authorization: Bearer <JWT-token>` header, where `JWT-token` is obtained from the gateway


## See also
- [DEHIA Circuit Breaker PoC Gateway](https://github.com/mokocchi/dehia-cb-poc-gateway)
- [DEHIA Circuit Breaker PoC Results Service](https://github.com/mokocchi/dehia-cb-poc-results)
- [DEHIA Circuit Breaker PoC Frontend](https://github.com/mokocchi/dehia-cb-poc-frontend)
