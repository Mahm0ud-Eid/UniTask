FROM yiisoftware/yii2-php:8.2-fpm-nginx

# Change document root for Apache
RUN sed -i -e 's|/app/web|/app/api/web|g' /etc/apache2/sites-available/000-default.conf
