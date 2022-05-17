<?php
class Database{
	
	private $host  = '127.0.0.1';
	// private $host  = '165.22.208.86';
    // private $user  = 'root';
    private $user  = 'pmauser';
    // private $password   = "";
    private $password   = "Nmsgames@123";
    private $database  = "tambola"; 
    
    public function getConnection(){		
		$conn = new mysqli($this->host, $this->user, $this->password, $this->database);
		if($conn->connect_error){
			die("Error failed to connect to MySQL: " . $conn->connect_error);
		} else {
			return $conn;
		}
    }
}
?>