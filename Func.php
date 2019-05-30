<?php
$config = include('config.php');
error_reporting(0);
ini_set('display_errors', 0);
set_time_limit(1);
function connectDB()
{
    global $config;
    $servername = $config["database"]["servername"];
    $username = $config["database"]["username"];
    $password = $config["database"]["password"];
    $dbname = $config["database"]["dbname"];

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        return null;
        // die("Connection failed: " . $conn->connect_error);
    } 
    return $conn;
}
function getUnsentMessages(){
    $conn = connectDB();
    if(is_null($conn))
        return false;

    $res = new \stdClass();

    $sql = "
        SELECT 
            id, phone, body, sent
        FROM
            messages
        WHERE
            sent=0";
    $result = $conn->query($sql);
    $res->count = $result->num_rows;

    $res->count = $result->num_rows;
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $res->results[] = $row;
        }
    }

    return $res;
    $conn->close();
}
function getMessages(){
    $conn = connectDB();
    if(is_null($conn))
        return false;

    $res = new \stdClass();

    $sql = "
        SELECT 
            phone, body, sent
        FROM
            messages;";
    $result = $conn->query($sql);
    $res->count = $result->num_rows;

    $res->count = $result->num_rows;
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $res->results[] = $row;
        }
    }

    return $res;
    $conn->close();
}
function findWithPhone($phone){
    $conn = connectDB();
    if(is_null($conn))
        return false;

    $res = new \stdClass();

    $sql = "
        SELECT 
            phone, body, sent
        FROM
            messages
        WHERE
            phone LIKE '%$phone%';";
    $result = $conn->query($sql);

    $res->count = $result->num_rows;
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $res->results[] = $row;
        }
    }

    return $res;
    $conn->close();
}
function getReport(){
    $conn = connectDB();
    if(is_null($conn))
        return false;

    $res = new \stdClass();

    //          messages count          //
    $sql = "SELECT 
        (SELECT COUNT(id) FROM messages WHERE sent = 0) as remaining,
        (SELECT COUNT(id) FROM messages where sent = 1) as sent";
    $result = $conn->query($sql);
    $fetch_res = $result->fetch_assoc();
    $res->sent_count = $fetch_res["sent"];
    $res->remaining_count = $fetch_res["remaining"];

    //          most common          //
    $sql = "SELECT phone, COUNT(*) AS count 
            FROM messages 
            GROUP BY phone 
            ORDER BY count DESC
            LIMIT 10";
    $result = $conn->query($sql);

    $res->most_common = new \stdClass(); 
    $res->most_common->count = $result->num_rows;
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $res->most_common->results[] = $row;
        }
    }

    //          apis          //
    $sql = 'SELECT api, COUNT(*) AS usage_count, (1-AVG(sent)) AS avg_error FROM sent_log GROUP BY api';
    $result = $conn->query($sql);
    
    $res->apis = new \stdClass();   
    $res->apis->count = $result->num_rows;
    $res->logs_count = 0;
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $res->apis->results[] = $row;
            $res->logs_count += $row["usage_count"];
        }
    }

    return $res;
    $conn->close();
}
function getLogs(){
    $conn = connectDB();
    if(is_null($conn))
        return false;

    $res = new \stdClass();
    
    $sql = "SELECT id, phone, body, sent FROM messages";
    $result = $conn->query($sql);
    $res->count = $result->num_rows;
    if ($result->num_rows > 0) {
        // messages
        while($message = $result->fetch_assoc()) {
            $sql = 'SELECT api, sent, date FROM sent_log WHERE message_id='.$message['id'];
            $mes_result = $conn->query($sql);
            $message = (object) $message;
            $message->logs = new \stdClass();
            $message->logs->count = $mes_result->num_rows;
            if ($mes_result->num_rows > 0) {
                // logs
                while($log = $mes_result->fetch_assoc()) {
                    $message->logs->results[] = $log;
                }
            }
            $res->messages[] = $message;
        }
    } else {
        // echo "0 results";
    }
    return $res;
    $conn->close();
}
function saveMessage($phone, $body, $sent)
{
    $conn = connectDB();
    if(is_null($conn))
        return false;

    $sql = "INSERT INTO messages (phone, body, sent)
    VALUES ('$phone', '$body', $sent)";
    if ($conn->query($sql) === TRUE) {
        $last_id = $conn->insert_id;
        // echo "New record created successfully";
        $conn->close();
        return $last_id;
    } else {
        // echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
    return -1;  
}
function setSentMessage($id)
{
    $conn = connectDB();
    if(is_null($conn))
        return false;

    $sql = "UPDATE messages SET sent=1 WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        // echo "Record updated successfully";
    } else {
        // echo "Error updating record: " . $conn->error;
    }
    
    $conn->close();
}
function saveLog($message_id, $sent, $api)
{
    $conn = connectDB();
    if(is_null($conn))
        return false;

    $sql = "INSERT INTO sent_log (message_id, sent, api)
    VALUES ($message_id, $sent, $api)";
    if ($conn->query($sql) === TRUE) {
        $last_id = $conn->insert_id;
        // echo "New record created successfully";
        $conn->close();
        return $last_id;
    } else {
        // echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
    return -1;    
}
function CallAPI($method, $url, $data = false)
{
    $curl = curl_init();

    switch ($method)
    {
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);

            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;
        case "PUT":
            curl_setopt($curl, CURLOPT_PUT, 1);
            break;
        default:
            if ($data)
                $url = sprintf("%s?%s", $url, http_build_query($data));
    }

    // Optional Authentication:
    // curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    // curl_setopt($curl, CURLOPT_USERPWD, "username:password");

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    $result = curl_exec($curl);

    curl_close($curl);

    return $result;
}
function Redirect($url, $permanent = false)
{
    header('Location: ' . $url, true, $permanent ? 301 : 302);

    exit();
}
?>