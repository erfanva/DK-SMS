<?php
$res = new \stdClass();
$res->sent = rand(0,1) < 0.5;
print(json_encode($res));
?>