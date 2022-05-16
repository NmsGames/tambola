<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config.php';
include_once '../Class/SubCategories.php';
include_once '../Class/Users.php';
include_once '../Class/Tickets.php';
$database = new Database();
$db = $database->getConnection();
session_start();
 
 
 /**
  * GET Path url
  */
function pathUrl($dir = __DIR__){ 
    $root = "";
    $dir = str_replace('\\', '/', realpath($dir));

    //HTTPS or HTTP
    $root .= !empty($_SERVER['HTTPS']) ? 'https' : 'http';

    //HOST
    $root .= '://' . $_SERVER['HTTP_HOST'];

    //ALIAS
    if(!empty($_SERVER['CONTEXT_PREFIX'])) {
        $root .= $_SERVER['CONTEXT_PREFIX'];
        $root .= substr($dir, strlen($_SERVER[ 'CONTEXT_DOCUMENT_ROOT' ]));
    } else {
        $root .= substr($dir, strlen($_SERVER[ 'DOCUMENT_ROOT' ]));
    }

    $root .= '/';

    return $root;
}
 
 
if(isset($_GET['amount']) && isset($_GET['ticket']))
{   
    //path
    $siteUrl = pathUrl(__DIR__ . '/../');
    $tck    = new Tickets($db);
    $ticket_number = isset($_GET['ticket'])?$_GET['ticket']:0;
    $ticket_amount = isset($_GET['amount'])?$_GET['amount']:0;
    $sql = "SELECT * FROM tickets_purchase_history";
    $result = $db->query($sql);
    if($ticket_number<=6 && $ticket_number>=0){
        if ($result->num_rows > 0){

        } else{
            $sql = "SELECT * FROM tickets ORDER BY RAND() LIMIT ".$ticket_number.""; 
            $result = $db->query($sql); 
            if($result->num_rows > 0)
            {    
                $itemRecords=array();
                $itemRecords["tickets"]=array(); 
                while ($item = $result->fetch_assoc()) { 	
                    extract($item);  
                    $itemDetails=array(
                        "ticket_id" => $ticket_id,
                        "ticket_name" => $ticket_name,
                        "ticket_image" => $siteUrl.$ticket_file_url		
                    ); 
                array_push($itemRecords["tickets"], $itemDetails);
                }    
                http_response_code(200);     
                echo json_encode($itemRecords);
            }else{     
                http_response_code(404);     
                echo json_encode(
                    array("message" => "No item found.")
                );
            }
        }
    }else{
        http_response_code(404);     
        echo json_encode(
            array("message" => "Please valid ticket.")
        );
    } 
     
}
 