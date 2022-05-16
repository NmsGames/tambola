<?php
class Events{   
    
    private $itemsTable = "theme_events";      
    public $event_id;
    public $theme_id;
    public $event_name; 
    private $conn;
    public $current_time; 
    public $current_date; 
	
    public function __construct($db){
        $this->conn = $db;
    }	
    
    //check event or updated status
	function checkEvent(){
        date_default_timezone_set('Asia/Kolkata'); 
        $this->current_date = date('Y-m-d');
        $this->current_time = date('H:i:s');
        
        $stmt = $this->conn->prepare("SELECT * FROM ".$this->itemsTable." WHERE date(event_date) <= ? AND time(event_time) <= ?");
        $stmt->bind_param("ss", $this->current_date,$this->current_time);	 	
		$stmt->execute();			
		$result = $stmt->get_result(); 
        

        if($result->num_rows>0){ 
            while ($itemsName = $result->fetch_assoc()) { 	
                extract($itemsName);   
                $stmt1 = $this->conn->prepare("UPDATE tickets_purchase_history SET is_status=1 WHERE event_id= ?"); 
                $stmt1->bind_param("s", $event_id);
                if($stmt1->execute()){
                    continue ;
                } 
            }
            $stmt3 = $this->conn->prepare("UPDATE ".$this->itemsTable." SET is_expired=1 WHERE date(event_date) <= ? AND time(event_time) <= ?"); 
            $stmt3->bind_param("ss", $this->current_date,$this->current_time);
            if($stmt3->execute()){
                return true;
            } 
             
        }	
		return $result;	
	}

     //check event or updated status
	function checkEventByDateTime($user_id,$event_date,$event_time)
    {
        date_default_timezone_set('Asia/Kolkata'); 
        $event_date = date("Y-m-d", strtotime($event_date));
        $event_time = date("H:i:s", strtotime($event_time));
        $stmt = $this->conn->prepare("SELECT * FROM ".$this->itemsTable." WHERE date(event_date) = ? AND time(event_time) = ? AND user_id=?");
        $stmt->bind_param("ssi", $event_date,$event_time,$user_id);	 	
		$stmt->execute();			
		$result = $stmt->get_result();  
         
        return $result;
	}

    //CREATe Event 
    function createEvent($data){
        
            date_default_timezone_set('Asia/Kolkata');  
            $event_date = date("Y-m-d", strtotime($data->event_date));
            $event_time = date("H:i:s", strtotime($data->event_time));
             
            $stmt = $this->conn->prepare("
            INSERT INTO ".$this->itemsTable."(`ticket_cost`,
            `tickets`,`user_id`,`event_time`,`event_date`,`event_id`,`sub_id`)
            VALUES(?,?,?,?,?,?,?)"); 
            $user_id        = htmlspecialchars(strip_tags($data->user_id));
            $category_id    = htmlspecialchars(strip_tags($data->category_id));
            $sub_category_id= htmlspecialchars(strip_tags($data->sub_category_id));
            $cost_of_ticket = htmlspecialchars(strip_tags($data->cost)); 
            $number_of_ticket= htmlspecialchars(strip_tags($data->number_of_ticket));
           
            $event_id = $category_id.$sub_category_id.$user_id.rand(1000,9999); 
            $stmt->bind_param("ssssssi", 
             $cost_of_ticket,
             $number_of_ticket,
             $user_id,
             $event_time,
             $event_date,
             $event_id,
             $sub_category_id
              );  
              
            if($stmt->execute()){ 
                return $event_id;
            }
	 
		return false;
	}
    //CREATe Event 
    function createEventHistory($data)
    { 
        date_default_timezone_set('Asia/Kolkata');  
        $purchase_date = date("Y-m-d"); 
        $stmt = $this->conn->prepare("INSERT INTO tickets_purchase_history (
        `ticket_id`,`user_id`,`purchase_date`,`category_id`,`sub_category_id`,`event_id`)
        VALUES(?,?,?,?,?,?)"); 
     
        $event_id        = htmlspecialchars(strip_tags($data['event_id']));
        $category_id     = htmlspecialchars(strip_tags($data['category_id']));
        $sub_category_id = htmlspecialchars(strip_tags($data['sub_category_id']));
        $user_id         = htmlspecialchars(strip_tags($data['user_id'])); 
        $ticket_id       = htmlspecialchars(strip_tags($data['ticket_id']));
        $per_ticket_cost = htmlspecialchars(strip_tags($data['per_ticket_cost']));   
        $stmt->bind_param("ssssss", 
         $ticket_id,
         $user_id,
         $purchase_date,
         $category_id,
         $sub_category_id,
         $event_id
          );  
        if($stmt->execute()){
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE user_id =?");
            $stmt->bind_param("i", $user_id);	 	
            $stmt->execute();			
            $result = $stmt->get_result(); 
            if($result->num_rows>0){
                $rows = $result->fetch_assoc();
                $updated_coins = $rows['coins']-$per_ticket_cost;
                $stmt1 = $this->conn->prepare("UPDATE users SET coins=? WHERE user_id= ?"); 
                $stmt1->bind_param("ii", $updated_coins,$user_id);
                if($stmt1->execute()){
                    return true;
                } 

            }
            
           
        }
 
    return false;
}
    function readById($evenId){ 
        $this->user_id = $evenId;
        $stmt = $this->conn->prepare("SELECT * FROM ".$this->itemsTable." LEFT JOIN users ON users.user_id =theme_events.user_id WHERE theme_events.user_id = ?");
        $stmt->bind_param("i", $this->user_id);	 	
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result;	
	}
    function getCreateEvents($userID){ 
        $this->user_id = $userID;
        $stmt = $this->conn->prepare("SELECT * FROM ".$this->itemsTable." LEFT JOIN sub_categories ON theme_events.sub_id =sub_categories.sub_category_id WHERE theme_events.user_id = ?");
        $stmt->bind_param("i", $this->user_id);	 	
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result;	
	}
    function ticketListByUserID($userID){ 
        $this->user_id = $userID;
        $stmt = $this->conn->prepare("SELECT * FROM tickets_purchase_history LEFT JOIN tickets ON tickets.ticket_id = tickets_purchase_history.ticket_id WHERE tickets_purchase_history.user_id = ? order by tickets_purchase_history.tp_id desc;");
        $stmt->bind_param("i", $this->user_id);	 	
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result;	
	}
	function read(){	
		if($this->theme_id) {
			$stmt = $this->conn->prepare("SELECT * FROM ".$this->itemsTable." WHERE theme_id = ?");
			$stmt->bind_param("i", $this->theme_id);					
		} else {
			$stmt = $this->conn->prepare("SELECT * FROM ".$this->itemsTable);		
		}		
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result;	
	}
	
    function readCheckEvent($data)
    {	 
        date_default_timezone_set('Asia/Kolkata');  
        $user_id    = htmlspecialchars(strip_tags($data->user_id));  
        $event_date = date("Y-m-d", strtotime($data->event_date));
        $event_time = date("H:i:s", strtotime($data->event_time));
        $stmt = $this->conn->prepare("SELECT * FROM ".$this->itemsTable." WHERE date(event_date) = ? AND time(event_time) = ? AND user_id=? limit 1");
        $stmt->bind_param("ssi", $event_date,$event_time,$user_id);	 	
		$stmt->execute();			
		$result = $stmt->get_result(); 	
        return $result;	
	}
	 
}
?>