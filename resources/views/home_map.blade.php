@extends('layouts_app')
@section('content')
    <?php
        $dead_color = "#FF0000";//RED
        $live_color = "#006400";//GREEN
        $invalid_color = "#000000";//BLACK
        $local_color = "#000064";//BLUE

        if(!islive() || read("profiletype") == 1){
            echo view("popups_time")->render();
        }
        use App\Http\Controllers\HomeController;//used for order "closestrestaurant"
        //profiletypes: 0=user, 1=admin, 2=restaurant
        echo '<DIV CLASS="row"><DIV CLASS="col-md-11">';
        echo view("popups_address", array("dontincludeGoogle" => true, "unit" => false, "title" => "", "address" => $_GET))->render();
        echo '</DIV><DIV CLASS="col-md-1"><BUTTON CLASS="btn btn-sm btn-success full-width full-height" ONCLICK="search();">Search</BUTTON></DIV></DIV>';

        function get_number($GET){
            if(isset($_GET[$GET]) && is_numeric($_GET[$GET])){return $_GET[$GET];}
            return false;
        }

        $markers = array();
        $latitude = get_number("latitude");
        $longitude = get_number("longitude");
        $radius = iif(read("profiletype") == 1, 100, 10);//range
        $restaurants = [];//is a user, no address specified (list no restaurants)
        $restaurant_id = -1;

        if(read("profiletype") == 2 || isset($_GET["restaurantid"])){//is a restaurant, or restaurant ID specified (list only their restaurant)
            if(isset($_GET["restaurantid"])){//restaurant ID specified
                $restaurant_id = $_GET["restaurantid"];
            } else {//is a restaurant
                $restaurant_id = findrestaurant(read("id"));
            }
            $restaurants = Query("SELECT * FROM restaurants WHERE id = " . $restaurant_id, true, "home_map");
        } else if(read("profiletype") == 1 && (!$latitude && !$longitude)){//is an admin, no address specified (list all restaurants)
            $restaurants = Query("SELECT * FROM restaurants WHERE address_id > 0", true, "home_map");
        } else if ($latitude && $longitude) {//address specified (list restaurants within range)
            $restaurants = App::make('App\Http\Controllers\HomeController')->closestrestaurant(["limit" => 5, "latitude" => $latitude, "longitude" => $longitude, "radius" => $radius, "merge" => true], false, false);
            $markers[] = array($_GET["formatted_address"], $_GET["formatted_address"], $_GET["latitude"], $_GET["longitude"], -1, -1);
            //closestrestaurant($data[restaurant_id, limit, latitude, longitude, radius, merge], $gethours = false, $includesql = true)
        }

        $addressIDs = array();
        foreach($restaurants as $restaurant){
            if(!in_array($restaurant["address_id"], $addressIDs)){
                $addressIDs[] = $restaurant["address_id"];
            }
        }
        if($addressIDs){
            $addressIDs = implode(",", $addressIDs);
            $addresses = Query("SELECT * FROM useraddresses WHERE id IN (" . $addressIDs . ")", true, "home_map");
            $hours = Query("SELECT * FROM hours WHERE restaurant_id IN (0," . $addressIDs . ")", true, "home_map");
        }

        function findwhere($array, $key, $value, $retdata = false){
            foreach($array as $index => $data){
                if($data[$key] == $value){
                    if($retdata){return $data;}
                    return $index;
                }
            }
            return false;
        }

        function restaurantdata($restaurant, $includeName = true, $newline = '<BR>'){
            $HTML = iif($includeName, $restaurant["name"]);
            if (strlen(filternonnumeric($restaurant["email"])) == 10 && !textcontains($restaurant["email"], "@")){
                $restaurant["phone"] = $restaurant["email"];
                $restaurant["email"] = "";
            }
            if($restaurant["email"]){$HTML .= $newline . "Email: " . $restaurant["email"];}
            if($restaurant["phone"]){$HTML .= $newline . "Phone: " . formatphone($restaurant["phone"]);}
            $HTML .= $newline . "Address: " . addressdata($restaurant["address"], $newline);
            $HTML .= $newline . "Hours: " . hourdata($restaurant["hours"]);
            $HTML .= $newline . "Is live: " . iif($restaurant["is_delivery"], "Yes", "No");
            if(startswith($HTML, $newline)){$HTML = right($HTML, strlen($HTML) - strlen($newline));}
            return $HTML;
        }

        function addressdata($address, $newline){
            $debugdata = "";
            if(!is_numeric($address["latitude"]) || !is_numeric($address["longitude"])){
                $debugdata = $newline . "Invalid Latitude and/or Longitude data for address ID: " . $address["id"];
            }
            if($address["number"] == 0){
                return $address["street"] . $debugdata;
            } else {
                return $address["number"] . " " . iif($address["unit"], " (Unit" . $address["unit"] . ")") . $address["street"] . ", " . $address["city"] . ", " . $address["province"] . " " . $address["postalcode"] . $debugdata;
            }
        }

        function hourdata($hours){
            $dayofweek = date("w");
            $time = date("Gi");
            $today_open = $hours[$dayofweek . "_open"];
            $today_close = $hours[$dayofweek . "_close"];
            $dayofweek = $dayofweek - 1;
            if($dayofweek == -1){$dayofweek = 6;}
            $yesterday_open = $hours[$dayofweek . "_open"];
            $yesterday_close = $hours[$dayofweek . "_close"];
            if($today_close > $today_open && $today_open < $time && $today_close > $time ){
                return "Open: " . GenerateTime($today_open) . " - Close: " . GenerateTime($today_close);
            } else if ( $today_close < $today_open && $today_open < $time) {
                return "Open: " . GenerateTime($today_open) . " - Close: " . GenerateTime($today_close);
            } else if ( $yesterday_close < $yesterday_open && $yesterday_close > $time){
                return "Open: " . GenerateTime($yesterday_open) . " - Close: " . GenerateTime($yesterday_close);
            } else if ( $time > $today_close && $today_close > 0 ) {
                return "Closed at: " . GenerateTime($today_close);
            }
            return "Closed";
        }

        echo '<DIV CLASS="row"><DIV CLASS="col-md-2">';
        if(count($restaurants)){
            echo 'Restaurants:';
            foreach($restaurants as $index => $restaurant){
                $address = findwhere($addresses, "id", $restaurant["address_id"], true);
                $hoursID = findwhere($hours, "restaurant_id", $restaurant["id"]);
                if($hoursID == false){$hoursID = findwhere($hours, "restaurant_id", 0);}
                $restaurant["hours"] = $hours[$hoursID];
                $restaurant["address"] = $address;
                $distance = "";
                if(isset($restaurant["distance"])){$distance = " (" . round($restaurant["distance"],2) . " km)";}
                if($address !== false){
                    $class = "invalid";
                    if(is_numeric($address["latitude"]) && is_numeric($address["longitude"])){
                        $markers[] = array(
                                $restaurant["name"],
                                restaurantdata($restaurant),
                                $address["latitude"],
                                $address["longitude"],
                                iif($restaurant["is_delivery"], "1", "0"),
                                $restaurant["id"]
                        );
                        $class = iif($restaurant["is_delivery"], "live", "dead");
                    }
                    echo '<BR><A CLASS="rest-' . $class . '" lat="' . $address["latitude"] . '" long="' . $address["longitude"] . '" HREF="#" ONCLICK="return clickrest(this);" marker="' . (count($markers)-1) . '" TITLE="' . restaurantdata($restaurant, false, ' - ') . '" ID="rest_' . $restaurant["id"] . '" NAME="' . $restaurant["name"] .'"';
                    for($day = 0; $day < 7; $day++){
                        echo $day . '_open="' . $restaurant["hours"][$day . "_open"] . '" ' . $day . '_close="' . $restaurant["hours"][$day . "_close"] . '"';
                    }
                    echo '>' . $restaurant["name"] . $distance . '</A>';
                }
            }
        } else if($latitude && $longitude) {
            echo 'No restaurants found within ' . $radius . " km";
        } else {
            echo 'No address specified';
        }
        echo '</DIV><DIV CLASS="col-md-10">';
        echo view("popups_googlemaps");

        $GET = $_GET;
        unset($GET["restaurantid"]);
    ?>
            <DIV ID="orders" STYLE="display:none;" CLASS="row">
                <DIV ID="orders_header" CLASS="col-md-12 col-right">
                    Orders for: <SPAN ID="rest_name"></SPAN>
                    <INPUT TYPE="BUTTON" VALUE="View Map" CLASS="vieworders float-right" ONCLICK="vieworders();">
                </DIV>
                <DIV ID="orders_sidebar" CLASS="col-md-4">
                    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
                    <link rel="stylesheet" href="/resources/demos/style.css">
                    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
                    <INPUT TYPE="BUTTON" VALUE="Select Today" ONCLICK="selectdate();">
                    <INPUT TYPE="BUTTON" VALUE="Refresh" ONCLICK="refresh();">
                    <div id="datepicker"></div>
                    <DIV ID="orders_list"></DIV>
                </DIV>
                <DIV ID="orders_content" CLASS="col-md-8 col-right"></DIV>
            </DIV>
        </DIV>
    </DIV>
    <STYLE>
        .rest-dead{
            color: <?= $dead_color; ?>;
        }
        .rest-live{
            color: <?= $live_color; ?>;
        }
        .rest-invalid{
            color: <?= $invalid_color; ?>;
            animation: blinker 1s linear infinite;
        }

        @keyframes blinker {
            50% {
                opacity: 0;
            }
        }

        .full-width{
            width: 100%;
        }
        .full-height{
            height: 43px;
        }

        .vieworders{
            margin-left: auto;
            margin-right: auto;
            display: block;
        }

        INPUT[TYPE=BUTTON]{
            padding-left: 6px !important;
        }

        .col-right{
            padding-right: 16px;
        }
    </STYLE>
    <SCRIPT>
        doreset = false;
        var currentURL = "<?= Request::url(); ?>";
        var locations = <?= json_encode($markers); ?>;
        var islive_icon, isntlive_icon, local_icon;
        var currentrestaurant = <?= $restaurant_id; ?>;
        var APIURL = "<?= webroot('public/list/orders'); ?>";
        var GETQUERY = "<?= http_build_query($GET); ?>";

        function getIcon(pinColor, google) {
            var data = new google.maps.MarkerImage("http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|" + pinColor.replace("#", ''),
                    new google.maps.Size(21, 34), new google.maps.Point(0,0), new google.maps.Point(10, 34));
            data.labelOrigin = {x: 10, y: -10};
            return data;
        }

        function iconcolor(islive){
            if(islive == 1){return islive_icon;}
            if(islive == -1){return local_icon;}
            return isntlive_icon;
        }

        function getColor(islive){
            if(islive == 1){return "<?= $live_color; ?>";}
            if(islive == -1){return "<?= $local_color; ?>";}
            return "<?= $dead_color; ?>";
        }

        $(window).load(function () {
            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 10,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            });

            islive_icon = getIcon(getColor(1), google);
            isntlive_icon = getIcon(getColor(0), google);
            local_icon = getIcon(getColor(-1), google);
            var infowindow = new google.maps.InfoWindow();

            var marker, i;
            var bounds = new google.maps.LatLngBounds();
            for (i = 0; i < locations.length; i++) {
                marker = new google.maps.Marker({
                    label: {
                        text: locations[i][0],
                        fontSize: "16px",
                        fontWeight: "bold",
                        color: getColor(locations[i][4]),
                        strokeColor: "black",
                        strokeWeight: 1
                    },
                    position: new google.maps.LatLng(locations[i][2], locations[i][3]),
                    map: map,
                    icon: iconcolor(locations[i][4])
                });
                locations[i][4] = marker;
                bounds.extend(marker.getPosition());

                google.maps.event.addListener(marker, 'click', (function (marker, i) {
                    return function () {
                        var HTML = '';
                        if (locations[i][5] > -1) {
                            HTML = '<BR><INPUT TYPE="BUTTON" VALUE="View Orders" CLASS="vieworders" ONCLICK="vieworders(' + locations[i][5] + ');">'
                        }
                        infowindow.setContent(locations[i][1] + HTML);
                        infowindow.open(map, marker);
                    }
                })(marker, i));
            }
            map.fitBounds(bounds);
            if (currentrestaurant > -1){
                vieworders(currentrestaurant);
            }
        });

        function clickrest(element){
            new google.maps.event.trigger( locations[element.getAttribute("marker")][4], 'click' );
            return false;
        }

        function search(){
            window.location = currentURL + "?" + serializeaddress();
        }

        function vieworders(restaurant){
            if(isUndefined(restaurant)){
                $("#map").show();
                $("#orders").hide();
            } else {
                currentrestaurant = restaurant;
                $("#map").hide();
                $("#rest_name").text($("#rest_" + restaurant).attr("name"));
                $("#orders_content").html("TESTING");
                $("#orders").show();
                selectdate();

                var URL = currentURL + "?" + GETQUERY;
                if(GETQUERY.length>0){URL += "&";}
                URL += "restaurantid=" + restaurant;
                history.pushState("", document.title, URL);
            }
        }

        $( function() {
            $("#datepicker").datepicker({
                onSelect: function(date) {
                    selectdate(date);
                }
            });
        });

        function refresh(){
            selectdate(toMMDDYYY($( "#datepicker" ).datepicker( "getDate" )));
        }

        function toMMDDYYY(today){
            if(isUndefined(today)){today = new Date();}
            var dd = today.getDate();
            var mm = today.getMonth()+1; //January is 0!
            var yyyy = today.getFullYear();
            if(dd<10) {dd = '0'+dd;}
            if(mm<10) {mm = '0'+mm;}
            return mm + '/' + dd + '/' + yyyy;
        }

        function toHMMSS(today){
            if(isUndefined(today)){today = new Date();}
            var h = today.getHours();
            var am = "AM";
            var mm = today.getMinutes();
            var ss = today.getSeconds();
            if(mm<10) {mm = '0'+mm;}
            if(ss<10) {ss = '0'+mm;}
            if(h == 0){
                h = 12;
            } else if (h > 11){
                am = "PM";
                if (h > 12){h = h - 12;}
            }
            return h + ":" + mm + ":" + ss + " " + am;
        }

        function selectdate(today){//today = mm/dd/yyyy
            //http://api.jqueryui.com/datepicker
            if(isUndefined(today)){
                today = toMMDDYYY();
                $('#datepicker').datepicker('setDate', today);
            }

            //accounts for being before closing time for yesterday's business day
            var actualdate = new Date();
            var is_yesterday = false;
            var currenttime = actualdate.getHours() * 100 + actualdate.getMinutes();
            actualdate = $( "#datepicker" ).datepicker( "getDate" );
            var dayofweek = actualdate.getDay();
            if(testing){
                currenttime = newtime;
                dayofweek = newday;
            }
            dayofweek = dayofweek - 1;
            if(dayofweek == -1){dayofweek = 6;}
            var closingtime = $("#rest_" + currentrestaurant).attr(dayofweek + "_close");
            if(currenttime <= closingtime){
                actualdate.setDate(actualdate.getDate() - 1);
                today = toMMDDYYY(actualdate);
                is_yesterday = true;
            }
            //end yesterday

            $("#orders_list").html("Loading...");
            $("#orders_content").html();
            $.post(APIURL, {
                _token: token,
                action: "getorders",
                restaurant: currentrestaurant,
                date: today
            }, function (result) {
                result = JSON.parse(result).data;
                var listHTML = 'Checked at: ' + toHMMSS();
                var contentHTML = '';
                if(result.length == 0){
                    contentHTML += "No orders found on " + today + " at " + $("#rest_name").text() + '<BR>';
                }
                if(is_yesterday){
                    contentHTML += "(Checking orders for yesterday since it's close to closing for that day)<BR>";
                }
                for(var index = 0; index < result.length; index++){
                    listHTML += '<A HREF="javascript:loadorder(' + result[index].id + ');">View order: ' + index + '<DIV ID="order_' + result[index].id + '" CLASS="order" STYLE="display: none;">' + result[index].html + '</DIV></A><BR>';
                }
                $("#orders_list").html(listHTML);
                $("#orders_content").html(contentHTML);
            });
        }

        function loadorder(OrderID){
            $("#orders_content").html($("#order_" + OrderID).html());
        }
    </SCRIPT>
@endsection