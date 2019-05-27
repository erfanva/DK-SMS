<?php
$config = include('config.php');
include_once('Func.php');
include_once('Route.php');
Route::add('/',function(){
    echo 'Welcome :-)';
});
Route::add('/sms/send',function($query){  
    global $config;
    $res = CallAPI("GET", $config["apis"][0]);
    print $res;
});
Route::add('/sms/([0-9]*)/(.*)/send',function($query, $number,$s){  
    print_r($s);
    print json_encode($query);
});
Route::run('/dk');
?>