<?php
$config = include('config.php');
include_once('Func.php');
include_once('Route.php');
Route::add('/',function(){
    include('index.htm');
    // include('static/index.html');
});
Route::add('/sms/logs',function(){
    echo getLogs();
});
Route::add('/sms/report',function(){
    echo getReport();
});
Route::add('/sms/get',function($query){ 
    if(isset($query["number"]))
        echo findWithPhone($query["number"]);
    else
        echo getMessages();
});
Route::add('/sms/send',function($query){  
    global $config;
    if(isset($query["number"]) && isset($query["body"])){
        $res = json_decode(CallAPI("GET", $config["apis"][0]."?".http_build_query($query)));
        $sent = $res->sent ? 1 : 0;
        $message_id = saveMessage($query["number"], $query["body"], $sent);
        saveLog($message_id, $sent, 0);
        if(!$sent){
            $res = json_decode(CallAPI("GET", $config["apis"][1]."?".http_build_query($query)));
            $sent = $res->sent ? 1 : 0;
            saveLog($message_id, $sent, 1);
            if(!$sent){

            } else {
                setSentMessage($message_id);
            }
        }
    }
});
Route::run('/dk');
?>