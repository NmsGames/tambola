<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
include_once '../../config.php';
include_once '../../Class/Users.php';
$method = $_SERVER['REQUEST_METHOD'];
$database = new Database();
$db = $database->getConnection();
if ($method == "POST") {
    $data = json_decode(file_get_contents("php://input"));
    $user = new Users($db);
    $user_email = isset($data->email) ? $data->email : null;
    $password  = isset($data->password) ? $data->password : null;
    $username  = isset($data->username) ? $data->username : null;
    $username  = isset($data->username) ? $data->username : null;
    $itemRecords = array();
    if (!empty($user_email)) {
        if (!empty($username)) {
            if (!empty($password)) {
                $result = $user->selectUser($user_email);
                if ($result->num_rows > 0) { 
                    $itemRecords = array('status' => 400, 'message' => 'Sorry! Email already exist');
                } else {
                    $user = new Users($db);
                    $result1 = $user->createUser($data); 
                    $itemRecords = array('status' => 200, 'message' => 'User created');
                    
                }
            } else {
                $itemRecords = array('status' => 400, 'message' => 'Password is required');
            }
        } else {
            $itemRecords = array('status' => 400, 'message' => 'Username is required');
        }
    } else {
        $itemRecords = array('status' => 400, 'message' => 'Email is required');
    }
    http_response_code(200);
    echo json_encode($itemRecords);
} else {
    http_response_code(404);
    echo json_encode(array('status' => 400, 'message' => 'Method not allowed'));
}
 
