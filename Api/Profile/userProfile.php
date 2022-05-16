<?php
include_once '../headerPage.php'; 

if($method =="POST"){
    $data = json_decode(file_get_contents("php://input"));
    $user = new Users($db); 
    $user_id   = isset($data->user_id)?$data->user_id:null;
    $device_id = isset($data->device_id)?$data->device_id:null;
    $itemRecords= array();
    if(!empty($user_id) || !empty($device_id)){
        $result = $user->checkUser($user_id); 
        if($result->num_rows > 0){  
            $users      = $result->fetch_assoc(); 
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
            
            $itemRecords=array('status'=>200,'message'=>'success','data'=>$user_data); 
        }else{
            $itemRecords=array('status'=>400,'message'=>'User not found'); 
        }
    }else{
        $itemRecords=array('status'=>400,'message'=>'User id and device id are empty.'); 
    }
    http_response_code(200);
    echo json_encode($itemRecords);
}else{
    $itemRecords=array('status'=>4,'message'=>'Method not allowed'); 
    http_response_code(200);
    echo json_encode($itemRecords);
    exit;
}
