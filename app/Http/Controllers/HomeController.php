<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Task;
use App\Repositories\TaskRepository;

class HomeController extends Controller {
    public function index(Request $request){
        return view("index")->render();
    }

    public function debug(Request $request){
        $CRLF = "\r\n";
        if(count($_POST) == 0){$_POST = $_GET;}
        $text = "Twilio error:";
        foreach($_POST as $key => $index){
            $text .= $CRLF . $key . "=" . $index;
        }
        debugprint($text);
        return $text;
    }

    public function help(Request $request){
        return view("home_help")->render();
    }

    public function search(Request $request){
        return view("home_search")->render();
    }

    public function map(Request $request){
        return view("home_map")->render();
    }

    public function newrest(Request $request){
        return view("home_newstore")->render();
    }

    public function hours(Request $request){
        return view("home_hours")->render();
    }

    public function cron(Request $request){
        return view("cron")->render();
    }

    public function tablelist($table){
        if (isset($_POST["action"])) {
            $ret = true;
            switch ($_POST["action"]) {
                case "testemail":
                    echo $this->sendEMail("email_test", array(
                        'mail_subject' => "test",
                        "email" => read("email"),
                        "body" => "This is a test email sent from the debug page"
                    ));
                    break;
                //sendSMS($Phone, $Message, $Call = false, $force = false, $gather = false)
                case "testSMS": echo $this->sendSMS(read("phone"), "This is a test SMS", false, true); break;
                case "testSMSADMINS": echo $this->sendSMS("admin", "This is test SMS [index] for all admins", false, true); break;
                case "testCALL": echo $this->sendSMS(read("phone"), "This is a test CALL", true, true); break;
                case "testCALLADMINS": echo $this->sendSMS("admin", "This is test CALL [index] for all admins", true, true); break;
                case "testGATHER": echo $this->sendSMS(read("phone"), "This is a test GATHER CALL", true, true, 1); break;
                case "testSMSTWICE":
                    echo $this->sendSMS(read("phone"), "This is test SMS 1", false, true) . "|";
                    echo $this->sendSMS(read("phone"), "This is test SMS 2", false, true);
                    break;
                case "testCALLTWICE":
                    echo $this->sendSMS(read("phone"), "This is test CALL 1", true, true) . "|";
                    echo $this->sendSMS(read("phone"), "This is test CALL 2", true, true);
                    break;
                default: $ret = false;
            }
            if($ret){return;}
        }
        return view("home_list", array("table" => $table))->render();
    }

    public function edituser($user_id = false){
        if (!$user_id) {
            $user_id = read("id");
        }
        return view("home_edituser", array("user_id" => $user_id))->render();
    }

    public function robocall(Request $request){
        if(!isset($_GET["message"]) && isset($_POST["message"])){
            $message = $_POST["message"];
        } else {
            $message = $_GET["message"];
        }

        $say = '<Say voice="woman" language="en">';
        $url = serverurl;
        if(defined("callurl")){$url = callurl;}//localhost must use a non-local URL
        if(isset($_GET["gather"])){// https://www.twilio.com/docs/voice/twiml/gather
            $message = '<Gather numDigits="1" action="http://' . $url . '/gather.php?gather=' . $_GET["gather"] . "&amp;message=" . urlencode($message) . '" method="GET" timeout="10">
                        ' . $say . $message . '</Say>
                   </Gather>
                   ' . $say . 'We did not receive any input. Goodbye!</Say>';
        } else {
            $message = '<Gather numDigits="1" action="http://' . $url . '/gather.php?message=' . urlencode($message) . '" method="GET" timeout="10">
                        ' . $say . $message . '</Say>
                   </Gather>';
        }
        die('<?xml version="1.0" encoding="UTF-8"?><Response>' . $message . '</Response>');
        /*die('<?xml version="1.0" encoding="UTF-8"?><Response><Say voice="woman" language="en">' . $_GET["message"] . '</Say></Response>');*/
    }

    public function edit(Request $request){
        return view("home_editor")->render();
    }

    public function edittable(Request $request){
        return view("home_edittable")->render();
    }

    public function editmenu(Request $request){
        return view("home_editmenu")->render();
    }

    public function termsofservice(Request $request){
        return view("home_tos")->render();
    }

    public function ourstory(Request $request){
        return view("home_ourstory")->render();
    }

    public function privacy(Request $request){
        return view("home_privacy")->render();
    }

    public function contact(Request $request){
        if(isset($_POST["contact_text"])){
            $message = "Message:<P>" . $_POST["contact_text"] . '<P>IP Address: ' . str_replace("::1", "localhost", $_SERVER['REMOTE_ADDR']) . '<P>User: ';
            if(read("id")){
                $message .= read("id") . " - " . read("email") . " - " . read("name");
            } else if(isset($_POST["contact_email"])) {
                $message .= "Anonymous - " . $_POST["contact_email"];
            }
            $this->sendEMail("email_test", [
                "email" => "admin",
                "mail_subject" => "Contact Us form used",
                "body" => $message
            ]);
        }
        return view("home_contact")->render();
    }

    public function saveorder($info, $order){
        $orderid = insertdb("orders", $info);
        $dir = public_path("orders");
        if (!is_dir($dir)) {mkdir($dir, 0777);}
        $dir = $dir . "/user" . $info["user_id"];//no / at the end (was in "user" . $info["user_id"])
        if (!is_dir($dir)) {mkdir($dir, 0777);}
        $filename = $dir . "/" . $orderid . ".json";
        file_put_contents($filename, json_encode($order, JSON_PRETTY_PRINT));
        if(defined("username") && posix_geteuid() != fileowner($filename)){chown($filename, username);}
        chmod($filename, 0755);
        return $orderid;
    }

    public function loaduser($userid = false, $newdata = false){
        //duplicate code from placeorder since it can't be used without an order id
        if(!$userid){$userid = read("id");}//$info["user_id"]
        $user = first("SELECT * FROM users WHERE id = " . $userid, true, "HomeController.loaduser");
        if($newdata) {//attempt to update user profile
            if (!isset($newdata["phone"])) {$newdata["phone"] = $user["phone"];}
            if ($user["name"] != $newdata["name"] || $user["phone"] != $newdata["phone"]) {
                $user["name"] = $newdata["name"];
                $user["phone"] = $newdata["phone"];
                insertdb("users", array("id" => $userid, "name" => $newdata["name"], "phone" => $newdata["phone"]));
            }
        }
        return $user;
    }

    public function placeorder($POST = ""){
        if (!read("id")) {
            return array("Status" => false, "Reason" => "You are not logged in");
        }
        date_default_timezone_set("America/Toronto");
        $info = "";
        if (isset($_POST["info"])) {
            $info = $_POST["info"];
            unset($info["formatted_address"]);
        }
        if (is_array($POST)) {$_POST = $POST;}
        if (isset($_POST["action"])) {
            $ret = array("Status" => true, "Reason" => "", "Type" => "System");
            switch ($_POST["action"]) {
                case "deleteaddress":
                    $user = first("SELECT user_id FROM useraddresses WHERE id = " . $_POST["id"])["user_id"];
                    if($user == read("id")){
                        deleterow("useraddresses", "id = " . $_POST["id"]);
                    } else {
                        $ret = array("Status" => false, "Reason" => "ACCESS DENIED", "Type" => "System");
                    }
                    break;
                case "deletecard":
                    initStripe();
                    $user = $this->loaduser();
                    try {
                        $ret["Status"] = false;
                        $cu = \Stripe\Customer::retrieve($user["stripecustid"]);
                        $cu->sources->retrieve($_POST["cardid"])->delete();
                        $ret["Reason"] = "'" . $_POST["cardid"] . "' was deleted";
                        $ret["Status"] = true;
                    } catch (\Stripe\Error\InvalidRequest $e) {
                        $ret["Reason"] = $e->getMessage();
                        $ret["Type"] = "stripe";
                        $ret["Status"] = false;
                    }
                    break;
                case "closestrestaurant":
                    $info["radius"] = "max_distance";
                    $ret["closest"] = $this->closestrestaurant($info, true);
                    if(!islive()) {
                        $ret["sql"] = $ret["closest"]["SQL"];
                        unset($ret["closest"]["SQL"]);
                        if (!$ret["closest"]) {
                            unset($info['radius']);// = 99999999;
                            $info["limit"] = 1;
                            $ret["closest"] = $this->closestrestaurant($info, true, false);
                            $ret["Reason"] = "Forcing a restaurant for testing purposes.";
                        }
                    }
                    break;
                case "changestatus":
                    if ($_POST["status"] == -1) {//email out
                        $info = false;
                        $user = $this->order_placed($_POST["orderid"], $info, 0);
                        $ret["Reason"] = "Receipt for Order ID " . $user["orderid"] . " sent to '" . $user["email"] . "'";
                    } else {
                        $Status = array("Pending", "Confirmed", "Declined", "Delivered", "Canceled");
                        insertdb("orders", array("id" => $_POST["orderid"], "status" => $_POST["status"]));
                        $Status = $Status[$_POST["status"]];
                        $ret["Reason"] = "Order #" . $_POST["orderid"] . ": " . $Status;
                        if (debugmode) {
                            $ret["Reason"] .= " - Action: order_" . strtolower($Status);
                        }
                        $info = false;
                        $this->order_placed($_POST["orderid"], $info, -1, "order_" . strtolower($Status), $_POST["reason"]);
                        if($_POST["delete"]){
                            deletefile(orderpath($_POST["orderid"]));
                        }
                    }
                    break;
                default:
                    $ret["Status"] = false;
                    $ret["Reason"] = "'" . $info["action"] . "' is unhandled";
            }
            return json_encode($ret);
        }
        if (!isset($info["user_id"]) || !$info["user_id"]) {
            $info["user_id"] = read("id");
        }
        $addressID = $this->processaddress($info);
        if (isset($_POST["order"])) {
            $info["placed_at"] = my_now();
            $info["last4"] = $_POST["last4"];
            unset($info["name"]);
            if(isset($info["creditcard"])) {
                $_POST["creditcard"] = $info["creditcard"];
                unset($info["creditcard"]);
            }
            unset($info["restaurant"]);
            if (isset($_POST["stripe"])) {
                $info["stripeToken"] = $_POST["stripe"];
            }

            $order = $_POST["order"];
            $info["deliver_at"] = delivery_at($info["placed_at"], $info["deliverytime"]);
            unset($info["istest"]);
            if(!isset($info["phone"]) || !trim($info["phone"])){
                $info["phone"] = read("phone");
            }
            $tip = 0.00;
            if(isset($_POST["tip"]) && is_numeric($_POST["tip"])){$tip = $_POST["tip"];}
            $info["tip"] = $tip;
            $amount = false;
            $savebefore = false;//false is not recommended
            $chargeinfo = [];
            if(!isset($_POST["last4"])){$_POST["last4"] = "";}
            $user = $this->loaduser($info["user_id"], $_POST);

            if($savebefore){
                $orderid = $this->saveorder($info, $order);
                $HTML = view("popups_receipt", array("orderid" => $orderid, "timer" => true, "place" => "placeorder", "style" => 2, "includeextradata" => true, "party" => "user", "last4" => $_POST["last4"]))->render();
                $description = "Order ID: " . $orderid;
            } else {
                $HTML = view("popups_receipt", array("Order" => array_merge($info, $user), "data" => $order, "timer" => true, "place" => "placeorder", "style" => 2, "includeextradata" => true, "party" => "user", "last4" => $_POST["last4"]))->render();
                $amount = getbetween($HTML, '<SPAN ID="total">', '</SPAN>');
                $description = "Customer ID: " . $user["id"] . " at: " . my_now();
            }

            //if ($text) {return $text;} //shows email errors. Uncomment when email works
            if (isset($info["stripeToken"]) || $user["stripecustid"]) {//process stripe payment here
                if(!$amount) {$amount = select_field_where("orders", "id=" . $orderid, "price");}
                if($GLOBALS["settings"]["onlyfiftycents"]) {
                    $amount = 50;//dont remove this
                } else if (strpos($amount, ".")) {
                    $amount = ($amount+$tip) * 100;//remove the period, make it in cents
                }
                $error = false;
                if ($amount > 0) {
                    initStripe();
                    $failedat = "";
                    try {
                        if ($user["stripecustid"]) {
                            $customer_id = $user["stripecustid"];//load customer ID from user profile
                            $cu = \Stripe\Customer::retrieve($customer_id);
                            //$_POST["creditcard"] will have a value only if the customer selected a saved card
                            //$info["stripeToken"] will have a value only if the customer made a new card
                            if (isset($info["stripeToken"]) && $info["stripeToken"]) {//update credit card info
                                $failedat = "Update card info";
                                $data = $cu->sources->create(array("source" => $info['stripeToken']));
                                $_POST["creditcard"] = $data["id"];//save it to $_POST["creditcard"] since it now exists
                                $cu->save();
                            }
                        } else {
                            $failedat = "Create customer";
                            $customer = \Stripe\Customer::create(array(
                                "source" => $info["stripeToken"],
                                "description" => $user["name"] . iif(debugmode, ' (ID:' . $user["id"] . ')')
                            ));
                            $customer_id = $customer["id"];
                            insertdb("users", array("id" => $user["id"], "stripecustid" => $customer_id));//attempt to update user profile
                        }

                        $charge = array(
                            "amount" => $amount,
                            "currency" => "cad",
                            //"source" => $info["stripeToken"],//charge card directly
                            "customer" => $customer_id,//charge customer ID
                            "description" => $description
                        );
                        if (isset($_POST["creditcard"]) && $_POST["creditcard"]) {
                            $charge["source"] = $_POST["creditcard"];//charge a specific credit card
                        }

                        // https://stripe.com/docs/charges https://stripe.com/docs/api
                        $failedat = "Charge the card";
                        $charge = \Stripe\Charge::create($charge);// Create the charge on Stripe's servers - this will charge the user's card
                        if($charge["outcome"]["type"] == "authorized") {
                            if(!$savebefore) {
                                $orderid = $this->saveorder($info, $order);
                                $HTML = str_replace('<span ID="receipt_id"></span>', '<span ID="receipt_id">' . $orderid . '</span>', $HTML);
                            }
                            insertdb("orders", array("id" => $orderid, "paid" => 1, "stripeToken" => $charge["id"], "last4" => $_POST["last4"]));//will only happen if the $charge succeeds
                            $this->order_placed($orderid, $info);
                            $chargeinfo = $charge["source"];
                            $chargeinfo['customer'] = $customer_id;
                            //die("Charged: " . $charge["source"]["id"]);
                        } else {
                            die($charge["outcome"]["type"]);
                        }
                    } catch (Stripe_CardError $e) {
                        $error = $e->getMessage();
                    } catch (Stripe_InvalidRequestError $e) {
                        $error = $e->getMessage();//Invalid parameters were supplied to Stripe's API
                    } catch (Stripe_AuthenticationError $e) {
                        $error = $e->getMessage();//Authentication with Stripe's API failed
                    } catch (Stripe_ApiConnectionError $e) {
                        $error = $e->getMessage();//Network communication with Stripe failed
                    } catch (Stripe_Error $e) {
                        $error = $e->getMessage();//Display a very generic error to the user
                    } catch (Exception $e) {
                        $error = $e->getMessage();//Something else happened, completely unrelated to Stripe
                    } catch (\Stripe\Error\Card $e) {
                        $error = $e->getMessage();
                    }
                } else {
                    $error = " Order ID " . $orderid . " total was $0.00";
                }
                if ($error) {
                    debugprint("Stripe error: " . $error);
                    return "[STRIPE]" . $error;// The card has been declined
                }
            }
            return '<div CLASS="ordersuccess" orderid="' . $orderid . '" addressid="' . $addressID . '" STYLE="display:none;">' . json_encode($chargeinfo) . '</div>' . $HTML;
        } else {
            return $addressID;
        }
    }

    function order_placed($orderid, $info = false, $party = -1, $event = "order_placed", $Reason = "", $RetActions = false){
        if (!$info) {$info = first("SELECT * FROM orders WHERE id = " . $orderid);}
        if (!$info) {return false;}
        $user = first("SELECT * FROM users WHERE id = " . $info["user_id"], true, "HomeController.order_placed1");
        $admin = first("SELECT * FROM users WHERE profiletype = 1", true, "HomeController.order_placed2");
        $restaurant = false;
        if ($party > -2) {
            $actions = actions($event, $party);
            if ($party > -1) {$actions = array($actions);}
            foreach ($actions as $index => $action) {
                $phone_restro = false;
                switch ($action["party"]) {
                    case 0://customer
                        $party = "customer";
                        $email = $user["email"];
                        $phone = $user["phone"];
                        $name = $user["name"];
                        break;
                    case 1://admin
                        $party = "admin";
                        $email = $admin["email"];
                        $phone = $admin["phone"];
                        $name = $admin["name"];
                        break;
                    case 2://restaurant
                        $restaurant = $this->processrestaurant($info["restaurant_id"]);
                        $party = "restaurant";
                        $email = $restaurant["user"]["email"];
                        $name = $restaurant["restaurant"]["name"];
                        $phone_restro = filternonnumeric($restaurant["restaurant"]["phone"]);
                        $phone = filternonnumeric($restaurant["user"]["phone"]);
                        break;
                }

                if ($Reason) {
                    $action["message"] = str_replace("[reason]", $Reason, $action["message"]);
                }

                $gather = false;
                switch($event){
                    case "cron_job": case "cron_job_final": $gather = $info["restaurant_id"]; break;
                    case "order_placed": if($party !== 0){$gather = $info["restaurant_id"];} break;
                }
                if($gather){
                    //$orders = first("SELECT count(*) as count FROM orders WHERE stripeToken <> '' AND status = 0 AND restaurant_id = " . $info["restaurant_id"], true, "HomeController.order_placed3")["count"];
                    $orders = collapsearray(query("SELECT users.name FROM orders RIGHT JOIN users ON users.id = orders.user_id WHERE stripeToken <> '' AND paid = 1 AND status = 0 AND restaurant_id = " . $info["restaurant_id"], true, "HomeController.order_placed3"), "name");
                    if(!$restaurant){$restaurant = $this->processrestaurant($info["restaurant_id"]);}
                    $action["message"] = str_replace("[#]", count($orders), $action["message"]);
                    $action["message"] = str_replace("[from]", implode2(", ", " and ", $orders), $action["message"]);
                    $action["message"] = str_replace("[restaurant]", $restaurant["restaurant"]["name"], $action["message"]);
                    $action["message"] = str_replace("[s]", iif(count($orders) == 1, "", "s"), $action["message"]);
                    if(!isset($info["attempts"])){$info["attempts"] = 0;}
                    $attempt = $info["attempts"] + 1;
                    if ($attempt == getsetting("max_attempts", 3)){
                        $attempt = "final";
                    } else if(is_numeric($attempt)){
                        $attempt = getordinal($attempt);
                    }
                    $action["message"] = str_replace("[attempt]", $attempt, $action["message"]);
                    if($action["phone"]){$this->recordattempt($info["restaurant_id"], "restaurant_id");}
                }

                foreach ($info as $key => $value) {
                    switch($key){
                        case "deliverytime":
                            if($value == "Deliver Now"){
                                $value = "ASAP";
                            } else {
                                $date = trim(right($value, 4));
                                $value = str_replace($date, GenerateTime($date), $value);
                                $value = str_replace(":", ", ", GenerateDate($value));
                            }
                            break;
                    }
                    $action["message"] = str_replace("[" . $key . "]", $value, $action["message"]);
                }

                //sendSMS($Phone, $Message, $Call = false, $force = false, $gather = false)
                $action["message"] = str_replace("[orderid]", $orderid, $action["message"]);
                $action["message"] = str_replace("[sitename]", sitename, $action["message"]);
                $action["message"] = str_replace("[name]", $name, $action["message"]);
                //if(debugmode){ }
                if ($action["email"]) {
                    $action["message"] = str_replace("[url]", "", $action["message"]);
                    debugprint("Sending email to " . $party . ": " . $email);//send emails to customer also generates the cost
                    $data = ["orderid" => $orderid, "email" => $email, "party" => $party, "mail_subject" => $action["message"], "last4" => $info["last4"]];
                    $this->sendEMail("email_receipt", $data);
                }
                if ($action["sms"]) {
                    $action["message"] = str_replace("[url]", webroot("list/orders?action=getreceipt&orderid=") . $orderid, $action["message"]);
                    debugprint("Sending SMS to " . $party . ": " . $phone);
                    $SMSdata = $this->sendSMS($phone, $action["message"]);
                    debugprint("SMS data: " . $SMSdata);
                }
                if ($action["phone"]) {
                    //if SMS restaurant then SMS user of the restaurant instead since all of the restaurant phones are land lines
                    $action["message"] = str_replace("[url]", "", $action["message"]);
                    //$action["message"] = $this->replacetag($action["message"], "press9torepeat");
                    if ($phone_restro) {$phone = $phone_restro;}
                    debugprint("Calling " . $party . ": " . $phone);
                    $SMSdata = $this->sendSMS($phone, $action["message"], true, false, $gather);
                    debugprint("CALL data: " . $SMSdata);
                }
                $actions[$index]["action"] = $action;
            }
        }
        $user["orderid"] = $orderid;
        $resttext = "";
        if(isset($restaurant)){
            $resttext = "for: " . $restaurant["restaurant"]["name"] . " (" . $info["restaurant_id"] . ")";
        }
        debugprint($event . ": " . $orderid . " by: " . $user["name"] . " (" . $info["user_id"] . ")" . $resttext);
        if($RetActions){return $actions;}
        return $user;
    }

    function replacetag($string, $tag){
        if(strpos($string, "[" . $tag . "]") !== false){
            $data = first("SELECT message FROM actions WHERE eventname = '" . $tag . "'");
            $string = str_replace("[" . $tag . "]", $data["message"], $string);
        }
        return $string;
    }

    function recordattempt($orderID, $field = "id"){
        //field can also be "restaurant_id"
        query("UPDATE orders SET attempts = attempts + 1 WHERE status = 0 AND " . $field . " = " . $orderID);
    }

    function closestrestaurant($data, $gethours = false, $includesql = true){
        if(!is_numeric($data['longitude']) || !is_numeric($data['latitude'])){return false;}
        $SQL = "SELECT restaurants.id, name, email, phone, is_delivery, address_id, max_distance, number, unit, buzzcode, street, postalcode, city, province, latitude, longitude, ( 6371 * acos( cos( radians('" . $data['latitude'] . "') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('" . $data['longitude'] . "') ) + sin( radians('" . $data['latitude'] . "') ) * sin( radians( latitude ) ) ) ) AS distance FROM restaurants LEFT JOIN useraddresses ON restaurants.address_id = useraddresses.id WHERE address_id > 0 AND is_delivery = 1";
        if (isset($data["restaurant_id"])) {
            if(!is_numeric($data["restaurant_id"])){return false;}
            $SQL .= " AND id = " . $data["restaurant_id"];
        }
        $limit = "";
        if (isset($data["limit"]) && is_numeric($data["limit"])) {
            $limit = " LIMIT " . $data["limit"];
        } else {
            $data["limit"] = 1;
        }
        if (isset($data['radius'])) {
            $SQL .= " HAVING distance <= " . $data['radius'];
        }
        $SQL .= " ORDER BY distance ASC" . $limit;
        $Restaurants = Query($SQL, true, "HomeController.closestrestaurant");
        if ($Restaurants) {
            if ($data["limit"] == 1) {
                if ($gethours) {$Restaurants = $this->processrestaurant($Restaurants[0]);}
                $Restaurants["SQL"] = $SQL;
            } else {
                foreach ($Restaurants as $Index => $Restaurant) {
                    if ($gethours) {$Restaurants[$Index] = $this->processrestaurant($Restaurant);}
                }
            }
        }
        if(!islive() && $includesql){$Restaurants["SQL"] = $SQL;}
        return $Restaurants;
    }

    function mergerestaurant($Restaurant, $restaurantdata){
        foreach($restaurantdata as $data){
            if($data["address_id"] == $Restaurant["id"]){
                foreach($data as $key => $value){
                    //if($key == "id"){$key = "restaurant_id";}
                    $Restaurant[$key] = $value;
                }
            }
        }
        return $Restaurant;
    }

    function processrestaurant($Restaurant){
        if (!is_array($Restaurant)) {$Restaurant = array("id" => $Restaurant);}
        $Restaurant["sql"][] = "SELECT * FROM restaurants WHERE id = " . $Restaurant["id"];
        $Restaurant["restaurant"] = first($Restaurant["sql"][0]);
        $Restaurant["sql"][] = "SELECT user_id FROM useraddresses WHERE id = " . $Restaurant["restaurant"]["address_id"];
        $UserID = first($Restaurant["sql"][1]);
        $Restaurant["user_id"] = $UserID["user_id"];
        $Restaurant["hours"] = gethours($Restaurant["id"]);
        $Restaurant["sql"][] = "SELECT id, name, phone, email FROM users WHERE id = " . $Restaurant["user_id"];
        $Restaurant["user"] = first($Restaurant["sql"][2]);//do not send password
        $Restaurant["sql"][] = "SELECT item_id, tablename FROM shortage WHERE restaurant_id = " . $Restaurant["id"];
        $Restaurant["shortage"] = first($Restaurant["sql"][3], false);
        if(islive()){unset($Restaurant["sql"]);}
        return $Restaurant;
    }

    function processaddress($info){
        $address = first("SELECT * FROM useraddresses WHERE user_id = " . $info["user_id"] . " AND number = '" . $info["number"] . "' AND street = '" . $info["street"] . "' AND city = '" . $info["city"] . "'");
        if (!$address) {
            $address = array(
                "user_id" => $info["user_id"],
                "number" => $info["number"],
                "city" => $info["city"],
                "unit" => $info["unit"],
                //"buzzcode" => $info["buzzcode"],
                "street" => $info["street"],
                "postalcode" => $info["postalcode"],
                "province" => $info["province"],
                "latitude" => $info["latitude"],
                "longitude" => $info["longitude"],
            );
            return insertdb("useraddresses", $address);
        } else if ($info["unit"] != $address["unit"]) {
            $address["unit"] = $info["unit"];
            return insertdb("useraddresses", $address);
        }
    }
}
