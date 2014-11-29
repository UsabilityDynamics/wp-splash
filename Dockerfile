#################################################################
## DiscoDonniePresents.com wpCloud Site
##
## @version 1.0.1
## @author potanin@UD
#################################################################

FROM          wpcloud/site

# RUN           nrsysmond-config --set license_key=f3f909635f44aa45e6d4f5f7d99e6a05c6114c11

ADD           / /var/www

ENV           DOCKER_IMAGE                                            DiscoDonniePresents/www.discodonniepresents.com
ENV           DOCKER_REGISTRY                                         http://registry.wpcloud.io
