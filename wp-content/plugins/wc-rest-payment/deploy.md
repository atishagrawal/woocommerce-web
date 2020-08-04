# wc-rest-payment.php

Add Changelog in readme.txt

> x.x.x

Update version number in /wc-rest-payment.php

> * Version:           x.x.x
> define('WC_REST_PAYMENT_VERSION', 'x.x.x');

# Git

> git add .
> git commit -m "Release x.x.x"
> git tag x.x.x
> git push
> git push --tag

# SVN

> svn co https://plugins.svn.wordpress.org/wc-rest-payment
## Copy every file from git repository to temp/trunk
> cp -R ./* temp/trunk/
## Manually Copy files to temp/tags folder for release
## Add new files to SVN
> svn add * --force
## Use SVN to commit new release
> svn ci -m "Release x.x.x"