PHPUnit TeamCity TestListener
========================================
[![Latest Stable Version](https://poser.pugx.org/munkie/phpunit-teamcity-testlistener/v/stable)](https://packagist.org/packages/munkie/phpunit-teamcity-testlistener)
[![Total Downloads](https://poser.pugx.org/munkie/phpunit-teamcity-testlistener/downloads)](https://packagist.org/packages/munkie/phpunit-teamcity-testlistener)
[![License](https://poser.pugx.org/munkie/phpunit-teamcity-testlistener/license)](https://packagist.org/packages/munkie/phpunit-teamcity-testlistener)
[![Build Status](https://travis-ci.org/munkie/phpunit-teamcity-testlistener.svg?branch=master)](https://travis-ci.org/munkie/phpunit-teamcity-testlistener)
[![Dependency Status](https://www.versioneye.com/user/projects/5566ee9b6365320015800800/badge.svg?style=flat)](https://www.versioneye.com/user/projects/5566ee9b6365320015800800)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/munkie/phpunit-teamcity-testlistener/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/munkie/phpunit-teamcity-testlistener/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/munkie/phpunit-teamcity-testlistener/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/munkie/phpunit-teamcity-testlistener/?branch=master)

`PHPUnit TeamCity TestListener` is extension for integration [PHPUnit](http://phpunit.de) and [TeamCity](http://www.jetbrains.com/teamcity/) continious integration server. Based on PHPUnit's TestListener feature and [TeamCity's Service Messages](https://confluence.jetbrains.com/display/TCD9/Build+Script+Interaction+with+TeamCity) providing fast and easy test reporting during build process.

Requirements
------------

* PHPUnit 3.7
* TeamCity 7.0+

Installation
------------

Simply add a dependency on `munkie/phpunit-teamcity-testlistener` to your project's `composer.json` file if you use [Composer](http://getcomposer.org/) to manage the dependencies of your project.
Here is a minimal example of a `composer.json` file:

```json
{
    "require-dev": {
        "munkie/phpunit-teamcity-testlistener": "dev-master"
    }
}
```

Documentation
-------------

Add Build Step in TeamCity:

```sh
phpunit --printer PHPUnit\\TeamCity\\TestListener
```

Press "Run..." button in TeamCity.
Now build will display executed tests in realtime in "Overview" screen