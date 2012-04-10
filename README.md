PHPUnit_Extensions_TeamCity_TestListener
========================================

`PHPUnit_Extensions_TeamCity_TestListener` is extension for integration PHPUnit and TeamCity. Based on PHPUnit's TestListner feature and TeamCity's Service Messages providing fast and easy test reporting during build process.

Requirements
------------

* PHPUnit 3.6
* TeamCity 7.0

Installation
------------

`PHPUnit_Extensions_TeamCity_TestListener` should be installed using the PEAR Installer, the backbone of the [PHP Extension and Application Repository](http://pear.php.net/) that provides a distribution system for PHP packages.

Depending on your OS distribution and/or your PHP environment, you may need to install PEAR or update your existing PEAR installation before you can proceed with the following instructions. `sudo pear upgrade PEAR` usually suffices to upgrade an existing PEAR installation. The [PEAR Manual ](http://pear.php.net/manual/en/installation.getting.php) explains how to perform a fresh installation of PEAR.

The following two commands (which you may have to run as `root`) are all that is required to install PHPUnit using the PEAR Installer:

    pear config-set auto_discover 1
    pear install badoo.github.com/PHPUnit_TestListener_TeamCity


After the installation you can find source files inside your local PEAR directory; the path is usually `/usr/lib/php/PHPUnit`.

Documentation
-------------

Add Build Step in TeamCity:

    phpunit --printer PHPUnit_Extensions_TeamCity_TestListener

Press "Run..." button in TeamCity.
Now build will display executed tests in realtime in "Overview" screen