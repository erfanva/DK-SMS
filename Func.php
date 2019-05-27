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
// function getLogs(){
//     $conn = connectDB();
//     if(is_null($conn))
//         return false;

//     $sql = "SELECT id, phone, sent FROM Logs";
//     $result = $conn->query($sql);

//     if ($result->num_rows > 0) {
//         // output data of each row
//         while($row = $result->fetch_assoc()) {
//             echo "id: " . $row["id"]. " - phone: " . $row["phone"]. " - sent: " . $row["sent"]. "<br>";
//         }
//     } else {
//         echo "0 results";
//     }
//     $conn->close();
// }
function saveMessage($phone, $body, $sent)
{
    $conn = connectDB();
    if(is_null($conn))
        return false;

    $sql = "INSERT INTO messages (phone, body, sent)
    VALUES ('$phone', '$body', $sent)";
    print $sql;
    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";
        $conn->close();
        return $conn->insert_id;
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
        echo "New record created successfully";
        $conn->close();
        return $conn->insert_id;
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