<?php
include_once '../headerPage.php'; 

if($method =="POST"){
    $data = json_decode(file_get_contents("php://input"));
    $user = new Users($db); 
    $user_id   = isset($data->user_id)?$data->user_id:null;
    $amount    = isset($data->chip_amount)?$data->chip_amount:null;
    $device_id      = isset($data->device_id)?$data->device_id :null;
    $itemRecords= array();
    if(!empty($user_id) && $amount > 0 && !empty($device_id) ){

        $result = $user->checkUser($user_id); 
        if($result->num_rows > 0){  
            $users      = $result->fetch_assoc(); 
            $userArr  = array('user_id' => $users['user_id'], 'amount'=>$amount, 'utype'=>'credit' );
            
            if($user->updateCoin($userArr)){
               $itemRecords=array('status'=>200,'message'=>'success');
            } else{
                $itemRecords=array('status'=>400,'message'=>'something went wrong. ');  
            }

        }else{
            $itemRecords=array('status'=>400,'message'=>'User not found'); 
        }
    }else{
        $itemRecords=array('status'=>400,'message'=>'User id, amount , device are Empty.'); 
    }
    http_response_code(200);
    echo json_encode($itemRecords);
}else{
    $itemRecords=array('status'=>4,'message'=>'Method not allowed'); 
    http_response_code(200);
    echo json_encode($itemRecords);
    exit;
}
