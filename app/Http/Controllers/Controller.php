<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController {
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function istesting(){
        if(defined("testing")){//config/database.php [constants][testing]
            return testing;
        }
        return false;
    }

    public function processadmin($data, $TrueForEmailFalseForPhoneNumber, $Where){
        if(is_string($data) && $data == "admin") {
            return enumadmins($TrueForEmailFalseForPhoneNumber, true, $Where);
        }
        return $data;
    }

    public function isadmin($emailORphone){
        $TrueForEmailFalseForPhoneNumber = !isvalidphone($emailORphone);
        $emailaddresses = enumadmins($TrueForEmailFalseForPhoneNumber);
        if(!$emailORphone){$emailORphone = filternonnumeric($emailORphone);}
        return in_array($emailORphone, $emailaddresses);
    }

    //sends an email using a template
    public function sendEMail($template_name = "", $array = array()){
        if($this->istesting()){$array['email'] = "admin";}
        if(!$template_name){die("template_name not set");}
        if (isset($array["message"])) {
            $array["body"] = $array["message"];
            unset($array["message"]);
        }
        $array['email'] = $this->processadmin($array['email'], true, "Controller.SendEmail");
        if (isset($array['email']) && is_array($array['email'])) {
            $emails = $array['email'];
            foreach ($emails as $email) {
                $array["email"] = $email;
                $this->sendEMail($template_name, $array);
            }
        } else if (isset($array['email']) && $array['email']) {
            if (!isset($array['mail_subject'])) {
                $array['mail_subject'] = "[NO mail_subject SET!]";
            }
            $array['mail_subject'] = striphtml($array['mail_subject']);
            if((!islive() || debugmode)){
                if(!$this->isadmin($array['email'])) {
                    $array['mail_subject'] .= " ([TEST] Email was: " . $array['email'] . ")";
                    $array['email'] = $this->processadmin("admin", true, "Controller.SendEmail");;
                    if(is_array($array['email'])){return $this->sendEMail($template_name, $array);}
                }
            }
            try {
                \Mail::send($template_name, $array, function ($messages) use ($array, $template_name) {
                    $messages->to($array['email'])->subject($array['mail_subject']);
                });
            } catch (\Swift_TransportException $e) {
                $text = $e->getMessage();
                $email = view($template_name, $array);
                debugprint($template_name . " EMAIL TO " . $array['email'] . " FAILED: " . $text . "<P>" . $email);
                return "Email error: " . $text;
            }
        }
    }

    //is data JSON-parseable?
    function isJson($string){
        if ($string && !is_array($string)) {
            json_decode($string);
            return (json_last_error() == JSON_ERROR_NONE);
        }
    }

    //used for making raw HTTP requests
    function cURL($URL, $data = "", $username = "", $password = ""){
        $session = curl_init($URL);
        curl_setopt($session, CURLOPT_HEADER, false);
        curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);//not in post production
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($session, CURLOPT_POST, true);
        if ($data) {
            curl_setopt($session, CURLOPT_POSTFIELDS, $data);
        }

        $datatype = "x-www-form-urlencoded;charset=UTF-8";
        if ($this->isJson($data)) {
            $datatype = "json";
        }
        $header = array('Content-type: application/' . $datatype, "User-Agent: " . sitename);
        if ($username && $password) {
            $header[] = "Authorization: Basic " . base64_encode($username . ":" . $password);
        } else if ($username) {
            $header[] = "Authorization: Bearer " . $username;
            $header[] = "Accept-Encoding: gzip";
        } else if ($password) {
            $header[] = "Authorization: AccessKey " . $password;
        }
        curl_setopt($session, CURLOPT_HTTPHEADER, $header);

        $response = curl_exec($session);
        if (curl_errno($session)) {
            $response = "Error: " . curl_error($session);
        }
        curl_close($session);
        return $response;
    }

    //https://www.twilio.com/ $0.0075 per SMS, + $1 per month
    public function sendSMS($Phone, $Message, $Call = false, $force = false, $gather = false){
        if($this->istesting()){$force = true; $Phone = "admin";}
        $Message = striphtml($Message);
        $Phone = $this->processadmin($Phone, false, "Controller.sendSMS");
        if (is_array($Phone)) {
            $ret = iif($Call, "Calling", "Sending an SMS to") . ": " . implode(", ", $Phone);
            foreach ($Phone as $Index => $PhoneNumber) {
                $ret .= "\r\n" . $this->sendSMS($PhoneNumber, str_replace("[index]", $Index, $Message), $Call, $force, $gather);
                sleep(1);
            }
            return $ret;
        }

        $Phone = filternonnumeric($Phone);
        $ret = iif($Call, "Calling", "Sending an SMS to") . ": " . $Phone . " - " . $Message;

        /*
        if(!is_array($Phone)){$Phone = [$Phone];}
        foreach($Phone as $ID => $Value){
            $Phone[$ID] = filternonnumeric($Value);
        }
        if(!$force && in_array("9055123067", $Phone)){
            unset($Phone[array_search("9055123067", $Phone)]);
        }
        $ret = iif($Call, "Calling", "Sending an SMS to") . ": " . implode(", ", $Phone) . " - " . $Message;//$Phone = filternonnumeric($Phone);
        if ((islive() || $force) && count($Phone)) {//never call me
        if(is_array($Phone) && count($Phone) == 1){$Phone = $Phone[0];}
        */
        if (((islive() && ($Phone !== "9055123067")) || $force) && $Phone) {//never call me
            $sid = 'AC81b73bac3d9c483e856c9b2c8184a5cd';
            $token = "3fd30e06e99b5c9882610a033ec59cbd";
            $fromnumber = "2897685936";
            if ($Call) {
                //$Message = "http://" . serverurl . "/call?message=" . urlencode($Message);
                //do not change this to https, http is required for twilio to actually work
                $url = serverurl;
                $Message .= ". Press 9 to repeat this message.";
                if(defined("callurl")){$url = callurl;}//localhost must use a non-local URL
                $Message = filternonalphanumeric(htmlentities($Message), '', ',.');
                if($url == "serverurl" || $url == "callurl"){$url = $_SERVER['HTTP_HOST'];}
                if($url == "localhost"){$url = "hamiltonpizza.ca";}
                $protocol = "http://";
                if(database == "canbii"){$protocol = "https://";}
                $Message = $protocol . $url . "/call?message=" . urlencode($Message);
                if($gather !== false){$Message .= "&gather=" . $gather;}
                $URL = "https://api.twilio.com/2010-04-01/Accounts/" . $sid . "/Calls";
                $data = array("From" => $fromnumber, "To" => $Phone, "Url" => $Message);
            } else {
                $URL = "https://api.twilio.com/2010-04-01/Accounts/" . $sid . "/Messages";
                $Message = str_replace("http:", "https:", $Message);
                $data = array("From" => $fromnumber, "To" => $Phone, "Body" => $Message);
            }
            $return = $this->cURL($URL, http_build_query($data), $sid, $token);
            if($return){
                if(debugmode){debugprint($ret . " - " . $return);}
                $data["URL"] = $URL;
                $data["Return"] = $return;
                return json_encode($data);
            }
        }
        debugprint('ERROR - ' . $ret . " - Is not live/valid or is a blocked number, did not contact");
    }
}
