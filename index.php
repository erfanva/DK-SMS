<?php
$api1 = "http://localhost/dk/api1.php";
$api2 = "http://localhost/dk/api2.php";
include_once('Func.php');
include_once('Route.php');
Route::add('/',function(){
    echo 'Welcome :-)';
});
Route::add('/sms/send',function(){  
    // $res = CallAPI("GET", $api1);
    // print $res;
});
Route::add('/sms/([0-9]*)/(.*)/send',function($query, $number){  
    print_r($number);
    print json_encode($query);
});
Route::run('/dk');
?>