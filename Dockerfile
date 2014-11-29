#################################################################
## DiscoDonniePresents.com wpCloud Site
##
## - nrsysmond-config --set license_key=f3f909635f44aa45e6d4f5f7d99e6a05c6114c11
##
## @version 1.0.1
## @author potanin@UD
#################################################################

FROM          wpcloud/site:0.2.0

RUN           rm -rf /var/www/**

ADD           /wp-admin       /var/www/wp-admin
ADD           /wp-content     /var/www/wp-content
ADD           /wp-includes    /var/www/wp-includes
ADD           /wp-vendor      /var/www/wp-vendor
ADD           /*.php          /var/www/
ADD           /*.json         /var/www/
ADD           /*.yml          /var/www/
ADD           /.htaccess      /var/www/

ENV           DOCKER_IMAGE                                            DiscoDonniePresents/www.discodonniepresents.com
ENV           DOCKER_REGISTRY                                         http://registry.wpcloud.io
