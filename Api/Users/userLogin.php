<?php
include_once '../headerPage.php'; 

if($method =="POST"){
$data = json_decode(file_get_contents("php://input"));
$user = new Users($db); 
$user_email = isset($data->email)?$data->email:null;
$password   = isset($data->password)?$data->password:null;
$itemRecords= array();
if(!empty($user_email) && !empty($password)){
    $result = $user->selectUser($user_email); 
    if($result->num_rows > 0){  
        $users = $result->fetch_assoc(); 
        if(md5($password) == $users['password'])
        {
            
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
        }else{
            $itemRecords=array('status'=>404,'message'=>'Passsword not match');      
        } 
    }else{
        $itemRecords=array('status'=>404,'message'=>'User not found'); 
    }
}else{
    $itemRecords=array('status'=>401,'message'=>'Username or Password should not be empty'); 
}
http_response_code(200);
echo json_encode($itemRecords);

}else{
$itemRecords=array('status'=>4,'message'=>'Method not allowed'); 
http_response_code(200);
    echo json_encode($itemRecords);
    exit;
}
