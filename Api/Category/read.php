<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8"); 
include_once '../../config.php';
include_once '../../Class/Categories.php'; 
$database = new Database();
$db = $database->getConnection();


$items = new Categories($db);

$result = $items->read();
if($result->num_rows > 0){    
    $itemRecords=array();
    $itemRecords=array(); 
	while ($item = $result->fetch_assoc()) { 	
        extract($item); 
        $itemDetails=array(
            "category_id" => $category_id,
            "category_name" => $category_name			
        ); 
       array_push($itemRecords, $itemDetails);
    }    
    http_response_code(200);     
    echo json_encode(['status'=>200,'items'=>$itemRecords]);
}else{     
    http_response_code(404);     
    echo json_encode(
        array("status"=>404,"message" => "No item found.")
    );
} 