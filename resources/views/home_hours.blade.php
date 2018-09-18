<?php
    if(isset($_POST["action"])){
        $data = "Action: '" . $_POST["action"] . "' is not handled";
        if(isset($_POST["restaurant"])){
            $data = first("SELECT * FROM hours WHERE restaurant_id = " . $_POST["restaurant"]);
        }
        switch($_POST["action"]){
            case "gethours": break;
            case "sethours":
                //if(count($data) != 0){$_POST["hours"]["id"] = $data["id"];}
                insertdb("hours", $_POST["hours"], "restaurant_id");
                $data = "Hours saved for '[name]'";
                break;
        }
        if(is_array($data) || is_object($data)){$data = json_encode($data);}
        die($data);
    }
?>
@extends("layouts_app")
@section("content")
<?php
    startfile("home_hours");
    $query = read("profiletype");
    $cansave = true;
    switch($query){
        case 0: die("ACCESS DENIED"); break;//user
        case 1: $query = "SELECT * FROM restaurants"; break;//admin
        case 2://restaurant
            $cansave = false;
            $user = getuser();
            $restaurant = first("SELECT id FROM restaurants WHERE address_id = " . $user["Addresses"][0]["id"])["id"];
            $query = "SELECT * FROM restaurants WHERE id = 0 or id = " . $restaurant;
            break;
    }
    $restaurants = query($query, true, "home_hours");
    echo '<LABEL>' . ucfirst(storename) . ': <SELECT ID="restaurant" ONCHANGE="loadhours();"><OPTION VALUE="0">[Default hours]</OPTION>';
    foreach($restaurants as $restaurant){
        echo '<OPTION VALUE="' . $restaurant["id"] . '">' . $restaurant["name"] . '</OPTION>';
    }
    echo '</SELECT></LABEL>';

    echo '<TABLE WIDTH="100%"><TR><TD>';
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
    echo '</TD></TR></TABLE></TD><TD>';
    echo '<BUTTON ID="savebutton" ONCLICK="doesneedsaving(false);" style="display: none;" class="btn btn-sm btn-success pull-center">Save Changes</BUTTON>';
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
<SCRIPT>
    var currentURL = "<?= Request::url(); ?>";
    var daysofweek = <?= json_encode($daysofweek); ?>;
    var defaulthours;
    var currentinput = "";
    var needssaving = false;
    var cansave = <?= iif($cansave, "true", "false"); ?>;

    function loadhours(){
        var storeID = $("#restaurant").val();
        $("#savebutton").hide();
        $.post(currentURL, {
            action: "gethours",
            _token: token,
            restaurant: storeID
        }).done(function (result) {
            result = JSON.parse(result);
            if(storeID == 0){defaulthours = result;}
            if(result.length == 0){result = defaulthours;}
            for (var day = 0; day < daysofweek.length; day++){
                $("#" + day + "_open").val( result[day + "_open"] );
                $("#" + day + "_close").val( result[day + "_close"] );
                $("#" + day + "_check").prop("checked", result[day + "_open"] == "-1" || result[day + "_close"] == "-1");
            }
        });
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
        doesneedsaving(true);
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

    function gethours(){
        var form = {};
        form["restaurant_id"] = $("#restaurant").val();
        for (var day = 0; day < daysofweek.length; day++){
            form[day + "_open"] = $("#" + day + "_open").val();
            form[day + "_close"] = $("#" + day + "_close").val();
        }
        if(form["restaurant_id"] == 0){defaulthours = form;}
        return form;
    }

    function getrestaurant_name(){
        return $("#restaurant option:selected").text();
    }

    function doesneedsaving(value){
        needssaving = value;
        if(value){
            if(!cansave && $("#restaurant").val() == 0){return;}
            $("#savebutton").show();
        } else {
            $("#savebutton").hide();
            var form = gethours();
            $.post(currentURL, {
                action: "sethours",
                _token: token,
                restaurant: form["restaurant_id"],
                hours: form
            }).done(function (result) {
                if(result){alert(result.replace("[name]", getrestaurant_name()));}
            });
        }
    }
    loadhours();
</SCRIPT>
<?php endfile("home_hours"); ?>
@endsection