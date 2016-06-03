<?php

class GCM{

	function __construct(){

	}


    public function sendMessage($gcm, $message){
        $data = array('message' => $message);
        $to = array($gcm);
        
        $fields = array(
            'registration_ids' => $to,
            'data' => $data
            );
        
        include_once __DIR__ . '/../../include/config.php';
 
        // Set POST variables
        $url = 'https://gcm-http.googleapis.com/gcm/send';
 
        $headers = array(
            'Authorization: key=' . GOOGLE_API_KEY,
            'Content-Type: application/json'
        );
        // Open connection
        $ch = curl_init();

         curl_setopt($ch, CURLOPT_URL, $url);
 
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
 
        // Close connection
        curl_close($ch);
 
        return $result;
       
    }
}



?>