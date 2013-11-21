PHPUnit TeamCity TestListener
========================================

`PHPUnit TeamCity TestListener` is extension for integration PHPUnit and TeamCity. Based on PHPUnit's TestListener feature and TeamCity's Service Messages providing fast and easy test reporting during build process.

Requirements
------------

* PHPUnit 3.7
* TeamCity 7.0+

Installation
------------

Simply add a dependency on `munkie/phpunit-teamcity-testlistener` to your project's `composer.json` file if you use [Composer](http://http://getcomposer.org/) to manage the dependencies of your project.
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

    phpunit --printer PHPUnit\\TeamCity\\TestListener

Press "Run..." button in TeamCity.
Now build will display executed tests in realtime in "Overview" screen