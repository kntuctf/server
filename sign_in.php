<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Origin,Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once 'config/config.php';
global $mysqli;

$ip = (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) ? $_SERVER["HTTP_CF_CONNECTING_IP"] : $_SERVER["REMOTE_ADDR"];

$rawBody = file_get_contents("php://input");
$data = array(); // Initialize default data array
$data = json_decode($rawBody,true); // Then decode itv
$username = $mysqli->escape_string($data['username']);
$password = $mysqli->escape_string($data['password']);

if ($mysqli->query("SELECT GET_LOCK('$username',1) ")) {

    $sign_in_result = $mysqli->query("SELECT token FROM users WHERE username='$username' AND password='$password' LIMIT 1");
    if ($sign_in_result->num_rows == 1) {

        $user_row = $sign_in_result->fetch_assoc();
        $token = $user_row['token'];
        http_response_code(200);
        echo json_encode(
            array(
                "token" => $token,
                "username"=> $username
            ));

    } else {

        http_response_code(400);
        echo json_encode(array("message" => "user doesnt exist!"));
            
    }
}else {
    $mysqli->query("INSERT INTO warnings VALUES ('$username')");
    http_response_code(406);
    echo json_encode(array("message" => "لطفا کمی آهسته تر"));

}
