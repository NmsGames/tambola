<?php
include_once '../headerPage.php'; 

if($method =="POST"){
$data = json_decode(file_get_contents("php://input"));
$user = new Users($db); 
$user_email = isset($data->email)?$data->email:null;
$password   = isset($data->password)?$data->password:null;
$itemRecords= array();
$ticketRecords= array();
$eventRecords= array();
if(!empty($user_email) || !empty($password)){
    $result = $user->selectUser($user_email); 
    if($result->num_rows > 0){  
        $users = $result->fetch_assoc(); 
        if(md5($password) == $users['password'])
        {
            $items = new Events($db); 
            //Check Event
            $items->checkEvent();
            //GET Events LIst
            $result1 = $items->getCreateEvents($users['user_id']);  
            if($result1->num_rows > 0)
            {     
                while ($item1 = $result1->fetch_assoc()) 
                { 	 
                    extract($item1); 
                    // print_r($item1); 
                    $ticket_count = new Tickets($db);
                    $ticket_result = $ticket_count->checkPurchaseTickets($event_id);
                    $ticketItems = array();
                    if($ticket_result->num_rows > 0)
                    {     
                        while ($ticketRows = $ticket_result->fetch_assoc())
                        { 	
                            extract($ticketRows);  
                            
                            $ticketDetails=array(
                                "ticket_id"        => $ticket_id,
                                "is_ticket_status" => $is_status==0?'Active':'Expired',
                                "ticket_name"       => $ticket_name,
                                "ticket_image"      => $siteUrl.$ticket_file_url		
                            ); 
                            array_push($ticketItems, $ticketDetails);
                        }  
                    }
                    $event_data=array(
                        "event_status"  => $is_expired == 1?"Expired":"Active",
                        "event_id"      => $event_id,
                        "event_name"    => $sub_category_name.''.$event_id,
                        "event_date"    => $event_date, 
                        "event_time"    => $event_time,
                        "number_of_ticket"=>$tickets,
                        "tickets"=>$ticketItems 
                    ); 
                    array_push($eventRecords, $event_data);
                }  
            } 
            $user_data=array(
                "user_id"  => $users['user_id'],
                "email"    => $users['email'],
                "total_coins"=> $users['coins'],
                "username"   => isset($users['username'])?$users['username']:'Guest'.$users['user_id'],
                "avatar_link"=> isset($users['avatar'])?$siteUrl.$users['avatar']:null
            ); 
            
            $itemRecords=array('status'=>200,'message'=>'login success','data'=>$user_data,'events'=>$eventRecords);      
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
