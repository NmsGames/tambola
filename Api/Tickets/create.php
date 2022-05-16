<?php
include_once '../headerPage.php'; 
$status = 404;
$message= null;
$items  = new Events($db);
$data   = json_decode(file_get_contents("php://input")); 
$method = $_SERVER['REQUEST_METHOD']; 
$itemRecords = array();   
$event_data  = array();
if($method =="POST"){
if(!empty($data->user_id)){
    $user = new Users($db);
    //Check User
    $user_result = $user->checkUser($data->user_id);
    $user_row = $user_result->fetch_assoc();
   
    if($user_result->num_rows>0){  
        if(!empty($data->number_of_ticket))
        {   
            if(!empty($data->category_id))
            { 
                if(!empty($data->sub_category_id))
                {  
                    if(!empty($data->event_date))
                    { 
                        if(!empty($data->event_time))
                        { 
                            $cat = new Categories($db);  
                            $cat_result = $cat->readById($data->category_id);
                            if($cat_result->num_rows>0){  
                                //ccheck valid date
                                date_default_timezone_set('Asia/Kolkata');   
                                $event_date = strtotime(date('Y-m-d',strtotime($data->event_date)));
                                $today_date = strtotime(date('Y-m-d',strtotime(date('Y-m-d'))));
                                    
                                $event_time = strtotime(date('H:i:s',strtotime($data->event_time)));
                                if($event_date>=$today_date)
                                {   
                                    // CHECK Ticket amounts 
                                    $ticket = new SubCategories($db);
                                    $ticket_result = $ticket->checkTicketAmount($data->category_id,$data->sub_category_id);
                                    if($ticket_result->num_rows>0)
                                    { 
                                        $ticket_row = $ticket_result->fetch_assoc(); 
                                        $data->cost_of_ticket = $ticket_row['ticket_amount'];
                                        $total_cost = $data->number_of_ticket*$ticket_row['ticket_amount']; 
                                        //Check coins available or not for Purchase tickets
                                        if($user_row['coins']>=$total_cost)
                                        { 
                                            //Add ticket cost 
                                            $data->cost = $total_cost; 
                                            $ticket = new Tickets($db);
                                            //Check ticket LImits
                                            $tickets_limit_result = $ticket->checkTicketLimit($data->category_id,$data->sub_category_id);
                                            if($tickets_limit_result->num_rows>=$data->number_of_ticket)
                                            {
                                                
                                                $current_event_time = getCurrentTime();
                                                $start = strtotime('+15 minutes',strtotime($current_event_time)); 
                                                $next_event_time =  strtotime(date('H:i:s',$start));
                                                $events = new Events($db);
                                                $events->checkEvent();  
                                                $total_tickets_availale = $tickets_limit_result->num_rows;
                                                if($today_date==$event_date)
                                                {  
                                                    
                                                    if($event_time>=$next_event_time)
                                                    {   
                                                        $event_result = $events->readCheckEvent($data); 
                                                        if($event_result->num_rows>0)
                                                        { 
                                                            $event_row = $event_result->fetch_assoc(); 
                                                            $rest_ticket =  $total_tickets_availale-$event_row['tickets'];
                                                            if($rest_ticket>=$data->number_of_ticket)
                                                            {
                                                                $event_id = $event_row['event_id'];
                                                                $pt_result = $ticket->checkPurchaseTickets($event_id);
                                                                $ticketId = array();
                                                                if($pt_result->num_rows>0)
                                                                { 
                                                                    //GET purchased ticket ID in array
                                                                    while ($ticket_items = $pt_result->fetch_assoc())
                                                                    { 	
                                                                        extract($ticket_items);  
                                                                        array_push($ticketId, $ticket_id);
                                                                    }  
                                                                    //CREATE EVEnt
                                                                    $purchased_result = $ticket->purchaseTicketAgain($data->category_id,$data->sub_category_id,$data->number_of_ticket,
                                                                    $ticketId
                                                                    );
                                                                    $eventID = $events->createEvent($data);
                                                                    while ($purchase_item = $purchased_result->fetch_assoc())
                                                                        {  
                                                                        extract($purchase_item);  
                                                                        // $event = new Events($db);
                                                                        $events->createEventHistory(array(
                                                                            'ticket_id' => $ticket_id,
                                                                            'user_id'   => $data->user_id,
                                                                            'event_id'  => $eventID,
                                                                            'category_id'=>  $data->category_id,
                                                                            'sub_category_id'=>$data->sub_category_id,
                                                                            'per_ticket_cost'=>$data->cost_of_ticket,
                                                                        ));
                                                                        $itemDetails=array(
                                                                            "ticket_id" => $ticket_id,
                                                                            "ticket_name" => $ticket_name,
                                                                            "ticket_image" => $siteUrl.$ticket_file_url		
                                                                        ); 
                                                                        array_push($itemRecords, $itemDetails);
                                                                    }
                                                                    $status = 200;
                                                                    $message= "Purchased success";
                                                                    $event_data=array(
                                                                        "event_status"=>"Active",
                                                                        "event_id" => $eventID,
                                                                        "event_name" => $ticket_row['sub_category_name'].''.$eventID,
                                                                        "event_date" => $data->event_date,	
                                                                        "event_time"=>$data->event_time,
                                                                        "number_of_ticket"=>$data->number_of_ticket	,
                                                                        "tickets"=>$itemRecords 
                                                                    ); 
                                                                }else{
                                                                    $message= "Something went wrong";
                                                                }
                                                            }else{
                                                                $status = 208;
                                                                $message= "Tickets already purchased";
                                                            }
                                                        }else{
                                                            /**
                                                             * When Event is not exist same date time
                                                             */
                                                            $new_ticket_result = $ticket->readTicketsByCategoryId($data->category_id,$data->sub_category_id,$data->number_of_ticket);
                                                            if($new_ticket_result->num_rows>0){
                                                                $eventID = $events->createEvent($data);
                                                                    while ($purchase_item = $new_ticket_result->fetch_assoc())
                                                                        {  
                                                                        extract($purchase_item);  
                                                                        // $event = new Events($db);
                                                                        $events->createEventHistory(array(
                                                                            'ticket_id' => $ticket_id,
                                                                            'user_id'   => $data->user_id,
                                                                            'event_id'  => $eventID,
                                                                            'category_id'=>  $data->category_id,
                                                                            'sub_category_id'=>$data->sub_category_id,
                                                                            'per_ticket_cost'=>$data->cost_of_ticket,
                                                                        ));
                                                                        $itemDetails=array(
                                                                            "ticket_id" => $ticket_id,
                                                                            "ticket_name" => $ticket_name,
                                                                            "ticket_image" => $siteUrl.$ticket_file_url		
                                                                        ); 
                                                                        array_push($itemRecords, $itemDetails);
                                                                    }
                                                                    $status = 200;
                                                                    $message= "Purchased success";
                                                                    $event_data=array(
                                                                        "event_status"=>"Active",
                                                                        "event_id" => $eventID,
                                                                        "event_name" => $ticket_row['sub_category_name'].''.$eventID,
                                                                        "event_date" => $data->event_date, 
                                                                        "event_time"=>$data->event_time,
                                                                        "number_of_ticket"=>$data->number_of_ticket	,
                                                                        "tickets"=>$itemRecords 
                                                                    ); 
                                                            }else{ 
                                                                $message = "Something went wrong!";
                                                            }
                                                            
                                                        }  
                                                    }
                                                    else{
                                                        $message = "Sorry! Invalid time..";
                                                    }
                                                }else{
                                                    /**
                                                     * Future date and time
                                                     */
                                                    $new_ticket_result = $ticket->readTicketsByCategoryId($data->category_id,$data->sub_category_id,$data->number_of_ticket);
                                                    if($new_ticket_result->num_rows>0){
                                                        $eventID = $events->createEvent($data);
                                                            while ($purchase_item = $new_ticket_result->fetch_assoc())
                                                                {  
                                                                extract($purchase_item);  
                                                                // $event = new Events($db);
                                                                $events->createEventHistory(array(
                                                                    'ticket_id' => $ticket_id,
                                                                    'user_id'   => $data->user_id,
                                                                    'event_id'  => $eventID,
                                                                    'category_id'=>  $data->category_id,
                                                                    'sub_category_id'=>$data->sub_category_id,
                                                                    'per_ticket_cost'=>$data->cost_of_ticket,
                                                                ));
                                                                $itemDetails=array(
                                                                    "ticket_id" => $ticket_id,
                                                                    "ticket_name" => $ticket_name,
                                                                    "ticket_image" => $siteUrl.$ticket_file_url		
                                                                ); 
                                                                array_push($itemRecords, $itemDetails);
                                                            }
                                                            $status = 200;
                                                            $message= "Purchased success";
                                                            $event_data=array(
                                                                "event_status"=>"Active",
                                                                "event_id" => $eventID,
                                                                "event_name" => $ticket_row['sub_category_name'].''.$eventID,
                                                                "event_date" => $data->event_date, 
                                                                "event_time"=>$data->event_time,
                                                                "number_of_ticket"=>$data->number_of_ticket	,
                                                                "tickets"=>$itemRecords 
                                                            );
                                                        }else{ 
                                                            $message = "Something went wrong!";
                                                        } 
                                                } 
                                            }else{
                                                $message = "Sorry! Ticket limits is exceeds!";
                                            }    
                                        }else{
                                            $message = "Insufficient balance!";
                                        }
                                    }else{
                                        $message = "Invalid Subcategory ID!";
                                    }
                                        
                                }else{
                                    $message = "Sorry! Event date must be future or present.";
                                } 
                                
                            }else{
                                $message = "Invalid category Id";
                            } 
                        }else{
                            $message = "Event time is missing";
                        }                        
                    }else{
                        $message = "Event date is missing";
                    }
                        
                }else{
                    $message = "Sub Category Id is missing";
                }
                
            }else{
                $message = "Category Id is missing";
            }  
            
        }else{
            $message = "Number of ticket is missing";
        }
    }else{
        $message = "Invalid User ID";
    }
}else{
    $message = "User Id is required";
}

http_response_code(200);     
echo json_encode(['status'=>$status,"message"=>$message,'events'=>$event_data]);
exit;
 
}else{
    http_response_code(404);     
    echo json_encode(['status'=>404,"message"=>'Method not allowed']);
    exit;
}

function getCurrentTime(){ 
    $diff = 15;  
    $addMin = 15;  //15 min more add to draw time for shifting bets
    $timestamp = time() + date("Z") + $addMin*60 - $diff*60;
    $hour = gmdate("H", $timestamp);
    $min  = gmdate("i", $timestamp);
    $cmin = $min%15;

    if($cmin > 0) {
        $min = $min - $cmin; 
    } 
    $min = (strlen($min) == 1)  ? '0'. $min  : $min;
    $drawTime = $hour.':'.$min;
    $result = date('H:i:s',strtotime($drawTime));
    return $result;
} 

 