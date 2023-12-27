# Deploying to Staging

`bowdoinorient.com` is hosted on WPEngine, a managed WordPress hosting vendor.

## Your SFTP Account

For every site, WPEngine provides two environments: a production environment and a staging environment. The staging environment has the same managed configuration as the production environment, so if something works on staging it'll almost certainly work on production. (This is not necessarily the case with [Local Development](Local%20Development.md); if something works locally, it might not work on staging or production. This is why it's important to test your changes on Staging before deploying them to production).

Bowdoin IT maintains the WPEngine subscription, and they vend SFTP accounts to upload source code files to the staging site. If you don't have an SFTP account, ask someone in IT. Once they give you an SFTP username and password, store them somewhere safe!

## The Upload Script

Once you have an SFTP username and password, you can use them with the upload script. From the root of the project, run:

```sh
$ npm run upload
```

This will start the upload tool. It will ask for your SFTP username and password, and then it will ask you which bundles you want to upload. You only need to upload what you've changed.

Once your files have been uploaded, you can see the published staging site at **<https://bowdoinoriedev.wpengine.com>**

## The Staging Environment

The staging environment uses WPEngine's caching system to make pages load faster. This means you might have to wait a few minutes before your changes are actually displayed on the staging site. If you need to clear the cache and see your changes immediately, you can do that from the WordPress admin interface [here](https://bowdoinoriedev.wpengine.com/wp-admin/admin.php?page=wpengine-common&tab=caching).vv