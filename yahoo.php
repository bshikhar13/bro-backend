<?php

	function processOutput($message){

		$arr = explode(' ',trim($message));
		
		if($arr[0] == 'wiki' || $arr[0] == 'Wiki'){
			//Wiki Search the content
			$api = "https://en.wikipedia.org/w/api.php?action=query&titles=".$arr[1]."&prop=revisions&rvprop=content&format=json";
			return httpGet($api);
			
		}else{
			return "Fuck Shanky";
		}
	}


function httpGet($url){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $content = curl_exec($ch);
    return $content;
}

//echo processOutput("wiki beacon");

?>