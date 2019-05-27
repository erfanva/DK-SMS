<?php
$api1 = "http://localhost/dk/api1.php";
$api2 = "http://localhost/dk/api2.php";
include_once('Func.php');
include_once('Route.php');
Route::add('/',function(){
    echo 'Welcome :-)';
});
Route::run('/');
// $res = CallAPI("GET", $api1);
// print $res
?>