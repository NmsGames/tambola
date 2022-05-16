<?php
class Tickets{   
    
    private $itemsTable = "tickets";       
    private $conn;
	public $ticket_id;
	public $ticketname;
	public $category_id;   
	public $ticket_file_url; 
	public $sub_category_id;  
	public $ticketId; 
    public function __construct($db){
        $this->conn = $db;
    }	
	 
	function read(){	
		 	
		if($this->ticketname) { 
			$name = strtolower($this->ticketname);
			$stmt = $this->conn->prepare("SELECT * FROM tickets WHERE LOWER(ticket_name) =? LIMIT 1");
			$stmt->bind_param("s", $name);					
		}
		elseif($this->ticketId) { 
			$stmt = $this->conn->prepare("SELECT * FROM tickets WHERE LOWER(ticket_id) =? LIMIT 1");
			$stmt->bind_param("i", $this->ticketId);					
		}
		else{
			$stmt = $this->conn->prepare("SELECT * FROM ".$this->itemsTable." INNER join categories on categories.category_id = tickets.category_id LEFT join sub_categories on sub_categories.sub_category_id = tickets.sub_category_id");
		}	
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result;	
	}
	function readTickets(){ 
		$stmt = $this->conn->prepare("SELECT * FROM ".$this->itemsTable." INNER join categories on categories.category_id = tickets.category_id LEFT join sub_categories on sub_categories.sub_category_id = tickets.sub_category_id"); 
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result;	
	}

	function readPurchasedTickets($userId){ 
		$stmt = $this->conn->prepare("SELECT tickets.*,theme_events.* FROM tickets_purchase_history 
		LEFT JOIN theme_events ON theme_events.user_id = tickets_purchase_history.user_id
		INNER JOIN tickets ON tickets.ticket_id = tickets_purchase_history.ticket_id
		WHERE theme_events.user_id = ? ORDER BY tickets_purchase_history.tp_id DESC"); 
		$stmt->bind_param("i", $userId);
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result;	
	}

	function readTicketsByCategoryId($category_id,$sub_category_id,$number_of_ticket){ 
		$stmt = $this->conn->prepare("SELECT * FROM ".$this->itemsTable." WHERE category_id = ? AND sub_category_id = ? ORDER BY RAND() LIMIT ?"); 
		$stmt->bind_param("iii", $category_id,$sub_category_id,$number_of_ticket);
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result;	
	}
	//PURCHASE TICKETS HISTORY
	function purchaseTicketHistory(){ 
		$stmt = $this->conn->prepare("SELECT * FROM theme_events INNER JOIN users ON users.user_id = theme_events.user_id");  
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result;	
	}
	
	function create(){ 
		$stmt = $this->conn->prepare("
			INSERT INTO ".$this->itemsTable."(`category_id`, `sub_category_id`, `ticket_name`,`ticket_file_url`)
			VALUES(?,?,?,?)");
		
		$this->ticket_file_url = htmlspecialchars(strip_tags($this->ticket_file_url));
		$this->sub_category_id = htmlspecialchars(strip_tags($this->sub_category_id));
		$this->ticketname      = htmlspecialchars(strip_tags($this->ticketname));
		$this->category_id     = htmlspecialchars(strip_tags($this->category_id)); 
		
		
		$stmt->bind_param("iiss", $this->category_id, $this->sub_category_id, $this->ticketname, $this->ticket_file_url); 
		if($stmt->execute()){
			return true;
		}
	 
		return false;		 
	}
		
	function update(){
	 
		$stmt = $this->conn->prepare("
			UPDATE tickets 
			SET sub_category_id= ?, category_id = ?, ticket_name = ?, ticket_file_url = ? WHERE ticket_id = ?");
	 
		$this->ticket_id 		= htmlspecialchars(strip_tags($this->ticket_id));
		$this->ticket_file_url 	= htmlspecialchars(strip_tags($this->ticket_file_url));
		$this->sub_category_id 	= htmlspecialchars(strip_tags($this->sub_category_id));
		$this->ticketname 		= htmlspecialchars(strip_tags($this->ticketname));
		$this->category_id 		= htmlspecialchars(strip_tags($this->category_id)); 
		
		
		$stmt->bind_param("iissi", $this->sub_category_id, $this->category_id, $this->ticketname, $this->ticket_file_url, $this->ticket_id);
		
		if($stmt->execute()){
			return true;
		}
	 
		return false;
	}
	
	function delete(){  
		$stmt = $this->conn->prepare("
			DELETE FROM ".$this->itemsTable." 
			WHERE ticket_id = ?"); 
		$this->ticket_id = htmlspecialchars(strip_tags($this->ticket_id)); 
		$stmt->bind_param("i", $this->ticket_id); 
		if($stmt->execute()){
			return true;
		}
	 
		return false;		 
	}
	function deleteTickets($ticket_id){  
		$stmt = $this->conn->prepare("
			DELETE FROM ".$this->itemsTable." 
			WHERE ticket_id = ?"); 
		$ticket_id = htmlspecialchars(strip_tags($ticket_id)); 
		$stmt->bind_param("i", $ticket_id); 

		if($stmt->execute()){
			$stmt = $this->conn->prepare("
			DELETE FROM ticket_numbers 
			WHERE ticket_id = ?");  
            $stmt->bind_param("i", $ticket_id); 
            if($stmt->execute()){
                return true;
            }
		} 
		return false;		 
	}

	//Check Ticket Limits
	function checkTicketLimit($category_id,$sub_category_id){ 
		$stmt = $this->conn->prepare("SELECT * FROM ".$this->itemsTable." WHERE category_id = ? AND sub_category_id = ? "); 
		$stmt->bind_param("ii", $category_id,$sub_category_id);
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result;	
	}
	//Check Ticket Limits
	//GET all ticket by eventID
	function checkPurchaseTickets($event_id){ 
		$stmt = $this->conn->prepare("SELECT * FROM tickets_purchase_history LEFT JOIN tickets ON tickets.ticket_id = tickets_purchase_history.ticket_id WHERE tickets_purchase_history.event_id = ?"); 
		$stmt->bind_param("s", $event_id);
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result;	
	}

	//PUrchase ticket again 
	function purchaseTicketAgain($category_id,$sub_category_id,$number_of_ticket,$ticketId)
	{ 
		 
		$cat_str = implode(",",$ticketId);
		$stmt = $this->conn->prepare("SELECT * FROM ".$this->itemsTable." WHERE category_id = ? AND sub_category_id = ? AND ticket_id NOT IN (?) ORDER BY RAND() LIMIT ?"); 
		$stmt->bind_param("iisi", $category_id,$sub_category_id,$cat_str,$number_of_ticket);
		$stmt->execute();			
		$result = $stmt->get_result();		 
		return $result;	
	}
	
}
?>