<?php
// header("Access-Control-Allow-Origin: *");
// header("Content-Type: application/json; charset=UTF-8"); 
// include_once '../../config.php';
// include_once '../../Class/Events.php'; 
// include_once '../../Class/Users.php';
// include_once '../../Class/Tickets.php';
// $database = new Database();
// $db = $database->getConnection();
include_once '../headerPage.php'; 

$status = 404;
$message= null;

 
if(isset($_GET['userId'])  && !empty($_GET['userId']))
{
    $eventRecords= array();
    $user = new Users($db);
    $user_result = $user->checkUser($_GET['userId']);
    $users = $user_result->fetch_assoc();
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
    }else{
        $message = "Events not founds!";
    } 
    http_response_code(200);     
    echo json_encode(['status'=>$status,"message"=>$message,'events'=>$eventRecords]);
    exit;
    
}else{
    $message = "Invalid Details";
}
http_response_code(401);     
echo json_encode(['status'=>$status,"message"=>$message]);
exit;