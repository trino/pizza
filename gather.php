<?php
	define("base_path", base_path());

	function base_path(){
		return __DIR__;
	}

	function public_path(){
		return base_path;
	}

	function env($key, $value){
		return $value;
	}

	function database_path($value){
		return "";
	}

	$path = base_path() . "/resources/views/api.php";
	//error_reporting(E_ALL);ini_set('display_errors', 1);
	include $path;
	$message = "Thank you";
	$say = '<Say voice="woman" language="en">';

	$digit = "";
	if(isset($_GET["Digits"])){
		$digit = $_GET["Digits"];
	}

	if(isset($_GET["gather"]) && $digit == "1"){
		$query = "UPDATE orders SET status = 1 WHERE restaurant_id = " . $_GET["gather"];
		query($query);
		//$message = "Updated orders for store " . $_GET["gather"];
	} else if ($digit == "9"){
		$message = $_GET["message"];
		$url = 'http://hamiltonpizza.ca/gather.php?message=' . urlencode($message);
		if(isset($_GET["gather"])){$url .= "&amp;gather=" . $_GET["gather"];}
		$message = '<Gather numDigits="1" action="' . $url . '" method="GET" timeout="10">
                        ' . $say . $message . '. Press 9 to repeat this message</Say>
                   </Gather>
                   ' . $say . 'We did not receive any input. Goodbye!';
	}
	if(strpos($message, $say) === false){$message = $say . $message;}
	echo '<?xml version="1.0" encoding="UTF-8"?><Response>' . $message . '</Say></Response>';
?>
