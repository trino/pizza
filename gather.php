<?php
	function base_path(){
        return __DIR__;
    }
	$path = base_path() . "/resources/views/api.php";
	//error_reporting(E_ALL);
	//ini_set('display_errors', 1);	
	include $path;

	if(isset($_GET["orderid"])){
		$query = "UPDATE orders SET status = 1 WHERE id = " . $_GET["orderid"];
		query($query);
	}
?>
<?xml version="1.0" encoding="UTF-8"?><Response><Say voice="woman" language="en">Thank you</Say></Response>
