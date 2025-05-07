php -dmemory_limit=6G  bin/magento c:c;
rm -rf var/view_preprocessed/*;
rm -rf pub/static/frontend/*;
rm -rf pub/static/adminhtml/*;
rm -rf pub/static/_cache/*;
php -dmemory_limit=6G  bin/magento setup:static-content:deploy -f en_US -t Codazon/unlimited_default;
php -dmemory_limit=6G  bin/magento setup:static-content:deploy -f en_US -t Codazon/unlimited_child;
php -dmemory_limit=6G  bin/magento setup:static-content:deploy -f en_US -t Magento/backend;
php -dmemory_limit=6G  bin/magento co:th:de -u;
rm -rf pub/media/codazon/amp/less/destination/*
#chmod -R 777 var pub generated;
