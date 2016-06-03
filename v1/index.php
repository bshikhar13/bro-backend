<?php
 
error_reporting();
ini_set('display_errors', 'On');
 
require_once '../include/db_handler.php';

require '.././libs/Slim/Slim.php';



\Slim\Slim::registerAutoloader();
 
$app = new \Slim\Slim();
 
 //TODO: Need to check if the user is the same who is changing. Need to implement security here
/* * *
 * Updating user
 *  we use this url to update user's gcm registration id
 */

$app->post('/login', function(){
    global $app;
    $db = new DbHandler();
    
    $gid = $app->request->post('gid');
    $name =  $app->request->post('name');
    $email = $app->request->post('email');
    $serverauthcode = $app->request->post('serverauthcode');
    $instanceid = $app->request->post('idtoken');
    $gcmtoken = $app->request->post('gcmtoken');
    
    $userExists = $db->isUserExists($gid);
    $response = array();

     if($userExists!=null){
        $tempresponse = $db->updateUserInfo($gid,$name,$email,$serverauthcode,$instanceid, $gcmtoken);
        if($tempresponse['error']==false){
            $response['error'] = false;
            $response['message'] = "Welcome back " + $tempresponse['data']['name'] +" !" ;        
            $response['data'] = $tempresponse['data'];
        }else{
            $response['error'] = true;
            $response['message'] = "Databases Error Encountered";        
        }
    }else{
        $tempresponse = $db->insertUserInfo($gid,$name,$email,$serverauthcode,$instanceid, $gcmtoken);
       if($tempresponse['error']==false){
            $response['error'] = false;
            $response['message'] = "Welcome " + $tempresponse['data']['name'] +" !" ;        
            $response['data'] = $tempresponse['data'];
        }else{
            $response['error'] = true;
            $response['message'] = "Databasesss Error Encountered";
        }
    }

   
    
    echoRespnse(200,$response);
});



$app->post('/sendmessage', function(){
     global $app;
    $db = new DbHandler();
    $response = array();
    $gid = $app->request->post('gid');
    $message = $app->request->post('message');
    $type = 0;
    
    $messageStoreResponse = $db->storeMessage($gid, $message, $type);
    $resp = array();

    if($messageStoreResponse['error']==false){
        require_once __DIR__. '/../processing/process.php';
        //TODO : Compute the Return message Right now just echoing it back. NLP Here !!
        $returnMessage = processOutput($message);
        
      
        require_once __DIR__ . '/../libs/gcm/gcm.php';
        require_once __DIR__ . '/../libs/gcm/push.php';
        $gcm = new GCM();
        $push = new Push();

        $gcmtoken = $messageStoreResponse['gcmtoken'];

        $gcm->sendMessage($gcmtoken, $returnMessage);
        $type = 1;
        $db->storeMessage($gid, $returnMessage, $type);
        
        
        $response['error']=false;
        $response['message'] = "done";
        
    }else{
        $response['error']=true;
        $response['message'] = "Not done";
    }

    echoRespnse(200, $response);
});




function echoRespnse($status_code, $response) {
    $app = \Slim\Slim::getInstance();
    // Http response code
    $app->status($status_code);
 
    // setting response content type to json
    $app->contentType('application/json');
 
    echo json_encode($response);
}
 
$app->run();



?>