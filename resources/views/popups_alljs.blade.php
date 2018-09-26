<?php
    startfile("popups_alljs");
    $CURRENT_YEAR = date("Y");
    $STREET_FORMAT = "[number] [street], [city] [postalcode]";
    //["id", "value", "user_id", "number", "unit", "buzzcode", "street", "postalcode", "city", "province", "latitude", "longitude", "phone"];
    $scripts = webroot("public/scripts");
    $nprog = "#F0AD4E";//color for loading bar
?>
<script>
    var debugmode = "<?= debugmode; ?>";

    function log(text) {
        @if(debugmode) console.log(text); @endif
        return text;
    }

    !function (e) {
        var a = e, r = "undefined" != typeof window && window, f = {
            frameLoaded: 0,
            frameTry: 0,
            frameTime: 0,
            frameDetect: null,
            frameSrc: null,
            frameCallBack: null,
            frameThis: null,
            frameNavigator: window.navigator.userAgent,
            frameDelay: 0,
            frameDataSrc: "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAANSURBVBhXYzh8+PB/AAffA0nNPuCLAAAAAElFTkSuQmCC"
        };
        a.fn.backDetect = function (e, r) {
            f.frameThis = this, f.frameCallBack = e, null !== r && (f.frameDelay = r), f.frameNavigator.indexOf("MSIE ") > -1 || f.frameNavigator.indexOf("Trident") > -1 ? setTimeout(function () {
                a('<iframe src="' + f.frameDataSrc + '?loading" style="display:none;" id="backDetectFrame" onload="jQuery.fn.frameInit();""></iframe>').appendTo(f.frameThis)
            }, f.frameDelay) : setTimeout(function () {
                a("<iframe src='about:blank?loading' style='display:none;' id='backDetectFrame' onload='jQuery.fn.frameInit();'></iframe>").appendTo(f.frameThis)
            }, f.frameDelay)
        }, a.fn.frameInit = function () {
            f.frameDetect = document.getElementById("backDetectFrame"), f.frameLoaded > 1 && 2 === f.frameLoaded && (f.frameCallBack.call(this), r.history.go(-1)), f.frameLoaded += 1, 1 === f.frameLoaded && (f.frameTime = setTimeout(function () {
                e.fn.setupFrames()
            }, 500))
        }, a.fn.setupFrames = function () {
            clearTimeout(f.frameTime), f.frameSrc = f.frameDetect.src, 1 === f.frameLoaded && -1 === f.frameSrc.indexOf("historyLoaded") && (f.frameNavigator.indexOf("MSIE ") > -1 || f.frameNavigator.indexOf("Trident") > -1 ? f.frameDetect.src = f.frameDataSrc + "?historyLoaded" : f.frameDetect.src = "about:blank?historyLoaded")
        }
    }(jQuery);
</script>
<?php
includefile("public/scripts/api.js");
?>
<STYLE>
    /* STOP MOVING THIS TO THE CSS, IT WON'T WORK! */
    #oldloadingmodal {
        display: none;
        position: fixed;
        z-index: 1000;
        top: 0;
        left: 0;
        height: 100%;
        width: 100%;
        background: rgba(0, 0, 0, .6) url('<?= webroot("public/images/slice.gif"); ?>') 50% 50% no-repeat;
    }

    #loading {
        z-index: 999999;
    }

    #nprogress {
        pointer-events: none;
    }

    #nprogress .bar {
        background: <?= $nprog; ?>;
        position: fixed;
        z-index: 1999999;
        top: 0;
        left: 0;
        width: 100%;
        height: 5px;
    }

    #nprogress .peg {
        display: block;
        position: absolute;
        right: 0px;
        width: 100px;
        height: 100%;
        box-shadow: 0 0 5px <?= $nprog; ?>, 0 0 5px<?= $nprog; ?>;
        opacity: 1.0;
        -webkit-transform: rotate(3deg) translate(0px, -4px);
        -ms-transform: rotate(3deg) translate(0px, -4px);
        transform: rotate(3deg) translate(0px, -4px);
    }

    /*#nprogress .spinner{display:block;position:fixed;z-index:11111;top:15px;right:15px;}
    #nprogress .spinner-icon{width:18px;height:18px;box-sizing:border-box;border:solid 2px transparent;border-top-color:
    <?= $nprog; ?> ;border-left-color:
    <?= $nprog; ?> ;border-radius:50%;-webkit-animation:nprogress-spinner 400ms linear infinite;animation:nprogress-spinner 400ms linear infinite;}*/
    .nprogress-custom-parent {
        overflow: hidden;
        position: relative;
    }

    .nprogress-custom-parent #nprogress .spinner, .nprogress-custom-parent #nprogress .bar {
        position: absolute;
    }

    @-webkit-keyframes nprogress-spinner {
        0% {
            -webkit-transform: rotate(0deg);
        }
        100% {
            -webkit-transform: rotate(360deg);
        }
    }

    @keyframes nprogress-spinner {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }
</STYLE>
<script>
    var currentURL = "<?= Request::url(); ?>";
    var debugmode = '<?= !islive(); ?>' == '1';
    var timestampoffset;
    timestampoffset = parseInt('<?= time(); ?>') - totimestamp();
    var database = "<?= database; ?>";

    function makestring(Text, Variables) {
        if (Text.startswith("{") && Text.endswith("}")) {
            Text = unikeys[Text.mid(1, Text.length - 2)];
        }
        <?php
            foreach($GLOBALS["settings"] as $key => $value){
                echo 'Text = Text.replaceAll("{' . $key . '}", "' . $value . '");';
            }
        ?>
        if (!isUndefined(Variables)) {
            if(isObject(Variables)) {
                var keys = Object.keys(Variables);
                for (var i = 0; i < keys.length; i++) {
                    var key = keys[i];
                    var value = Variables[key];
                    Text = Text.replaceAll("\\[" + key + "\\]", value);
                }
            } else {
                if(!isArray(Variables)){Variables = [Variables];}
                for (var i = 0; i < Variables.length; i++) {
                    var value = Variables[i];
                    Text = Text.replaceAll("\\[" + i + "\\]", value);
                }
            }
        }
        return Text;
    }

    function userphonenumber() {
        @if(needsphonenumber())
            return $("#order_phone").val();
        @else
            return "<?= read("phone"); ?>";
        @endif
    }

    $(document).ready(function () {
        if (getCookie("theorder")) {
            theorder = JSON.parse(getCookie("theorder"));
        }
        generatereceipt();
        $('[data-popup-close]').on('click', function (e) {
            var targeted_popup_class = jQuery(this).attr('data-popup-close');
            $('#' + targeted_popup_class).modal("hide");
        });
    });

    function storeorders() {
        $("#profilemodal").modal("hide");
        var HTML = '<INPUT TYPE="button" VALUE="Your orders" CLASS="btn btn-sm btn-secondary half-width" ONCLICK="orders();"><INPUT TYPE="button" VALUE="Store orders" CLASS="btn btn-sm btn-primary half-width" ONCLICK="storeorders();"><BR><ul class="list-group" ID="ordersHTML">';
        if (userdetails.hasOwnProperty("storeorders")) {
            HTML += userdetails["storeorders"];
        } else {
            $.post("<?= webroot('public/list/orders'); ?>", {
                _token: token,
                action: "getrecentorders"
            }, function (result) {
                var HTML = "";
                result = JSON.parse(result);
                for (var i = 0; i < result["data"].length; i++) {
                    var order = result["data"][i];
                    var ID = order["id"];
                    HTML += '<li ONCLICK="orders(' + ID + ');"><span class="text-danger strong">ORDER # ' + ID + ' </span><br>' + order["placed_at"] + '<br><DIV ID="pastreceipt' + ID + '">' + order["html"] + '</DIV></li>';
                }
                userdetails["storeorders"] = HTML;
                $("#ordersHTML").html(HTML);
            });
        }
        alert(HTML + '</ul>', 'Orders');
    }

    //handles the orders list modal
    function orders(ID, getJSON) {
        if (isUndefined(ID)) {//no ID specified, get a list of order IDs from the user's profile and make buttons
            $("#profilemodal").modal("hide");
            var HTML = '';// '<INPUT TYPE="button" VALUE="Your orders" CLASS="btn btn-sm btn-primary half-width" ONCLICK="orders();"><INPUT TYPE="button" VALUE="Store orders" CLASS="btn btn-sm btn-secondary half-width" ONCLICK="storeorders();"><BR>';
            var First = false;
            if (userdetails["Orders"].length > 0) {
                HTML += '<ul class="list-group">';
                for (var i = 0; i < userdetails["Orders"].length; i++) {
                    var order = userdetails["Orders"][i];
                    ID = order["id"];
                    if (!First) {
                        First = ID;
                    }
                    HTML += '<li ONCLICK="orders(' + ID + ');"><span class="text-danger strong">ORDER # ' + ID + ' </span><br>' + order["placed_at"] + '<br><DIV ID="pastreceipt' + ID + '"></DIV></li>';
                }
                HTML += '</ul>';
            } else {
                HTML += "No orders placed yet";
            }
            alert(HTML, 'Orders');
            if (First) {
                orders(First);//userdetails["Orders"]);
            }
        } else {
            if (isUndefined(getJSON)) {
                getJSON = false;
            }
            var Index = getIterator(userdetails["Orders"], "id", ID);
            if (!getJSON && userdetails["Orders"][Index].hasOwnProperty("Contents")) {
                $("#pastreceipt" + ID).html(userdetails["Orders"][Index]["Contents"]);
                GetNextOrder(ID);
                return;
            }
            if (getJSON) {
                CloseModal("LoadOrder");// $("#alertmodal").modal('hide');
            }
            $.post("<?= webroot('public/list/orders'); ?>", {
                _token: token,
                action: "getreceipt",
                orderid: ID,
                JSON: getJSON,
                party: "private"
            }, function (result) {
                if (getJSON) {
                    //JSON recieved, put it in the order
                    result = JSON.parse(result);
                    theorder = result["Order"];
                    $("#cookingnotes").val(result["cookingnotes"]);
                    generatereceipt();
                    scrolltobottom();
                } else {//HTML recieved, put it in the pastreceipt element
                    skipunloadingscreen = true;
                    setTimeout(function () {
                        loading(true, "SHOWRESULT");
                    }, 10);
                    $("#pastreceipt" + ID).html(result);
                    if (Index > -1) {
                        userdetails["Orders"][Index]["Contents"] = result;
                    }
                    GetNextOrder(ID);
                }
            });
        }
    }

    function reloadpage(hardway) {
        var allowredirect = true;
        if (isUndefined(hardway)) {
            hardway = false;
        }
        log("reloadpage: " + allowredirect + " " + hardway);
        if (allowredirect) {
            if (hardway) {
                window.location = "<?= webroot("", true); ?>";
            } else {
                location.reload();
            }
        }
    }

    var firstsignin = false;
    function handlelogin(action) {
        if (isUndefined(action)) {
            action = "verify";
        }
        if ($("#login_email").length > 0) {
            $("#login_email").val($("#login_email").val().trim());
        }
        if (action !== "logout" && $("#login_email").length > 0) {
            if (!$("#login_email").valid()) {
                return validateinput("#login_email", makestring("{email_needed}"));
            }
        }
        skipunloadingscreen = true;
        var redirectonlogin = false;
        var redirectonlogout = false;
        $.post(webroot + "auth/login", {
            action: action,
            _token: token,
            email: $("#login_email").val(),
            password: $("#login_password").val()
        }, function (result) {
            try {
                var data = JSON.parse(result);
                skipunloadingscreen = false;
                log("ACTION: " + action + " STATUS: " + data["Status"] + " REASON: " + data["Reason"]);
                if (data["Status"] == "false" || !data["Status"]) {
                    data["Reason"] = data["Reason"].replace('[verify]', '<A onclick="handlelogin();" CLASS="hyperlink" TITLE="Click here to resend the email">verify</A>');
                    validateinput();
                    switch (action) {
                        case "login":
                            validateinput("#login_email", false);
                            validateinput("#login_password", data["Reason"]);
                            break;
                        case "forgotpassword":
                        case "verify":
                            validateinput("#login_email", data["Reason"]);
                            break;
                        case "logout":
                            log("logging out");
                            break;
                        default:
                            ajaxerror(data["Reason"], makestring("{error_login}"));
                    }
                } else {
                    switch (action) {
                        case "login":
                            token = data["Token"];
                            if (!login(data["User"], true)) {
                                redirectonlogin = false;
                            }
                            $("#loginmodal").modal("hide");
                            if (redirectonlogin) {
                                skipunloadingscreen = true;
                                reloadpage();
                            } else if(firstsignin) {
                                toast("Welcome, " + userdetails["name"]);
                            } else {
                                toast("Welcome back, " + userdetails["name"]);
                            }
                            break;
                        case "forgotpassword":
                        case "verify":
                            validateinput("#login_email", data["Reason"]);
                            break;
                        case "logout":
                            removeCookie();
                            logout();
                            if (redirectonlogout) {
                                skipunloadingscreen = true;
                                reloadpage(true);
                            } else {
                                switch (currentRoute) {
                                    case "index"://resave order as it's deleted in removeCookie();
                                        if (!isUndefined(theorder)) {
                                            if (theorder.length > 0) {
                                                createCookieValue("theorder", JSON.stringify(theorder));
                                            }
                                        }
                                        break;
                                }
                            }
                            if (!isUndefined(collapsecheckout)) {
                                collapsecheckout();
                            }
                            toast("You are logged out");
                            break;
                    }
                }
            } catch (err) {
                ajaxerror(err.message + "<BR>" + result, makestring("{error_login}"));
            }
        });
    }

    function logout(){
        $('[class^="session_"]').text("");
        $(".loggedin").hide();
        $(".loggedout").show();
        $(".clear_loggedout").html("");
        $(".profiletype").hide();
        userdetails = false;
        firstsignin = false;
        if(isIndex()){showlogin("logout");}
        handlelinks();
    }

    var generalhours = <?= json_encode(gethours()) ?>;

    $(document).ready(function () {
        //make every AJAX request show the loading animation
        $body = $("body");

        $('.modal').on('hidden.bs.modal', function () {
            reseturl("hidden.bs.modal");//clean #modal from url
        });

        $(document).on({
            ajaxStart: function () {
                //ajaxSend: function ( event, jqxhr, settings ) {log("settings.url: " + settings.url);//use this event if you need the URL
                if (skiploadingscreen) {
                    if (!lockloading) {
                        skiploadingscreen = false;
                    }
                } else {
                    loading(true, "ajaxStart");
                }
            },
            ajaxStop: function () {
                if (!skipunloadingscreen) {
                    loading(false, "ajaxStop");
                }
                skipone = Date.now() + 100;//
            }
        });

        @if(isset($user) && $user)
            login(<?= json_encode($user); ?>, false); //user is already logged in, use the data
        @else
            logout();
        @endif

        var HTML = '';
        var todaysdate = isopen(generalhours);
        if (todaysdate == -1) {
            HTML = 'Currently closed';
            todaysdate = getToday();
            if (generalhours[todaysdate].open > now()) {
                HTML = 'Opens at: ' + GenerateTime(generalhours[todaysdate].open);
            }
        } else {
            HTML = 'Open until: ' + GenerateTime(generalhours[todaysdate].close);
        }
        GenerateHours(generalhours);
        $("#openingtime").html(HTML);
    });

    function isIndex(){
        return currentRoute == "/" || currentRoute == "index";
    }

    function showlogin(Why){
        if(isUndefined(Why)){Why = "Unknown";}
        log("showlogin: " + Why + " route: " + currentRoute + " isIndex: " + isIndex() + " Islogged in:" + userisloggedin());
        if(isIndex()){
            if(!userisloggedin()) {
                $("#loginmodal").modal("show");
                $("#logintab").click();
                suppressback = Date.now() + 200;
            }
        } else if(Why == "login") {
            window.location = webroot;
        }
    }

    var links = "";
    function handlelinks(){
        if(links.length == 0){links = $(".profiletype1").html();}
        if(userdetails == false || userdetails["profiletype"] != 1){
            $(".profiletype1").html("");
        } else {
            $(".profiletype1").html(links);
        }
    }

    //handle a user login
    function login(user, isJSON) {
        if (isUndefined(user)) {
            user = userdetails;
        } else {
            userdetails = user;
        }
        var keys = Object.keys(user);
        for (var i = 0; i < keys.length; i++) {
            var key = keys[i];
            var val = user[key];
            createCookieValue("session_" + key, val);//save data to cookie
            $(".session_" + key).text(val);//set elements text to data
            $(".session_" + key + "_val").val(val);//set elements value to data
        }
        $(".loggedin").show();//show loggedin class
        $(".loggedout").hide();//hide loggedout class
        $(".profiletype").hide();//hide all profile type clasdses
        $(".profiletype" + user["profiletype"]).show();//show classes for this profile type

        handlelinks();

        $(".profiletype_not").show();
        $(".profiletype_not" + user["profiletype"]).hide();

        var HTML = 'form-control saveaddresses" id="saveaddresses" onchange="addresschanged(' + "'saveaddress'" + ');"><OPTION value="0">Select Delivery Address</OPTION>';
        var FirstAddress = false;

        if (user["Addresses"].length > 0) {
            HTML = '<SELECT class="' + HTML;
            addresskeys = Object.keys(user["Addresses"][0]);
            for (i = 0; i < user["Addresses"].length; i++) {
                if (!FirstAddress) {
                    FirstAddress = user["Addresses"][i]["id"];
                }
                HTML += AddressToOption(user["Addresses"][i], addresskeys);
            }
            HTML += '</SELECT>';
        } else {
            HTML = '<SELECT class="dont-show ' + HTML + '</SELECT>';
        }
        $(".addressdropdown").html(HTML);
        if (user["profiletype"] == 2) {
            user["restaurant_id"] = FirstAddress;
            var URL = '<?= webroot("public/list/orders"); ?>';
            if (window.location.href != URL && isJSON) {
                redirectonlogin = false;
                window.location.href = URL;
                return false;
            }
        }
        if(needscheckout){showcheckout();}
        return true;
    }

    //convert an address to a dropdown option
    function AddressToOption(address, addresskeys) {
        if (isUndefined(addresskeys)) {
            addresskeys = ["id", "value", "user_id", "number", "unit", "buzzcode", "street", "postalcode", "city", "province", "latitude", "longitude", "phone"];
        }
        var tempHTML = '<OPTION';
        var streetformat = "<?= $STREET_FORMAT; ?>";
        if (address["unit"].trim()) {
            streetformat = streetformat + " - [unit]";
        }
        for (var keyID = 0; keyID < addresskeys.length; keyID++) {
            var keyname = addresskeys[keyID];
            if (address.hasOwnProperty(keyname)) {
                var value = address[keyname];
                streetformat = streetformat.replace("[" + keyname + "]", value);
                if (keyname == "id") {
                    keyname = "value";
                }
                tempHTML += ' ' + keyname + '="' + value + '"'
            }
        }
        return tempHTML + '>' + streetformat + '</OPTION>';
    }

    //address dropdown changed
    function addresschanged(why) {
        clearphone("addresschanged - " + why);
        var Selected = $("#saveaddresses option:selected");
        var SelectedVal = $(Selected).val();
        var Text = '<?= $STREET_FORMAT; ?>';
        visible_address(false);
        $("#add_unit").hide();
        if (addresskeys.length == 0) {
            addresskeys = ["id", "value", "user_id", "number", "unit", "buzzcode", "street", "postalcode", "city", "province", "latitude", "longitude", "phone"];
        }
        for (var keyID = 0; keyID < addresskeys.length; keyID++) {
            var keyname = addresskeys[keyID];
            if (SelectedVal == 0) {
                var keyvalue = "";
            } else {
                var keyvalue = $(Selected).attr(keyname);
            }
            Text = Text.replace("[" + keyname + "]", keyvalue);
            $("#add_" + keyname).val(keyvalue);
        }
        $("#ffaddress").hide();
        clearvalidation("#red_address");
        refreshform("#saveaddresses").trigger("click");
        if (SelectedVal == 0) {
            Text = '';
        } else {
            $("#formatted_address").hide();
            if (SelectedVal == "addaddress") {
                visible_address(true);
                $("#add_unit").show();
                //focuson(getGoogleAddressSelector(true));
                Text = "";
                handlefirefox("addresschanged:" + why);
            }
        }
        $("#formatted_address").val(Text);
        $("#restaurant").html('<OPTION VALUE="0" SELECTED>{{ucfirst(storename)}}</OPTION>');
        addresshaschanged();
    }

    function validtime(Time) {
        var Hours = Math.floor(Time / 100);
        var Minutes = Time % 100;
        while (Minutes > 59) {
            Minutes = Minutes - 60;
            Hours += 1;
        }
        while (Hours > 23) {
            Hours = Hours - 24;
        }
        return Hours + "" + Minutes;
    }

    function validdayofweek(day) {
        return day % 7;
    }

    function verbosedate(date, today, today_text, tomorrow, tomor_text, time) {
        var dayofweek = date.getDay();
        var thetime = " at " + GenerateTime(time);
        if (dayofweek == today) {
            return today_text + thetime;
        } else if (dayofweek == tomorrow) {
            return tomor_text + thetime;
        } else {
            return daysofweek[dayofweek] + " " + monthnames[date.getMonth()] + " " + date.getDate() + thetime;
        }
    }


    function GenerateHours(hours, increments) {
        //doesn't take into account <= because it takes more than 1 minute to place an order
        //now.setMinutes(now.getMinutes() + minutes);//start 40 minutes ahead
        if (isUndefined(increments)) {
            increments = 15;
        }
        var minutes = <?= getdeliverytime(); ?>;
        var dayofweek = getNow(3);//day of week (virtual)
        var minutesinaday = 1440;
        var totaldays = 2;
        var dayselapsed = 0;
        var today = getNow(3, false);//day of week (actual)
        var tomorrow = validdayofweek(today + 1);
        var now = getNow(4, false);//date (today, actual)
        var tomorrowdate = getNow(5, false);//date (tomorrow, actual)
        var today_text = "Today, " + monthnames[now.getMonth()] + " " + now.getDate();
        var tomor_text = "Tomorrow, " + monthnames[tomorrowdate.getMonth()] + " " + tomorrowdate.getDate();
        now = getNow(4);//date (today, virtual)

        var time = getNow(2);//24 hour time (virtual)
        var oldtime = time;
        time = validtime(time + (increments - (time % increments)));

        var oldValue = $("#deliverytime").val();
        var HTML = '';
        var temp = gettime(now, minutes, 15, time);
        if (time < oldtime) {
            dayofweek = validdayofweek(dayofweek + 1);
        }
        log("GenerateHours: " + temp + " Today: " + today + " (" + today_text + ") Tomorrow: " + tomorrow + " (" + tomor_text + ")");
        now = temp[0];
        time = temp[1];

        dayofweek = now.getDay();
        if (isopen(hours, dayofweek, temp[2]) > -1) {
            thedayname = verbosedate(now, today, today_text, tomorrow, tomor_text, time);
            HTML = '<option value="Deliver Now" timestamp="' + totimestamp(time, now) + '">Deliver ASAP (' + thedayname + ')</option>';
            time = addtotime(time, increments);
        }
        var thetime, minutes, thedayname, thedate;
        var totalInc = (minutesinaday * totaldays) / increments;

        for (var i = 0; i < totalInc; i++) {
            if (isopen(hours, dayofweek, time) > -1) {
                minutes = time % 100;
                if (minutes < 60) {
                    thedate = monthnames[now.getMonth()] + " " + now.getDate();
                    thetime = GenerateTime(time);
                    dayofweek = now.getDay();
                    if (dayofweek == tomorrow) {
                        now = tomorrowdate;
                    }
                    thedayname = verbosedate(now, today, today_text, tomorrow, tomor_text, time);
                    var timestamp = totimestamp(time, now);
                    var tempstr = '<OPTION VALUE="' + thedate + " at " + time.pad(4) + '" timestamp="' + timestamp + '"';
                    tempstr += ' now="' + now + '"';
                    HTML += tempstr + '>' + thedayname + '</OPTION>';
                }
            }
            time = addtotime(time, increments);
            if (time >= 2400) {
                time = 0;
                dayselapsed += 1;
                dayofweek = (dayofweek + 1) % 7;
                now = new Date(now.getTime() + 24 * 3600 * 1000);
                if (dayofweek == today || dayselapsed == totaldays) {
                    i = totalInc;
                }
            }
        }

        $("#deliverytimealias").html(HTML);
        $("#deliverytime").html(HTML).val(oldValue);
    }

    var doreset = true;
    $(document).ready(function () {
        if (doreset) {
            reseturl("document ready");
        }
        <?php
            if (islive()) {
                echo "setPublishableKey('pk_vnR0dLVmyF34VAqSegbpBvhfhaLNi', 'live');";
            } else {
                echo "setPublishableKey('pk_rlgl8pX7nDG2JA8O3jwrtqKpaDIVf', 'test');";
            }
        ?>
        $("input").blur(function () {
            var ID = $(this).attr("id");
            if (!isUndefined(ID)) {
                if (ID.length == 0) {
                    if ($(this)[0].hasAttribute("autocomplete")) {
                        if ($(this).attr("autocomplete") == "really-truly-off") {
                            ID = "address [no id]";
                            addressstatus(true, false, true, false, "input blur");
                        }
                    }
                }
            } else {
                log("Aborted");
                return;
            }
            log("Attempting to force validate " + ID + " to true");
            validateinput(this, true);
        });
    });

    function testcard() {
        var cardnumbers = ['4242424242424242', '4000001240000000', '4012888888881881', '4000056655665556', '5555555555554444', '5200828282828210', '5105105105105100', '378282246310005', '371449635398431'];
        var cardnumber = $("#testresult").val();
        if(cardnumber.length == 0){
            cardnumber = cardnumbers[random(0, cardnumbers.length - 1)];
        } else if($("#saved-credit-info").val()) {
            $("#saved-credit-info").val("");
            changecredit(true, 'testcard');
        }
        $('input[data-stripe=number]').val(cardnumber).trigger("click");
        $('input[data-stripe=address_zip]').val('L8L6V6').trigger("click");
        $('input[data-stripe=cvc]').val(rnd(100, 999)).trigger("click");
        $('select[data-stripe=exp_year]').val({{ right($CURRENT_YEAR,2) }} +1).trigger("click");
        @if(islive())
            log("Changing stripe key");
            $("#istest").val("true");
            setPublishableKey('pk_rlgl8pX7nDG2JA8O3jwrtqKpaDIVf', "test");
            log("Stripe key changed");
        @endif
    }

    $(document).on("click", function () {
        if ($(".dropdown-menu").is(":visible")) {
            $(".dropdown-menu").hide();
        }
    });

    //MODAL HIDING CODE
    $(window).load(function () {
        log("Back button detection in place");
        $('body').backDetect(function () {
            if (HandleBack("backDetect")) {
                return false;
            }
        });
    });

    var suppressback = 0;

    function HandleBack(Where) {
        log("BACK BUTTON DETECTED: " + Where);
        var ret = CloseModal("BACK BUTTON DETECTED: " + Where);
        if (ret) {
            suppressback = Date.now() + 100;
        }
        if (Date.now() < suppressback) {
            log("BACK BUTTON SUPPRESSED");
            ret = true;
        }
        return ret;
    }

    document.addEventListener('keydown', HandleBackKey);

    function HandleBackKey(e) {
        if (isObject(e)) {
            e = e.keyCode;
        }
        var doit = false;
        var keyname = "keycode " + e;
        if (e == 27) {
            doit = true;
            keyname = "escape";
        }
        if (e == 8) {
            var focused = document.activeElement.tagName.toLowerCase();
            keyname = "delete " + focused;
            switch (focused) {
                case "input":
                case "select":
                case "textarea":
                    break;
                default:
                    doit = true;
            }
        }
        if (doit) {
            if (CloseModal("HandleBackKey: " + keyname)) {
                return false;
            }
        } else {
            log("Skip close: " + keyname);
        }
    }

    window.addEventListener('popstate', function (event) {
        hashchange("popstate");
    }, false);
    $(window).on('hashchange', function (event) {//delete button closes modal
        hashchange("hashchange");
    });

    var prevhash = "";

    function hashchange(WHERE) {
        if (window.location.hash.length > 0) {
            prevhash = window.location.hash;
        }
        if (!skiphash() && document.readyState === 'complete') {
            if (!HandleBack("hashchange: " + WHERE + " (" + window.location.hash + ")") && WHERE == "popstate") {
                log("Back button allowed");
                history.back();
            }
        }
    }


    function setPublishableKey(Key, mode) {
        try {
            stripemode = mode;
            Stripe.setPublishableKey(Key);
            @if(!islive())
            log(mode + " stripe mode");
            @endif
        } catch (error) {
            log("Stripe not available on this page");
        }
    }

    @if(debugmode)
        window.onbeforeunload = function () {
            return "Are you sure?";
        };
    @endif

    function skiphash() {
        log("Checking hash: " + window.location.hash);
        if (window.location.hash == "#modal") {
            suppressback = Date.now() + 100;
            return true;
        }
        return Date.now() < suppressback;
    }
</SCRIPT>

<div class="modal z-index-9999" id="alertmodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content ">
            <div class="modal-header">
                <h2 id="alertmodallabel">Title</h2>
                <button data-dismiss="modal" class="btn  ml-auto bg-transparent align-middle">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <DIV ID="alertmodalbody" class="py-1"></DIV>
                <div CLASS="pull-center">
                    <button class="btn btn-outline-primary alert-button" id="alert-cancel" data-dismiss="modal">
                        CANCEL
                    </button>
                    <button class="btn {{btncolor}} alert-button" id="alert-confirm" data-dismiss="modal">
                        OK
                    </button>
                </div>
                <DIV CLASS="clearfix"></DIV>
            </div>
        </div>
    </div>
</DIV>


<?php endfile("popups_alljs"); ?>