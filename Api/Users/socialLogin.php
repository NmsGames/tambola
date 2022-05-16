<?php
include_once '../headerPage.php'; 

if ($method == "POST") {
    $data = json_decode(file_get_contents("php://input"));
    $user = new Users($db);

    $google_id = isset($data->google_id) ? $data->google_id : null;
    $fb_id     = isset($data->fb_id) ? $data->fb_id : null;
    $username  = isset($data->username) ? $data->username : null;
    $device_id = isset($data->device_id) ? $data->device_id : null;
    $email     = isset($data->email) ? $data->email : null;

    $itemRecords = array();

    if (!empty($google_id) || !empty($fb_id) ) {
        
        if (!empty($email)) {

            $users = null;

            $checkResult = $user->getSocialUser($data);

            if(!empty($checkResult) && $checkResult->num_rows > 0)
            {
                $users = $checkResult->fetch_assoc(); // find user detail
            }
            else
            {
                $checkEmail = $user->selectUser($email);
                if ($checkEmail->num_rows > 0) { 
                    $itemRecords = array('status' => 400, 'message' => 'Oauth id already register with other account.');
                } else {
                    $user->createSocialUser($data);   //Create New User
                    $userResult = $user->getSocialUser($data);
                    if(!empty($userResult) && $userResult->num_rows > 0)
                    {
                        $users = $userResult->fetch_assoc(); // find user detail
                    }
                }
            }

            if(!empty($users) && count($users) > 0){

                $rankResult = $user->getUserRank($users['user_id']);
                $player     = ($rankResult->num_rows > 0) ? $rankResult->fetch_assoc(): null;

                $user_data=array(
                    "user_id"    => $users['user_id'],
                    "profile_id" => $users['profile_id'],
                    "device_id" => $users['device_id'],
                    "email"      => $users['email'],
                    "total_coins"=> $users['coins'],
                    "username"   => isset($users['username'])?$users['username']:'Guest'.$users['user_id'],
                    "avatar_link"=> isset($users['avatar'])?$siteUrl.'uploads/avatar/'.$users['avatar']:$siteUrl.'uploads/404.jpg',
                    "player_rank"=> !empty($player['rank'])?$player['rank']: 0
                );  

                $itemRecords=array('status'=>200,'message'=>'login success','data'=>$user_data); 
            } else{
                $itemRecords =  $itemRecords ? $itemRecords : array('status' => 404, 'message' => 'Some error occur.');
            }

        }else{
            $itemRecords = array('status' => 401, 'message' => 'Email is empty.');
        }    
        
    } else {
        $itemRecords = array('status' => 404, 'message' => 'User oauth id is required');
    }
    http_response_code(200);
    echo json_encode($itemRecords);
} else {
    http_response_code(404);
    echo json_encode(array('status' => 404, 'message' => 'Method not allowed'));
}
 
