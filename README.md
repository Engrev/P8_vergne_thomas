# P8_vergne_thomas [![Codacy Badge](https://app.codacy.com/project/badge/Grade/fe06eb2798b348acb06cc4e28ddc9a09)](https://www.codacy.com/gh/Engrev/P8_vergne_thomas/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=Engrev/P8_vergne_thomas&amp;utm_campaign=Badge_Grade)

Project 8 of the course Application developer - PHP / Symfony of OpenClassrooms : improve an existing ToDo & Co application in symfony.

This [commit](https://github.com/Engrev/P8_vergne_thomas/commit/bf7addc7ac92e36212c33974aa4456aa53dcbffb) is an update of the [basic project](https://github.com/saro0h/projet8-TodoList) to symfony version 5.1.

Require :
* Development environment
* Apache server 2.2.31
* PHP >= 7.2
* MySQL 5.7.24

Get started :
* Clone the repository with : `git clone https://github.com/Engrev/P8_vergne_thomas.git`.
* Make a `composer install` in the project folder.
* Modify `DATABASE_URL` in `.env.local` file.
* Make a `php bin/console doctrine:database:create` in the console in the projet folder to create database.
* Make a `php bin/console doctrine:migrations:migrate` in the console in the projet folder to create tables.
* Make a `php bin/console doctrine:fixtures:load` in the console in the project folder to create the first entities.
* Go to the root of the project on your browser (the root have to redirect in the public folder of the project folder).