##How To Install Extension Manually

- Download Ithinklogistics Extension zip file from magento marketplace
- Create New Directory “Ithinklogistics/Ithinklogistics” inside magento folder(app/code)

##Folder structure like this:
“app/code/Ithinklogistics/Ithinklogistics/” => Unzip extension file here

Run the following command in Magento 2 root folder:
```
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy
php bin/magento cache:flush
```