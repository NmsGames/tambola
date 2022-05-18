<?php
include_once '../headerPage.php'; 

if($method =="POST"){
    $data = json_decode(file_get_contents("php://input"));
    $userClaim = new UserClaim($db);
    $user    = new Users($db); 
    $userClaim->game_id = isset($data->game_id) ? $data->game_id : null;
    $itemRecords= array();
    
    if(!empty($userClaim->game_id)){
        $result = $userClaim->read(); 
        if($result->num_rows > 0){ 
            $itemDetails= array();
            while ($item = $result->fetch_assoc()) {    
                extract($item); 
                $records=array(
                    "game_id" => $game_id,
                    "user_id" => $user_id,
                    "claim_name" => $claim_name,           
                ); 
               array_push($itemDetails, $records);
            }    

            $itemRecords=array('status'=>200,'message'=>'success','items'=>$itemDetails);
        
        }else{
            $itemRecords=array('status'=>200,'message'=>'Records not found'); 
        }
    }else{
        $itemRecords=array('status'=>400,'message'=>' Game id is empty.'); 
    }
    http_response_code(200);
    echo json_encode($itemRecords);
}else{
    $itemRecords=array('status'=>4,'message'=>'Method not allowed'); 
    http_response_code(200);
    echo json_encode($itemRecords);
    exit;
}
