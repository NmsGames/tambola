<?php
 header("Access-Control-Allow-Origin: *");
 header("Content-Type: application/json; charset=UTF-8"); 
 include_once '../../config.php';
include_once '../../Class/Events.php'; 
include_once '../../Class/Tickets.php'; 
include_once '../../Class/Users.php'; 
include_once '../../Class/Categories.php'; 
include_once '../../Class/SubCategories.php';
include_once '../../Class/UserClaim.php';  
 $method = $_SERVER['REQUEST_METHOD'];

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

date_default_timezone_set('Asia/Kolkata');  
$range=range(strtotime("01:00"),strtotime("23:59"),15*60);
$timeInterval = array();
foreach($range as $time){ 
    array_push($timeInterval, date("H:i:s",$time));
}

$siteUrl = pathUrl(__DIR__ . '/../');
$database = new Database();
$db = $database->getConnection();