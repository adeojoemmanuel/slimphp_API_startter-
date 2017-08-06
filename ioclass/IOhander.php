<?php 
	class IOhandler{
		private $DBcon;

		public function __construct(){
	 		include('dbconfig.php');
	 		$db = new connect();
        	$this->DBcon = $db->startConn();
		}

		public function doo($input){
			$q = $this->DBcon->prepare($input);
			$q->execute();
		}

		public function countRow($value, $table, $params){
			$sql = "SELECT $value from $table where $value = $params";
			$result = $this->DBcon->query($sql);
			$row_cnt = $result->rowCount();
			return $this->reponse(200, $row_cnt);
		}

		public function get_all($table, $foreign, $id) {
		    $query = "SELECT message, time_sent FROM $table WHERE $foreign=$id";
		    $sql = $this->DBcon->prepare($query);
		    $sql->execute();
		    $data = $sql->fetchAll();
		    return $data;
		  //   foreach ($data as $key => $val) {
			 //    $value = $data[1];
			 //    return $value;
			 // }
		}

		public function getAll($table){
			$query = "SELECT * FROM $table";
			$q = $this->DBcon->prepare($query);
			$q->execute();
			$datas = $q->fetchAll();
			return $datas;
		}

		public function get_sent($table, $id) {
		    $query = "SELECT * FROM $table WHERE sender_id='$id'";
		    $sql = $this->DBcon->prepare($query);
		    $sql->execute();
		    $data = $sql->fetchAll();
		    return $data;
		}

		public function get_receive($table, $id) {
		    $query = "SELECT * FROM $table WHERE receiver_id='$id'";
		    $sql = $this->DBcon->prepare($query);
		    $sql->execute();
		    $data = $sql->fetchAll();
		    return $data;
		}


		private function out($value){
			echo json_encode($what);
		}

		public function reponse($code, $message){
			$response['status'] = $code;
			$response['message'] = $message;
			out($response);
		}

		public function getBy_id($params, $id, $table){
			$SQL = "SELECT * from $table where $params = '$id'";
			$q = $this->DBcon->prepare($SQL);
			$q->execute();
			$data = $q->fetch(PDO::FETCH_ASSOC);
			return $data;
		}
		public function insert($table, array $fields, array $values) {
		    $numFields = count($fields);
		    $numValues = count($values);
		    if($numFields === 0 or $numValues === 0)
		        throw new Exception("At least one field and value is required.");
		    if($numFields !== $numValues)
		        throw new Exception("Mismatched number of field and value arguments.");

		    $fields = '`' . implode('`,`', $fields) . '`';
		    $values = "'" . implode("','", $values) . "'";
		    $sql = "INSERT INTO {$table} ($fields) VALUES($values)";
			
			if ($q=$this->DBcon->prepare ( $sql )) {
		       // echo json_encode($sql);
		        if ($q->execute()) {
		            return "successfully registered";
		        }else{
		        	return "error in queary";
		        }
		    }
		    return "error registering";
		}
		public function update($table,$values=array(),$where){
            $args=array();
			foreach($values as $field=>$value){
				$args[]=$field.'="'.$value.'"';
			}
			$spilt = implode(',',$args);
			$sql='UPDATE '.$table.' SET '.$spilt.' WHERE '.$where;
   			if($q=$this->DBcon->prepare($sql)){
   				if ($q->execute()) {
   					return true;
   				}
   			}
   			return false;
    	}
		public function deleteData($id, $table){
			$SQL = "DELETE from $table where _id = :id";
			$q = $this->DBcon->prepare($SQL);
			$q->execute(array(':id' => $id));
			return true;
		}
		public function startSession(){
			if (!isset($_SESSION['_id'])) {
				session_start();
			}
			if (isset($_SESSION['_id'])) {
				$sessid = $_SESSION['id'];
			}	
		}
		public function endSession(){
			if(!isset($_SESSION['id'])){
				session_start();
			}
	    	if(isset($_SESSION['id'])){
	    		session_destroy();  
			}
	    }
	    public function getSessiondata(){
	        if (!isset($_SESSION)) {
	            session_start();
	        }
	        $session = array();
	        if(isset($_SESSION['userid'])){
	            $session["userid"] = $_SESSION['userid'];
	        }else{
	            $session["userid"] = '';
	        }
	        return $session;
	    }
	    public function sendMail($values = array()){
	    	$values = '`' . implode ( '`,`', $values ) . '`';
		    $mail_status = mail($values);
		    if ($mail_status) { 
		        return true;    
		    }else{
		        return false;
		    }
	    }
	    public function checkTableExist($table){
	    	$sql = "'SHOW TABLES FROM '.$this->dbname.' LIKE '.$table.''";
	    	if($sql){
	        	if(mysql_num_rows($sql)==1){
	                return true;
	            }else{
	                return false;
	            }
	        }
	    }
	    public function validateInput($input){
			$input=preg_replace("#[^0-9a-z]#i","",$input);
	    }
	    
	    public function login($table, $username, $password, $userparam, $orderbyparam){
	    	session_start();
	    	$sql = "SELECT * FROM $table WHERE {$userparam}='{$username}' ORDER BY '{$orderbyparam}' DESC limit 1";
		    $q = $this->DBcon->prepare($sql);
			$q->execute();
			$data = $q->fetch(PDO::FETCH_ASSOC);
		    $count=$q->rowCount();
		    $getpw = $data['password'];
		    $username = $data['username'];
		    
			if(($count)){
		        if ($password == $getpw) {
		           $_SESSION['userid'] = $data['_id'];
		           return "ok";
		        } else {
		            return "incorrect password";
		        }
			}else {
			    return "email not exist try logging in with your username";
			}
		}

		public function GetClientMac(){
		    $macAddr=false;
		    $arp=`arp -n`;
		    $lines=explode("\n", $arp);

		    foreach($lines as $line){
		        $cols=preg_split('/\s+/', trim($line));

		        if ($cols[0]==$_SERVER['REMOTE_ADDR']){
		            $macAddr=$cols[2];
		        }
		    }
		    return $macAddr;
		}
		public function my_url(){
		    $url = (!empty($_SERVER['HTTPS'])) ?
		               "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] :
		               "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
		    return $url;
		}
		public function count_rows_of_foreign($table, $foreignkey, $id){
			$result = $this->DBcon->query("SELECT * FROM $table WHERE $foreignkey=$id");
			$num_rows = $result->rowCount();
			return $num_rows;
		}
		public function forgotpass(){

		}
	}		
?>