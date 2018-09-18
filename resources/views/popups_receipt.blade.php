@extends(isset($isGET) ? 'layouts_app' : 'layouts_blank')
@section('content')
    <?php
    //do not in-line the styles as this is used in emails
    //http://localhost/ai/public/list/orders?action=getreceipt&orderid=224
    startfile("popups_receipt");
    $debugmode = !islive();
    $debug = "";
    if(!isset($Order) && isset($orderid)){
        $Order = first("SELECT orders.*, users.name, users.id as userid, users.email FROM orders, users WHERE orders.id = " . $orderid . " HAVING user_id = users.id");
        /* testing
        $Order["userid"] = 12;
        if($Order["userid"] <> read("id") && $party == "user"){$party = "private";}
                echo "Order User ID: " . $Order["userid"];
                echo "PARTY: " . $party;
        */
        $filename = orderpath($orderid);//
        if (!isset($includeextradata)) {
            $includeextradata = false;
        }
        if (isset($JSON)) {//get raw JSON instead
            $style = 2;
            if ($JSON && $JSON != "false") {
                if (file_exists($filename)) {
                    $Order["Order"] = json_decode(file_get_contents($filename));
                    echo json_encode($Order);
                    die();//only the JSON is desired, send it
                }
                echo json_encode(array("Status" => false, "Reason" => "File not found"));
                die();
            }
        } else if (!isset($style)) {
            $style = 1;
        }
        if (!$Order) {
            echo 'Order not found';
            return false;
        }
    } else if(isset($data)) {
        $filename = $data;
        $orderid = false;
    }
    switch ($style) {
        case 1:
            $includeextradata = true;
            $colspan = 6;
            if (!$debugmode) {
                $colspan -= 1;
            }
            break;
        case 2:
            $colspan = 4;
            break;
    }
    //Hack to put CSS inline for emails
    if (!isset($inline)) {
        $inline = false;
    }
    if (!isset($timer)) {
        $timer = false;
    }
    $GLOBALS["inline"] = $inline;
    if (!function_exists("inline")) {
        function tomin($time){
            return left($time, strlen($time) - 2) * 60 + right($time, 2);
        }

        function minpad($time){
            if ($time < 10) {
                return "0" . $time;
            }
            return $time;
        }

        function inline($Class, $OnlyInline = false){
            if ($GLOBALS["inline"]) {
                $Style = array();
                $Class = explode(" ", $Class);
                foreach ($Class as $Classname) {
                    switch (strtolower($Classname)) {
                        //table-sm
                        case "table":
                            $Style[] = "border-collapse: collapse !important; border: none !important;";
                            break;
                        case "table-bordered":
                            $Style[] = "";
                            break;
                        case "bg-primary":
                            $Style[] = "";
                            break;
                        case "table-inverse":
                            $Style[] = "";
                            break;
                    }
                }
                return ' style="' . implode(" ", $Style) . '"';
            } else if (!$OnlyInline) {
                return ' class="' . $Class . '"';
            }
        }

        function parsetime($Time){
            return strtotime(date("j F Y ") . left($Time, 1) . " hours " . right($Time, 2) . " minutes");
        }

        function roundTime($timestamp, $increment = 15){
            $BEFOREminutes = date('i', $timestamp);
            $AFTERminutes = $increment + $BEFOREminutes - ($BEFOREminutes % $increment);
            $DIFFERENCEminutes = $AFTERminutes - $BEFOREminutes;
            return $timestamp + ($DIFFERENCEminutes * 60);
        }

        function todate($timestamp){
            return date("F j, Y G:i", $timestamp);
        }

        function getdiscount($subtotal){
            $subtotal = floor($subtotal/10)*10;
            for($dollars = $subtotal; $dollars > 0; $dollars-=10){
                $keyname = "over$" . $dollars;
                if(isset($GLOBALS["discounts"][$keyname])){
                    return $GLOBALS["discounts"][$keyname];
                }
            }
            return 0;
        }

        function formatlast4($last4){//ABBBBCCDD
            if(strlen($last4) <> 9){return "Missing Data";}
            $card_types = [1 => "American Express", 2 => "Visa", 3 => "MasterCard"];
            $card_type = left($last4, 1);
            $card_number = mid($last4, 1, 4);
            //$card_month = mid($last4, 5, 2);
            //$card_year = right($last4, 2);
            return $card_types[$card_type] . " x-" . $card_number;// . " Expires: " . $card_month . "/20" . $card_year;
        }
    }
    //edit countdown timer duration
    $minutes = getdeliverytime();
    $seconds = 0;
    $hours = 0;
    $duration = "";
    $timer = $place != "email";
    $day_of_week = date("w");
    if (isset($_GET["day"]) && is_numeric($_GET["day"]) && $_GET["day"] >= 0 && $_GET["day"] < 7) {
        $day_of_week = $_GET["day"];
    }

    if ($Order["deliverytime"]) {
        $duration = $Order["deliverytime"];
        $Time = trim(right($Order["deliverytime"], 4));//1500
        if (is_numeric($Time)) {
            $CurrentTime = date("Gi");
            if (isset($_GET["time"]) && is_numeric($_GET["time"]) && $_GET["time"] >= 0 && $_GET["time"] < 2400) {
                $CurrentTime = $_GET["time"];
            }
            $date = str_replace(" at ", "", left($Order["deliverytime"], strlen($Order["deliverytime"]) - 4));
            $tomorrow = date("F j", strtotime("+ 1 day"));
            if ($date == $tomorrow) {
                $DeliveryTime = strtotime("midnight tomorrow") + tomin($Time) * 60;
                $minutes = ceil(($DeliveryTime - time()) / 60);
            } else if ($CurrentTime <= $Time && $timer) {
                $minutes = tomin($Time) - tomin($CurrentTime) + 1;
            } else {
                $timer = false;
            }
            $duration = GenerateDate($date, $timer, true) . " at " . GenerateTime(intval($Time));
        } else if ($Order["deliverytime"] == "Deliver Now") {
            $time = strtotime($Order["placed_at"]) + ($minutes * 60);
            $open = parsetime(gethours($Order["restaurant_id"])[$day_of_week]["open"]) + ($minutes * 60);
            if ($time < $open && date("F j", $time) == date("F j", $open)) {
                $time = $open;
            }
            $time = roundTime($time);
            if (time() > $time) {
                $timer = false;//expired
            } else {
                $minutes = ceil(($time - time()) / 60);
            }
            $duration = GenerateDate(date("F j ", $time), $timer, true) . "at " . date("g:i A", $time);
        } else {
            $timer = false;
        }
    }
    if (is_numeric($minutes) && $minutes > 59) {
        $hours = floor($minutes / 60);
        $minutes = $minutes % 60;
    }
    $time = '';
    $Delivery = "Delivered on ";
    if($place == "email" || $timer){
        $Delivery = "Delivery ";
    }
    if ($timer) {
        if ($minutes < 60) {
            $time = $minutes;
        } else {
            $time = floor($minutes / 60) . " " . minpad($minutes % 60);
        }
        $time .= "m:" . minpad($seconds) . "s";
    }
    $onlydebug = "Only shows in debug mode! - ";
?>

    @if($party != "private")
            <h2 class="text-center py-2" style="color: #FF0000">{{ $Delivery . $duration }}</h2>
            <!--p class="text-center">Please allow + or - 10 minutes for delivery.</p-->
    @endif

    @if($includeextradata)
        @if($timer)
            <!--div style="font-size: 2rem !important;" CLASS="mb-2 countdown btn-lg badge badge-pill badge-success" hours="<?= $hours; ?>" minutes="<?= $minutes; ?>" seconds="<?= $seconds; ?>"
             title="Time is approximate and not a guarantee"><?= $time; ?></div-->
        @elseif($place != "email")
            <span class="badge badge-pill badge-danger">[EXPIRED]</span>
        @endif
    @endif

    <?php
        $dir = public_path("orders") . "/user" . $Order["user_id"];
        if (!is_dir($dir)) {mkdir($dir, 0777);}
        $HTMLfilename = $dir . "/" . $orderid . "-" . $style . ".html";
        if(isset($Order["last4"]) && $Order["last4"]){$last4 = $Order["last4"];}
        if(file_exists($HTMLfilename)){
            $HTML = file_get_contents($HTMLfilename);
        } else {
            $data = array(
                    "style" => $style,
                    "debugmode" => $debugmode,
                    "onlydebug" => $onlydebug,
                    "filename" => $filename,
                    "place" => $place,
                    "colspan" => $colspan,
                    "party" => $party,
                    "Order" => $Order,
                    "includeextradata" => $includeextradata,
                    "orderid" => $orderid,
                    "debug" => $debug
            );
            //echo "last4-3: " . $last4;
            //vardump($data);
            $HTML = view("popups_receiptdata", $data)->render();
            if($orderid){
                if (!is_dir($dir)) {mkdir($dir, 0777, true);}
                file_put_contents($HTMLfilename, $HTML);
            }
        }

        if((read("id") != $Order["user_id"] && read("profiletype") == 0) || $party == "Private"){
            $last4 = "Private information";//do not allow other users to view this data!!!
        }
        if(is_numeric($last4)){
            $last4 = '<strong style="color:#ff0000">Paid by ' . formatlast4($last4) . '</strong>';
        } else if ($last4){
            $last4 = '<strong style="color:#ff0000">' . $last4 . '</strong>';
        } else {
            $last4 = "";
        }
        $HTML = str_replace("(LAST4)", $last4, $HTML);
        $HTML = str_replace(array('COLSPAN="-4"', 'COLSPAN="0"'), 'COLSPAN="2"', $HTML);

        echo $HTML;
        if($party != "private"){
            $Restaurant = first("SELECT * FROM restaurants WHERE id = " . $Order["restaurant_id"]);
            if(isset($Restaurant["address_id"])){
                $Raddress = first("SELECT * FROM useraddresses WHERE id = " . $Restaurant["address_id"]);
            } else {
                $unknown = "UNKNOWN";
                $Restaurant = [
                        "name" => $unknown,
                        "phone" => "",
                ];
                $Raddress = [
                        "name" => $unknown,
                        "number" => $unknown,
                        "street" => $unknown,
                        "city" => $unknown,
                        "province" => $unknown,
                        "postalcode" => $unknown,
                        "unit" => $unknown,
                        "latitude" => "0",
                        "longitude" => "0"
                ];
            }
        ?>
            <TABLE WIDTH="100%" STYLE="border-collapse:collapse;">
                <TR>
                    <TD ONCLICK="addmarker('<?= $Restaurant["name"] . "'s Address', " . $Raddress["latitude"] . ", " . $Raddress["longitude"]; ?>, true);">
                        <?php

                            /*
                            echo $Raddress["city"] . " " . $Raddress["province"] . " " . $Raddress["postalcode"] . '<BR>' . $Raddress["unit"] . " " . formatphone($Restaurant["phone"]);
                            echo '<INPUT TYPE="HIDDEN" ID="cust_latitude" VALUE="' . $Order["latitude"] . '"><INPUT TYPE="HIDDEN" ID="cust_longitude" VALUE="' . $Order["longitude"]
                                . '"><INPUT TYPE="HIDDEN" ID="rest_latitude" VALUE="' . $Raddress["latitude"]
                                . '"><INPUT TYPE="HIDDEN" ID="rest_longitude" VALUE="' . $Raddress["longitude"] . '">';
                            echo '</TD><TD ID="custaddress" ONCLICK="addmarker(' . "'" . $Order["name"] . "\'s Address\', " . $Order["latitude"] . ", " . $Order["longitude"] . ', true);" WIDTH="49%" ID="restaddress">';
                            */
                            echo '<h2 class="mt-2" style="margin-top: 0px; margin-bottom: 0px; vertical-align: top;">Delivery Info</h2>';
                            echo $Order["name"] . "<BR>" . $Order["number"] . " " . $Order["street"] . '<BR>' . $Order["city"] . " " . $Order["province"] . " " . $Order["postalcode"] . "<br>";
                            if($Order["unit"]){echo $Order["unit"]. '<BR>';}
                            echo formatphone($Order["phone"]);
                            $custaddress = $Order["number"] . " " . $Order["street"] . ", " . $Order["city"];


                        echo '<br><br><h2 class="mt-2" style="margin-top: 0px; margin-bottom: 0px; vertical-align: top;">Order #<span ID="receipt_id">'  . $orderid . '</span></h2>';
                        echo $Restaurant["name"] . "<BR>" . formatphone($Restaurant["phone"]) . "" ;
                        ?>
                    </TD>
                </TR>
                <?php
                    if(isset($isinmodal)){
                        echo '<TR><TD ALIGN="CENTER" COLSPAN="2"><SPAN ONCLICK="directions(' . "'" . $Order["name"] . "\'s Address', " . $Order["latitude"] . ", " . $Order["longitude"] . ", '";
                        echo $Restaurant["name"] . "\'s Address', " . $Raddress["latitude"] . ", " . $Raddress["longitude"] . ');" CLASS="hyperlink">Directions</SPAN></TD><TD>';
                        echo '<A TARGET="_blank" HREF="https://www.google.com/maps/dir/?api=1&origin_place_id=' . urlencode($Restaurant["name"]) . '&origin=' . $Raddress["latitude"] . '%2C' . $Raddress["longitude"] . '&destination=' . $Order["latitude"] . '%2C' . $Order["longitude"] . '&destination_place_id=Customer Address (' . urlencode($custaddress) . ')&travelmode=driving&dir_action=navigate">Directions in a new tab</A></TD></TR>';
                    }
                ?>
            </TABLE>
        <?php } ?>

    @if(isset($JSON))
        <BUTTON CLASS="btn btn-block {{btncolor}} mb-3 mt-2" ONCLICK="orders(<?= $orderid; ?>, true);">LOAD ORDER</BUTTON>
    @endif
    @if($includeextradata)
        <DIV CLASS="extrainfo">
            @if($party != "restaurant")
                <h2 class="mt-4">Questions about your order?</h2>
                <p>Please contact the {{storename}} directly.</p>
                <DIV CLASS="clearfix"></DIV>
            @endif
            <p>
            <a class="btn-link btn {{btncolor}} pl-0" href="<?= webroot("help"); ?>">About Us</a>
            </p>
        </DIV>
    @endif

    @if($timer && $place != "getreceipt")
        <SCRIPT>
            if (isUndefined(countdown)) {
                var countdown = window.setTimeout(function () {
                    incrementtime()
                }, 1000);
            }

            function backtotime(timestamp) {
                var d = new Date(timestamp * 1000);
                return d.getHours() + ":" + d.getMinutes();
            }

            function incrementtime() {
                if (!$(".countdown").hasAttr("timestamp")) {
                    var seconds = Number($(".countdown").attr("seconds"));
                    var minutes = Number($(".countdown").attr("minutes"));
                    var timestamp = getNow();
                    $(".countdown").attr("startingtime", backtotime(timestamp));
                    timestamp += (minutes * 60) + seconds;
                    $(".countdown").attr("endingtime", backtotime(timestamp));
                    $(".countdown").attr("timestamp", timestamp);
                } else {
                    var timestamp = $(".countdown").attr("timestamp");
                    var seconds = timestamp - getNow();
                    var minutes = Math.floor(seconds / 60);
                    seconds = seconds % 60;
                }
                var hours = Math.floor(minutes / 60);

                var result = false;
                if (seconds == 0) {
                    if (minutes == 0) {
                        result = "[EXPIRED]";
                        window.clearInterval(countdown);
                    } else {
                        minutes -= 1;
                    }
                    seconds = 59;
                } else {
                    seconds -= 1;
                }
                if (!result) {
                    if (hours == 0) {
                        result = minutes;
                    } else {
                        result = hours + "h:" + minpad(minutes % 60);
                    }
                    result += "m:" + minpad(seconds) + "s";
                }
                $(".countdown").text(result);
                countdown = window.setTimeout(function () {
                    incrementtime()
                }, 1000);
            }

            function minpad(time) {
                if (time < 10) {
                    return "0" + time;
                }
                return time;
            }
        </SCRIPT>
    @endif
    <?php endfile("popups_receipt"); ?>
@endsection