# DK-SMS
DigiKala Internship's task - You can read it [HERE](https://drive.google.com/file/d/1Su7dvXMlsN6uAevTCeXwBeSP4q8AHf40/view).
## Built With
* Pure PHP
* MySQl
* AngularJS
* BootStrap 4
## Config file
The config file is: config.php!
In this file you set:
* Apis urls
* DataBase connection config
* Sleep time between resending unsent messages (in seconds)
## dk-sms.sql
This file is for initializing your database.
## Root directory
You need to change the root directory of project in:
* .htaccess (change dk with yours)
```
RewriteBase /dk/
```
* index.php (change dk with yours)
```
Route::run('/dk');
```
## Logs
You can get logs in json format with a GET request to dk/sms/logs
