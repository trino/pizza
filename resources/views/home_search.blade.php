@extends('layouts_app')
@section('content')

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="/resources/demos/style.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <STYLE>
        label{
            margin-bottom: 00px;
        }

        .half-width{
            width: 45%;
        }

        input[type=text], input[type=number]{
            padding-left: 5px !important;
            padding-right: 5px !important;
            display: inline-block;
        }

        select{
            font-family: FontAwesome, sans-serif;
        }

        .float-bottom{
            position: absolute;
            bottom: 0px;
        }

        .dont-show{
            display: none !important;
        }

        option:disabled, option[disabled] {
            text-decoration: line-through !important;
            color: red;
        }

        .hasorder{
            background: black;
            color: white;
            cursor: pointer;
        }

        #calendar{
            width: 100%;
        }

        .float-right{
            position: absolute;
            right: 5px;
        }
    </STYLE>

    <DIV CLASS="row">

        @if(read("profiletype") == 2)
            <INPUT TYPE="hidden" ID="rest_id" VALUE="<?= findrestaurant(); ?>">
        @else
            <DIV CLASS="col-md-4">
                Address:
                <?= view("popups_address", array("dontincludeGoogle" => true, "unit" => false, "title" => "", "address" => $_GET, "style" => 2, "findclosest" => true))->render(); ?>
            </DIV>

            <DIV CLASS="col-md-2">
                Max Distance (km):
                <INPUT TYPE="number" MIN="0" MAX="100" ID="max_distance" CLASS="form-control" VALUE="0" ONCHANGE="addresshaschanged();">
            </DIV>

            <DIV CLASS="col-md-2">
                {{ucfirst(storename)}}:
                <SELECT ID="rest_id" CLASS="form-control" ONCHANGE="restchange();">
                    <OPTION VALUE="0">All</OPTION>
                    <?php
                        $restaurants = Query("SELECT restaurants.id, restaurants.name, phone, latitude, longitude, number, unit, street, postalcode, city, province FROM restaurants LEFT JOIN useraddresses ON restaurants.address_id = useraddresses.id ORDER BY name", true, "home_search");
                        $hours = Query("SELECT * FROM hours", true, "home_search");
                        foreach($restaurants as $restaurant){
                            echo '<OPTION VALUE="' . $restaurant["id"] . '" LATITUDE="' . $restaurant["latitude"] . '" LONGITUDE="' . $restaurant["longitude"] . '"' . ' NUMBER="' . $restaurant["number"] . '" UNIT="' . $restaurant["unit"] . '" STREET="' . $restaurant["street"] . '" POSTALCODE="' . $restaurant["postalcode"] . '", CITY="' . $restaurant["city"] . '", PROVINCE="' . $restaurant["province"] . '" PHONE="' . formatphone($restaurant["phone"]) . '">' . $restaurant["name"] . '</OPTION>';
                        }
                    ?>
                </SELECT>
            </DIV>
        @endif

        @if(read("profiletype") == 0)
            <INPUT TYPE="hidden" ID="user_id" VALUE="<?= read("id"); ?>">
        @else
            <DIV CLASS="col-md-2">
                User:
                <SELECT ID="user_id" CLASS="form-control">
                    <OPTION VALUE="0">All</OPTION>
                    <?php
                        $users = Query("SELECT * FROM users ORDER BY profiletype", true, "home_search");
                        foreach($users as $user){
                            echo '<OPTION VALUE="' . $user["id"] . '">';
                            switch ($user["profiletype"]){
                                case 0: echo '&#xf007;'; break; //customer
                                case 1: echo '&#xf234;'; break; //admin
                                case 2: echo '&#xf07a;'; break; //restaurant
                            }
                            echo $user["name"] . '</OPTION>';
                        }
                    ?>
                </SELECT>
            </DIV>
        @endif

        <DIV CLASS="col-md-2">
            Starting Date:
            <INPUT TYPE="TEXT" CLASS="datepicker form-control" ID="datepicker">
        </DIV>

        <DIV CLASS="col-md-2">
            <LABEL><INPUT TYPE="checkbox" ID="useenddate"> Use Ending Date:</LABEL>
            <INPUT TYPE="TEXT" CLASS="datepicker form-control" ID="datepicker_end">
        </DIV>

        <DIV CLASS="col-md-2">
            Search which date:<BR>
            <LABEL><INPUT TYPE="radio" NAME="datetype" VALUE="placed_at" CHECKED>Placed At</LABEL><BR>
            <LABEL><INPUT TYPE="radio" NAME="datetype" VALUE="deliver_at">Deliver At</LABEL>
        </DIV>

        <DIV CLASS="col-md-2">
            Price Range:<BR>
            <INPUT TYPE="NUMBER" MIN="0" MAX="1000" ID="minimum" CLASS="form-control half-width">
            <INPUT TYPE="NUMBER" MIN="0" MAX="1000" ID="maximum" CLASS="form-control half-width">
        </DIV>

        <DIV CLASS="col-md-4">
            Search Term:
            <INPUT TYPE="TEXT" CLASS="form-control" ID="searchterm" ONKEYPRESS="enterbutton(event);">
        </DIV>

        <DIV CLASS="col-md-2">
            <BUTTON CLASS="btn {{btncolor}} float-bottom form-control" ONCLICK="search();">Search</BUTTON>
        </DIV>

        <DIV CLASS="col-md-4">
            <A HREF="javascript:settings()"><i class="fas fa-cog"></i> Settings</A>
            <DIV ID="retain"></DIV>
            <DIV><?= view("popups_googlemaps"); ?></DIV>
        </DIV>
        <DIV ID="orders_list" CLASS="col-md-2"></DIV>
        <DIV CLASS="col-md-6" ID="orders_content">No search results</DIV>
        <DIV CLASS="col-md-6" ID="settings" STYLE="display: none;">
            <H2>Settings:</H2>
            <FORM ID="settingsform">
                <LABEL><INPUT TYPE="checkbox" NAME="loadall"> Load all orders</LABEL><BR>
                @if(read("profiletype") > 0) <LABEL><INPUT TYPE="checkbox" NAME="showdetails"> Show user details</LABEL><BR> @endif
                Sort by:
                <SELECT NAME="sortby" ID="sortby">
                    <OPTION VALUE="id">ID #</OPTION>
                    <OPTION VALUE="price">Price</OPTION>
                    <OPTION VALUE="user_id">User ID #</OPTION>
                    <OPTION VALUE="date">Placed/Deliver at time/date</OPTION>
                </SELECT>
                <LABEL><INPUT TYPE="RADIO" NAME="sortorder" VALUE="ASC" CHECKED>ASC</LABEL>
                <LABEL><INPUT TYPE="RADIO" NAME="sortorder" VALUE="DESC">DESC</LABEL>
            </FORM>
            <BUTTON CLASS="btn {{btncolor}} float-bottom float-right" ONCLICK="save();"><i class="fas fa-cog"></i> Save and apply changes</BUTTON>
        </DIV>
    </DIV>

    <SCRIPT>
        var APIURL = "<?= webroot('public/list/orders'); ?>";
        var restaurant_hours = <?= json_encode($hours); ?>;
        var SETTINGS = [];
        if(hasItem("settings")){
            SETTINGS = JSON.parse( getCookie("settings") );
            for (var key in SETTINGS) {
                switch(key){
                    case "sortby": $("#" + key).val(SETTINGS[key]); break;//select
                    case "sortorder": $("input:radio[name=" + key + "][value=" + SETTINGS[key] + "]").prop("checked", true); break;//radio
                    case "showdetails":case "loadall": if(SETTINGS[key] == "on"){$("input:checkbox[name=" + key + "]").prop("checked", true);} break;//checkbox
                }
            }
        }

        function findhours(restaurantid){
            for(var index = 0; index < restaurant_hours.length; index++){
                if( restaurant_hours[index].restaurant_id == restaurantid ){
                    for(var day = 0; day < 7; day++){
                        restaurant_hours[index][day + "_open"] = parseInt(restaurant_hours[index][day + "_open"]);
                        restaurant_hours[index][day + "_close"] = parseInt(restaurant_hours[index][day + "_close"]);
                    }
                    return restaurant_hours[index];
                }
            }
            return findhours(0);
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

        var hasloaded = false;
        function loadorder(OrderID, ShowIt){
            var html = $("#order_" + OrderID).html();
            showorders();
            if(isUndefined(ShowIt)){ShowIt = true;}
            if(html) {
                $("#orders_content").html(html);
            } else {
                $.post(APIURL, {
                    _token: token,
                    action: "getreceipt",
                    orderid: OrderID,
                    JSON: false,
                    settings: SETTINGS
                }, function (html) {
                    $("#order_" + OrderID).html(html);
                    if(ShowIt){
                        $("#orders_content").html(html);
                        hasloaded=true;
                    }
                    $("#aorder" + OrderID).attr("loaded", "yes");
                    $("#aorder" + OrderID).prepend('<i class="far fa-save"></i> ');
                    loadnextorder();
                });
            }
        }

        $( function() {
            $(".datepicker").datepicker();
            setDate(".datepicker");
            restchange();
            loading(false, "page done");
        } );

        function getDate(picker_selector){
            return toMMDDYYY($(picker_selector).datepicker("getDate"));
        }

        function setDate(picker_selector, date, month, year){
            if(isUndefined(date)){
                date = toMMDDYYY();
            }
            if(!isUndefined(month) && !isUndefined(year)){
                date = new Date(year, month - 1, date);// months are 0-based!
            }
            $(picker_selector).datepicker('setDate', date);
        }

        function enterbutton(e){
            if (e.keyCode == 13) {
                search();
            }
        }

        function selectedradio(name){
            return $("input[name=" + name + "]").filter(":checked").val();
        }

        function search(){
            var today = getDate("#datepicker");
            var actualdate = new Date();
            var is_yesterday = false;
            var currenttime = actualdate.getHours() * 100 + actualdate.getMinutes();
            actualdate = $( "#datepicker" ).datepicker( "getDate" );
            var enddate = toMMDDYYY($( "#datepicker_end" ).datepicker( "getDate" ));
            var dayofweek = actualdate.getDay();
            var useend = $("#useenddate").prop("checked");
            var searchterm = $("#searchterm").val().trim();
            var currentrestaurant = $( "#rest_id").val();

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

            hasloaded = false;
            $("#orders_list").html("");
            $("#orders_content").html("Loading...");
            $.post(APIURL, {
                _token: token,
                action: "getorders",
                restaurant: currentrestaurant,
                userid: $("#user_id").val(),
                date: today,
                enddate: enddate,
                useend: useend,
                search: searchterm,
                minimum: $("#minimum").val(),
                maximum: $("#maximum").val(),
                datetype: selectedradio("datetype"),
                settings: SETTINGS
            }, function (result) {
                result = JSON.parse(result);
                var listHTML = 'Checked at: ' + toHMMSS() + '<BR>';
                var contentHTML = '';
                if(result.data.length == 0){
                    if(useend){
                        contentHTML += "No orders found between " + result.startdate + " and " + result.enddate;
                    } else {
                        contentHTML += "No orders found on " + result.startdate;
                    }
                    if(searchterm.length > 0){contentHTML += " containing '" + searchterm + "'";}
                    contentHTML += " at " + $("#rest_name").text() + "<BR>";
                    if(result.hasOwnProperty("query")) {contentHTML += "SQL: " + result.query + "<BR>";}
                }
                if(is_yesterday){
                    contentHTML += "(Checking orders for yesterday since it's close to closing for that day)<BR>";
                }

                var actualindex = 1;
                for(var index = 0; index < result.data.length; index++){
                    //if(result.data[index].hasOwnProperty("html")) {
                    @if(debugmode) actualindex = result.data[index].id; @endif
                        var orderid = result.data[index].id;
                        listHTML += '<A ID="aorder' + orderid + '" loaded="no" orderid="' + orderid + '" CLASS="aorder" HREF="javascript:loadorder(' + orderid + ');">Order: ' + actualindex + " ($" + result.data[index].price + ')<DIV ID="order_' + orderid + '" CLASS="order" STYLE="display: none;"></DIV></A><BR>';
                    //result.data[index].html
                    actualindex++;
                    //}
                }
                $("#orders_list").html(listHTML);
                $("#orders_content").html(contentHTML);
                loadnextorder();
            });
        }

        function setting(name){
            if(SETTINGS.hasOwnProperty(name)){
                return SETTINGS[name];
            }
        }

        function loadnextorder(){
            if(setting("loadall")) {
                var orderid = $(".aorder[loaded=no]").first().attr("orderid");
                if(!isNaN(orderid) && !isUndefined(orderid)){
                    loadorder(orderid, !hasloaded);
                }
            }
        }

        //address searching
        function addresshaschanged(place){
            var latitude, longitude, found = 0, max_distance = $("#max_distance").val(), temp_distance;
            latitude = parseFloat($("#add_latitude").val());
            longitude = parseFloat($("#add_longitude").val());
            if(isNaN(latitude) || isNaN(longitude)){max_distance = 0;}

            setAll("rest_id");
            if(max_distance == 0) {
                $("#rest_id option").each(function (index) {
                    $(this).removeAttr("hidden");
                });
            } else {
                $("#rest_id option").each(function (index) {
                    if (index > 0) {
                        var latitude2 = parseFloat($(this).attr("latitude")), longitude2 = parseFloat($(this).attr("longitude"));
                        temp_distance = distance(latitude, longitude, latitude2, longitude2);
                        $(this).attr("distance", temp_distance);//"Distance between " + latitude + "," + longitude + " and " + latitude2 + "," + longitude2 + " is " + temp_distance);
                        if (temp_distance <= max_distance) {
                            found++;
                            $(this).removeAttr("hidden");
                        } else {
                            $(this).attr("hidden", true);
                        }
                    }
                });
                if(found == 0){
                    setAll("rest_id", "No {{storenames}} found");
                }
            }
            loadmap();
        }

        function setAll(ID, Title){
            if(isUndefined(Title)){Title = "All";}
            $("#" + ID + " option[value=0]").html(Title);
        }

        function restchange(currentrestaurant){
            if(isUndefined(currentrestaurant)) {
                currentrestaurant = $("#rest_id").val();
            } else {
                $("#rest_id").val(currentrestaurant);
            }
            var current_date = new Date();
            var firstday = new Date(current_date.getFullYear(),current_date.getMonth(),1);
            var lastday  = daysInMonth(current_date.getMonth(), current_date.getFullYear());
            var useend = $("#useenddate").prop("checked");

            $.post(APIURL, {
                _token: token,
                action: "getorders",
                restaurant: currentrestaurant,
                userid: $("#user_id").val(),
                date: toMMDDYYY(firstday),
                enddate: toMMDDYYY(lastday),
                useend: useend,
                datetype: selectedradio("datetype"),
                settings: SETTINGS
            }, function (result) {
                result = JSON.parse(result);
                const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
                var month = firstday.getMonth() + 1;
                var year = firstday.getFullYear();
                var HTML = '<TABLE ID="calendar" BORDER="1"><TR><TD COLSPAN="7" ALIGN="CENTER"><STRONG>' + monthNames[month] + '</STRONG></TD></TR><TR>';
                HTML += '<TH>S</TH><TH>M</TH><TH>T</TH><TH>W</TH><TH>T</TH><TH>F</TH><TH>S</TH></TR>'.replaceAll("<TH>", '<TD ALIGN="CENTER"><STRONG>').replaceAll('</TH>', '</STRONG></TD>');
                var dayofweek = firstday.getDay();
                var hours = findhours(currentrestaurant);
                for(current_date = 1; current_date <= lastday.getDate(); current_date++){
                    if(current_date == 1) {
                        if (dayofweek > 0) {
                            HTML += '<TR><TD COLSPAN="' + dayofweek + '"></TD>';
                        }
                    }
                    if(dayofweek == 0){
                        HTML += '<TR>';
                    }
                    HTML += '<TD ALIGN="RIGHT"';
                    if(hasOrders(result.data, current_date, dayofweek, hours)){
                        HTML += ' CLASS="hasorder" ONCLICK="loaddate(' + current_date + ', ' + month + ', ' + year + ');"';
                    }
                    HTML += '>' + current_date + '</TD>';
                    if (dayofweek == 6){
                        HTML += '</TR>';
                    }
                    dayofweek = (dayofweek + 1) % 7;
                }
                if (dayofweek > 0 && dayofweek < 6) {
                    HTML += '<TD COLSPAN="' + (7-dayofweek) + '"></TD></TR>';
                }
                $("#calendar").remove();
                $("#retain").prepend(HTML + '</TABLE>');
            });
        }

        function loaddate(day, month, year){
            $("#useenddate").prop("checked", false);//07/24/2018
            setDate("#datepicker", day, month, year);
            search();
        }

        function getHMMint(date){
            var hours = date.getHours();
            var minutes = date.getMinutes();
            return hours * 100 + minutes;
        }

        function hasOrders(result, current_date, dayofweek, hours){
            if(result.length == 0){return false;}
            var yesterday_ofweek = dayofweek - 1;
            if(yesterday_ofweek < 0){yesterday_ofweek = 6;}
            for(var index = 0; index < result.length; index++){
                var data = result[index].date;//2018-07-16 11:53:23
                var date = new Date(data);
                var date_dayofweek = date.getDay();
                var time = getHMMint(date);
                var IsYesterday = hours[yesterday_ofweek + "_close"] < hours[yesterday_ofweek + "_open"] && hours[yesterday_ofweek + "_close"] > time;
                //result=JSON from server, current_date=1 to 31, dayofweek=0 to 6
                //hours={"0_open":"1100","0_close":"300","1_open":"1100","1_close":"300","2_open":"1100","2_close":"300","3_open":"1100","3_close":"300","4_open":"1100","4_close":"2300","5_open":"1000","5_close":"2300","6_open":"1100","6_close":"2300"}
                if( date.getDate() == current_date -1 ){
                    if(IsYesterday){
                        return true;
                    }
                } else if (date.getDate() == current_date){
                    if(!IsYesterday){
                        return true;
                    }
                }
            }
            return false;
        }

        function outerHTML(selector){
            selector = $(selector);
            if(!isUndefined(selector) && selector.length > 0) {
                return selector[0].outerHTML;
            }
            return "";
        }

        function daysInMonth (month, year) {
            return new Date(year, month+1, 0);
        }

        function distance(lat1, lon1, lat2, lon2, unit) {
            //unit: 'M' is statute miles, 'K' is kilometers (default), 'N' is nautical miles
            if(isUndefined(unit)){unit = "K";}
            var radlat1 = Math.PI * lat1/180;
            var radlat2 = Math.PI * lat2/180;
            var theta = lon1-lon2;
            var radtheta = Math.PI * theta/180;
            var dist = Math.sin(radlat1) * Math.sin(radlat2) + Math.cos(radlat1) * Math.cos(radlat2) * Math.cos(radtheta);
            if (dist > 1) {dist = 1;}
            dist = Math.acos(dist);
            dist = dist * 180/Math.PI;
            dist = dist * 60 * 1.1515;
            if (unit=="K") { dist = dist * 1.609344; }
            if (unit=="N") { dist = dist * 0.8684; }
            return dist;
        }

        function formataddress(element){
            //<OPTION VALUE="' . $restaurant["id"] . '" LATITUDE="' . $restaurant["latitude"] . '" LONGITUDE="' . $restaurant["longitude"] . '"' . ' NUMBER="' . $restaurant["number"] . '" UNIT="' . $restaurant["unit"] . '" STREET="' . $restaurant["street"] . '" POSTALCODE="' . $restaurant["postalcode"] . '", CITY="' . $restaurant["city"] . '", PROVINCE="' . $restaurant["province"] . '" PHONE="' . formatphone($restaurant["phone"]) . '
            var unit = "";
            if(element.attr("unit")){
                unit  = " (Unit: " + element.attr("unit") + ")";
            }
            return element.attr("number") + unit + " " + element.attr("street") + ", " + element.attr("city") + ", " + element.attr("province") + " " + element.attr("postalcode");
        }

        $( document ).ready(function() {
            setTimeout(function(){
                loadmap();
            }, 500);
        });

        function loadmap(){
            var locations = [];//["Queens Pizza and Wings","Queens Pizza and Wings<BR>Phone: (905) 577-0900<BR>Address: 178 Queen St S, Hamilton, ON L8P 3S7<BR>Hours: Open: 11:00 AM - Close: 3:00 AM<BR>Is live: Yes","43.253782","-79.881281","1" (1 if islive, 0 if not),"4" (restaurant id)]
            $("#rest_id option").each(function (index) {
                if (index > 0) {
                    if(!$(this).hasAttr("hidden")) {
                        var latitude = parseFloat($(this).attr("latitude")), longitude = parseFloat($(this).attr("longitude"));
                        locations.push([$(this).html(), $(this).html() + "<BR>Phone: " + $(this).attr("phone") + "<BR>Address: " + formataddress($(this)), latitude, longitude, 1, $(this).val()]);
                    }
                }
            });

            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 10,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            });
            var infowindow = new google.maps.InfoWindow();

            var marker, i;
            var bounds = new google.maps.LatLngBounds();
            for (i = 0; i < locations.length; i++) {
                marker = new google.maps.Marker({
                    label: {
                        text: locations[i][0],
                        fontSize: "16px",
                        fontWeight: "bold",
                        strokeColor: "black",
                        strokeWeight: 1
                    },
                    position: new google.maps.LatLng(locations[i][2], locations[i][3]),
                    map: map
                });
                locations[i][4] = marker;
                bounds.extend(marker.getPosition());
                google.maps.event.addListener(marker, 'click', (function (marker, i) {
                    return function () {
                        var HTML = '';
                        if (locations[i][5] > -1) {
                            HTML = '<BR><INPUT TYPE="BUTTON" VALUE="View Orders" CLASS="vieworders" ONCLICK="restchange(' + locations[i][5] + ');">'
                        }
                        infowindow.setContent(locations[i][1] + HTML);
                        infowindow.open(map, marker);
                    }
                })(marker, i));
            }
            map.fitBounds(bounds);
        }

        function scrolltobottom(){
            window.location.href = "<?= webroot(''); ?>";
        }

        function settings(){
            $("#orders_content").hide();
            $("#settings").show();
        }

        function save(){
            SETTINGS = getform("#settingsform");
            createCookieValue("settings", JSON.stringify(SETTINGS));
            showorders();
            alert("Settings saved");
        }

        function showorders(){
            $("#settings").hide();
            $("#orders_content").show();
        }
    </SCRIPT>
@endsection