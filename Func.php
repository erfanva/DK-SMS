<?php
$config = include('config.php');
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
// function createLogsTable()
// {
//     $conn = connectDB();
//     if(is_null($conn))
//         return false;

//     // sql to create table
//     $sql = "CREATE TABLE Logs (
//             id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
//             phone varchar(12) NOT NULL, 
//             body TEXT,
//             sent BOOLEAN,
//             date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
//         )";

//     if ($conn->query($sql) === TRUE) {
//         echo "Table Logs created successfully";
//     } else {
//         echo "Error creating table: " . $conn->error;
//         $conn->close();
//         return false;
//     }

//     $conn->close();
//     return true;
// }
function getMessages(){
    $conn = connectDB();
    if(is_null($conn))
        return false;

    $res = new \stdClass();

    $sql = "
        SELECT 
            phone, 
            body, 
            sent
        FROM
            messages;";
    $result = $conn->query($sql);
    $res->count = $result->num_rows;

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $res->messages[] = $row;
        }
    }

    return json_encode($res);
    $conn->close();
}
function findWithPhone($phone){
    $conn = connectDB();
    if(is_null($conn))
        return false;

    $res = new \stdClass();

    $sql = "
        SELECT 
            phone, 
            body, 
            sent
        FROM
            messages
        WHERE
            phone LIKE '%$phone%';";
    $result = $conn->query($sql);
    $res->count = $result->num_rows;

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $res->messages[] = $row;
        }
    }

    return json_encode($res);
    $conn->close();
}
function getReport(){
    $conn = connectDB();
    if(is_null($conn))
        return false;

    $res = new \stdClass();

    $sql = "SELECT phone FROM messages where sent=1";
    $result = $conn->query($sql);
    $res->messages_count = $result->num_rows;
    // echo "messages count = ".$result->num_rows."<br>";

    // echo "most common numbers: <br>";
    $sql = "SELECT phone, COUNT(*) AS count 
            FROM messages 
            GROUP BY phone 
            ORDER BY count DESC
            LIMIT 10";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $res->most_common[] = $row;
            // echo "> phone: ".$row["phone"]." - count: ".$row["count"]."<br>";
        }
    }

    $sql = 'SELECT api, COUNT(*) AS usage_count, (1-AVG(sent)) AS avg_error FROM sent_log GROUP BY api';
    $result = $conn->query($sql);
    $res->apis_count = $result->num_rows;
    $res->logs_count = 0;
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $res->apis[] = $row;
            $res->logs_count += $row["usage_count"];
            // echo "api: ".$row["api"]." - usage: ".$row["count"]." - error: ".(1 - $row["avg_success"])."<br>";
        }
    }
    // for ($i=0; $i < $res->apis_count; $i++) { 
    //     $res->apis[$i]["usage"] = $res->apis[$i]["count"] / $res->logs_count;
    // }
    // for ($res->apis as $api) {
    //     $api["usage"] = $api["count"] / $res->logs_count;
    // }

    return json_encode($res);
    $conn->close();
}
function getLogs(){
    $conn = connectDB();
    if(is_null($conn))
        return false;


    $res = new \stdClass();
    
    $sql = "SELECT id, phone, body, sent FROM messages";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // output data of each row
        while($mes = $result->fetch_assoc()) {
            // echo "> id: ".$mes["id"]." - phone: ".$mes["phone"]." - body: ".$mes["body"]." - sent: ".$mes["sent"]."<br>";
            $sql = 'SELECT api, sent, date FROM sent_log WHERE message_id='.$mes['id'];
            $mes_result = $conn->query($sql);
            $message = (object) $mes;
            if ($mes_result->num_rows > 0) {
                // output data of each row
                while($log = $mes_result->fetch_assoc()) {
                    $message->logs[] = $log;
                    // echo "api: ".$log["api"]." - sent: ".$log["sent"]." - sent: ".$log["date"]."<br>";
                }
            }
            $res->messages[] = $message;
        }
    } else {
        echo "0 results";
    }
    return json_encode($res);
    $conn->close();
}
function saveMessage($phone, $body, $sent)
{
    $conn = connectDB();
    if(is_null($conn))
        return false;

    $sql = "INSERT INTO messages (phone, body, sent)
    VALUES ('$phone', '$body', $sent)";
    print $sql;
    if ($conn->query($sql) === TRUE) {
        $last_id = $conn->insert_id;
        echo "New record created successfully";
        $conn->close();
        return $last_id;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
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
        echo "Record updated successfully";
    } else {
        echo "Error updating record: " . $conn->error;
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
    print $sql;
    if ($conn->query($sql) === TRUE) {
        $last_id = $conn->insert_id;
        echo "New record created successfully";
        $conn->close();
        return $last_id;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
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
?>