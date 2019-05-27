<?php
$config = include('config.php');
include_once('Func.php');
include_once('Route.php');
Route::add('/',function(){
    // createLogsTable();
    getLogs();
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
Route::add('/sms/([0-9]*)/(.*)/send',function($query, $number,$s){  
    print_r($s);
    print json_encode($query);
});
Route::run('/dk');
?>