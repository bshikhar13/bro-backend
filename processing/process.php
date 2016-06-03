<?php 


	function processOutput($message){
		
		$arr = explode(' ',trim($message));
		$command = $arr[0];
		$param = explode(" ", $message);
		//return $param;
		$str = "";
		//return count($param);

		for($i=1;$i<count($param);$i++){
			//return $param[i];
			$temp = ucwords(strtolower($param[$i]));
			$str = $str.$temp." ";
		}	

		$param = rawurlencode($str);
		//return $param;
		if($command == 'wiki' || $command == 'Wiki'){
			//Wiki Search the content

			return strip_tags(httpGet($param));
		}else{
			return "wiki likh bhosdike";
		}
	}


function httpGet($query){
   	$url = 'https://en.wikipedia.org/w/api.php?action=query&prop=extracts&formatversion=2&format=json&exintro=&redirects=1&titles=';
	$url = $url.$query;
	//return $url;
	$ch = curl_init($url);
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	
	$c = curl_exec($ch);
	
	$data = json_decode($c, true);
	

	$extract =$data['query']['pages'][0]['extract']; 
	return $extract;
}

?>