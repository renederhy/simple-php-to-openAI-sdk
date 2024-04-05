<?php

function chat( $model='mistral-tiny', $prompt='who are you?', $vendor= "mistral") {

define('MISTRAL', "YOUR_MISTRAL_KEY_HERE" ) ;
define('OPENAI' , 'YOUR_OPEN_AU_KEY_HERE' ) ; 

$mistralurl = "https://api.mistral.ai/v1/chat/completions"; 
$openaiurl  = "https://api.openai.com/v1/chat/completions"; 
	switch($vendor){
		case "openai":
			$API_KEY = OPENAI ; 
			$url = $openaiurl ; 
			$model = "gpt-4-turbo-preview" ; 
			break; 
		case "mistral":
		default :
			$API_KEY = MISTRAL ; 
			$url = $mistralurl ; 
	}

	$postfields = array( "model" => $model, "messages"=>array( ["role"=>"user", "content"=>$prompt]), "temperature"=>0.7) ; 
	$postfields = json_encode($postfields, JSON_UNESCAPED_SLASHES ) ; 
	$params = array(
			CURLOPT_URL => $url,  
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => $postfields,  
			CURLOPT_HTTPHEADER => [
			    "Content-Type: application/json",
			    "Accept: application/json",
			    "Authorization: Bearer $API_KEY",
			],) ; 
	$curl = curl_init();
	curl_setopt_array($curl,$params); // appel  ! 
	$response = curl_exec($curl);
	$err = curl_error($curl);
	curl_close($curl);
	if ($err) {
		return "cURL Error #:" . $err;
	} 
	$data = json_decode($response, true);
	$message = $data['choices'][0]['message']['content'];
	return $message."\n".$vendor;
}
