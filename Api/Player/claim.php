<?php
include_once '../headerPage.php'; 

if($method =="POST"){
    $data = json_decode(file_get_contents("php://input"));
    $userClaim = new UserClaim($db);
    $user    = new Users($db); 
    $game_id = isset($data->game_id) ? $data->game_id : null;
    $user_id = isset($data->user_id) ? $data->user_id : null;
    $claim_name = isset($data->claim_name) ? $data->claim_name : null;
    $itemRecords= array();
    
    if(!empty($game_id) && !empty($user_id) && !empty($claim_name)){
        $result = $user->checkUser($user_id); 
        if($result->num_rows > 0){ 
            if($userClaim->create($data)){
                $itemRecords=array('status'=>200,'message'=>'success');
            }else{
                 $itemRecords=array('status'=>400,'message'=>'Some error occur.'); 
            }
        }else{
            $itemRecords=array('status'=>400,'message'=>'User not found'); 
        }
    }else{
        $itemRecords=array('status'=>400,'message'=>'Userid, gameid and claim are empty.'); 
    }
    http_response_code(200);
    echo json_encode($itemRecords);
}else{
    $itemRecords=array('status'=>4,'message'=>'Method not allowed'); 
    http_response_code(200);
    echo json_encode($itemRecords);
    exit;
}
