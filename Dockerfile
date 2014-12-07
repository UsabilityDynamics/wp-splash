#################################################################
## DiscoDonniePresents.com wpCloud Site
##
## - nrsysmond-config --set license_key=f3f909635f44aa45e6d4f5f7d99e6a05c6114c11
##
## @version 1.0.1
## @author potanin@UD
#################################################################

FROM          wpcloud/site:latest
MAINTAINER    UsabilityDynamics, Inc.   <info@usabilitydynamics.com>

RUN           rm -rf /var/www/**

ADD           /wp-admin             /var/www/wp-admin
ADD           /wp-content           /var/www/wp-content
ADD           /wp-includes          /var/www/wp-includes
ADD           /wp-vendor            /var/www/wp-vendor
ADD           /.htaccess            /var/www/
ADD           /composer.json        /var/www/
ADD           /index.php            /var/www/
ADD           /wp-cli.yml           /var/www/
ADD           /wp-activate.php      /var/www/
ADD           /wp-blog-header.php   /var/www/
ADD           /wp-config.php        /var/www/
ADD           /wp-cron.php          /var/www/
ADD           /wp-links-opml.php    /var/www/
ADD           /wp-load.php          /var/www/
ADD           /wp-login.php         /var/www/
ADD           /wp-mail.php          /var/www/
ADD           /wp-settings.php      /var/www/
ADD           /wp-signup.php        /var/www/
ADD           /wp-trackback.php     /var/www/
ADD           /xmlrpc.php           /var/www/

ENV           DOCKER_IMAGE          DiscoDonniePresents/www.discodonniepresents.com
ENV           DOCKER_REGISTRY       http://registry.wpcloud.io
