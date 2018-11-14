<?php
namespace App\Http\Controllers;

use App\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Newsletter;

class AuthController extends Controller {
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login / registration.
     * @var string
     */
    protected $redirectTo = '/tasks';

    /**
     * Create a new authentication controller instance.
     * @return void
     */
    public function __construct() {
        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * Get a validator for an incoming registration request.
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data) {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     * @param  array  $data
     * @return User
     */
    protected function create(array $data) {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    public function gettoken(){
        return csrf_token();
    }

    //handles all authentication actions (login, logout, register, verify, forgot password)
    public function login($action = false, $email = false) {
        $now = my_now(true);//seconds from epoch date
        $attempttime = 300;//5 minutes
        if(!count($_POST)){$_POST = $_GET;}
        if(!$action && isset($_POST["action"])){$action = $_POST["action"];}
        $ret = array("Status" => false, "Action" => "NOT SPECIFIED");
        if($action) {
            $ret = array("Status" => true, "Action" => $action);
            if ($action == "logout") {
                foreach (array("id", "name", "email", "phone", "profiletype") as $Key) {
                    write($Key, '');
                }
                \Session::save();
            } else if ($action == "verify" && isset($_POST["code"])) {//verification code URL clicked
                $user = first("SELECT * FROM users WHERE authcode = '" . $_POST["code"] . "'", true, "AuthController.login");
                if ($user) {
                    $user["authcode"] = "";
                    insertdb("users", $user);
                    die("Your account has been verified");
                } else {
                    die("Code not found");
                }
            } else {//actions which require a user
                if (!$email) {
                    $email = trim($_POST["email"]);
                }
                $user = getuser($email);// first("SELECT * FROM users WHERE email = '" . $email . "'");
                $passwordmismatch = "Password and email address do not match a known account";
                if ($user) {
                    switch ($action) {
                        case "registration":
                            $ret["Status"] = false;
                            $ret["Reason"] = "Email address is in use";
                            break;
                        case "verify":
                            $this->sendverifemail($email);
                        case "login":
                            if ($user["lastlogin"] >= ($now - $attempttime) && $user["loginattempts"] > 5) {
                                $ret["Status"] = false;//brute-force prevention
                                $ret["Reason"] = "Too many login attempts. Please wait 5 minutes";
                            } else if ($user["authcode"]) {
                                $ret["Status"] = false;//require the user to be verified
                                $ret["Reason"] = 'Email address not verified. Please click the [verify] button in your email';
                            } else if (\Hash::check($_POST["password"], $user["password"])) {//login successful
                                unset($user["password"]);//do not send this to the user!
                                $ret["User"] = $user;
                                foreach ($user as $Key => $Value) {
                                    write($Key, $Value);
                                }
                                \Session::save();
                                $ret["Token"] = csrf_token();
                            } else {//login failed
                                $ret["Status"] = false;
                                $ret["Reason"] = $passwordmismatch;//"Password mismatch";
                                $user["lastlogin"] = $now;
                                if ($user["lastlogin"] >= ($now - $attempttime)) {
                                    $user["loginattempts"]++;
                                } else {
                                    $user["loginattempts"] = 1;
                                }
                                insertdb("users", $user);
                            }
                            break;
                        case "forgotpassword":
                            $user["password"] = $this->generateRandomString(6);
                            $user["mail_subject"] = "Forgot password";
                            $ret["Status"] = false;
                            if (strtolower($email) == "roy@trinoweb.com") {
                                $ret["Reason"] = "I refuse to reset this account";
                            } else {
                                $user["alldata"] = $user;
                                $text = $this->sendEMail("email_forgotpassword", $user);
                                if ($text) {//email failed to send
                                    $ret["Reason"] = $text;
                                } else {//only save change if email was sent
                                    $ret["Status"] = true;
                                    $ret["Reason"] = "A new password has been emailed to you";
                                    $user["password"] = \Hash::make($user["password"]);
                                    unset($user["mail_subject"]);
                                    unset($user["Addresses"]);
                                    unset($user["Others"]);
                                    insertdb("users", $user);
                                    $text = "Email sent";
                                }
                                debugprint("Reseting password for " . $user["name"] . " (" . $user["email"] . ") = " . $text);
                            }
                            break;
                    }
                } else {
                    switch ($action) {
                        case "registration":
                            $RequireAuthorization = false;
                            $oldpassword = $_POST["password"];
                            $user = [
                                "name" => $_POST["name"],
                                "email" => $_POST["email"],
                                "phone" => $_POST["phone"],
                                "password" => \Hash::make($_POST["password"]),
                                "created_at" => now(),
                                "updated_at" => 0
                            ];
                            $this->Subscribe($_POST["name"], $_POST["email"], $_POST["phone"]);
                            if ($RequireAuthorization) {
                                $user["authcode"] = $this->guidv4();
                            }
                            $user["id"] = insertdb("users", $user);

                            if(isset($_POST["address"])) {
                                $address = $_POST["address"];
                                if (is_array($address)) {
                                    unset($address["formatted_address"]);
                                    if (!islive() && strtolower($_POST["name"]) == "test") {
                                        $address["user_id"] = first("SELECT id FROM users WHERE profiletype = 1", true, "AuthController.login")["id"];
                                    } else {
                                        $address["user_id"] = $user["id"];
                                        insertdb("useraddresses", $address);
                                    }
                                }
                            }

                            $actions = actions("user_registered");//phone sms email
                            foreach ($actions as $action) {
                                switch ($action["party"]) {
                                    case 0://customer
                                        if ($action["email"]) {
                                            $this->sendverifemail($_POST["email"], $RequireAuthorization, $oldpassword);
                                        }
                                        if ($action["phone"]) {
                                            $this->sendSMS($_POST["phone"], $action["message"], true);
                                        }
                                        if ($action["sms"]) {
                                            $this->sendSMS($_POST["phone"], $action["message"]);
                                        }
                                        break;
                                    case 1://admin
                                        if ($action["email"]) {
                                            $this->sendEMail("email_test", array(
                                                'mail_subject' => "A new user has registered",
                                                "email" => "admin",
                                                "body" => $_POST["name"] . " has registered"
                                            ));
                                        }
                                        if ($action["phone"]) {
                                            $this->sendSMS("admin", $action["message"], true);
                                        }
                                        if ($action["sms"]) {
                                            $this->sendSMS("admin", $action["message"]);
                                        }
                                        break;
                                }
                            }
                            break;
                        case "forgotpassword":
                            $ret["Status"] = false;
                            $ret["Reason"] = "The email address was not found.";
                            break;
                        default:
                            $ret["Status"] = false;
                            $ret["Reason"] = $passwordmismatch;//"Email address not found."
                            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                $ret["Reason"] = "Email address is not valid";
                            }
                    }
                }
            }
        }
        die(json_encode($ret));
    }

    function Subscribe($name, $email, $phone){
        $firstname = $name;
        $lastname = "";
        $names = explode(" ", $firstname);
        if(isset($names[0])) {$firstname = $names[0];}
        if(count($names) > 1){$lastname = $names[count($names) - 1];}
        Newsletter::subscribeOrUpdate($email, ["FNAME" => $firstname, "LNAME" => $lastname, "PHONE" => $phone]);
    }

    //make a GUID
    function guidv4() {
        if (function_exists('com_create_guid') === true) {
            return trim(com_create_guid(), '{}');
        }
        $data = openssl_random_pseudo_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
    function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    //sends the verification email to the user
    function sendverifemail($email, $RequireAuthorization, $oldpassword){
        $user = first("SELECT * FROM users WHERE email = '" . $email . "'", true, "AuthController.sendverifemail");
        $user["password"] = $oldpassword;
        $user["requiresauth"] = $RequireAuthorization;
        if($RequireAuthorization) {
            $user["mail_subject"] = "Please click the verify button";
        } else {
            $user["mail_subject"] = "You have successfully registered!";
        }
        $text = $this->sendEMail("email_verify", $user);
        if($text){
            $ret["Status"] = false;
            $ret["Reason"] = $text;
        } else {
            $ret["Status"] = true;
            if($RequireAuthorization) {
                $ret["Reason"] = "Please click the Verify button in your email";
            } else {
                $ret["Reason"] = $user["mail_subject"];
            }
        }
        die(json_encode($ret));
    }
}
