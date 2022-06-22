## Documentation

- Installation guide: https://www.magenest.com/install-magento-2-extension/#solution-1-ready-to-paste
- User Guide: https://docs.magenest.com/store-credit/index.html
- Product page: https://www.magenest.com/magento-2-store-credit/
- FAQs: https://www.magenest.com/faqs/
- Get Support: https://www.magenest.com/contact.html or support@magenest.com
- Changelog: https://www.magenest.com/releases/store-credit
- License agreement: https://www.magenest.com/LICENSE.txt

## How to install

### Install ready-to-paste package (Recommended)

- Installation guide: https://www.magenest.com/install-magento-2-extension/

## How to upgrade

1. Backup

Backup your Magento code, database before upgrading.

2. Remove StoreCredit folder 

In case of customization, you should backup the customized files and modify in newer version. 
Now you remove `app/code/Magenest/StoreCredit` folder. In this step, you can copy override StoreCredit folder but this may cause of compilation issue. That why you should remove it.

3. Upload new version
Upload this package to Magento root directory

4. Run command line:

```
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy
```


## FAQs


#### Q: I got error: `Magenest_Core has been already defined`
A: Read solution: https://github.com/magenest/module-core/issues/3


#### Q: My site is down
A: Please follow this guide: https://www.magenest.com/blog/magento-site-down.html


## Support

- FAQs: https://www.magenest.com/faqs/
- https://www.magenest.com/contact.html
- support@magenest.com
