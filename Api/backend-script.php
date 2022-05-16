<?php
include_once '../config.php';
include_once '../Class/SubCategories.php';
include_once '../Class/Users.php';
include_once '../Class/TicketCodes.php';
include_once '../Class/Tickets.php';
$database = new Database();
$db = $database->getConnection();
session_start();    
if(isset($_GET['del']) && isset($_GET['codeId'])){
    $id = isset($_GET['codeId'])?$_GET['codeId']:0;
     $ticket = new TicketCodes($db);
     $ticket->deleteCodes($id);
     header("Location: {$_SERVER['HTTP_REFERER']}");
exit;
}
//Delete tickets
if(isset($_GET['del']) && isset($_GET['ticketId']) && $_GET['del'] =="TKT"){
     $id = isset($_GET['ticketId'])?$_GET['ticketId']:0;
     $ticket = new Tickets($db);
     $ticket->deleteTickets($id);
     
     header("Location: {$_SERVER['HTTP_REFERER']}");
exit;
}
if(isset($_GET['type'])){
    if(($_GET['type'] == "sub") && isset($_GET['type'])){
        $items = new SubCategories($db); 
        $items->id = (isset($_GET['Id']) && $_GET['Id']) ? $_GET['Id'] : '0'; 
        $result = $items->read();
        if($result->num_rows > 0){    
            $itemRecords=array();
            $itemRecords=array(); 
            while ($item = $result->fetch_assoc()) {  
                $itemDetails="<option  value='" . $item['sub_category_id'] . "'>" . $item['sub_category_name'] . "</option>"; 
               array_push($itemRecords, $itemDetails);
            }    
            http_response_code(200);     
            echo json_encode($itemRecords);
        }
        exit;
    }
}


//login userif()
if(isset($_POST['typ'])){
    if(($_POST['typ'] == "login") ){
    
        $user = new Users($db); 
        $user_email = isset($_POST['username'])?$_POST['username']:null;
        $password = isset($_POST['password'])?$_POST['password']:null;
        $itemRecords = array();
        if(!empty($user_email) || !empty($password)){
            $result = $user->selectUser($user_email); 
            if($result->num_rows > 0){  
                $item = $result->fetch_assoc(); 
                if(md5($password) == $item['password']){
                    //The passwords are equal
                    $_SESSION['user'] = $item['user_id'];
                    $itemRecords=array('status'=>200,'message'=>'success');      
                }else{
                    $itemRecords=array('status'=>404,'message'=>md5($password));      
                } 
            }else{
                $itemRecords=array('status'=>404,'message'=>'User not found'); 
            }
        }else{
            $itemRecords=array('status'=>401,'message'=>'Username or Password should not be empty'); 
        }
        http_response_code(200);
        echo json_encode($itemRecords);
        exit;
    }
}

