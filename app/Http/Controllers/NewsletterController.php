<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Newsletter;

class NewsletterController extends Controller {
    public function apikey(){
        return $GLOBALS["app"]["config"]["newsletter"]["apiKey"];
    }
    public function listid(){
        return $GLOBALS["app"]["config"]["newsletter"]["lists"]["subscribers"]["id"];
    }

    public function issubscribed(Request $request){
        $email = strtolower($_POST["email"]);
        $data = ["Status" => true, "Reason" => Newsletter::hasMember($email)];
        return json_encode($data);
    }

    public function subscribe(Request $request){
        $email = strtolower($_POST["email"]);
        $status = $_POST["status"] == "true";
        if($status) {
            $firstname = $_POST["name"];
            $lastname = "";
            $names = explode(" ", $firstname);
            if(isset($names[0])) {$firstname = $names[0];}
            if(count($names) > 1){$lastname = $names[count($names) - 1];}
            Newsletter::subscribeOrUpdate($email, ["FNAME" => $firstname, "LNAME" => $lastname, "PHONE" => $_POST["phone"]]);
        } else {
            Newsletter::delete($email);
        }
        $data = ["Status" => true, "Reason" => "Subscription status changed to " . $status];
        return json_encode($data);
    }
}
