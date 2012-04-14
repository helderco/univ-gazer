Siriux Gazer Gallery
====================

Welcome to the Siriux Gazer Gallery application, a PHP project made for a
class in college, using Symfony2.

This document contains information on how to download and start using Gazer.
For a more detailed explanation on Symfony2 installation, see the
[Installation chapter](http://symfony.com/doc/current/book/installation.html)
of the Symfony Documentation.

1) Download Gazer
-----------------

To download Gazer, you have two options:

### Download an archive file (*recommended*)

The easiest way to get started is to download an archive of Gazer
(https://github.com/helderco/gazer/downloads) with vendors. Unpack it
somewhere under your web server root directory and you're done. The web
root is wherever your web server (e.g. Apache) looks when you access
`http://localhost` in a browser.

### Clone the git Repository

If you want to use Git, run the following commands:

    git clone git://github.com/helderco/gazer.git
    cd gazer
    mkdir -p app/logs app/cache web/uploads/media
    cp app/config/parameters.ini.dist app/config/parameters.ini

2) Installation
---------------

Once you've downloaded Gazer, installation is easy, and basically
involves making sure your system is ready for Symfony.

### a) Check your System Configuration

Before you begin, make sure that your local system is properly configured
for Symfony. To do this, execute the following:

    php app/check.php

If you get any warnings or recommendations, fix these now before moving on.

If the PHP version check failed, and you have a `php-5.3` and `php` installed
(where `php` is 5.2), you can create a symbolic link to `php-5.3` somewhere
and add it to your `$PATH`.

    ln -s `which php-5.3` ~/bin/php
    export PATH=~/bin/:$PATH

### b) Install the Vendor Libraries

If you downloaded via git, then you need to download all of the necessary
vendor libraries. If you're not sure if you need to do this, check to see
if you have a ``vendor/`` directory. If you don't, or if that directory is
empty, run the following:

    php bin/vendors install

Note that you **must** have git installed and be able to execute the `git`
command to execute this script. If you don't have git available, either install
it or download the Gazer archive with the vendor libraries already included.

### c) Configure

Edit the file `app/config/parameters.ini` to configure your database and
email credentials, and also create a secret key. If you don't yet have a
database created for this application, create one now.

If you downloaded the archive, it comes with a `gazer.sql` file that you can
use to create the database tables and the first user, but if you're using Git,
you can create the database tables with the following:

    php app/console doctrine:schema:create

And add an administrator:

    php app/console fos:user:create admin admin@gazer.org s3cr3t --super-admin

You now have your first user with access to the administration area, with
username `admin` and password `s3cr3t`.

### d) Access the Application via the Browser

Congratulations! You're now ready to use Gazer. If you've unzipped Gazer
in the web root of your computer, then you should be able to access the
application via:

    http://localhost/gazer/web/

It is recommended that you create a virtual host pointing to the `web` folder
of Gazer. If you know how to do this, you can have a URL like this:

    http://gazer.local/

### c) Create Galleries

If you didn't take advantage of the `gazer.sql` you should access the
administration area now, and create some galleries. Without them, users
can't upload any photos.

Enjoy!
