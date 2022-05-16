<?php
class TicketCodes{   
    
    private $itemsTable = "ticket_numbers";   
    private $conn;
	
    public function __construct($db){
        $this->conn = $db;
    }	

    function selecCodes($data){
        $ticket_id      = htmlspecialchars(strip_tags($data['ticket_id'])); 
        $type_id        = htmlspecialchars(strip_tags($data['type_id']));
        $code           = htmlspecialchars(strip_tags($data['code']));
        
        $stmt = $this->conn->prepare("SELECT * FROM ".$this->itemsTable." WHERE type_id = ? AND ticket_id = ? AND code = ? limit 1");
        $stmt->bind_param("iii", $type_id,$ticket_id,$code); 		
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result;
    } 

    function createCodes($data){
        $stmt = $this->conn->prepare("
        INSERT INTO ".$this->itemsTable."( `category_id`, `sub_category_id`,`ticket_id`,`type_id`, `code`)
        VALUES(?,?,?,?,?)");
        
        $type_id        = htmlspecialchars(strip_tags($data['type_id']));
        $category_id    = htmlspecialchars(strip_tags($data['category_id']));
        $code           = htmlspecialchars(strip_tags($data['code']));
        $ticket_id      = htmlspecialchars(strip_tags($data['ticket_id'])); 
        $sub_category_id= htmlspecialchars(strip_tags($data['sub_category_id']));
        
        $stmt->bind_param("iiiis", $category_id, $sub_category_id, $ticket_id, $type_id,$code); 
        if($stmt->execute()){
            return true;
        }
    } 
    function updateCodes($data){
	 
		$stmt = $this->conn->prepare("
			UPDATE ticket_numbers
			SET category_id= ?, sub_category_id = ?, ticket_id = ?, type_id = ? code = ? WHERE ticket_number_id = ?");
 
            $type_id        = htmlspecialchars(strip_tags($data['type_id']));
            $category_id    = htmlspecialchars(strip_tags($data['category_id']));
            $code           = htmlspecialchars(strip_tags($data['code']));
            $ticket_id      = htmlspecialchars(strip_tags($data['ticket_id'])); 
            $sub_category_id= htmlspecialchars(strip_tags($data['sub_category_id']));
            $ticket_number_id= htmlspecialchars(strip_tags($data['ticket_number_id']));
            $stmt->bind_param("iiiiii", $category_id, $sub_category_id, $ticket_id, $type_id,$code,5); 
		
		if($stmt->execute()){
			return true;
		}
	 
		return false;
	}
    function deleteCodes($ticket_id){  
		$stmt = $this->conn->prepare("
			DELETE FROM ".$this->itemsTable." 
			WHERE ticket_number_id = ?"); 
		$ticket_id = htmlspecialchars(strip_tags($ticket_id)); 
		$stmt->bind_param("i", $ticket_id); 
		if($stmt->execute()){
			return true;
		} 
		return false;		 
	}
	 
}
?>