<?php

use RecastAI\Client;

require_once 'config.php';

/*
* message.js
* This file contains your bot code
*/
function replyMessage ($message) {
  /*
  * Instantiate Recast.AI SDK, just for connect service
  */
  $request = Client::Request($_ENV["REQUEST_TOKEN"]);

  /*
  * Get text from message received
  */
  $text = $message->content;

  /*
  * Get senderId to catch unique conversation_token
  */
  $senderId = $message->senderId;

  /*
  * Call Recast.AI SDK, through /converse route
  */
  $response = $request->converseText($text, [ 'conversation_token' => $senderId ]);

  /*
  * Here, you can add your own process.
  * Ex: You can call any external API
  * Or: Update your DB
  * etc...
  */
  $server = "localhost";
$dbusername = "root";
$dbpassword = "root";
$dbname = "phplms";
if ($response->action->slug == 'greetings') {
  // Do your code
echo "Greetings User!";


$usernametext = "sid";
$passwordtext = "sid";
//array_push($response->replies, "Welcome to LMS. You can enquire about your marks as of now.");

$conn = new mysqli($server, $dbusername, $dbpassword, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
else{
$sql = "SELECT * from register where username = 'sid' and password = 'sid';";
$result = $conn->query($sql);

//if ($result->num_rows > 0) {
if($result!=null){
  //array_push($response->replies, "Inside rows > 0");
    // output data of each row
    while($row = $result->fetch_assoc()) {
      //array_push($response->replies, "Inside While");
        //echo "id: " . $row["id"]. " - Name: " . $row["firstname"]. " " . $row["lastname"]. "<br>";
        if($usernametext==$row["username"]){
            if($passwordtext==$row["password"]){
                //session_start();
                echo "User found! Username = ".$row['username']."\n";
    echo "Password = ".$row['password']."\n";
    //array_push($response->replies, "Welcome to LMS. You can enquire about your marks as of now.");
                //$_SESSION['username']=$usernametext;
                //$_SESSION['name']=$row["name"];
                //header('Location: Auth.php');
                $conn->close();
                //exit;
            }
        }
    }
}
else{
    echo "0 results";
    $conn->close();
}
}
}

if ($response->action->slug == 'online-test-marks') {
  $sql = "";
  $test_number = $response->memory->number->scalar;
  $username = $response->memory->username->value;
  if($username==null){
    array_push($response->replies, "Code : Username is missing");
  }
  //array_push($response->replies, "Test number = ".$test_number);
  else if($test_number==null){
    //array_push($response->replies, "Test number null");

    
  }else{
    $sql = "select * from onlinetest".$test_number." where username = '".$username."';";
    $conn = new mysqli($server, $dbusername, $dbpassword, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
else{
$result = $conn->query($sql);

//if ($result->num_rows > 0) {
if($result!=null){
  array_push($response->replies, "Your onlinetest".$test_number." marks :");
    // output data of each row
    while($row = $result->fetch_assoc()) {
        //echo "id: " . $row["id"]. " - Name: " . $row["firstname"]. " " . $row["lastname"]. "<br>";
      array_push($response->replies, $row["cname"]." - ".$row["marks"]);
  }
  }
}
}
}

if ($response->action->slug == 'login-1') {


$usernametext = $response->memory->username->value;
$passwordtext = $response->memory->password->value;
array_push($response->replies, "Username :".$usernametext."END Password :".$passwordtext."END");
//array_push($response->replies, "Welcome to LMS. You can enquire about your marks as of now.");
//$result = null;
$conn = new mysqli($server, $dbusername, $dbpassword, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
else{
$sql = "SELECT * from register where username = '.$usernametext.' and password = '.$passwordtext.';";
$result = $conn->query($sql);
array_push($response->replies, "No. of rows : ".mysqli_num_rows($result));
//if ($result->num_rows > 0) {
if($result!=null){
//if(mysqli_num_rows($result)>0){
      array_push($response->replies, "Code : Successfully logged in as ".$usernametext);
      array_push($response->replies, "Welcome to LMS. You can enquire about your marks as of now.");
      $conn->close();
}
else{
    array_push($response->replies, "Code : Username or password is wrong! Please log in again.");
    $conn->close();
}
}
}

if ($response->action->slug == 'my-details') {
  $usernametext = $response->memory->username->value;
  $passwordtext = $response->memory->password->value;
  array_push($response->replies, "Username : ".$usernametext." Password : ".$passwordtext);
  }
  /*
  * Add each replies received from API to replies stack
  */
  foreach ($response->replies as $reply) {
    $message->addReply([(object)['type' => 'text', 'content' => $reply]]);
  }

  $message->reply();
}
