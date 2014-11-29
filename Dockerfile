#################################################################
## DiscoDonniePresents.com wpCloud Site
##
## @version 1.0.1
## @author potanin@UD
#################################################################

FROM          wpcloud/site

#ADD           /*.php /*.yml /*.json /.htaccess /.gitignore /wp-admin /wp-contenet /wp-inclucdes /wp-vendor /.git /var/www
ADD           / /var/www

ENV           DOCKER_IMAGE_NAME                                       DiscoDonniePresents/www.discodonniepresents.com
ENV           DOCKER_REGISTRY                                         http://registry.wpcloud.io
ENV           DEPLOYMENT_VERSION                                      2.0.1
ENV           AWS_ACCESS_KEY_ID                                       AKIAJCDAT2T7FESLH3IQ
ENV           AWS_SECRET_ACCESS_KEY                                   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
ENV           REPOSITORY_AUTH                                         8282b219ff377f9e209463564800879d7651b475
ENV           STORAGE_URI                                             gs://discodonniepresents.com