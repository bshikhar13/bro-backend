<?php

class DbHandler {
	private $conn;

	function __construct(){
		require_once dirname(__FILE__) . '/db_connect.php';
		$db = new DbConnect();
		$this->conn = $db->connect();
	}

	public function updateUserInfo($gid,$name,$email,$serverauthcode,$instanceid, $gcmtoken){
		$response = array();
		$stmt = $this->conn->prepare("UPDATE users SET name = ?, email = ?, server_auth_code = ?, instance_id = ?, gcm_token = ? WHERE g_id = ?");
		$stmt->bind_param("ssssss", $name, $email, $serverauthcode, $instanceid, $gcmtoken, $gid);
        $result = $stmt->execute();
       
		if($result){
			//GCM updated successfully
			$response["error"] = false;
			$response["message"] = 'GCM registration ID updated successfully';
            $response['data'] = array(
                'name' => $name,
                'gcmtoken' => $gcmtoken,
                'email' => $email,
                'gid' => $gid
                );
		}else{
			// Failed to update user
            $response["error"] = true;
            $response["message"] ="Error occured in DB";
            $stmt->error;
		}
		$stmt->close();
		
        return $response;
	   
    } 


    public function insertUserInfo($gid,$name,$email,$serverauthcode,$instanceid, $gcmtoken){
        $response = array();
        $stmt = $this->conn->prepare("INSERT INTO users (g_id, email,  name, instance_id, server_auth_code, gcm_token, created_at) VALUES(?, ?, ?, ? , ?, ?, CURRENT_TIMESTAMP)");
        $stmt->bind_param("ssssss", $gid , $email,$name, $instanceid, $serverauthcode, $gcmtoken);
       
        if($stmt->execute()){
            //GCM updated successfully
            
            $response["error"] = false;
            $response["message"] = 'GCM registration ID updated successfully';
            $response["data"] = array(
                'name' => $name,
                'gcmtoken' => $gcmtoken,
                'email' => $email,
                'gid' => $gid
                );
        }else{
            // Failed to update user
            $response["error"] = true;
            $response["message"] ="Error occured in DB";
            
        }
        
        $stmt->close();
       
        return $response;
    } 


 
 	public function storeMessage($gid, $message, $type) {
        $response = array();
        $gcmtoken = self::isUserExists($gid);

        if($gcmtoken){
            //Insert only if user exists
            //TODO : Check if the user is authenticated by the google.
            
              $stmt = $this->conn->prepare("INSERT INTO messages (g_id, type, message) values(?, ?, ?)");
              $stmt->bind_param("sis", $gid, $type, $message);

              $result = $stmt->execute();

              if($result){

                $response['error'] = false;
                $response['message'] = 'Message inserted in DB';
                $response['gcmtoken'] = $gcmtoken;  
            }else{
                $response['error'] = true;
                $response['message'] = 'Message not inserting in DB';
              }

        }else{

             $response['error'] = true;
             $response['message'] = 'User does not exist';
        }
        return $response;
    }
    



    public function isUserExists($gid) {
        $stmt = $this->conn->prepare("SELECT gcm_token from users WHERE g_id = ?");
        $stmt->bind_param("s", $gid);
        $stmt->execute();
        $stmt->bind_result($gcm);
        $stmt->fetch();
        $stmt->close();
        //echo $gcm;
        return $gcm;
    }

     public function getUserByEmail($email) {
        //echo $email;
        $stmt = $this->conn->prepare("SELECT gcm_registration_id from users WHERE email = ?");
        $stmt->bind_param("s", $email);

        if ($stmt->execute()) {
            // $user = $stmt->get_result()->fetch_assoc();
            $stmt->bind_result($gcm);
            $stmt->fetch();
            $user = array();
            $user["gcm"] = $gcm;
            $stmt->close();
            return $user;
        } else {
            return NULL;
        }
    }


}



?>