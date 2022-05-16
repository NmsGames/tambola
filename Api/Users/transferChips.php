<?php
include_once '../headerPage.php'; 

if($method =="POST"){
    $data = json_decode(file_get_contents("php://input"));
    $user = new Users($db); 
    $sender_id   = isset($data->sender_id)?$data->sender_id:null;
    $receiver_id = isset($data->receiver_id)?$data->receiver_id:null;
    $amount      = isset($data->amount)?$data->amount :null;
    $itemRecords= array();
    if(!empty($sender_id) && !empty($receiver_id) && !empty($amount) && ($sender_id !=$receiver_id )){

        $sender_data = $user->getProfile($sender_id);
        $receiver_data = $user->getProfile($receiver_id);

        if($sender_data->num_rows > 0){
            $sender = $sender_data->fetch_assoc();
            if($receiver_data->num_rows > 0){
                $receiver = $receiver_data->fetch_assoc();
                $sender_coins = $sender['coins'];
                $receiver_coins = $receiver['coins'];

                if($sender_coins > $amount){
                    $senderArr = array('user_id' => $sender['user_id'], 'amount'=>$amount, 'utype'=>'debit' );
                    $receiverArr  = array('user_id' => $receiver['user_id'], 'amount'=>$amount, 'utype'=>'credit' );

                    if( $user->updateCoin($senderArr) && $user->updateCoin($receiverArr) ){

                        $itemRecords=array('status'=>200,'message'=>'Chips Transfer Successfully.'); 

                    } else{
                        $itemRecords=array('status'=>400,'message'=>'something went wrong. ');  
                    }
                } else{
                    $itemRecords=array('status'=>400,'message'=>'Insufficient Chips. ');  
                }
            } else{
                $itemRecords=array('status'=>400,'message'=>'Receiver details not found. ');  
            }
        } else{
            $itemRecords=array('status'=>400,'message'=>'Sender details not found. ');  
        }
    }else{
        $itemRecords=array('status'=>400,'message'=>'Invalid details.'); 
    }
    http_response_code(200);
    echo json_encode($itemRecords);
}else{
    $itemRecords=array('status'=>4,'message'=>'Method not allowed'); 
    http_response_code(200);
    echo json_encode($itemRecords);
    exit;
}
