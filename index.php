<?php
$config = include('config.php');
include_once('Func.php');
include_once('Route.php');
Route::add('/',function(){
    Redirect('report');
});
Route::add('/report',function(){
    include('static/index.html');
});
// Route::add('/report/logs',function(){
//     include('static/logs.html');
// });
Route::add('/sms/logs',function(){
    echo json_encode(getLogs());
});
Route::add('/sms/report',function(){
    echo json_encode(getReport());
});
Route::add('/sms/get',function($query){ 
    if(isset($query["number"]))
        echo json_encode(findWithPhone($query["number"]));
    else
        echo json_encode(getMessages());
});
Route::add('/sms/send',function($query){ 
    global $config;
    $res = new \stdClass();
    $res->status = "ERROR";
    $res->messages[] = "something is wrong!";
    if(isset($query["number"]) && isset($query["body"])){
        $result = json_decode(CallAPI("GET", $config["apis"][0]."?".http_build_query($query)));
        $sent = $result->sent ? 1 : 0;
        $message_id = saveMessage($query["number"], $query["body"], $sent);
        saveLog($message_id, $sent, 0);
        if($sent){
            $res->status = "SENT";
            $res->messages = ["api0"];
        } else {
            $result = json_decode(CallAPI("GET", $config["apis"][1]."?".http_build_query($query)));
            $sent = $result->sent ? 1 : 0;
            saveLog($message_id, $sent, 1);
            if($sent){
                $res->status = "SENT";
                $res->messages = ["api1"];
                setSentMessage($message_id);
            } else {
                $res->status = "QEUED";
                $res->messages = [];
                CallAPI("GET","http://localhost/dk/sms/resend/");
            }
        }
    }
    echo json_encode($res);
});
Route::add('/sms/resend/',function($query){  
    include("loop.php");
});
Route::run('/dk');
?>