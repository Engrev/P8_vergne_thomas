# Contributing

## Project

To contribute to this project, you must :
1. clone the project ;
2. create your branch ;
3. send your branch online ;
4. create an issue for the feature you want to develop / modify ;
5. make a pull-request.

You have to use best practices related to symfony 5.1 (autowiring, voters, etc). Rely on the files already present.

## Testing

### Files

For unit and functional tests, you must reproduce the path of the method you want to test in the tests folder at the root of the project.
A test class must have the same name as the class to test prefixed by `Test`.

> Example : You want to test the `DefaultController.php` class.

> You must to create the `DefaultControlerTest.php` class in `tests/Controller` folder.

Then, for all the methods you want to test, you must rewrite them suffixed this time by `test`.

> Example : You want to test the `index` method in `DefaultController.php` class.

> You must to create the `testIndex` method  in `DefaultControllerTest.php` class.

You can read the [doc](https://symfony.com/doc/current/testing.html) for more information and to complete your test methods.

### Run

There are several commands to run tests :
* `php bin/phpunit` : run all tests of the application.
* `php bin/phpunit tests/Controller` : run all tests in the Controller/ folder.
* `php bin/phpunit tests/Controller/DefaultControllerTest.php` : run tests for the DefaultControllerTest class.