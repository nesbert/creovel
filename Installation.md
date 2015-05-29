# Installation #

Creovel has been developed in a LAMP environment. It has been installed on Linux, FreeBSD, OS X and Windows (use at your own risk).

Requirements:

  * PHP 5
  * MySQL 4 (_more database adapters coming..._)
  * Apache
  * Linux or FreeBSD or OS X


Default Apache Directory that forms the main document tree visible from the web.
```
DocumentRoot /usr/local/apache/htdocs
```

## Single Website Environment ##

Change the default path to the following:
```
DocumentRoot /usr/local/apache/htdocs/creovel/public
```

Extract Creovel into htdocs directory. Directory structure below:
```
.../creovel
	/app
		/controllers
		/helpers
		/models
		/views
	/config
	/db
	/log
	/public (DocumentRoot)
	/script
	/test
	/vendor
```

_This is one example of the many ways of setting up Creovel for a single website environment._

## Multiple Website Environment ##

With the use the all mighty _.htaccess_ we can set up a multiple domain environment. You will find the _.htaccess_ file and our directory structure for a three site environment below.

**_.htaccess_**
```
RewriteEngine On
Options +FollowSymlinks
RewriteBase /

# code subdomain
RewriteCond %{HTTP_HOST} code.creovel.org
RewriteCond %{REQUEST_URI} code.creovel.org/public/
RewriteRule ^(.*)$ code.creovel.org/public/$1 [L]

# docs subdomain
RewriteCond %{HTTP_HOST} docs.creovel.org
RewriteCond %{REQUEST_URI} !docs.creovel.org/public/
RewriteRule ^(.*)$ docs.creovel.org/public/$1 [L]

# main site
RewriteCond %{HTTP_HOST} creovel.org
RewriteCond %{REQUEST_URI} !www.creovel.org/public/
RewriteRule ^(.*)$ www.creovel.org/public/$1 [L]
```

**_Directory Structure_**
```
.../htdocs (DocumentRoot)
	.htaccess (the almighty)
	/code.creovel.org
		/app
			/controllers
			/helpers
			/models
			/views
		/config
		/db
		/log
		/public
		/script
		/test
		/vendor
	/docs.creovel.org
		/app
			/controllers
			/helpers
			/models
			/views
		/config
		/db
		/log
		/public
		/script
		/test
		/vendor
	/www.creovel.org
		/app
			/controllers
			/helpers
			/models
			/views
		/config
		/db
		/log
		/public
		/script
		/test
		/vendor
```

_Again, this is one example of the many ways of setting up Creovel for a multiple website environment._

## Browse and See ##
Once installed browse to your site and you will see a [welcome screen](http://www.creovel.org/welcome). You are now ready to _build faster_.