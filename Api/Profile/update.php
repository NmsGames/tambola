<?php 
include_once '../headerPage.php';  
$method = $_SERVER['REQUEST_METHOD']; 
$server_url = 'http://localhost/tambola';
$upload_dir =  '../../uploads/avtar/';
  
$response = array();
if ($method == "POST") 
{
     
    $user = new Users($db);
    $user_id= isset($_POST['user_id'])?$_POST['user_id'] : null; 
    $username  = isset($_POST['username']) ? $_POST['username'] : null;
    $itemRecords = array();
    if (!empty($user_id)) 
    {
        if (!empty($username)) { 
                if(!empty($_FILES['avatar']['name']))
                { 
                $result = $user->checkUser($user_id);
                if ($result->num_rows > 0)
                {    
                    $users = $result->fetch_assoc(); 
                    $avatar_name      = $_FILES["avatar"]["name"];
                    $avatar_tmp_name  = $_FILES["avatar"]["tmp_name"];
                    $error            = $_FILES["avatar"]["error"];
                //path to upload in server
                    $targetDir      = "../../uploads/avtar/"; 
                    $random_name    = $user_id."-".$avatar_name;
                    $targetFilePath = $targetDir.$avatar_name; 
                    $targetFilePath = $targetDir.strtolower($random_name);
                    $targetFilePath = preg_replace('/\s+/', '-', $targetFilePath);
                    $fileType       = pathinfo($targetFilePath,PATHINFO_EXTENSION); 
                    //Check Image type  
                    $allowTypes = array('jpg','JPG','JPEG','PNG','png','jpeg');
                    if(in_array($fileType, $allowTypes))
                    { 
                        if($error > 0){
                            $response = array(
                                "status" => "error",
                                "error" => true,
                                "message" => "Error uploading the file!"
                            );
                        }
                        else 
                        { 
                            if(move_uploaded_file($avatar_tmp_name , $targetFilePath))
                            {
                                $uploadDir = 'uploads/avtar/';
                                $uploadPath = $uploadDir.strtolower($random_name);
                                $uploadPath = preg_replace('/\s+/', '-', $uploadPath);
                                $response = array(
                                    "user_id" => $user_id,
                                    "username" => $username,
                                    "avatar_link" => $uploadPath
                                  );
                             $res_result = $user->uploadProfile(array(
                                    "user_id" => $user_id,
                                    "username" => $username,
                                    "avatar_link" => $uploadPath
                                  ));
                                  if($res_result){
                                      $response = array(
                                        "status" => 200,
                                        "error" => false,
                                        "message" => "Profile updated"
                                    );
                                  }else{
                                    $response = array(
                                        "status" => 404,
                                        "error" => true,
                                        "message" => "Someting went wrong!server side"
                                    );
                                  } 
                            }else
                            {
                                $response = array(
                                    "status" => "error",
                                    "error" => true,
                                    "message" => "Error uploading the file!"
                                );
                            }
                        } 
                    }else{
                        $response = array('status' => 401, 'message' => 'Sorry, only JPG, JPEG, PNG files are allowed to upload.');  
                    } 
                } else { 
                    $response = array('status' => 401, 'message' => 'Invalid User ID'); 
                }
            }else{
                $response = array('status' => 401, 'message' => 'Profile avatar is required'); 
            } 
        } else {
            $response = array('status' => 404, 'message' => 'Username is required');
        }
    } else {
        $response = array('status' => 401, 'message' => 'User ID is required');
    }
    http_response_code(200);
    echo json_encode($response);
} else {
    http_response_code(404);
    echo json_encode(array('status' => 404, 'message' => 'Method not allowed'));
}
 
