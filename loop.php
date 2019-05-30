<?php
include_once("Func.php");
$config = include('config.php');
ignore_user_abort(true);
set_time_limit(0);
$resending_checker_file = 'resending';

$data = file_get_contents($resending_checker_file);
if($data == "0"){
    file_put_contents($resending_checker_file, time());
    $unsent_messages = getUnsentMessages();
    while ($unsent_messages->count > 0){
        // Wait 60 seconds
        sleep(60);
        foreach ($unsent_messages->results as $message) {
            print_r($message);
            $message_id = $message["id"];
            $query["number"] = $message["phone"];
            $query["body"] = $message["body"];

            $result = json_decode(CallAPI("GET", $config["apis"][0]."?".http_build_query($query)));
            $sent = $result->sent ? 1 : 0;
            saveLog($message_id, $sent, 0);
            if($sent){
                setSentMessage($message_id);
            } else {
                $result = json_decode(CallAPI("GET", $config["apis"][1]."?".http_build_query($query)));
                $sent = $result->sent ? 1 : 0;
                saveLog($message_id, $sent, 1);
                if($sent){
                    setSentMessage($message_id);
                }
            }
        }
        $unsent_messages = getUnsentMessages();
    }
    file_put_contents($resending_checker_file, "0");
} else {
    echo "We have a running server ".(time() - $data)." seconds ago!";
}
?>