<?php 
	class connect{
		private $dbhost = "localhost";
		private $dbuser = "root";
		private $dbpass = "";
		private $dbname = "chatty";

		public function startConn(){
			$this->DBcon = null;
			try{
				$this->DBcon = new PDO("mysql:host=".$this->dbhost.";dbname=".$this->dbname, $this->dbuser, $this->dbpass);
			}catch(Exception $e){
				echo "error connecting:";
			}
			return $this->DBcon;
		}
	} 
?>
