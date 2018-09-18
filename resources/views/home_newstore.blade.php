<?php
    function restaurantexists($name, $email, $phone){
        $query = "SELECT * FROM restaurants WHERE name = '" . $name ."' OR email = '" . $email . "' OR phone = '" . $phone . "'";
        $restaurant = first($query);
        if(is_null($restaurant)){return false;}
        return count($restaurant) > 0;
    }

    if(isset($_POST["action"])){
        foreach(["restaurant"] as $key){
            $_POST[$key] = escapeSQL($_POST[$key]);
        }

        $phone = filternonnumeric($_POST["phonenumber"]);
        if(restaurantexists($_POST["restaurant"], $_POST["emailaddress"], $phone)){
            die("This " . storename . " exists already");
        }

        if(!$_POST["add_number"] && !$_POST["add_street"] && !$_POST["add_city"] && !$_POST["add_province"] && !$_POST["add_postalcode"]){
            $address = explode(", ", $_POST["address"]);
            $_POST["add_number"] = filternonnumeric($address[0]);
            $_POST["add_street"] = trim(filternumeric($address[0]));
            $_POST["add_city"] = $address[1];
            $_POST["add_postalcode"] = right(trim($address[2]), 7);
            $_POST["add_province"] = trim(left($address[2], strlen($address[2]) - 7));
        }
        //restaurant, password, address, latitude, longitude, emailaddress, phonenumber, hours, add_number, add_street, add_city, add_province, add_postalcode

        //function insertdb($Table, $DataArray, $PrimaryKey = "id", $Execute = True);
        //create user
        $userid = insertdb("users", [
            "name"          => $_POST["restaurant"],
            "email"         => $_POST["emailaddress"],
            "created_at"    => now(),
            "updated_at"    => 0,
            "password"      => \Hash::make($_POST["password"]),
            "phone"         => $phone,
            "profiletype"   => 2
        ]);
        //create address
        $addressid = insertdb("useraddresses", [
            "user_id"       => $userid,
            "number"        => $_POST["add_number"],
            "street"        => $_POST["add_street"],
            "postalcode"    => $_POST["add_postalcode"],
            "city"          => $_POST["add_city"],
            "province"      => $_POST["add_province"],
            "latitude"      => $_POST["latitude"],
            "longitude"     => $_POST["longitude"],
            "phone"         => $phone
        ]);

        $is_live = iif($_POST["is_live"] == "true", 1, 0);

        //create restaurant
        $restaurantid = insertdb("restaurants", [
            "name"          => $_POST["restaurant"],
            "email"         => $_POST["emailaddress"],
            "phone"         => $phone,
            "is_delivery"   => $is_live,
            "address_id"    => $addressid
        ]);
        //create hours
        $hours = $_POST["hours"];
        $hours["restaurant_id"] = $restaurantid;
        $hoursid = "[N/A]";
        if($_POST["use_default_hours"] == "false"){
            $hoursid = insertdb("hours", $hours, "restaurant_id");
            $hoursid = "[Yes]";
        }

        echo "<B>Created</B><BR>" . ucfirst(storename) . " ID: " . $restaurantid;
        echo "<BR>User ID: " . $userid;
        echo "<BR>Address ID: " . $addressid;
        echo "<BR>Hours ID: " . $hoursid;
        echo "<BR>Is Live: " . iif($is_live, "Yes", "No");
        die();
    }
    startfile("home_newstore");
?>
@extends("layouts_app")
@section("content")
    <STYLE>
        #add_unit{
            display: none;
        }

        .inilist{
            max-height: 500px;
            overflow-y: scroll;
            overflow-x: hidden;
        }
    </STYLE>
    <DIV CLASS="row">
        <DIV CLASS="col-md-2">
            <?php
                $dir = base_path();
                $files = scandir($dir);
                foreach($files as $file){
                    if (getextension($file) == "ini"){
                        echo '<B>INI file: ' . left($file, strlen($file) - 4) . '</B><DIV CLASS="inilist">';
                        echo view("api_ini", ["filename" => $dir . "/" . $file, "onclick" => "loadstore", "class" => "cursor-pointer"])->render();
                        echo '</DIV><HR>';
                    }
                }
            ?>
        </DIV>
        <DIV CLASS="col-md-10">
            Store creator:
            <INPUT TYPE="text" ID="storename" placeholder="Store Name" class="form-control">
            <?= view("popups_address", ["title" => "", "unit" => false, "address_placeholder" => "Address"])->render(); ?>
            <INPUT TYPE="email" ID="emailaddress" placeholder="Email Address" class="form-control">
            <INPUT TYPE="phone" ID="phonenumber" placeholder="Phone Number" class="form-control">
            <INPUT TYPE="password" ID="password" placeholder="password" class="form-control" value="admin">
            <LABEL><INPUT TYPE="checkbox" ID="islive" CHECKED>Is Live</LABEL><BR>
            <LABEL><INPUT TYPE="checkbox" ID="usedefaulthours">Use Default Hours instead of the below</LABEL>
            <?php
                echo '<TABLE><TR><TH>Day</TH><TH>Open</TH><TH>Close</TH></TR>';
                $daysofweek = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
                foreach($daysofweek as $day => $name){
                echo '<TR><TD>' . $name . ':</TD><TD>';
                        maketimeselect($day . '_open');
                        echo '</TD><TD>';
                        maketimeselect($day . '_close');
                        echo '</TD></TR>';
                }
                echo '<TR><TD><STRONG>All</STRONG>:</TD><TD>';
                maketimeselect('all_open');
                echo '</TD><TD>';
                maketimeselect('all_close');
                echo '</TD></TABLE>';
                function maketimeselect($ID){
                    echo '<SELECT ID="' . $ID . '" ONCHANGE="handleinput(this);" TYPE="select"><OPTION VALUE="-1">Closed</OPTION>';
                    echotime(0, "12", "AM (Midnight)");
                    for($hour = 1; $hour < 12; $hour++){
                        echotime($hour, $hour, "AM");
                    }
                    echotime(12, "12", "PM (Noon)");
                    for($hour = 1; $hour < 12; $hour++){
                        echotime($hour+12, $hour, "PM");
                    }
                    echo '</SELECT>';
                }
                function echotime($actualhour, $displayhour, $ampm){
                    for($minute = 0; $minute < 60; $minute+= 5){
                        $actualminute = str_pad($minute, 2, '0', STR_PAD_LEFT);
                        echo '<OPTION VALUE="' . $actualhour . $actualminute . '">' . $displayhour . ":" . $actualminute . ' ' . $ampm . '</OPTION>';
                    }
                }
            ?>
            <BUTTON CLASS="btn btn-sm btn-success cursor-pointer" ONCLICK="createstore();">Create</BUTTON>
        </DIV>
    </DIV>
    <SCRIPT>
        var daysofweek = <?= json_encode($daysofweek); ?>;

        function loadstore(element){
            //<div onclick="loadstore(this);" class="cursor-pointer" address="2372 Barton St E, Hamilton, ON L8E 2W7" gps="43.2376506,-79.7672911" url="qualitypizzaandwings.com (doesn't work)" phone="(905) 573-8800" sunday="12PM-12AM" monday="11AM-12AM" tuesday="11AM-12AM" wednesday="11AM-12AM" thursday="11AM-2AM" friday="11AM-2AM" saturday="11AM-2AM">Quality Pizza &amp; Wings</div>
            $("#storename").val( $(element).text() );
            $(getGoogleAddressSelector()).val( $(element).attr("address") );
            $("#emailaddress").val( "roy+" + $(element).text().replaceAll(" ", "").replaceAll("&", "").replaceAll("'", "") + "@trinoweb.com" );
            $("#phonenumber").val( $(element).attr("phone") );
            if($(element).hasAttr("gps")) {
                var gps = $(element).attr("gps").split(",");
                $("#add_latitude").val(gps[0]);
                $("#add_longitude").val(gps[1]);
            } else {
                $("#add_latitude").val($(element).attr("latitude"));
                $("#add_longitude").val($(element).attr("longitude"));
            }

            var hoursfound = 0;
            for (var day = 0; day < daysofweek.length; day++){
                var dayofweek = daysofweek[day];
                if($(element).hasAttr(dayofweek)) {
                    var hours = $(element).attr(dayofweek).split("-");
                    $("#" + day + "_open").val(formattime(hours[0]));
                    $("#" + day + "_close").val(formattime(hours[1]));
                    //console.log(dayofweek + " = open " + hours[0] + " close " + hours[1]);
                    hoursfound++;
                }
            }
            if(hoursfound == 0){
                $('#usedefaulthours').prop('checked', true);
            }
        }

        function formattime(time){
            //12, 11PM, 10:30AM
            var before = time, addto = 0, ret = 0, mode = 0;
            if(time == "12AM") {
                mode = 5;
                ret = "000";
            } else if(time == "12PM"){
                mode = 5;
                ret = "1200";
            } else if(time.endswith("AM") || time.endswith("PM")){
                if(time.endswith("PM")){addto = 12;}
                time = time.left( time.length - 2 );
                if(time.contains(":")){
                    mode = 1;
                    ret = Number(time.replace(":", "")) + (addto * 100);
                } else {
                    mode = 2;
                    ret = (Number(time) + addto) + "00";
                }
            } else if(time == "12") {
                mode = 3;
                ret = "1200";
            }
            console.log("BEFORE: " + before + " AFTER: " + ret + " MODE: " + mode + " TIME: " + time);
            return ret;
        }

        function createstore(){
            $.post("<?= Request::url(); ?>", {
                action: "save",
                _token: token,
                restaurant: $("#storename").val(),
                password: $("#password").val(),
                address: $(getGoogleAddressSelector()).val(),
                latitude: $("#add_latitude").val(),
                longitude: $("#add_longitude").val(),
                emailaddress: $("#emailaddress").val(),
                phonenumber: $("#phonenumber").val(),
                hours: gethours(),
                add_number: $("#add_number").val(),
                add_street: $("#add_street").val(),
                add_city: $("#add_city").val(),
                add_province: $("#add_province").val(),
                add_postalcode: $("#add_postalcode").val(),
                is_live: $("#islive").is(":checked"),
                use_default_hours: $("#usedefaulthours").is(":checked")
            }).done(function (result) {
                alert(result);
            });
        }

        function gethours(){
            var form = {};
            for (var day = 0; day < daysofweek.length; day++){
                form[day + "_open"] = $("#" + day + "_open").val();
                form[day + "_close"] = $("#" + day + "_close").val();
            }
            return form;
        }

        function handleinput(e){
            var elementid = $(e).attr("id");
            var elementyp = $(e).prop("tagName").toLowerCase();
            var currvalue = $(e).val();
            var index = elementid.replace(/\D/g,'');
            if(!index){index = "all";}
            var ending = "_close";
            var oppositeending = "_open";
            if(elementid.endswith("_open")){oppositeending = "_close"; ending = "_open";}
            if(elementyp == "input"){elementyp = $(e).attr("type").toLowerCase();}
            if(elementyp == "checkbox"){currvalue = e.checked;}
            currentinput = elementid;
            if(elementyp == "select"){
                if(index == "all"){
                    for (var day = 0; day < daysofweek.length; day++){
                        $("#" + day + ending).val(currvalue);
                        if(currvalue == "-1"){$("#" + day + oppositeending).val(-1);}
                    }
                }
                if(currvalue == -1){$("#" + index + oppositeending).val(-1);}
            }
            if(currvalue == -1){currvalue += " (Closed)";}
            log(elementid + " is a " + elementyp + " = " + currvalue);
        }
    </SCRIPT>
<?php endfile("home_newstore"); ?>
@endsection
