<?php

//chatImage("https://shop.derhy.com/shopping/Ibig/A410004-7-175.jpg", "anthropic") ;
$ref = $_GET['ref'];
$photo = $_GET['photo'];
$theme = $_GET['theme'];
$designation = $_GET['designation'];
$url = "https://shop.derhy.com/shopping/Ibig/".$photo ;
//$url = "https://www.renederhy.com/1200/".$photo ;
$prompt = "Peux-tu me décrire le vêtement sur cette photo? Il s'agit de $designation, pour le site web." ; 
$system = "Respond only in French, Please describe the clothing in the image for a web site, like an SEO pro, please, insist on the facts"
                        ." and avoid subjective opinions. The goal is to describe the clothing in the image in a way that is useful for a web site."
                        ." Avoid introduction and conclusion, just describe the clothing in the image, like a pro SEO."
                        ." Can you also genereate a title for the product, and a short description for the product?" ;

chatImage($url, "anthropic", "claude-3-sonnet-20240229", $designation, $prompt, $system) ;

function chatImage( $url, $vendor= "anthropic", $model="claude-3-haiku-20240307", $designation="", $prompt="", $system="") {
    define('CLAUDE' , 'YOUR_API_KEY') ; 
    $claudeurl  = "https://api.anthropic.com/v1/messages"; 
    $temperature =  0.7 ; 
    $API_KEY = CLAUDE ;
    $version = "anthropic-version: 2023-06-01"  ; 
    //$model   = "claude-3-sonnet-20240229"; //claude-3-sonnet-20240229
    //$model ="claude-3-opus-20240229";
    $model =  "claude-3-haiku-20240307"; 
    $image_bytes = file_get_contents($url);
    $base64 = base64_encode($image_bytes);

    $messages = array(
        array( "role" => "user"
            ,"content" => array(
                array(
                    "type" => "image",
                    "source" => array(
                        "type" => "base64",
                        "media_type" => "image/jpeg",
                        "data" => $base64
                    )
                ),
                array(
                    "type" => "text",
                    "text" => $prompt
                )
            )
        )
    );

    $postfields = array(
        "model" => $model,
        "max_tokens" => 1024,
        "system"  => $system,
        "messages" => $messages
    );

    $postfields = json_encode($postfields , JSON_UNESCAPED_SLASHES);
    $curl = curl_init();
	curl_setopt_array($curl, [
	  CURLOPT_URL => $claudeurl , 
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 30,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => "POST",
	  CURLOPT_POSTFIELDS => $postfields ,
	  CURLOPT_HTTPHEADER => [
	    "Content-Type: application/json",
	    "anthropic-version: 2023-06-01",
	    "x-api-key: ".$API_KEY
	  ],
	]);

	$response = curl_exec($curl);
	$err = curl_error($curl);
	curl_close($curl);

	if ($err) {
	  echo "cURL Error #:" . $err;
	} else {
        echo "\n--------\n"; 
        $data = json_decode($response, true);
        $message = $data['content'][0]['text'];
        echo $message ; 
        return $message."\n".$vendor.", ".$model ;
	}
}
