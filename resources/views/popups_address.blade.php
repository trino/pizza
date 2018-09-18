<?php
    startfile("popups_address");
    if (!isset($style))                 {$style = 0;}
    if (!isset($address_placeholder))   {$address_placeholder = '';} else {$address_placeholder = ' PLACEHOLDER="' . $address_placeholder . '"';}
    if (!isset($unit))                  {$unit = true;}
    if (!isset($firefox))               {$firefox = true;}
    if (!isset($required))              {$required = "";} else {$required = " required";}
    if (!isset($class))                 {$class = "";} else {$class = " " . $class;}
    if (!isset($icons))                 {$icons = false;}
    if (isset($autored))                {$required .= ' autored="' . $autored . '"';}
    if(!isset($title))                  {$title = "Address";}
    if(isset($address))                 {$GLOBALS["address"] = $address;}
    if(isset($unsetaddress))            {unset($GLOBALS["address"]);}

    function address($key){
        if(isset($GLOBALS["address"][$key])){
            return ' VALUE="' . $GLOBALS["address"][$key] . '"';
        }
        return "";
    }

    $q = "'";
    //chrome autofill avoidance
    $rndname = "formatted_address";// str_replace(" ", "-", str_replace(":", "-",now()));
    $autocompleteblocker = ' ONCLICK="autofix(this);" onkeydown="gmapkeypress(event);" parentlevel="2"';
    $unitname = "suba" . time();
    echo '<SPAN ID="mirror"></SPAN>';

    switch ($style) {
        case 0:
            echo '<DIV><DIV CLASS="col-md-2">' . $title . '</DIV><DIV CLASS="col-md-12" ID="gmapc">';
            echo '<INPUT class="form-control" TYPE="text" ID="formatted_address" ' . $required . ' name="' . $rndname . '"' . $autocompleteblocker . $address_placeholder . address("formatted_address") . '></div></DIV>';
            break;
        case 1:
            if($icons) {echo '<div class="input_left_icon"><span class="fa-stack fa-2x"><i class="fa fa-circle fa-stack-2x"></i><i class="fa fa-car text-white fa-stack-1x"></i></span></div><div class="input_right">';}
            echo '<SPAN ID="gmapc"><INPUT TYPE="text" ID="formatted_address" PLACEHOLDER="Start typing your address" CLASS="form-control formatted_address' . $class . '"' . $required . ' name="' . $rndname . '"' . $autocompleteblocker . address("formatted_address") . '"></SPAN>';
            if($icons) {echo '</div>';}
            echo '<STYLE>.address:focus{z-index: 999;}</STYLE>';
            break;
        case 2:
            echo '<INPUT class="form-control" TYPE="text" ID="formatted_address" ' . $required . ' name="' . $rndname . '"' . $autocompleteblocker . $address_placeholder . address("formatted_address") . '>';
            break;
    }
    if (!isset($user_id)) {$user_id = read("id");}
    if (!isset($form)) {$form = true;}
?>
<STYLE>
    .pac-container {
        z-index: 99999999999 !important;
    }
</STYLE>
    @if($form)
        <FORM ID="googleaddress">
    @endif
    @if($icons)
        <div class="input_left_icon d-none">
            <span class="fa-stack fa-2x">
            <i class="fa fa-circle fa-stack-2x"></i>
            <i class="fa fa-pencil-alt text-white fa-stack-1x"></i></span>
        </div>
        <div class="input_right">
    @endif
    @if($unit)
        <INPUT TYPE="text" NAME="<?= $unitname; ?>" ID="add_unit" PLACEHOLDER="Apt/Buzzer" CLASS="form-control address" TITLE="ie: Apt/Unit, buzz code, which door to go to" <?=address("unit"); ?> >
    @endif
    @if($icons)
        </div>
    @endif
        <INPUT TYPE="text" NAME="number" ID="add_number" PLACEHOLDER="Street Number" {{ $required }} CLASS="form-control street_number address dont-show" <?=address("number"); ?> >
        <INPUT TYPE="text" NAME="street" ID="add_street" PLACEHOLDER="Street" {{ $required }} CLASS="form-control route address dont-show" <?=address("street"); ?> >
        <INPUT TYPE="text" NAME="city" ID="add_city" PLACEHOLDER="City" {{ $required }} CLASS="form-control locality address dont-show" <?=address("city"); ?> >
        <INPUT TYPE="text" NAME="province" ID="add_province" PLACEHOLDER="Province" {{ $required }} CLASS="form-control administrative_area_level_1 address dont-show" <?=address("province"); ?> >
        <INPUT TYPE="text" NAME="postalcode" ID="add_postalcode" PLACEHOLDER="Postal Code" {{ $required }} CLASS="form-control postal_code address dont-show" <?=address("postalcode"); ?> >
        <INPUT TYPE="text" NAME="latitude" ID="add_latitude" PLACEHOLDER="Latitude" {{ $required }} CLASS="form-control latitude address dont-show" <?=address("latitude"); ?> >
        <INPUT TYPE="text" NAME="longitude" ID="add_longitude" PLACEHOLDER="Longitude" {{ $required }} CLASS="form-control longitude address dont-show" <?=address("longitude"); ?> >
        <INPUT TYPE="hidden" NAME="user_id" ID="add_user_id" PLACEHOLDER="user_id" {{ $required }} CLASS="form-control session_id_val address" value="{{$user_id}}">
    @if($form) </FORM> @endif

<SCRIPT>
    var formatted_address = "has not initialized";
    //if($firefox)
    /*  why is this commented out?
        if(is_firefox_for_android) {
            $(window).load(function () {
                var HTML = $("#gmapc").html();
                HTML = HTML.replaceAll("style=", "oldstyle=");
                log("Moving: " + HTML);
                $("#gmapffac").html(HTML);
                $("#gmapc").html('<DIV CLASS="fake-form-control"><SPAN CLASS="address fake-address" ID="ffaddress"></SPAN><BUTTON CLASS="btn btn-sm btn-primary radius0 pull-right full-height" ONCLICK="handlefirefox();return false;">EDIT</BUTTON></DIV><DIV CLASS="separator"></DIV>');
                initAutocomplete();
            });
        }
        */
    //endif

    function visible_address(state) {
        visible(getGoogleAddressSelector(), state);
        visible("#add_unit", state);
    }

    function getGoogleAddressSelector(clear){
        if(isUndefined(clear)){clear = false;}
        if(clear){
            //formatted_address.set('place',null);
        }
        if(GoogleAddressID.length > 0){
            return "#" + GoogleAddressID;
            //return "input[autocomplete=really-truly-off]";
        }
        return "input[name=formatted_address]";
    }

    function testaddress(element){
        //if(isUndefined(element)){element = $(getGoogleAddressSelector());}
        //validateform("#" + getformid($(element)));
    }

    function transferdata(value){
        $('input[autocomplete=really-truly-off]').val(value);
        $("#formatted_address").val(value);
    }

    function gmapkeypress(e){
        var event = window.event ? window.event : e;
        var keycode = event.keyCode;
        if(keycode == 8 || keycode == 46){
            clearaddress();
        }
    }

    function clearaddress(){
        var fields = ["formatted_address", "add_latitude", "add_longitude"];
        for (i = 0; i < fields.length; i++) {
            $("#" + fields[i]).val("");
        }
    }

    function randomString(len, charSet) {
        len = len || 20;
        charSet = charSet || 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        var randomString = '';
        for (var i = 0; i < len; i++) {
            var randomPoz = Math.floor(Math.random() * charSet.length);
            randomString += charSet.substring(randomPoz,randomPoz+1);
        }
        return randomString;
    }

    var GoogleAddressID = '';
    function autofix(element){
        if(is_chrome) {
            if(isUndefined(element)){element = document.getElementById("formatted_address");}
            if(element.getAttribute("autocomplete") != "really-truly-off") {
                GoogleAddressID = "omit_" + randomString();
                element.setAttribute("name", GoogleAddressID);
                element.setAttribute("id", GoogleAddressID);
                element.setAttribute("class", "form-control");
                element.setAttribute("autocomplete", "really-truly-off");
                $("#mirror").html('<INPUT TYPE="TEXT" NAME="formatted_address" ID="formatted_address" class="nevershow">');
            }
        }
    }

    function editaddresses() {
        $("#checkoutmodal").modal("hide");
        $("#profilemodal").modal("show");
    }

    function isnewaddress(number, street, city) {
        var AddNew = number && street && city;
        $("#saveaddresses option").each(function () {
            var ID = $(this).val();
            if (ID > 0) {
                if (number.isEqual($(this).attr("number")) && street.isEqual($(this).attr("street")) && city.isEqual($(this).attr("city"))) {
                    return false;
                }
            }
        });
        return AddNew;
    }

    function deleteaddress(ID) {
        if (ID < 0) {//add new address
            var address = serializeaddress("#orderinfo");
            $.post(webroot + "placeorder", {
                _token: token,
                info: address
            }, function (result) {
                address["id"] = result;
                var HTML = AddressToOption(address);
                $(".saveaddresses").append(HTML);
                if (ID == -1) {
                    addresses();
                }
            });
        } else {
            confirm3("add_" + ID, "Are you sure you want to delete '" + $("#add_" + ID).text().trim() + "'?", 'Delete Address', function () {
                $.post(webroot + "placeorder", {
                    _token: token,
                    action: "deleteaddress",
                    id: ID
                }, function (result) {
                    if (handleresult(result, "toast")) {
                        toast("'" + $("#add_" + ID).text().trim() + "' deleted");
                        $("#add_" + ID).fadeOut(500, function () {
                            $("#add_" + ID).remove();
                            if(!$("#addresses").html()){
                                $("#addresses").hide().html(makestring("{noaddresses}")).fadeIn(fade_speed);
                            }
                        });
                        $(".saveaddresses option[value=" + ID + "]").remove();
                        for(var index = 0; index < userdetails.Addresses.length; index++){
                            if(userdetails.Addresses[index].id == ID){
                                userdetails.Addresses.splice(index, 1);
                            }
                        }
                    }
                });
            });
        }
    }

    function initAutocomplete(ElementID, Action, AutoFix) {
        var needstoset = false;
        if(isUndefined(ElementID)){ElementID = "formatted_address"; needstoset = true;}
        if(isUndefined(Action)){Action = function(){fillInAddress(ElementID);}}
        var cityBounds = new google.maps.LatLngBounds(
                //new google.maps.LatLng(42.873863, -81.501312), new google.maps.LatLng(43.043212, -81.092071)//southWest, northEast (LONDON ONTARIO)
                new google.maps.LatLng(43.164135, -79.981296), new google.maps.LatLng(43.264183, -79.512758)//southWest, northEast (HAMILTON ONTARIO)
        );//city boundaries

        var addressbar = new google.maps.places.Autocomplete(
                /** @type {!HTMLInputElement} */(document.getElementById(ElementID)), {
                bounds: cityBounds,//limit to a specific city
                types: ['geocode'],
                componentRestrictions: {country: "ca"}
        });
        addressbar.addListener('place_changed', Action);
        if(!isUndefined(AutoFix)){autofix();}
        if(needstoset){formatted_address = addressbar;};
        return addressbar;
    }

    function formataddress(place, streetformat){
        log("formataddress: " + JSON.stringify(place));
        var lat = place.geometry.location.lat();
        var lng = place.geometry.location.lng();

        var addressdata = {};

        $('.formatted_fordb').val(place.formatted_address); // this formatted_address is a google maps object
        $('.latitude').val(lat);
        $('.longitude').val(lng);

        var componentForm = {
            street_number: 'short_name',
            route: 'short_name',//street name
            locality: 'long_name',//ON Canada
            administrative_area_level_1: 'short_name',//province
            country: 'long_name',
            postal_code: 'short_name'
        };
        //Example: 2396 Kingsway, locality: Vancouver, administrative_area_level_1: British Columbia, country: Canada, postal_code: V5R 5G9
        if(isUndefined(streetformat)){streetformat = "[street_number] [route], [locality], [administrative_area_level_1_s]";}// [postal_code]";
        //if(isUndefined(streetformat)){streetformat = "street_number: [street_number] route: [route], locality: [locality], administrative_area_level_1_s: [administrative_area_level_1_s]";}// [postal_code]";

        for (var i = 0; i < place.address_components.length; i++) {
            var addressType = place.address_components[i].types[0];
            if (componentForm[addressType]) {
                var val = place.address_components[i][componentForm[addressType]];
                addressdata[addressType] = val;
                streetformat = streetformat.replace("[" + addressType + "]", val);
                $('.' + addressType).val(val);
                val = place.address_components[i]['short_name'];
                streetformat = streetformat.replace("[" + addressType + "_s]", val);
                val = place.address_components[i]['long_name'];
                streetformat = streetformat.replace("[" + addressType + "_l]", val);
            }
        }
        addressdata.streetformat = streetformat;
        return addressdata;
    }


    function fillInAddress(ElementID) {
        // Get the place details from the formatted_address object.
        var place = formatted_address.getPlace();
        var addressdata = formataddress(place);
        var streetformat = addressdata.streetformat;
        if (isnewaddress(addressdata["street_number"], addressdata["route"], addressdata["locality"])) {
            $("#saveaddressbtn").removeAttr("disabled");
        } else {
            $("#saveaddressbtn").attr("disabled", true);
        }
        $('.formatted_address').val(streetformat);
        transferdata(streetformat);
        place.formatted_address = streetformat;
        @if(isset($findclosest))
            if (isFunction(addresshaschanged)) {
                addresshaschanged(place);
            }
        @endif
        testaddress();
        return place;
    }

    function tobool(value){
        if(value){
            return 1;
        }
        return 0;
    }

    function addressstatus(checkaddress, checkrestaurant, forceaddress, forcerestaurant, calledfrom){
        if(isUndefined(checkaddress)){checkaddress = true;}
        if(isUndefined(checkrestaurant)){checkrestaurant = true;}
        if(isUndefined(forceaddress)){forceaddress = false;}
        if(isUndefined(forcerestaurant)){forcerestaurant = false;}
        if(isUndefined(calledfrom)){calledfrom = "[UNKNOWN]";}
        if(checkaddress) {
            if (validaddress() || forceaddress) {
                $("#red_address").removeClass("redhighlite");
                validateinput("#saveaddresses", true);
                $("#reg_address-error").remove();
            } else {
                $("#red_address").addClass("redhighlite");
                var code = "";
                @if(debugmode)
                    code = " (CODE: " + tobool(checkaddress) + tobool(checkrestaurant) + tobool(forceaddress) + tobool(forcerestaurant) + " - " + calledfrom + ")";
                @endif
                validateinput("#saveaddresses", "Please check your address" + code);
            }
        }
        if(checkrestaurant) {
            if ($("#restaurant").val() != 0 || forcerestaurant) {
                $("#red_rest").removeClass("redhighlite");
                validateinput("#restaurant", true);
            } else {
                $("#red_rest").addClass("redhighlite");
                var message = "Please select your desired {{storename}}";
                var children = $("#restaurant").children();
                if(children.length == 1){
                    if(children[0].text == makestring("{norestaurants}")){
                        message = "No {{storenames}} within range";
                    }
                }
                validateinput("#restaurant", message);
            }
        }
    }

    function serializeaddress(asdata){
        var ret;
        if(isUndefined(asdata)){asdata = false;}
        if(asdata){
            ret = $(asdata).html().trim().length;
            if(ret == 0){return false;}
            ret = getform(asdata);
            ret["unit"] = ret["<?= $unitname; ?>"];
            delete ret["<?= $unitname; ?>"];
            return ret;
        }
        ret = $("#googleaddress").serialize();
        if(ret.length > 0){ret += "&";}
        ret = ret.replace("&<?= $unitname; ?>=", "&unit=");
        return ret + "formatted_address=" + encodeURIComponent($(getGoogleAddressSelector()).val());
    }
</SCRIPT>
<?php
    if (!isset($dontincludeGoogle)) {
        echo '<script src="https://maps.googleapis.com/maps/api/js?signed_in=true&libraries=places&callback=initAutocomplete&key=AIzaSyBWSUc8EbZYVKF37jWVCb3lpBQwWqXUZw8"></script>';
    } else {
        ?>
            <SCRIPT LANGUAGE="JavaScript">
                window.onload = function () {
                    log("init autocomplete");
                    formatted_address = initAutocomplete();
                };
            </SCRIPT>
        <?php
    }
    endfile("popups_address");
?>