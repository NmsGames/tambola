<?php
class Users{   
    
    private $itemsTable = "users";   
    private $conn;
	
    public function __construct($db){
        $this->conn = $db;
    }	

    function read(){ 
        $stmt = $this->conn->prepare('SELECT *,SUM(theme_events.tickets) AS total_ticktes FROM users 
        INNER JOIN theme_events ON users.user_id = theme_events.user_id  GROUP BY theme_events.user_id'); 
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result;
    }
    function selectUser($user){
         
        $stmt = $this->conn->prepare("SELECT * FROM ".$this->itemsTable." WHERE email = ? limit 1");
        $stmt->bind_param("s", $user); 		
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result;
    } 
    function checkUser($user){
         
        $stmt = $this->conn->prepare("SELECT * FROM ".$this->itemsTable." WHERE user_id = ?");
        $stmt->bind_param("i", $user); 		
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result;
    } 

    function getProfile($profile_id){
         
        $stmt = $this->conn->prepare("SELECT * FROM ".$this->itemsTable." WHERE profile_id = ?");
        $stmt->bind_param("i", $profile_id);      
        $stmt->execute();           
        $result = $stmt->get_result();      
        return $result;
    } 

    function getUsers(){
         
        $stmt = $this->conn->prepare("SELECT * FROM ".$this->itemsTable." Order BY user_id");      
        $stmt->execute();           
        $result = $stmt->get_result();      
        return $result;
    } 

    function createUser($data){
        $email       = htmlspecialchars(strip_tags($data->email));
        $username    = htmlspecialchars(strip_tags($data->username));
        $password    = htmlspecialchars(strip_tags($data->password)); 
        $pass        = md5($password);
        $device_id   = isset($data->device_id) ? $data->device_id : null;
        $created     = date('y-m-d H:i:s');
        $profile_id  = round(microtime(true));
        $stmt = $this->conn->prepare("
        INSERT INTO ".$this->itemsTable."(`email`,`username`,`password`,`profile_id`,`device_id`,`created`)
        VALUES(?,?,?,?,?,?)");
        $stmt->bind_param("ssssss", $email,$username,$pass,$profile_id,$device_id,$created); 		
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result;
    } 
    function uploadProfile($data){
        $user_id     = $data['user_id'];
        $username    = $data['username'];
        $avatar_link = $data['avatar_link']; 
        $stmt1 = $this->conn->prepare("UPDATE ".$this->itemsTable." SET avatar=?, username=? WHERE user_id= ?"); 
        $stmt1->bind_param("ssi",$avatar_link,$username, $user_id);
        if($stmt1->execute()){
          return true;
        }
        return false;
    }


    function updateCoin($data){
        $user_id     = $data['user_id'];
        $coins       = $data['amount'];
        $utype       = $data['utype']; 
        $user_result = $this->checkUser($user_id);
        $user_row    = $user_result->fetch_assoc();
        if($user_result->num_rows>0){  
            $coin_balance  = $user_row['coins'];
            if($utype == 'credit'){
                $bal_coins =  $coin_balance + $coins;
            }elseif ($utype == 'debit') {
                $bal_coins =  $coin_balance - $coins;
            }else{
                $bal_coins =  $coin_balance ;
            }
            $stmt1 = $this->conn->prepare("UPDATE ".$this->itemsTable." SET coins=?  WHERE user_id= ?"); 
            $stmt1->bind_param("ss",$bal_coins,$user_id);
            if($stmt1->execute()){
              return true;
            }
        }    
        return false;
    }

    function getSocialUser($data){
        $result = null;
        $google_id = isset($data->google_id) ? $data->google_id : null;
        $fb_id     = isset($data->fb_id) ? $data->fb_id : null;
        if(!empty($google_id)){
            $stmt = $this->conn->prepare("SELECT * FROM ".$this->itemsTable." WHERE google_id = ?");
           $stmt->bind_param("s", $google_id);      
            $stmt->execute();           
            $result = $stmt->get_result();      
        }elseif (!empty($fb_id)) {
            $stmt = $this->conn->prepare("SELECT * FROM ".$this->itemsTable." WHERE fb_id = ?");
            $stmt->bind_param("s", $fb_id);      
            $stmt->execute();           
            $result = $stmt->get_result(); 
        }

        return $result;
    }

    function createSocialUser($data){

        $google_id = isset($data->google_id) ? $data->google_id : null;
        $fb_id     = isset($data->fb_id) ? $data->fb_id : null;
        $reg_id    = !empty($google_id)  ? $google_id   : $fb_id;
        $username  = isset($data->username) ? $data->username : 'Guest';
        $email     = isset($data->email) ? $data->email : null;
        $device_id = isset($data->device_id) ? $data->device_id : null;
        $created    = date('y-m-d H:i:s');
        $profile_id = round(microtime(true));
        if(!empty($google_id)){
            $stmt = $this->conn->prepare("INSERT INTO ".$this->itemsTable."(`username`,`email`,`profile_id`,`google_id`,`device_id`,`created`)
            VALUES(?,?,?,?,?,?)");
        }else{
            $stmt = $this->conn->prepare("INSERT INTO ".$this->itemsTable."(`username`,`email`,`profile_id`,`fb_id`,`device_id`,`created`)
            VALUES(?,?,?,?,?,?)");
        }
        $stmt->bind_param("ssssss", $username,$email,$profile_id,$reg_id,$device_id,$created); 
        if($stmt->execute()){
           return true;
        }
        return false;  
    }

    // function checkOauthUser($data){ 
    //     $google_id = isset($data->google_id) ? $data->google_id : null;
    //     $fb_id     = isset($data->fb_id) ? $data->fb_id : null;
    //     $username  = isset($data->username) ? $data->username : null;
    //     $email     = isset($data->email) ? $data->email : null;
    //     $device_id = isset($data->device_id) ? $data->device_id : null;

    //     $checkResult =  $this->getSocialUser($data);

    //     if(!empty($checkResult) && $checkResult->num_rows > 0){
    //        return $checkResult;
    //     }else{
    //         //Create New User
    //         $this->createSocialUser($data);
    //     }

    //     $userData =  $this->getSocialUser($data);
    //     return $userData;
    // } 

    function getUserRank($user){
        $stmt = $this->conn->prepare(
        "SELECT rank
        from (SELECT @rownum := @rownum + 1 AS rank, u.* 
        FROM ".$this->itemsTable." AS u, (SELECT @rownum := 0) t ORDER BY coins DESC) z
        where z.user_id = ?");
        $stmt->bind_param("i", $user);      
        $stmt->execute();           
        $result = $stmt->get_result();    
        return $result;
    } 

    function genProfileId($l=10){
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyz"), 0, $l);
    }
   
}
?>