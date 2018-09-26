<?php
$islive=true;
$dirroot = getcwd();
if (!endswith($dirroot, "/public")){
    $dirroot = $dirroot . "/public";
}
$servername = strtolower($_SERVER["SERVER_NAME"]);
if(left($servername, 4) == "www."){$servername = right($servername, strlen($servername) - 4);}

function setupconstants(){
    global $islive;
    $dirroot = str_replace("/public/", "", str_replace("\\", "/", public_path()) . "/");
    $data = include($dirroot . "/config/database.php");
    //$data = $GLOBALS["app"]["config"]["database"]; vardump($data);die();
    if(!defined("database")){
        $database = $data["connections"]["mysql"]["database"];
        if(textcontains($database, "canbii")){$database = "canbii";} else {$database = "ai";}
        define("database", $database);
    }
    if(!defined("serverurl")){
        foreach($data["constants"] as $key => $value){
            switch(filternumeric($key)){
                case "islive": $islive = $value; break;
                case "include":
                    $value = str_replace("[public_html]", $dirroot, $value);
                    if(file_exists($value)){
                        require_once($value);
                    } else {
                        die($value . " NOT FOUND!");
                    }
                    break;
                case "timezone": date_default_timezone_set($value); break;
                default:
                    $value = str_replace("[SERVER_NAME]", $_SERVER["SERVER_NAME"], $value);
                    define($key, $value);
            }
        }
    }
    return $data;
}

setupconstants();

$webroot = webroot();
$Filename = base_path() . "/ai.sql";
function webroot($file = "", $justroot = false){
    $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
    $webroot = $_SERVER["REQUEST_URI"];
    $start = strpos($webroot, "/", 1) + 1;
    $webroot = substr($webroot, 0, $start);
    $protocol = "http";
    $public = "";
    if(strpos(serverurl, ".com") !== false){
        if($file && strpos($file, "public/") === false){
            $public = "public/";
        }
        return serverurl . "/" . $public . $file;
    } else if (islive()) {
        $webroot = "/";
        if ($isSecure) {
            $protocol = "https";
        }
        if(strpos($file, "images/") !== false){$public = "public/";}
    } else {
        $justroot = false;
        $public = "public/";
        if(strpos($file, $public) !== false || $justroot){$public = "";}
    }
    $public = $protocol . '://' . $_SERVER['HTTP_HOST'] . $webroot . $public . $file;
    return $public;
}

if(!function_exists("posix_geteuid")){
    function posix_geteuid(){
        return 0;
    }
}

//error_reporting(E_ERROR | E_PARSE);//suppress warnings
//include("../veritas3-1/config/app.php");//config file is not meant to be run without cake, thus error reporting needs to be suppressed
//error_reporting(E_ALL);//re-enable warnings
$con = connectdb();
function connectdb($database = false, $username = false, $password = false){
    global $con;
    if(!$database || !$username || !$password){
        $dirroot = getcwd();
        if(endswith($dirroot, "public")){ $dirroot .= "/.."; }
        $data = setupconstants();
        $data = $data["connections"]["mysql"];
        if(!$database){$database = $data["database"];}
        if(!$username){$username = $data["username"];}
        if(!$password){$password = $data["password"];}
    }
    $localhost = "localhost";
    if ($_SERVER["SERVER_NAME"] == "localhost") {
        $localhost .= ":3306";
    }
    if (!islive()) {
        $_SERVER['SERVER_ADDR'] = gethostbyname(gethostname());
    }
    $GLOBALS["database"] = $database;
    $con = mysqli_connect($localhost, $username, $password, $database) or die("Error " . mysqli_connect_error($con));
    return $con;
}

function enumadmins($TrueForEmailFalseForPhoneNumber, $CollapseIf1 = false, $Where = false){
    if(is_string($TrueForEmailFalseForPhoneNumber)){
        $Field = $TrueForEmailFalseForPhoneNumber;
    } else if($TrueForEmailFalseForPhoneNumber){
        $Field = "email";
    } else {
        $Field = "phone";
    }
    $data = Query("SELECT " . $Field . " FROM users WHERE profiletype = 1", true, "enumadmin's " . $Field . " " . $Where);
    $data = collapsearray($data, $Field);
    if($CollapseIf1 && count($data) == 1){return $data[0];}
    return $data;
}

function needsphonenumber(){
    $always = false;//should be set to true, customers are really bad at keeping the phone number up to date
    if($always){return true;}
    if(read("id")){if(isvalidphone(read("phone"))){return false;}}
    return true;
}

function isvalidphone($text){
    return strlen(filternonnumeric($text)) == 10;
}

function left($text, $length){
    return substr($text, 0, $length);
}
function startswith($text, $test){
    return left($text, strlen($test)) == $test;
}

function right($text, $length){
    return substr($text, -$length);
}
function endswith($text, $test){
    return right($text, strlen($test)) == $test;
}

function mid($text, $start, $length){
    return substr($text, $start, $length);
}
function textcontains($text, $searchfor){
    return strpos(strtolower($text), strtolower($searchfor)) !== false;
}

function escapeSQL($text){
    global $con;
    return mysqli_real_escape_string($con, $text);
}

function insertdb($Table, $DataArray, $PrimaryKey = "id", $Execute = True){
    global $con;
    if (is_object($con)) {
        $DataArray = escapearray($DataArray, $con);
    }
    filtersubarrays($DataArray);
    $query = "INSERT INTO " . $Table . " (" . getarrayasstring($DataArray, True) . ") VALUES (" . getarrayasstring($DataArray, False) . ")";
    if ($PrimaryKey && isset($DataArray[$PrimaryKey])) {
        $query .= " ON DUPLICATE KEY UPDATE";
        $delimeter = " ";
        foreach ($DataArray as $Key => $Value) {
            if(!startswith($Key, "omit_")) {
                if ($Key != $PrimaryKey) {
                    $query .= $delimeter . $Key . "='" . $Value . "'";
                    $delimeter = ", ";
                }
            }
        }
    }
    $query .= ";";
    if ($Execute && is_object($con)) {
        mysqli_query($con, $query) or die ('Unable to execute query. ' . mysqli_error($con) . "<P>Query: " . $query);
        return $con->insert_id;
    }
    return $query;
}

function escapearray($DataArray, $con){
    foreach ($DataArray as $Key => $Value) {
        if (!is_array($Value)) {
            $DataArray[$Key] = mysqli_real_escape_string($con, $Value);
        }
    }
    return $DataArray;
}

function getarrayasstring($DataArray, $Keys = True){
    if ($Keys) {
        $DataArray = array_keys($DataArray);
        return implode(", ", $DataArray);
    } else {
        $DataArray = array_values($DataArray);
        $DataArray = implode("', '", $DataArray);
        return "'" . $DataArray . "'";
    }
}

function filtersubarrays(&$array){
    foreach ($array as $key => $row) {
        if (is_array($row))
            unset($array[$key]);
    }
}

function implode2($mostglue, $lastglue, $array){
    reset($array);
    $first = key($array);
    end($array);
    $last = key($array);
    $ret = $array[$first];
    if(count($array) > 1) {
        foreach ($array as $key => $value) {
            if ($key == $last) {
                $ret .= $lastglue . $value;
            } else if ($key != $first) {
                $ret .= $mostglue . $value;
            }
        }
    }
    return $ret;
}

/*
 * $getcol:
 *  blank, returns first query results
 *  false, returns all results after the get(). use while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) { to get results
 *  true, returns the SQL query
 *  "COUNT()" returns the count of the results
 *  "ALL()" all the results in an array
 *  a string returns that specific column
 *  $OrderBy/$Dir/$GroupBy = order by column/direction (ASC/DESC)/group by column
*/
function select_field_where($Table, $Where, $getcol = "", $OrderBy = "", $Dir = "ASC", $GroupBy = "", $LimitBy = 0, $Start = 0){
    $query = "SELECT * FROM " . $Table;
    if ($Where) {$query .= " WHERE " . $Where;}
    if ($OrderBy) {$query .= " ORDER BY " . $OrderBy . " " . $Dir;}
    if ($GroupBy) {$query .= " GROUP BY " . $GroupBy;}
    if ($LimitBy) {
        $query .= " LIMIT " . $LimitBy;
        if ($Start) {$query .= " OFFSET " . $Start;}
    }
    if ($getcol === true) {return $query;}
    $result = Query($query, false, "API.select_field_where");
    if ($getcol !== false) {
        if ($getcol == "COUNT()") {
            return iterator_count($result);
        } else if ($getcol == "ALL()") {
            return first($result, false);
        } else {
            $result = first($result);
            if ($getcol) {return $result[$getcol];}
        }
    }
    return $result;
}

function describe($table){
    return Query("DESCRIBE " . $table, true, "API.describe");
}

function deleterow($Table, $Where = false){
    if ($Where) {$Where = " WHERE " . $Where;}
    Query("DELETE FROM " . $Table . $Where, false, "API.deleterow");
}

function first($query, $Only1 = true, $Why = "Unknown"){
    global $con;
    if (!is_object($query)) {$query = Query($query, false, $Why);}
    if ($query) {
        $ret = array();
        while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
            unescape($row);
            if ($Only1) {return $row;}
            $ret[] = $row;
        }
        return $ret;
    }
}

function unescape(&$data){
    if(is_array($data)){
        foreach($data as $key => $index){
            $data[$key] = unescape($index);
        }
    } else if (is_string($data)){
        $data = stripslashes($data);
    }
    return $data;
}

function get($Key, $default = "", $arr = false){
    if (is_array($arr)) {
        if (isset($arr[$Key])) {return $arr[$Key];}
    } else {
        if (isset($_POST[$Key])) {return $_POST[$Key];}
        if (isset($_GET[$Key])) {return $_GET[$Key];}
    }
    return $default;
}

function collapsearray($Arr, $ValueKey = false, $KeyKey = false, $Delimiter = false){
    foreach ($Arr as $index => $value) {
        if (!$ValueKey) {
            foreach ($value as $key2 => $value2) {
                $ValueKey = $key2;
                break;
            }
        }
        if ($Delimiter) {
            $Arr[$index] = explode($Delimiter, $value[$ValueKey]);
        } else {
            if ($KeyKey) {
                $Arr[$value[$KeyKey]] = $value[$ValueKey];
                unset($Arr[$index]);
            } else {
                $Arr[$index] = $value[$ValueKey];
            }
        }
    }
    return $Arr;
}

function flattenarray($arr, $key){
    foreach ($arr as $index => $value) {
        $arr[$index] = $value[$key];
    }
    return $arr;
}

function enum_tables($table = "", $Why = "UNKNOWN"){
    return flattenarray(Query("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA='" . $GLOBALS["database"] . "'" . iif($table, " AND TABLE_NAME='" . $table . "'"), true, "API.enum_tables: " . $Why), "TABLE_NAME");
}

if (!function_exists("mysqli_fetch_all")) {
    function mysqli_fetch_all($result) {
        $data = [];
        if (is_object($result)) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }
}

function Query($query, $all = false, $Where = "Unknown"){
    global $con;//use while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) { to get results
    $ret = false;
    $debugmode = defined('debugmode');
    if($debugmode){$debugmode = debugmode;}
    if($debugmode){$now = millitime();}
    /*
    $query2 = strtolower($query);
    foreach(["--", "/*", ";", "if(", "0x", "concat", "load_file", "hex", "#", ":", "waitfor", "delay", "into ", "true", "false"] as $search){
        if(strpos($query2, $search) !== false){
            die("MySQL attack intercepted: " . $search);
        }
    }
    */
    if ($all) {
        $result = $con->query($query);
        if (is_object($result)) {
            $ret = true;
            $data = mysqli_fetch_all($result, MYSQLI_ASSOC);// or die ('Unable to execute query. '. mysqli_error($con) . "<P>Query: " . $query);
            unescape($data);
        } else {
            debugprint($query . " returned no results");
        }
    }
    if(!$ret) {$data = $con->query($query);}
    if($debugmode){
        $now = millitime() - $now;
        $GLOBALS["SQL"][] = ["Time" => $now, "Query" => $query, "Where" => $Where];
    }
    return $data;
}

function GenerateDate($Date, $Ignored = false, $ShortForm = false){
    $today = date("F j");
    $Date = str_replace($today, "Today, " . $today, $Date);
    $tomorrow = date("F j", strtotime("+ 1 day"));
    $Date = str_replace($tomorrow, "Tomorrow, " . $tomorrow, $Date);
    if($ShortForm){
        $monthnames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        foreach($monthnames as $monthname){
            $Date = str_replace($monthname, left($monthname, 3), $Date);
        }
    }
    return $Date;
}

function printoption($option, $selected = "", $value = ""){
    $tempstr = "";
    if ($option === $selected || $value === $selected) {
        $tempstr = " selected";
    }
    if (strlen($value) > 0) {
        $value = " value='" . $value . "'";
    }
    return '<option' . $value . $tempstr . ">" . $option . "</option>";
}

function printoptions($name, $valuearray, $selected = "", $optionarray = false, $isdisabled = ""){
    $tempstr = '<SELECT ' . $isdisabled . ' name="' . $name . '" id="' . $name . '" CLASS="form-control">';
    if (!$optionarray) {
        $optionarray = $valuearray;
    }
    for ($temp = 0; $temp < count($valuearray); $temp += 1) {
        if (is_array($optionarray)) {
            $value = $optionarray[$temp];
        } else {
            $value = $temp;
        }
        $tempstr .= printoption($valuearray[$temp], $selected, $value);
    }
    $tempstr .= '</SELECT>';
    return $tempstr;
}

function iif($value, $istrue, $isfalse = ""){
    if ($value) {
        return $istrue;
    }
    return $isfalse;
}

if(!function_exists("is_iterable")) {
    function is_iterable($var) {
        return (is_array($var) || $var instanceof Traversable);
    }
}

function constants($key, $file = "database", $section = "constants"){
    return $GLOBALS["app"]["config"][$file][$section][$key];
}

function getbetween($text, $start, $end = false){
    $startpos = strpos($text, $start);
    if($startpos === false){return false;}
    $text = right($text, strlen($text) - ($startpos + strlen($start)));
    if($end === false){return $text;}
    $startpos = strpos($text, $end);
    if($startpos === false){return false;}
    $text = left($text, $startpos);
    return $text;
}

//$src = source array, $keys = the keys to remove
function removekeys($src, $keys){
    return array_diff_key($src, array_flip($keys));
}

function printrow($row, &$FirstResult = false, $PrimaryKey = "id", $TableID = ""){
    if ($FirstResult) {
        echo '<TABLE BORDER="1" CLASS="autotable"';
        if ($TableID) {
            echo ' ID="' . $TableID . '"';
        }
        echo '><THEAD><TR>';
        $ID = 0;
        foreach ($row as $Key => $Value) {
            echo '<TH CLASS="' . $TableID . 'colheader ' . $Key . '" ID="' . $TableID . '-col' . $ID . '">' . $Key . '</TH>';
            $ID++;
        }
        echo '</TR></THEAD>';
        $FirstResult = false;
    }

    echo '<TR ID="' . $TableID . 'row' . $row[$PrimaryKey] . '">';
    foreach ($row as $Key => $Value) {
        echo '<TD CLASS="' . $Key . '" ID="' . $TableID . 'row' . $row[$PrimaryKey] . '-' . $Key . '"';
        if (is_numeric($Value)) {
            echo ' ALIGN="RIGHT"';
        }
        if ($Value == "*") {
            echo ' ALIGN="CENTER"';
        }
        echo '>';
        if (is_array($Value)) {
            $FirstResult2 = true;
            printrow($Value, $FirstResult2, $PrimaryKey, $TableID . $row[$PrimaryKey] . "-" . $Key);
            echo '</TABLE>';
        } else {
            echo $Value;
        }
        echo '</TD>';
    }
    echo '</TR>';
}

function printfile($filename){
    echo '<DIV CLASS="blue">' . $filename . '</DIV>';
}

//removes numbers
function filternumeric($text, $withwhat = ''){
    return preg_replace('/[0-9]/', $withwhat, $text);
}

//removes non-numbers
function filternonnumeric($text, $withwhat = ''){
    return preg_replace('/[^0-9]/', $withwhat, $text);
}

function filternonalphanumeric($text, $withwhat = '', $anythingelse = ''){
    return preg_replace("/[^A-Za-z0-9 " . $anythingelse . "]/", $withwhat, $text);
}

function setsetting($Key, $Value){
    return insertdb("settings", array("keyname" => $Key, "value" => $Value), "keyname");
}

function millitime() {
    $microtime = microtime();
    $comps = explode(' ', $microtime);
    return sprintf('%d%03d', $comps[1], $comps[0] * 1000);
}

function getordinal($number){
    $ends = array('th','st','nd','rd');
    if (((($number % 100) >= 11) && (($number%100) <= 13)) || !isset($ends[$number % 10])) {
        return $number . 'th';
    }
    return $number . $ends[$number % 10];
}

function getsetting($Key, $Default = ""){
    if(!isset($GLOBALS["variables"]["hassettingtable"])) {
        if (enum_tables("settings", "API.getsetting")) {
            $GLOBALS["variables"]["hassettingtable"] = true;
        } else {
            return $Default;
        }
    }
    $Value = Query("SELECT value FROM settings WHERE keyname='" . $Key . "'", true, "API.getsetting");
    if (isset($Value[0]["value"])) {
        return $Value[0]["value"];
    }
    return $Default;
}

function drop_table($table = false){
    if ($table === false) {
        Query("SET foreign_key_checks = 0", false, "API.drop_table");
        $tables = enum_tables("", "API.drop_table");
        foreach ($tables as $table) {
            drop_table($table);
        }
    } else {
        Query("DROP TABLE IF EXISTS " . $table, false, "API.drop_table");
    }
}

function importSQL($filename){
    drop_table();
    $templine = '';// Temporary variable, used to store current query
    $lines = file($filename);// Read in entire file
    foreach ($lines as $line) {// Loop through each line
        if (substr($line, 0, 2) == '--' || $line == '') {
            continue;
        }// Skip it if it's a comment
        $templine .= $line;// Add this line to the current segment
        if (substr(trim($line), -1, 1) == ';') {// If it has a semicolon at the end, it's the end of the query
            Query($templine, false, "API.importSQL");// Perform the query
            $templine = '';// Reset temp variable to empty
        }
    }
    Query("COMMIT;", false, "API.importSQL");
}

function isFileUpToDate($SettingKey, $Filename = false){
    if(!is_numeric($SettingKey)){
        $SettingKey = getsetting($SettingKey, "0");
    }
    if($Filename === false){
        return $SettingKey;
    } else if (file_exists($Filename)) {
        $lastFILupdate = filemtime($Filename);
        return $lastFILupdate > $SettingKey;
    }
}

function loadsettings(){
    $settings = first("SELECT * FROM `settings` WHERE keyname NOT IN (SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA='" . $GLOBALS["database"] . "') AND keyname NOT IN ('lastSQL', 'menucache')", false, "API.loadsettings");
    foreach($settings as $ID => $Value){
        $settings[$Value["keyname"]] = $Value["value"];
        define($Value["keyname"], $Value["value"]);
        unset($settings[$ID]);
    }
    return $settings;
}

$con = connectdb();
$settings = loadsettings();

/*
if (isFileUpToDate("lastSQL", $Filename)) {
    $lastSQLupdate = getsetting("lastSQL", "0");
    $lastFILupdate = filemtime($Filename);
    importSQL($Filename);
    $con = connectdb();
    $settings = loadsettings();
    setsetting("lastSQL", $lastFILupdate);
    echo '<DIV CLASS="red">' . $lastSQLupdate . ' SQL was out of date, imported AI.sql on ' . $lastFILupdate . '</DIV>';
}
*/

function read($Name){
    if (\Session::has('session_' . $Name)) {
        return \Session::get('session_' . $Name);
    }
}

//write to session
function write($Name, $Value, $Save = false){
    \Session::put('session_' . $Name, $Value);
    if ($Save) {
        \Session::save();
    }
}

//returns the current date/time
function my_now($totime = false, $now = false){
    if (!$now) {
        $now = time();
        if (read("profiletype") == 1 && isset($_GET["time"])) {
            if (is_numeric($_GET["time"]) && $_GET["time"] >= 0 && $_GET["time"] <= 2400) {
                $hour = floor($_GET["time"] / 100);
                $minute = $_GET["time"] % 100;
                $now = mktime($hour, $minute);
            }
        }
    }
    if (!is_numeric($now)) {return $now;}
    if ($totime === true) {return $now;}
    if ($totime !== false && $totime !== true) {return date($totime, $now);}
    return date("Y-m-d H:i:s", $now);
}

//write text to error_log
function debugprint($text, $path = "error_log", $DeleteFirst = false){
    $todaytime = date("Y-m-d") . " " . date("h:i:s a");
    $dashes = "----------------------------------------------------------------------------------------------\r\n";
    if (is_array($text)) {
        $text = print_r($text, true);
    }
    $dir = getdirectory($path);
    if (!is_dir($dir) && $dir) {
        mkdir($dir, 0777, true);
    }
    $ID = read("id");
    $Name = iif($ID, read("name"), "[NOT LOGGED IN]");
    $text = $dashes . $todaytime . ' (USER # ' . $ID . ": " . $Name . ")  --  " . str_replace(array("%dashes%", "<BR>", "%20"), array($dashes, "\r\n", " "), $text) . "\r\n";
    file_put_contents($path, $text, iif($DeleteFirst, 0, FILE_APPEND));
    return $text;
}

function orderpath($OrderID, $userid = false){
    $oldfilename = public_path("orders") . "/" . $OrderID . ".json";
    if (file_exists($oldfilename)) {
        return $oldfilename;
    }
    if(!$userid){$userid = first("SELECT user_id FROM orders WHERE id = " . $OrderID, true, "API.orderpath");}
    if($userid) {
        $userid = $userid["user_id"];
        $dir = public_path("orders/user" . $userid);//no / at the end
        $newfilename = $dir . "/" . $OrderID . ".json";
        if (!is_dir($dir)) {mkdir($dir, 0777, true);}
        return $newfilename;
    }
    return "";
}

function deletefile($file){
    if(file_exists($file)){
        unlink($file);
        return true;
    }
    return false;
}

function getdirectory($path){
    return pathinfo(str_replace("\\", "/", $path), PATHINFO_DIRNAME);
}

function getfilename($path, $WithExtension = false){
    if ($WithExtension) {
        return pathinfo($path, PATHINFO_BASENAME); //filename only, with extension
    } else {
        return pathinfo($path, PATHINFO_FILENAME); //filename only, no extension
    }
}

//get the lower-cased extension of a file path
//HOME/WINDOWS/TEST.JPG returns jpg
function getextension($path){
    return strtolower(pathinfo($path, PATHINFO_EXTENSION)); // extension only, no period
}

function file_size($path){
    if (file_exists($path)) {
        return filesize($path);
    }
    return 0;
}

//gets the last key of an array
function lastkey($array){
    $keys = array_keys($array);
    return last($keys);
}

function getiterator($arr, $key, $value, $retValue = true){
    foreach ($arr as $index => $item) {
        if (is_array($item)) {
            if (isset($item[$key]) && $item[$key] == $value) {
                if ($retValue) {
                    return $value;
                }
                return $index;
            }
        } else if (is_object($item)) {
            if (isset($item->$key) && $item->$key == $value) {
                if ($retValue) {
                    return $value;
                }
                return $index;
            }
        }
    }
}

function getuser($IDorEmail = false, $IncludeOther = true){
    $field = "email";
    if (!$IDorEmail) {
        $IDorEmail = read("id");
    }
    if (is_numeric($IDorEmail)) {
        $field = "id";
    } else {
        $IDorEmail = "'" . $IDorEmail . "'";
    }
    $user = first("SELECT * FROM users WHERE " . $field . " = " . $IDorEmail, true, "API.getuser");
    if (!$user) {
        return false;
    }
    if ($IncludeOther) {
        $user["Addresses"] = Query("SELECT * FROM useraddresses WHERE user_id = " . $user["id"], true, "API.getuser");
        $user["Orders"] = Query("SELECT id, placed_at FROM `orders` WHERE user_id = " . $user["id"] . " ORDER BY id DESC LIMIT 5", true, "API.getuser");
        foreach ($user["Orders"] as $Index => $Order) {
            $user["Orders"][$Index]["placed_at"] = verbosedate($Order["placed_at"]);
        }
        if ($user["stripecustid"]) {
            initStripe();
            try {
                $customer = \Stripe\Customer::Retrieve($user["stripecustid"]);//get all credit cards
                //vardump($customer->sources->data);die();
                foreach ($customer->sources->data as $Index => $Value) {
                    $customer->sources->data[$Index] = getProtectedValue($Value, "_values");
                    unset($customer->sources->data[$Index]["metadata"]);
                }
                $user["Stripe"] = $customer->sources->data;
            } catch (Stripe\Error\Base $e) {
                Query("UPDATE users SET stripecustid = '' WHERE " . $field . " = " . $IDorEmail . ";", false, "API.getuser");//stripecustid is likely invalid, delete it
                $user["StripeError"] = $e->getMessage();
            }
        }
    }
    return $user;
}

function vardump($data){
    echo '<PRE CLASS="vardump">';
    var_dump($data);
    echo '</PRE>';
}

//gets the protected value of an object ("_properties" is one used by most objects)
function getProtectedValue($obj, $name = "_properties"){
    $array = (array)$obj;
    $prefix = chr(0) . '*' . chr(0);
    if (isset($array[$prefix . $name])) {
        return $array[$prefix . $name];
    }
}

$GLOBALS["testlive"] = false;
function initStripe(){
    //Set secret key: remember to change this to live secret key in production
    $mode = islive() ? "live" : "test";
    if (isset($_POST["stripemode"]) && $_POST["stripemode"]){
        $mode = $_POST["stripemode"];
    }
    if($mode == "test"){
        \Stripe\Stripe::setApiKey("BJi8zV1i3D90vmaaBoLKywL84HlstXEg"); //test
    } else {
        \Stripe\Stripe::setApiKey("3qL9w2o6A0xePqv8C6ufRKbAqkKTDJAW"); //live
    }
}

function isencrypted($text){
    if (left($text, 9) == "eyJpdiI6I") {
        try {
            $value = decrypt($text);
            $text = $value;
        } catch (\Exception $e) {
        }
    }
    return $text;
}

function islive(){
    setupconstants();
    $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
    if($isSecure){return true;}
    $server = $_SERVER["SERVER_NAME"];
    return textcontains($server, serverurl);
    /*if($server == "localhost" || $server == "127.0.0.1"){return false;}
    if(strpos($server, ".") !== false){
        if(is_numeric(str_replace(".", "", $server))) {
            $server = explode(".", $server);
            if ($server[0] == "10") {return false;}
            if ($server[0] == "172" && $server[1] > "15" && $server[1] < 32) {return false;}
            if ($server[0] == "192" && $server[1] == "168") {return false;}
        }
    }
    return true;*/
}

function verbosedate($date){
    if (!is_numeric($date)) {
        $date = strtotime($date);
    }
    $append = "";
    switch (date("G:i", $date)) {
        case  "0:00": $append = " (Midnight)"; break;
        case "12:00": $append = " (Noon)"; break;
    }
    return date("l F j, Y @ g:i A", $date) . $append;
}

function findrestaurant($UserID = false){
    if(!$UserID){$UserID = read("id");}
    $addresses = Query("SELECT id FROM useraddresses WHERE user_id = " . $UserID, true, "API");
    $addresses = implode(",", collapsearray($addresses, "id"));
    $restaurants = collapsearray(Query("SELECT id FROM restaurants WHERE address_id IN (" . $addresses. ")", true, "API"), "id");
    if(count($restaurants) == 1){return $restaurants[0];}
    return $restaurants;
}

function gethours($RestaurantID = -1){
    $hours = first("SELECT * FROM `hours` WHERE restaurant_id = " . $RestaurantID . " or restaurant_id = 0 ORDER BY restaurant_id DESC LIMIT 1", true, "API.gethours");
    $ret = array();
    foreach ($hours as $day => $time) {
        $dayofweek = left($day, 1);
        if (is_numeric($dayofweek)) {
            $timeofday = right($day, strlen($day) - 2);
            $ret[$dayofweek][$timeofday] = $time;
        }
    }
    return $ret;
}

function GenerateTime($time = ""){
    if (!$time) {
        $time = date("Gi");
    }
    $minutes = $time % 100;
    $thehours = intval(floor($time / 100));
    $hoursAMPM = $thehours % 12;
    if ($hoursAMPM == 0) {
        $hoursAMPM = 12;
    }
    $tempstr = $hoursAMPM . ":";
    if ($minutes == 0) {
        $tempstr .= "00";
    } else if ($minutes < 10) {
        $tempstr .= "0" + $minutes;
    } else {
        $tempstr .= $minutes;
    }
    $extra = "";
    if ($time == 0) {
        $extra = " (Midnight)";
    } else if ($time == 1200) {
        $extra = " (Noon)";
    }
    if ($time < 1200) {
        return $tempstr . " AM" . $extra;
    } else {
        return $tempstr . " PM" . $extra;
    }
}

function like_match($pattern, $subject){
    $pattern = str_replace('%', '.*', preg_quote($pattern, '/'));
    return (bool)preg_match("/^{$pattern}$/i", $subject);
}

function lastupdatetime($table){//will not work on live!
    if (first("SHOW TABLE STATUS FROM " . $GLOBALS["database"] . " LIKE '" . $table . "';", true , "API.lastupdatetime")["Engine"] == "InnoDB") {
        $filename = first('SHOW VARIABLES WHERE Variable_name = "datadir"', true , "API.lastupdatetime")["Value"] . $GLOBALS["database"] . '/' . $table . '.ibd';
        return filemtime($filename);//UNIX datestamp
    }
    return first("SELECT UPDATE_TIME FROM information_schema.tables WHERE  TABLE_SCHEMA = '" . $GLOBALS["database"] . "' AND TABLE_NAME = '" . $table . "'", true , "API.lastupdatetime")["UPDATE_TIME"];//unknown format
}

function startfile($filename){
    date_default_timezone_set("America/Toronto");
    $GLOBALS["filetimes"][$filename]["start"] = microtime(true);
    if(isset($GLOBALS["filetimes"][$filename]["times"])) {
        $GLOBALS["filetimes"][$filename]["times"] += 1;
        return true;
    } else {
        $GLOBALS["filetimes"][$filename]["times"] = 1;
    }
}

function endfile($filename){
    $GLOBALS["filetimes"][$filename]["end"] = microtime(true);
}

function countSQL($table, $SQL = "*"){
    return first("SELECT COUNT(" . $SQL . ") as count FROM " . $table, true , "API.countSQL")["count"];
}

function includefile($path){
    $extension = getextension($path);
    $actualpath = base_path() . "/" . $path;
    if(file_exists($actualpath)) {
        $webpath = webroot($path);
        //if($GLOBALS["settings"]["domenucache"] != 1){
            $webpath .= "?" . filemtime($actualpath);
        //}
        switch ($extension) {
            case "css":
                echo '<link rel="stylesheet" href="' . $webpath . '">' . "\r\n";
                break;
            case "js":
                echo '<script src="' . $webpath . '"></script>' . "\r\n";
                break;
        }
    } else {
        die("file not found: " . $path . " (" . $actualpath . ")");
    }
}

function actions($eventname, $party = -1){
    //party:  0=customer, 1=admin, 2=restaurant
    //events: order_placed, order_canceled/order_pending/order_confirmed/order_declined/order_delivered (done), user_registered (done)
    $SQL = "SELECT party, sms, phone, email, message FROM actions WHERE eventname = '" . $eventname . "'";
    if ($party > -1) {
        $SQL .= " AND party = " . $party;
    }
    return first($SQL, $party > -1, "API.actions");
}

function getdeliverytime($var = "DeliveryTime"){
    if(!isset($GLOBALS["deliverytime"])){
        $GLOBALS["deliverytime"] = first("SELECT price FROM additional_toppings WHERE size = '" . $var . "'", true, "API.actions")["price"];
    }
    return $GLOBALS["deliverytime"];
}

function formatphone($phone){
    $phone = filternonnumeric($phone);
    if (strlen($phone) == 10) {
        return preg_replace('/(\d{3})(\d{3})(\d{4})/', "($1) $2-$3", $phone);
    }
    return $phone;
}

function pad_left($value, $length){
    return str_pad($value, $length, '0', STR_PAD_LEFT);
}

function delivery_at($placed_at, $deliverytime, $delivery_delay = false){
    $monthnames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    $placed_at_time = strtotime($placed_at);
    $placed_at_date = explode("-", $placed_at);
    if(!$delivery_delay){$delivery_delay = getdeliverytime() * 60;}
    $should_be = $placed_at_time + $delivery_delay;
    if($deliverytime == "Deliver Now"){//convert 'July 9 at 2345' to '2018-07-11 15:22:40'
        $ret = date("Y-m-d G:i:s", $should_be);
    } else {
        $delivery_date = explode(" ", $deliverytime);
        $month   = array_search($delivery_date[0], $monthnames) + 1;//month
        $year    = $placed_at_date[0];
        $day     = $delivery_date[1];
        $minutes = $delivery_date[3];
        $hours   = floor($minutes / 100);
        $minutes = $minutes % 100;
        if($month < $placed_at_date[1]){$year++;}
        $ret = $year . "-" . pad_left($month,2) . "-" . pad_left($day,2) . " " . $hours . ":" . pad_left($minutes,2) . ":00";
        if(strtotime($ret) < $should_be){$ret = date("Y-m-d G:i:s", $should_be);}
    }
    return $ret;
}

$GLOBALS["strings"] = [
    "aboutus" => 'Our Story'
];
function makestring($string, $variables = []){
    if(startswith($string, "{") && endswith($string, "}")){
        $string = mid($string, 1, strlen($string) - 2);
        if(!isset($GLOBALS["strings"][$string])){return false;}
        $string = $GLOBALS["strings"][$string];
    }
    foreach($variables as $key => $value){
        $string = str_replace("[" . $key . "]", $value, $string);
    }
    return $string;
}

function striphtml($HTML){
    $HTML = preg_replace('#<[^>]+>#', ' ', $HTML);
    $HTML = preg_replace('#\s+#', ' ', $HTML);
    return trim($HTML);
}
?>