var is_firefox = navigator.userAgent.toLowerCase().indexOf('firefox') > -1;
var is_android = navigator.userAgent.toLowerCase().indexOf('android') > -1;
var is_chrome = /Chrome/.test(navigator.userAgent) && /Google Inc/.test(navigator.vendor);
var is_firefox_for_android = is_firefox && is_android;
var currentitemID = -1;
var lockloading = false, previoushash = "", $body = "";
var stripemode = "";
var fade_speed = 600;

String.prototype.isEqual = function (str) {
    if (isUndefined(str)) {
        return false;
    }
    if (isNumeric(str) || isNumeric(this)) {
        return this == str;
    }
    return this.toUpperCase().trim() == str.toUpperCase().trim();
};

function isUndefined(variable) {
    return typeof variable === 'undefined';
}

function isArray(variable) {
    return Array.isArray(variable);
}

//returns true if $variable appears to be a valid number
function isNumeric(variable) {
    return !isNaN(Number(variable));
}

//returns true if $variable appears to be a valid object
//typename (optional): the $variable would also need to be of the same object type (case-sensitive)
function isObject(variable, typename) {
    if (typeof variable == "object") {
        if (isUndefined(typename)) {
            return true;
        }
        return variable.getName().toLowerCase() == typename.toLowerCase();
    }
    return false;
}

String.prototype.contains = function (str) {
    return this.toLowerCase().indexOf(str.toLowerCase()) > -1;
};

//returns true if the string starts with str
String.prototype.startswith = function (str) {
    return this.substring(0, str.length).isEqual(str);
};
String.prototype.endswith = function (str) {
    return this.right(str.length).isEqual(str);
};
//returns the left $n characters of a string

String.prototype.left = function (n) {
    return this.substring(0, n);
};

String.prototype.mid = function (start, length) {
    return this.substring(start, start + length);
};

String.prototype.GetBetween = function (startingtext, endingtext) {
    var target = this;
    if(target.indexOf(startingtext) < 0 || target.indexOf(endingtext) < 0) return false;
    var SP = target.indexOf(startingtext)+startingtext.length;
    var string1 = target.substr(0,SP);
    var string2 = target.substr(SP);
    var TP = string1.length + string2.indexOf(endingtext);
    return target.substring(SP,TP);
};

String.prototype.SetSlice = function (Start, End, ReplaceText) {
    var target = this;
    return target.left(Start) + ReplaceText + target.right(target.length - End);
};

Number.prototype.pad = function (size, rightside) {
    var s = String(this);
    if (isUndefined(rightside)) {
        rightside = false;
    }
    while (s.length < (size || 2)) {
        if (rightside) {
            s = s + "0";
        } else {
            s = "0" + s;
        }
    }
    return s;
};

//returns the right $n characters of a string
String.prototype.right = function (n) {
    return this.substring(this.length - n);
};

function getKeyByValue(object, value) {
    return Object.keys(object).find(key => object[key] === value);
}

//Period: year, month, day, hour, minute, second, millisecond
Date.prototype.add = function (Period, Increment){
    switch(Period){
        case "year":        this.setYear(this.getYear() + Increment); break;
        case "month":       this.setMonth(this.getMonth() + Increment); break;
        case "day":         this.setDate(this.getDate() + Increment); break;
        case "hour":        this.setHours(this.getHours() + Increment); break;
        case "minute":      this.setMinutes(this.getMinutes() + Increment); break;
        case "second":      this.setSeconds(this.getSeconds() + Increment); break;
        case "millisecond": this.setMilliseconds(this.getMilliseconds() + Increment); break;
    }
    return this;
};

function right(text, length) {
    return String(text).right(length);
}

//returns true if $variable appears to be a valid function
function isFunction(variable) {
    var getType = {};
    return variable && getType.toString.call(variable) === '[object Function]';
}

//replaces all instances of $search within a string with $replacement
String.prototype.replaceAll = function (search, replacement) {
    var target = this;
    if (isArray(search)) {
        for (var i = 0; i < search.length; i++) {
            if (isArray(replacement)) {
                target = target.replaceAll(search[i], replacement[i]);
            } else {
                target = target.replaceAll(search[i], replacement);
            }
        }
        return target;
    }
    return target.replace(new RegExp(search, 'g'), replacement);
};

String.prototype.between = function (leftside, rightside) {
    var target = this;
    var start = target.indexOf(leftside);
    if (start > -1) {
        var finish = target.indexOf(rightside, start);
        if (finish > -1) {
            return target.substring(start + leftside.length, finish);
        }
    }
};

function storageAvailable(type) {
    try {//types: sessionStorage, localStorage
        var storage = window[type], x = '__storage_test__';
        storage.setItem(x, x);
        storage.removeItem(x);
        return true;
    } catch(e) {
        return false;
    }
}
var uselocalstorage = storageAvailable('localStorage');
log("Local storage is available: " + iif(uselocalstorage, "Yes", "No (use cookie instead)"));
function hasItem(c_name){
    if(uselocalstorage){
        return window['localStorage'].getItem(c_name) !== null;
    } else {
        var i, x, y, ARRcookies = document.cookie.split(";");
        for (i = 0; i < ARRcookies.length; i++) {
            x = ARRcookies[i].substr(0, ARRcookies[i].indexOf("="));
            y = ARRcookies[i].substr(ARRcookies[i].indexOf("=") + 1);
            x = x.replace(/^\s+|\s+$/g, "");
            if (x == c_name) {
                return true;
            }
        }
    }
    return false;
}

function setCookie(c_name, value, exdays) {
    if(uselocalstorage){
        window['localStorage'].setItem(c_name, value);
    } else {
        var exdate = new Date();
        exdate.setDate(exdate.getDate() + exdays);
        var c_value = value + ((exdays == null) ? "" : "; expires=" + exdate.toUTCString());
        c_value = c_name + "=" + c_value + ";path=/;";
        document.cookie = c_value;
    }
}

//gets a cookie value
function getCookie(c_name) {
    if(hasItem(c_name)){
        return window['localStorage'].getItem(c_name);
    }
    var i, x, y, ARRcookies = document.cookie.split(";");
    for (i = 0; i < ARRcookies.length; i++) {
        x = ARRcookies[i].substr(0, ARRcookies[i].indexOf("="));
        y = ARRcookies[i].substr(ARRcookies[i].indexOf("=") + 1);
        x = x.replace(/^\s+|\s+$/g, "");
        if (x == c_name) {
            return unescape(y);
        }
    }
}

//deletes a cookie value
function removeCookie(cname, forcecookie) {
    if(isUndefined(forcecookie)){forcecookie = false;}
    if (isUndefined(cname)) {//erase all cookies
        var cookies = document.cookie.split(";");
        for (var i = 0; i < cookies.length; i++) {
            var cookie = cookies[i];
            var eqPos = cookie.indexOf("=");
            var name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;
            removeCookie(name, true);
        }
        if(uselocalstorage) {
            cookies = Object.keys(window['localStorage']);
            for (var i = 0; i < cookies.length; i++) {
                removeCookie(cookies[i]);
            }
        }
    } else if(hasItem(cname) && !forcecookie){
        window['localStorage'].removeItem(cname);
    } else {
        document.cookie = cname + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/;";
    }
}

function focuson(selector){
    $(selector).focus();
    //$('html, body').animate({scrollTop: $(selector).offset().top}, 1000);
}

//creates a cookie value that expires in 1 year
function createCookieValue(cname, cvalue) {
    //log("Creating cookie value: '" + cname + "' with: " + cvalue);
    setCookie(cname, cvalue, 365);
}


function getform(Selector, IncludeType) {
    var data = $(Selector).serializeArray();
    var ret = {};
    for (var i = 0; i < data.length; i++) {
        if(!data[i].name.startswith("omit_")) {
            ret[data[i].name] = data[i].value.trim();
        }
    }
    $(Selector + " input:checkbox:not(:checked)").each(function (index) {
        if($(this).hasAttr("name")){
            ret[$(this).attr("name")] = "off";
        }
    });
    if(!isUndefined(IncludeType) && IncludeType){
        for (var key in ret) {
            ret[key] = {value: ret[key], type: inputtype(Selector, key)};
        }
    }
    return ret;
}

function inputtype(FormSelector, InputName){
    var element = $(FormSelector + " [name=" + InputName + "]");
    if(element.length == 0){return;}
    var ret = element.get(0).tagName.toLowerCase();
    if(ret == "input"){
        if (element.hasAttr("type")){
            ret = element.attr("type").toLowerCase();
        }
    }
    return ret;
}

function inputbox2(Text, Title, Default, retfnc) {
    Text += '<INPUT TYPE="TEXT" ID="modal_inputbox" CLASS="form-control margin-top-15px" VALUE="' + Default + '">';
    confirm2(Text, Title, function () {
        retfnc($("#modal_inputbox").val());
    });
}

var confirm3action = function () {};
function confirm3(OverlapID, Prompt, Title, Action){
    $(".confirm3").remove();
    if(isUndefined(Title)){
        if(Prompt){confirm3action();}
    } else {
        confirm3action = Action;
        var HTML = '<DIV ID="' + OverlapID + '-confirm" CLASS="confirm3">' + Prompt + '<DIV CLASS="confirm3-answers float-right">';
        HTML += '<DIV ONCLICK="confirm3(' + "'" + OverlapID + "', true" + ');" CLASS="confirm3-yes btn btn-primary">Yes</DIV>';
        HTML += '<DIV ONCLICK="confirm3(' + "'" + OverlapID + "', false" + ');" CLASS="confirm3-no btn btn-danger">No</DIV>';
        HTML += '</DIV></DIV>';
        $("#" + OverlapID).append(HTML).addClass("confirm3-behind");
    }
}

function confirm2() {
    var Title = "Confirm";
    var action = function () {};
    $('#alert-confirm').unbind('click');
    if (arguments.length > 1) {
        for (var index = 0; index < arguments.length; index++) {
            if (isFunction(arguments[index])) {
                action = arguments[index];
            } else {
                Title = arguments[index];
            }
        }
    }
    alert(arguments[0], Title);
    $("#exclame").show();
    $("#alert-cancel").show();
    $("#alert-confirm").click(action);
}

function removeindex(arr, index, count, delimiter) {
    if (!isArray(arr)) {
        if (isUndefined(delimiter)) {delimiter = " ";}
        arr = removeindex(arr.split(delimiter), index, count, delimiter).join(delimiter);
    } else {
        if (isNaN(index)) {
            index = hasword(arr, index);
        }
        if (index > -1 && index < arr.length) {
            if (isUndefined(count)) {
                count = 1;
            }
            arr.splice(index, count);
        }
    }
    return arr;
}

function visible(selector, status) {
    if (isUndefined(status)) {status = false;}
    if (status) {
        $(selector).show();
    } else {
        $(selector).hide();
    }
}

$.fn.hasAttr = function (name) {
    return this.attr(name) !== undefined;
};

$.validator.addMethod('phonenumber', function (Data, element) {
    Data = Data.replace(/\D/g, "");
    if (Data.substr(0, 1) == "0") {
        return false;
    }
    return Data.length == 10;
}, "Invalid phone number");

$.validator.addMethod('validaddress', function (Data, element) {
    log("TESTING ADDRESS: " + Data);
}, "Please check your address");

function isvalidaddress() {
    var fields = ["formatted_address", "add_latitude", "add_longitude"];//, "add_postalcode"
    //if ($("#add_city").val().toLowerCase() != "london") {return false;}
    for (i = 0; i < fields.length; i++) {
        var value = $("#" + fields[i]).val();
        if(isUndefined(value)){return false;}
        log(fields[i] + ": " + value.length + " chars = " + value);
        if (value.length == 0 || (value.indexOf("[") > -1 && value.indexOf("]") > -1)) {
            return false;
        }
    }
    return true;
}

function findwhere(data, key, value) {
    for (var i = 0; i < data.length; i++) {
        if (data[i][key].isEqual(value)) {
            return i;
        }
    }
    return -1
}

$(document).on('touchend', function () {
    $(".select2-search, .select2-focusser").remove();
});

//generates the order menu item modal
var currentitem;

function loadmodal(element, notparent) {
    if (isUndefined(notparent)) {
        element = $(element);
    }
    var items = ["name", "price", "id", "size", "cat"];
    for (var i = 0; i < items.length; i++) {
        $("#modal-item" + items[i]).text($(element).attr("item" + items[i]));
    }
    var itemname = $(element).attr("itemname");
    var itemcost = $(element).attr("itemprice");
    var size = $(element).attr("itemsize");
    var toppingcost = 0.00;
    if (size) {
        toppingcost = Number(freetoppings[size]).toFixed(2);
        $(".toppings").attr("data-placeholder", "Add Toppings: $" + toppingcost);
        $(".toppings_price").text(toppingcost);
    }
    $("#modal-toppingcost").text(toppingcost);
    if (toppingcost > 0) {
        $("#toppingcost").show();
    } else {
        $("#toppingcost").hide();
    }
    currentitem = {itemname: itemname, itemcost: itemcost, size: size, toppingcost: toppingcost};

    for (var tableid = 0; tableid < tables.length; tableid++) {
        var table = tables[tableid];
        var Quantity = Number($(element).attr(table));
        if (Quantity > 0) {
            list_addons_quantity(Quantity, table, false, itemname, itemcost, toppingcost);
            tableid = tables.length;
        }
    }
    currentitemID = -1;
    var title = '<SPAN ID="modal-itemtotalprice-all"><div class="pull-left d-inline"><i class="fa fa-shopping-cart"></i></div><div class="ml-2 pull-right d-inline">$<SPAN ID="modal-itemtotalprice"></SPAN></div></SPAN>';
    if (!isUndefined(notparent)) {
        $("#menumodal").modal("show");
        refreshremovebutton();
    }
    $("#additemtoorder").html(title);
    itemtotalprice(itemcost, false);
}

function refreshremovebutton() {
    if (currentaddonlist[currentitemindex].length > 0) {
        var index = currentaddonlist[currentitemindex].length - 1;
        var lastitem = currentaddonlist[currentitemindex][index];
        $(".removeitemarrow").fadeTo(fade_speed, 1.00);
        $("#removeitemfromorder").attr("title", "Remove: " + lastitem.name + " from " + $("#item_" + currentitemindex).text()).attr("onclick", "removelistitem(" + currentitemindex + ", " + index + ");").attr("style", "");
    }
}

//get the data from the modal and add it to the order
function additemtoorder(element, Index) {
    // Get the snackbar DIV
    // var x = document.getElementById("snackbar");
    // Add the "show" class to DIV
    //  x.className = "show";
    // After 3 seconds, remove the show class from DIV
    var itemid = 0, itemname = "", itemprice = 0.00, itemaddons = new Array, itemsize = "", toppingcost = 0.00, toppingscount = 0, itemcat = "", oldcost = "";
    if (!isUndefined(Index)) {currentitemID = Index;}
    if (isUndefined(element)) {//modal with addons
        itemid = $("#modal-itemid").text();
        itemname = $("#modal-itemname").text();
        itemprice = $("#modal-itemprice").text();
        itemsize = $("#modal-itemsize").text();
        itemcat = $("#modal-itemcat").text();
        itemaddons = getaddons();
        if (itemsize) {
            toppingcost = Number(freetoppings[itemsize]).toFixed(2);
        }
        for (var i = 0; i < itemaddons.length; i++) {
            toppingscount += itemaddons[i]["count"];
        }
    } else {//direct link, no addons
        element = $(element);
        itemid = $(element).attr("itemid");
        itemname = $(element).attr("itemname");
        itemprice = $(element).attr("itemprice");
        itemcat = $(element).attr("itemcat");
        for (var index = 0; index < theorder.length; index++) {
            if (theorder[index].itemid == itemid) {
                oldcost = $('#cost_' + index).text();
                break;
            }
        }
    }

    data = {
        quantity: 1,
        itemid: itemid,
        itemname: itemname,
        itemprice: itemprice,
        itemsize: itemsize,
        category: itemcat,
        toppingcost: toppingcost,
        toppingcount: toppingscount,
        itemaddons: itemaddons,
        isnew: true
    };
    if (currentitemID == -1) {
        theorder.push(data);
        var ret = theorder.length - 1;
    } else {
        theorder[currentitemID] = data;
        var ret = currentitemID;
    }
    var oldtext = getitemtext(ret);
    generatereceipt(true);
    fadereceiptitem(ret, oldtext);
    if (oldcost) {
        refreshcost(index, oldcost);
    }
    return ret;
}

function getitemtext(itemindex, text){
    itemindex = getsrcid(itemindex);
    var id = "#receipt_item_" + itemindex + " .";
    if(isUndefined(text)) {
        text = [$(id + "item_qty").text(), $(id + "itemname").html()];
        return text;
    }
    $(id + "item_qty").text(text[0]);
    if(text[1]){$(id + "itemname").html(text[1]);}
    return $("#receipt_item_" + itemindex);
}

function getsrcid(itemindex){
    if($("#receipt_item_" + itemindex).length == 0){
        itemindex = findfirstofclone(itemindex);
    }
    return itemindex;
}

function fadereceiptitem(itemindex, oldtext){
    itemindex = getsrcid(itemindex);
    var newtext = getitemtext(itemindex);
    if(oldtext[1]) {
        getitemtext(itemindex, oldtext).fadeOut(fade_speed, function () {
            getitemtext(itemindex, newtext).fadeIn(fade_speed);
        });
    } else {
        getitemtext(itemindex, newtext).delay(fade_speed).fadeIn(fade_speed);
    }
}

function findfirstofclone(itemindex){
    for (var itemid = 0; itemid < theorder.length; itemid++) {
        if (theorder[itemid]["itemid"] == theorder[itemindex]["itemid"]){
            if (theorder[itemid]["itemname"] == theorder[itemindex]["itemname"]) {
                if (theorder[itemid]["category"] == theorder[itemindex]["category"]) {
                    return itemid;
                }
            }
        }
    }
}

function unclone() {
    for (var itemid = 0; itemid < theorder.length; itemid++) {
        delete theorder[itemid]["clone"];
    }
    generatereceipt(true);
}

function cloneitem(me, itemid) {
    var clone = JSON.parse(JSON.stringify(theorder[itemid]));
    clone.clone = true;
    clone.quantity = 1;
    theorder.push(clone);
    var oldtext = getitemtext(itemid);
    var oldcost = $('#cost_' + itemid).text();
    generatereceipt(true);
    fadereceiptitem(itemid, oldtext);
    refreshcost(itemid, oldcost);
}

function refreshcost(itemid, oldcost) {
    var newcost = $('#cost_' + itemid).text();
    $('#cost_' + itemid).show().text(oldcost).fadeOut(fade_speed,
        function () {
            $('#cost_' + itemid).text(newcost).fadeIn(fade_speed);
        }
    );
}

function getDiscount(subtotal){
    for (var tens = Math.floor(subtotal / 10) * 10;tens >= 0; tens-=10){
        if(freetoppings.hasOwnProperty('over$' + tens)){
            return freetoppings['over$' + tens];
        }
    }
    return 0;
}

var pretiptotal = 0;
var tip = 0.00;
function changetip(){
    var value = tip, ispercent = false;
    if(value < 0){
        ispercent = true;
        value = value * -100;
    }
    var HTML = '<INPUT TYPE="TEXT" VALUE="' + value + '" ID="tip-value" ONCHANGE="recalctip();" ONKEYUP="recalctip();">';
    HTML += ' <LABEL><INPUT TYPE="RADIO" NAME="tip" ID="tip-percent" VALUE="%"' + iif(ispercent, " CHECKED", "") + ' ONCLICK="recalctip();"> % </LABEL>';
    HTML += ' <LABEL><INPUT TYPE="RADIO" NAME="tip" ID="tip-dollars" VALUE="$"' + iif(ispercent, "", " CHECKED") + ' ONCLICK="recalctip();"> $ </LABEL>';
    HTML += '&nbsp;<SPAN ONCLICK="settip(0);" CLASS="cursor-pointer"><i class="fas fa-times-circle" STYLE="color: red;"></i></SPAN><BR>';
    HTML += tippreset(0.05) + tippreset(0.10) + tippreset(0.15) + tippreset(1) + tippreset(3) + tippreset(5);
    HTML += '<TABLE>';
    HTML += '<TR><TD>Sub-total:</TD><TD>$</TD><TD ALIGN="RIGHT"><SPAN ID="tip-subtotal">' + pretiptotal.toFixed(2) + '</SPAN></TD></TR>';
    HTML += '<TR><TD>Tip:</TD><TD>$</TD><TD ALIGN="RIGHT"><SPAN ID="tip-actual"></SPAN></TD></TR>';
    HTML += '<TR><TD>Total:</TD><TD>$</TD><TD ALIGN="RIGHT"><SPAN ID="tip-total"></SPAN></TD></TR>';
    HTML += '</TABLE>';
    alert(HTML, "Driver's Tip");
    recalctip();
    $("#alert-confirm").click(function(){recalctip(true);});
}
function settip(value, save){
    if(value > 0 && value < 1){
        value = value * 100;
        $("#tip-percent").prop("checked", true);
    } else {
        $("#tip-dollars").prop("checked", true);
    }
    $("#tip-value").val(value);
    recalctip(save, value);
}
function tippreset(value){
    var HTML = '<BUTTON CLASS="btn btn-primary btn-space" ONCLICK="settip(' + value + ');">';
    if(value > 0 && value < 1){
        HTML += (value * 100) + "%";
    } else {
        HTML += "$" + value.toFixed(2);
    }
    return HTML + '</BUTTON>';
}
function recalctip(save, value){
    if(isUndefined(save)){save = false;}
    if(isUndefined(value)){value = $("#tip-value").val();}
    if(isNaN(value) || isUndefined(value) || !value){value = 0.00;} else {value = parseFloat(value);}
    var tiptype = $("input[name='tip']:checked").val();
    var ispercent = tiptype == "%";
    var actual = value;
    if(ispercent) {
        value = -value*0.01;
        actual = -pretiptotal*value;
    }
    $("#tip-actual").text(actual.toFixed(2));
    var total = (pretiptotal+actual).toFixed(2);
    $("#tip-total").text(total);
    if(save){
        tip = value;
        if($("#tiprow").is(":visible")){
            if(tip == 0){
                $("#tiprow").fadeOut(fade_speed);
            } else {
                fadetext("#thetip", "$" + actual.toFixed(2));
            }
        } else if (tip > 0) {
            $("#thetip").text("$" + actual.toFixed(2));
            $("#tiprow").fadeIn(fade_speed);
        }
        fadetext("#thetotal", "$" + total);
        fadetext("#checkout-total", '$' + total);
    }
}
function calculatetip(totalcost){
    if(isUndefined(totalcost)){
        totalcost = pretiptotal;
    } else {
        pretiptotal = totalcost;
    }
    var ret = tip;
    if(ret < 0){ret = -tip * totalcost;}
    return ret;
}
function addtip(value, removeit, removeall){
    if(isUndefined(removeit)){removeit = false;}
    if(isUndefined(removeall)){removeall = false;}
    if(tip < 0) {
        tip = value;
    } else if(removeall){
        tip = tip % value;
    } else if(removeit) {
        tip -= value;
        if (tip < 0) {tip = 0;}
    } else {
        tip += value;
    }
    settip(tip, true);
    var HTML = '';
    var tips = [1];//5 , 3 ,
    value = tip;
    for(var index = 0; index < tips.length; index++){
        var temptip = Math.floor(value / tips[index]);
        if (temptip > 0) {
            value = value % tips[index];
            var tempHTML = '<div class="receipt_item">';
            if(temptip > 1){
                tempHTML += '<span class="item_qty" onclick="addtip(' + tips[index] + ', true, true);" title="Remove All">' + temptip + ' x&nbsp;</span> ';
            }
            tempHTML += '<span class="mr-auto itemname cursor-pointer" ONCLICK="changetip();" >$' + tips[index] + ' Tip</span><span>$' + (tips[index] * temptip) + '.00</span>';
            tempHTML += '<button class="bg-transparent" onclick="addtip(' + tips[index] + ', true);"><i class="fa fa-minus"></i></button>';
            tempHTML += '<button class="bg-transparent" onclick="addtip(' + tips[index] + ');"><i class="fa fa-plus"></i> </button></div>';
            HTML += tempHTML;
        }
    }
    fadetext("#tipcontrols", HTML);
}


function hastax(item){
    return notaxes.indexOf(item["itemid"]) == -1;
}

//convert the order to an HTML receipt
function generatereceipt(forcefade) {
    if ($("#myorder").length == 0) {
        log("ABORT");
        return false;
    }
    var HTML = '<div class="clearfix"></div>', tempHTML = "", subtotal = 0, fadein = false, oldvalues = "", fadein2 = false, subtotal_notax = 0;
    if (isUndefined(forcefade)) {
        forcefade = false;
    }
    if ($("#newvalues").length > 0) {
        oldvalues = $("#newvalues").html();
    }
    $("#oldvalues").stop().html("").hide().remove();
    $("#newvalues").stop().html("").hide().remove();
    var itemnames = {toppings: "pizza", wings_sauce: "lb"};
    var nonames = {toppings: "toppings", wings_sauce: "sauce"};
    for (var itemid = 0; itemid < theorder.length; itemid++) {
        var item = theorder[itemid];
        var hasaddons = item.hasOwnProperty("itemaddons") && item["itemaddons"].length > 0;
        if (!item.hasOwnProperty("clone")) {
            var quantity = 1;
            if (!hasaddons) {
                for (var seconditemid = itemid + 1; seconditemid < theorder.length; seconditemid++) {
                    var clone = theorder[seconditemid];
                    if (item.itemid == clone.itemid) {
                        theorder[seconditemid].clone = true;
                        quantity += 1;
                    }
                }
                theorder[itemid].quantity = quantity;
            }
            var totalcost = ((Number(item["itemprice"]) + (Number(item["toppingcost"]) * Number(item["toppingcount"]))) * quantity).toFixed(2);
            var category = "pizza";
            var sprite = "pizza";
            if (item.hasOwnProperty("category")) {
                category = item["category"].toLowerCase().replaceAll(" ", "_");
                sprite = category.trim();
                if (category.endswith("pizza")) {
                    category = "pizza";
                    if (item["itemname"].startswith("2")) {
                        sprite = "241_pizza";
                    }
                }
            }
            if (item.hasOwnProperty("isnew")) {
                if (item["isnew"]) {
                    item["isnew"] = false;
                    fadein = "#receipt_item_" + itemid + "-master";
                    //fadein2 = "#subitem_" + itemid
                }
            }

            if(hastax(item)) {
                subtotal += Number(totalcost);
            } else {
                subtotal_notax += Number(totalcost);
            }

            if (sprite == "sides") {
                sprite = toclassname(item["itemname"].trim()).replaceAll("_", "-");
                if (sprite.endswith("lasagna")) {
                    sprite = "lasagna";
                } else if (sprite.endswith("chicken-nuggets")) {
                    sprite = "chicken-nuggets";
                } else if (sprite.endswith("salad")) {
                    sprite = "salad";
                }
            } else if (sprite == "drinks") {
                sprite += " sprite-" + toclassname(item["itemname"].trim()).replaceAll("_", "-").replace(/\./g, '');
            }

            tempHTML = '<DIV ID="receipt_item_' + itemid + '-master"><DIV ID="receipt_item_' + itemid + '" class="receipt_item"><SPAN CLASS="item_qty" onclick="removeall(' + itemid + ');" title="Remove All">';
            if(quantity > 1) {
                tempHTML += quantity + ' x&nbsp;';
            }

            var itemname = item["itemname"];

            if(itemname.contains("[") && itemname.contains("]")){
                var units = itemname.between("[", "]").replaceAll(" ", "");
                itemname = itemname.replace("[", "").replace("]", "");
                var value = "", unit = "";
                if(quantity > 1) {
                    if (units.contains("/")) {
                        var top = ("[" + units).between("[", "/");
                        var bottom = (units + "]").between("/", "]");
                        var unit = filternumeric(bottom);
                        bottom = filternonnumeric(bottom);
                        top = top * quantity;
                        var whole = Math.floor(top / bottom);
                        top = top % bottom;
                        if (whole == 0) {whole = "";}
                        if (top == 0) {
                            value = whole;
                        } else {
                            value = whole + '<SUP>' + top + '</SUP>/<SUB>' + bottom + '</SUB>';
                        }
                    } else {
                        value = filternonnumeric(units) * quantity;
                        unit = filternumeric(units);
                    }
                    itemname += " (" + value + unit + ")";
                    theorder[itemid].units = value + unit;
                }
            }

            tempHTML += '</SPAN> <span class="mr-auto itemname">' + itemname + '</SPAN>';
            tempHTML += '<span id="cost_' + itemid + '" >$' + totalcost +'</span>';
            tempHTML += '<button class="bg-transparent " onclick="removeorderitem(' + itemid + ', ' + quantity + ');"><I CLASS="fa fa-minus"></I></button>';
            if (hasaddons) {
                tempHTML += '<button class="bg-transparent" onclick="edititem(this, ' + itemid + ');"><I CLASS="fa fa-pencil-alt"></I></button>';
            } else {
                tempHTML += '<button class="bg-transparent" onclick="cloneitem(this, ' + itemid + ');"><I CLASS="fa fa-plus"></I></button>';
            }
            tempHTML += '</div>';

            var itemname = "";
            if (hasaddons) {
                var tablename = item["itemaddons"][0]["tablename"];
                if (item["itemaddons"].length > 1) {
                    itemname = itemnames[tablename];
                }
                for (var currentitem = 0; currentitem < item["itemaddons"].length; currentitem++) {
                    var addons = item["itemaddons"][currentitem];
                    tempHTML += '<DIV CLASS="receipt_item sub_item text-muted" ID="subitem_' + itemid + '">';
                    if (itemname) {
                        tempHTML += ordinals[currentitem] + " " + itemname + ': ';
                    }
                    if(!addons.hasOwnProperty("addons") || addons["addons"].length == 0) {
                        tempHTML += 'no ' + nonames[tablename];
                    } else {
                        for (var addonid = 0; addonid < addons["addons"].length; addonid++) {
                            if (isfirstinstance2(addons["addons"], addonid)) {
                                if (addonid > 0) {
                                    tempHTML += ", ";
                                }
                                var addonname = addons["addons"][addonid]["text"];
                                var isfree = isaddon_free(tablename, addonname);
                                addonname = countaddons2(addons["addons"], addonid) + addonname;
                                if (isfree) {
                                    tempHTML += '' + addonname + '';
                                } else {
                                    tempHTML += addonname;
                                }
                            }
                        }
                    }
                    tempHTML += '</DIV>';

                }
            }
            HTML += tempHTML + '</DIV>';
        }
    }
    HTML += '<DIV ID="tipcontrols"></DIV>';

    var discountpercent = getDiscount(subtotal);
    var discount = (discountpercent * 0.01 * subtotal).toFixed(2);

    var taxes = (subtotal + deliveryfee - discount) * 0.13;//ontario only
    totalcost = subtotal + subtotal_notax - discount + deliveryfee + taxes;

    visible("#checkout", userdetails);
    createCookieValue("theorder", JSON.stringify(theorder));

    oldvalues = '<DIV id="oldvalues" class="float-right">' + oldvalues + '</div>';

    if (theorder.length == 0) {
        HTML = oldvalues + '<DIV CLASS="clearfix"></DIV><div CLASS="list-padding py-3 btn-block radius0"><div class="d-flex justify-content-center"><i class="fa fa-shopping-cart empty-shopping-cart fa-2x pb-1 text-muted"></i></div><div class="d-flex justify-content-center text-muted">Empty</div></div>';
        $("#checkout").hide();
        $("#checkoutbutton").hide();
        $("#confirmclearorder").hide();
        removeCookie("theorder");
        collapsecheckout();
        $("#checkout-btn").hide();
        fadetext("#checkout-total", '$0.00');
        forcefade = true;
    } else {
        tempHTML = "";
        tempHTML += '<DIV id="newvalues" class="float-right" ';
        if (fadein || forcefade) {
            tempHTML += 'style="display:none;"';
        }
        tempHTML += '><div><TABLE><TR><TD>Sub-total &nbsp;</TD><TD> $' + (subtotal+subtotal_notax).toFixed(2) + '</TD></TR>';
        if(discount>0){
            tempHTML += '<TR><TD>Discount (' + discountpercent + '%) &nbsp;</TD><TD> $' + discount + '</TD></TR>';
        }
        if(deliveryfee>0){ tempHTML += '<TR><TD>Delivery &nbsp;</TD><TD> $' + deliveryfee.toFixed(2) + '</TD></TR>';}
        tempHTML += '<TR><TD>Tax &nbsp;</TD><TD> $' + taxes.toFixed(2) + '</TD></TR>';

        //var thetip = calculatetip(totalcost);
        //var tipstyle = iif(thetip == 0, ' STYLE="display: none;"');
        //tempHTML += '<TR ID="tiprow"' + tipstyle + '><TD>Tip &nbsp;</TD><TD><BUTTON ONCLICK="changetip();" ID="thetip" CLASS="btn btn-sm btn-secondary">$ ' + thetip.toFixed(2) + '</BUTTON></TD></TR>';
        //totalcost = totalcost + thetip;

        tempHTML += '<TR><TD class="strong">Total &nbsp;</TD><TD class="strong" ID="thetotal"> $' + totalcost.toFixed(2) + '</TD></TR>';
        tempHTML += '</TABLE><div class="clearfix py-2"></div></DIV></DIV>';
        $("#confirmclearorder").show();
        fadetext("#checkout-total", '$' + totalcost.toFixed(2));
        if (fadein || forcefade) {
            tempHTML += oldvalues;
        }
        if (totalcost >= minimumfee) {
            $("#checkout-btn").show();
        } else {
            $("#checkout-btn").hide();
            tempHTML += '<button CLASS="list-padding bg-secondary btn-block no-icon text-dark">Minimum $' + minimumfee + ' to Order</button>';
        }
    }
    $("#myorder").html(HTML + tempHTML);
    if (fadein || forcefade) {
        fadeinall(fadein, fadein2);
    }
    if($(".circlebutton").hasClass("dont-show")){
        setTimeout(function(){
            $(".circlebutton").removeClass("dont-show").hide().fadeIn(fade_speed);
        }, fade_speed);
    }
}

function fadeinall(fadein, fadein2, doit){
    if(isUndefined(doit)){
        if (fadein) {
            $(fadein).hide();
            $(fadein2).hide();
        }
        if ($("#oldvalues").html()) {
            $("#oldvalues").show().fadeOut(fade_speed, function () {
                fadeinall(fadein, fadein2, true);
            });
        } else {
            fadeinall(fadein, fadein2, true);
        }
    } else {
        if (fadein) {
            $(fadein).fadeIn(fade_speed);
            $(fadein2).fadeIn(fade_speed);
        }
        $("#newvalues").fadeIn(fade_speed);
    }
}

function fadetext(selector, newtext){
    $(selector).fadeOut(fade_speed, function () {
        $(selector).html(newtext).fadeIn(fade_speed);
    });
}

function isfirstinstance2(addons, addonid){
    for (var i = 0; i < addonid; i++) {
        if (ismatch2(addons, addonid, i)){
            return false;
        }
    }
    return true;
}
function countaddons2(addons, addonid){
    var total = 0;
    for (var i = 0; i < addons.length; i++) {
        if (ismatch2(addons, addonid, i)){
            total+=1;
        }
    }
    if (total < 2){return "";}
    return total + "x ";
}
function ismatch2(addons, addonid1, addonid2){
    if(addons[addonid1]["isfree"] == addons[addonid2]["isfree"]){
        return addons[addonid1]["text"] == addons[addonid2]["text"];
    }
    return false;
}

//hides the checkout form
function collapsecheckout() {
    if ($("#collapseCheckout").attr("aria-expanded") == "true") {
        $("#checkout").trigger("click");
    }
}

function confirmclearorder() {
    if (theorder.length > 0) {
        confirm2("", makestring("{clear_order}"), function () {
            clearorder();
        });
    }
}

function clearorder() {
    theorder = new Array;
    tip = 0.00;
    removeorderitemdisabled = true;
    $("#newvalues").fadeOut(fade_speed);
    $(".receipt_item").fadeOut(fade_speed, function () {
        removeorderitemdisabled = false;
        generatereceipt();
        $("#oldvalues").hide();
    });
}

function edititem(element, Index) {
    var theitem = theorder[Index];
    if (!$(element).hasAttr("itemname")) {
        $(element).attr("itemname", theitem.itemname);
        $(element).attr("itemprice", theitem.itemprice);
        $(element).attr("itemid", theitem.itemid);
        $(element).attr("itemsize", theitem.itemsize);
        $(element).attr("itemcat", theitem.category);
        for (var i = 0; i < tables.length; i++) {
            $(element).attr(tables[i], 0);
        }
        $(element).attr(theitem.itemaddons[0].tablename, theitem.itemaddons.length);
    }
    loadmodal(element, true);
    currentitemID = Index;
    for (var i = 0; i < theitem.itemaddons.length; i++) {
        var tablename = theitem.itemaddons[i].tablename;
        for (var i2 = 0; i2 < theitem.itemaddons[i].addons.length; i2++) {
            var theaddon = theitem.itemaddons[i].addons[i2].text;
            currentaddonlist[i].push({name: theaddon, qual: 1, side: 1, type: tablename, group: getaddon_group(tablename, theaddon)});
        }
    }
    generateaddons();
}

//gets the addons from each dropdown
function getaddons() {
    var itemaddons = new Array;
    for (var tableid = 0; tableid < tables.length; tableid++) {
        var table = tables[tableid];
        if (table == currentaddontype) {
            for (var itemid = 0; itemid < currentaddonlist.length; itemid++) {
                var addonlist = currentaddonlist[itemid];
                var addons = new Array;
                var toppings = 0;
                for (var addonid = 0; addonid < addonlist.length; addonid++) {
                    var name = addonlist[addonid].name;
                    var isfree = isaddon_free(table, name);
                    addons.push({
                        text: name,
                        isfree: isfree
                    });
                    if (!isfree) {
                        toppings++;
                    }
                }
                itemaddons.push({tablename: table, addons: addons, count: toppings});
            }
        }
    }
    return itemaddons;
}

//get the size of a pizza
function getsize(Itemname) {
    var sizes = Object.keys(freetoppings);
    var size = "";
    for (var i = 0; i < sizes.length; i++) {
        if (!isArray(freetoppings[sizes[i]])) {
            if (Itemname.contains(sizes[i]) && sizes[i].length > size.length) {
                size = sizes[i];
            }
        }
    }
    return size;
}

function getaddon_group(Table, Addon) {
    if (groups.hasOwnProperty(Table)) {
        if (groups[Table].hasOwnProperty(Addon)) {
            return Number(groups[Table][Addon]);
        }
    }
    return 0;
}

//checks if an addon is free
function isaddon_free(Table, Addon) {
    switch (Addon.toLowerCase()) {
        case "lightly done": case "well done": return true; break;
        default: return freetoppings[Table].indexOf(Addon) > -1;
    }
}

//checks if an addon is on the whole pizza (for when we implement halves)
function isaddon_onall(Table, Addon) {
    return freetoppings["isall"][Table].indexOf(Addon) > -1;
}

function removeall(index){
    removeorderitem(index, -1);
    removeorderitem(index, 1);
}

//remove an item from the order
var removeorderitemdisabled = false;
function removeorderitem(index, quantity) {
    if (removeorderitemdisabled) {return;}
    if (quantity == 1) {
        removeindex(theorder, index);
        removeorderitemdisabled = true;
        $("#newvalues").fadeOut(fade_speed);
        $("#subitem_" + index).fadeOut(fade_speed);
        $("#receipt_item_" + index + "-master").fadeOut(fade_speed, function () {
            removeorderitemdisabled = false;
            generatereceipt(true);
            $("#oldvalues").hide();
            $("#newvalues").fadeIn(fade_speed);
        });
    } else {
        var original = theorder[index];
        var oldtext = getitemtext(index);
        for (var i = theorder.length - 1; i > index; i--) {
            if (original.itemid == theorder[i].itemid) {
                removeindex(theorder, i);
                if(quantity > -1){i = 0;}
            }
        }
        if(quantity > -1){
            var oldcost = $('#cost_' + index).text();
            unclone();
            refreshcost(index, oldcost);
            fadereceiptitem(index, oldtext);
        }
    }
}

//checks if the result is JSON, and processes the Status and Reasons
function handleresult(result, title) {
    var isToast = false;
    if(!isUndefined(title)){isToast=title == "toast";}
    try {
        var data = JSON.parse(result);
        if (data["Status"] == "false" || !data["Status"]) {
            if(isToast){
                toast(data["Reason"]);
            } else {
                alert(data["Reason"], title);
            }
        } else {
            return true;
        }
    } catch (e) {
        if(isToast){
            toast(result);
        } else {
            alert(result, title);
        }
    }
    return false;
}

function validaddress() {
    var savedaddress = $("#saveaddresses").val();
    if (savedaddress == 0) {return false;}
    if (savedaddress == "addaddress") {return isvalidaddress();}
    return true;
}

//1=American Express, 2=Visa, 3=MasterCard
function cardtype(CardNumber){
    CardNumber = CardNumber.replace(/\D/g, '');
    var digits1 = Number(CardNumber.left(1)), digits2 = Number(CardNumber.left(2)), digits4 = Number(CardNumber.left(4));
    if(digits2 == 34 || digits2 == 37){return 1;}//American Express
    if(digits1 == 4){return 2;}//Visa
    if((digits4 >= 2221 && digits4 <= 2720) || (digits2 >= 51 && digits2 <= 55)){return 3;}//MasterCard
}

//returns -1=unknown, 0=success, 1=bad card number, 2=bad expiry date, 3=bad CVV, 4=bad card type
function isvalidcreditcard(CardNumber, Month, Year, CVV) {
    var nCheck = 0, value = $("#saved-credit-info").val();
    if(value.length > 0){
        value = $("#card_" + value).html().right(7);
        CVV = 100;
        Month = value.left(2);
        Year = value.right(2);
    } else {
        if (isUndefined(CardNumber)) {CardNumber = $("[data-stripe=number]").val();}
        if (isUndefined(Month)) {Month = $("[data-stripe=exp_month]").val();}
        if (isUndefined(Year)) {Year = $("[data-stripe=exp_year]").val();}
        if (isUndefined(CVV)) {CVV = $("[data-stripe=cvc]").val().trim();}
        CardNumber = CardNumber.replace(/\D/g, '');
        switch(cardtype(CardNumber)){
            case 1: if (CardNumber.length != 15){return 5;} break;//American Express
            case 2: if (CardNumber.length != 13 && CardNumber.length != 16 && CardNumber.length != 19){return 5;} break;//Visa
            case 3: if (CardNumber.length != 16){return 5;} break;//MasterCard
            default: return 4;
        }
        var nDigit = 0, bEven = false;
        for (var n = CardNumber.length - 1; n >= 0; n--) {
            var cDigit = CardNumber.charAt(n);
            var nDigit = parseInt(cDigit, 10);
            if (bEven) {
                if ((nDigit *= 2) > 9) {nDigit -= 9;}
            }
            nCheck += nDigit;
            bEven = !bEven;
        }
    }
    if ((nCheck % 10) == 0) {
        var ExpiryDate = Number(Year) * 100 + Number(Month);
        var d = new Date();
        var CurrentDate = (d.getYear() % 100) * 100 + d.getMonth();
        if (ExpiryDate > CurrentDate) {
            if(CVV.length < 3){return 3;}
            return 0;
        } else {
            log("Failed expiry date check: " + ExpiryDate + " <= " + CurrentDate);
            return 2;
        }
    } else {
        log("Failed card number check: " + CardNumber);
        return 1;
    }
    return -1;
}

function canplaceanorder(checkpaydisabled, where) {
    var valid_creditcard = true;
    if (!$("#saved-credit-info").val() && isvalidcreditcard() != 0) {valid_creditcard = false;}
    var visible_errors = $(".error:visible").text().length == 0;
    var selected_rest = $("#restaurant").val() > 0;
    var phone_number = validphonenumber(userphonenumber());
    var valid_address = validaddress();
    var reasons = new Array();
    if (!valid_creditcard) {reasons.push("valid credit card");}
    if (!visible_errors) {reasons.push("errors in form");}
    if (!selected_rest) {reasons.push("no selected restaurant");}
    if (!phone_number) {reasons.push("phone number missing or invalid");}
    if (!valid_address) {reasons.push("valid address");}
    if (!validdeliverytime()){reasons.push("valid delivery time");}
    if ($("#orderinfo").find(".error:visible[for]").length > 0) {reasons.push("jquery validation errors");}
    if (paydisabled && checkpaydisabled){reasons.push("Already placing an order");}
    if(alertshortage()){reasons.push("Product shortage");}
    if (reasons.length > 0) {
        log("canplaceanorder: " + where + " - " + reasons.join(", "));
        return false;
    }
    return true;
}

function validphonenumber(text){
    text = text.replace(/\D/g,'');
    return text.length == 10;
}

function isnewcard(){
    return $("#saved-credit-info").val() == "";
}

var credit_card_types = {1: "American Express", 2: "Visa", 3: "MasterCard"};
function last4(LongForm, includeExpiry){
    var value = $("#saved-credit-info option:selected").val(), ret = "", endit = 0;
    if(isUndefined(LongForm)){LongForm = false;}
    if(isUndefined(includeExpiry)){includeExpiry = true;}
    if(value){
        ret = $("#saved-credit-info option:selected").text();
        if(!includeExpiry) {
            endit = ret.indexOf("Expires:");
            ret = ret.left(endit);
        }
        if(!LongForm){
            ret = ret.replace(credit_card_types[1], 1);
            ret = ret.replace(credit_card_types[2], 2);
            ret = ret.replace(credit_card_types[3], 3);
            ret = ret.replace(' Expires: ', '');
            ret = ret.replace(' x-', '');
            ret = ret.replace("/20", '');
        }
        endit = ret.indexOf("(ID:");
        if(endit > -1){ret = ret.left(endit);}
    } else {
        var card_type = cardtype($("input[data-stripe=number]").val());
        var card_number = $("input[data-stripe=number]").val().trim().right(4);
        var card_month = $("select[data-stripe=exp_month]").val();
        var card_year = $("select[data-stripe=exp_year]").val();
        if(LongForm) {
            ret = credit_card_types[card_type] + " x-" + card_number + " Expires: " + card_month + "/20" + card_year;
        } else if (includeExpiry){
            ret = card_type + "" + card_number + "" + card_month + "" + card_year;
        } else {
            ret = card_type + "" + card_number;
        }
    }
    return ret.trim();
}

//send an order to the server
function placeorder(StripeResponse) {
    if (!canplaceanorder(false, "placeorder")) {return cantplaceorder("placeorder");}
    if (isUndefined(StripeResponse)) {StripeResponse = "";}
    if (isObject(userdetails)) {
        var addressinfo = serializeaddress("#orderinfo");//i don't know why the below 2 won't get included. this forces them to be
        addressinfo["cookingnotes"] = $("#cookingnotes").val();
        addressinfo["deliverytime"] = $("#deliverytime").val();
        addressinfo["restaurant_id"] = $("#restaurant").val();
        $.post(webroot + "placeorder", {
            _token: token,
            info: addressinfo,
            stripe: StripeResponse,
            stripemode: stripemode,
            order: theorder,
            tip: calculatetip(),
            last4: last4(),
            name: $("#reg_name").val(),
            phone: $("#order_phone").val(),
            isnewcard: isnewcard()
        }, function (result) {
            placeorderstate(false);
            paydisabled = false;
            forceloading(false, "placeorder");
            if (result.contains("ordersuccess")) {
                var creditinfoval = $("#saved-credit-info").val();
                var toasttext = ["Order was placed successfully"];
                if ($("#saveaddresses").val() == "addaddress") {
                    ProcessNewAddress(result);
                    toasttext.push("New address saved");
                } else {
                    toasttext.push("Used existing address");
                }
                if(!userdetails.phone || $("#order_phone").val()){
                    userdetails.phone = $("#order_phone").val();
                    $("#user_phone").val(userdetails.phone);
                    toasttext.push("New phone number saved");
                }
                if(!debugmode) {$(".ordersuccess").html("");}
                clearorder();
                $("#checkoutmodal").modal("hide");
                handleresult(result, "ORDER RECEIPT");
                userdetails["Orders"].unshift({
                    id: $(".ordersuccess").attr("orderid"),
                    placed_at: formattednow(),
                    html: result
                });
                if (creditinfoval == ""){
                    ProcessNewCreditCard($(".ordersuccess").html());
                    toasttext.push("New credit card saved to Stripe");
                }
                //toast(toasttext.join("<BR>"));
            } else if(result.contains("[STRIPE]")) {
                validateinput("#saved-credit-info", result.replace("[STRIPE]", ""));
            } else {
                ajaxerror("Error:" + result, makestring("{not_placed}"));
            }
        });
    } else {
        showlogin("placeorder");
    }
}

function formattednow(timestamp){
    if(isUndefined(timestamp)){timestamp = Date.now();}
    var the_date = new Date(timestamp);
    var days_of_week = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
    var months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    //Wednesday August 22, 2018 @ 1:34 PM
    var day_of_month = the_date.getDate();//1-31
    var day_of_week = the_date.getDay();//0-6
    var the_month = the_date.getMonth();//0-11
    var the_year = the_date.getFullYear();//2017
    var hours24 = the_date.getHours();//0-23
    var hours12 = hours24;
    if(hours12 == 0){hours12 = 12;} else if(hours12 > 12){hours12 = hours12 - 12;}
    var minutes = the_date.getMinutes();//0-59
    var antepost = iif(hours24 < 12, "AM", "PM");
    if(minutes<10){minutes = "0" + minutes;}
    return days_of_week[day_of_week] + " " + months[the_month] + " " + day_of_month + ", " + the_year + " @ " + hours12 + ":" + minutes + " " + antepost;
}

function filternumeric(text){
    return text.replace(/[0-9]/g, '');
}
function filternonnumeric(text){
    return text.replace(/\D/g,'');
}

function ProcessNewCreditCard(Card){
    Card = JSON.parse(Card);
    Card.AddedBy = "ProcessNewCreditCard";
    if(userdetails.hasOwnProperty("Stripe")) {
        userdetails.Stripe.push(Card);
    } else {
        userdetails.Stripe = [Card];
    }
    if(!userdetails.stripecustid){
        userdetails.stripecustid = Card.customer;
    }
    log("New card detected: " + JSON.stringify(Card));
    return Card;
}
function ProcessNewAddress(result){
    var Address = {
        id: result.GetBetween('addressid="', '"'),//$(".ordersuccess").attr("addressid"),
        buzzcode: "",
        city: $("#add_city").val(),
        latitude: $("#add_latitude").val(),
        longitude: $("#add_longitude").val(),
        number: $("#add_number").val(),
        phone: userphonenumber(),
        postalcode: $("#add_postalcode").val(),
        province: $("#add_province").val(),
        street: $("#add_street").val(),
        unit: $("#add_unit").val(),
        user_id: $("#add_user_id").val()
    };
    var AddressID = IsAddressUnique(userdetails.Addresses, Address.id);
    if(AddressID == -1) {
        userdetails.Addresses.push(Address);
        $("#saveaddresses").append(AddressToOption(Address));
        refreshAddAddress();
        log("New address detected: " + JSON.stringify(Address));
    } else {
        log("Duplicate address of " + AddressID + " detected: " + JSON.stringify(Address));
    }
    return AddressID > -1;
}

function refreshAddAddress(){
    $("#addaddress").remove();
    $("#saveaddresses").append('<option value="addaddress" id="addaddress">Add Address</option>');
}

function IsAddressUnique(Addresses, ID){
    for(var i=0; i<Addresses.length; i++){
        if(Addresses[i].id == ID){return i;}
    }
    return -1;
}

if (!Date.now) {
    Date.now = function () {
        return new Date().getTime();
    }
}

var modalID = "", skipone = 0;

$(window).on('shown.bs.modal', function () {
    modalID = $(".modal:visible").attr("id");
    $("#" + modalID).hide().fadeIn(fade_speed);
    skipone = Date.now() + 100;//blocks delete button for 1/10 of a second
    switch (modalID) {
        case "profilemodal":
            $("#addresslist").html(addresses());
            $("#creditcardlist").html(creditcards());
            //checknewsletter();
            break;
    }
    window.location.hash = "modal";
});

function checknewsletter(status, where){
    if(isUndefined(where)){where = "show profilemodal";}
    console.log("checknewsletter: " + where);
    if(isUndefined(status)) {
        if (userdetails.hasOwnProperty("newsletter") && userdetails.newsletter) {
            checknewsletter(userdetails.newsletter, "has subscribed");
        } else if(userdetails === false) {
            checknewsletter(false, "no userdetails");
        } else {
            $("#newsletter").html("Checking subscription status...");
            $.post(webroot + "newsletter/issubscribed", {
                _token: token,
                email: userdetails.email
            }, function (result) {
                if (handleresult(result)) {
                    result = JSON.parse(result);
                    checknewsletter(result["Reason"]);
                }
            });
        }
    } else if(where == "changesubsscriptionstatus") {
        $("#newsletter").html("Changing subscription status...");
        $.post(webroot + "newsletter/subscribe", {
            _token: token,
            email: userdetails.email,
            name: userdetails.name,
            phone: userdetails.phone,
            status: status
        }, function (result) {
            console.log("changesubsscriptionstatus: " + result);
            checknewsletter(status, "changed status");
        });
    } else {
        var HTML = "";
        userdetails.newsletter = status;
        where = "'changesubsscriptionstatus'";
        if(status){
            HTML = 'Subscribed. <BUTTON CLASS="btn btn-primary btn-sm" ONCLICK="checknewsletter(false, ' + where + ');">Would you like to unsubscribe?</BUTTON>';
        } else {
            HTML = 'Not subscribed. <BUTTON CLASS="btn btn-primary btn-sm" ONCLICK="checknewsletter(true, ' + where + ');">Would you like to subscribe?</BUTTON>';
        }
        $("#newsletter").html(HTML);
    }
}

//generate a list of addresses and send it to the alert modal
function addresses() {
    var HTML = '<DIV CLASS="section"><div class="clearfix mt-1"></div><h2>' + makestring("{myaddress}") + '</h2><SPAN ID="addresses">';
    var number = $("#add_number").val();
    var street = $("#add_street").val();
    var city = $("#add_city").val();
    var AddNew = false;//number && street && city;
    $("#saveaddresses option").each(function () {
        var ID = $(this).val();
        if (ID > 0) {
            HTML += '<DIV class="list-group-item" ID="add_' + ID + '"><A TITLE="Delete this address" onclick="deleteaddress(' + ID + ');" class="cursor-pointer"><i class="fa fa-fw fa-times error"></i></A> ';
            HTML += $(this).text() + '</DIV>';
            AddNew = true;
        }
    });
    if (!AddNew) {
        HTML += makestring("{noaddresses}");
    }
    return HTML + "</SPAN></DIV>";
}

function creditcards() {
    var HTML = '<DIV CLASS="section"><div class="clearfix mt-1"></div><h2>' + makestring("{mycreditcard}") + '</h2><DIV ID="cardlist">';
    if (!loadsavedcreditinfo()) {
        return HTML + makestring("{nocreditcards}");
    }
    for (var i = 0; i < userdetails.Stripe.length; i++) {
        var card = userdetails.Stripe[i];
        //id,object=card,brand,country,customer,cvc_check=pass,exp_month,exp_year=2018,funding=credit,last4=4242
        HTML += '<DIV id="card_' + i + '" class="list-group-item"><A ONCLICK="deletecard(' + i + ", '" + card.id + "', " + card.last4 + ", '" + card.exp_month.pad(2) + "', " + right(card.exp_year, 2) + ');" CLASS="cursor-pointer">';
        HTML += '<i class="fa fa-fw fa-times error"></i></A> ' + card.brand + ' x-' + card.last4 + ' Expires: ' + card.exp_month.pad(2) + '/20' + right(card.exp_year, 2) + '</DIV>';
    }
    return HTML + '</DIV></DIV>';
}

function deletecard(Index, ID, last4, month, year) {
    var cardname = $("#card_" + Index).text().trim(); "x-" + last4.pad(4) + " Expiring on " + month + "/" + year;
    confirm3("card_" + Index, "Are you sure you want to delete the credit card:<br>" + cardname + "?", 'Delete Credit Card', function () {
        $.post(webroot + "placeorder", {
            _token: token,
            action: "deletecard",
            cardid: ID
        }, function (result) {
            $("#card_" + Index).fadeOut(fade_speed, function () {
                $("#card_" + Index).remove();
            });
            removeindex(userdetails.Stripe, Index);//remove it from userdetails
            if(userdetails.Stripe.length == 0){
                $("#cardlist").html(makestring("{nocreditcards}"));
            }
            toast(cardname + " deleted");
        });
    });
}

function getIterator(arr, key, value) {
    for (var i = 0; i < arr.length; i++) {
        if (arr[i][key] == value) {
            return i;
        }
    }
    return -1;
}

function GetNextOrder(CurrentID) {
    var CurrentIndex = getIterator(userdetails["Orders"], "id", CurrentID);
    if (CurrentIndex > -1 && CurrentIndex < userdetails["Orders"].length - 1) {
        orders(userdetails["Orders"][CurrentIndex + 1]["id"]);
        return true;
    }
    setTimeout(function () {
        skipunloadingscreen = false;
        loading(false, "GetNextOrder");
    }, 10);
}

function enterkey(e, action) {
    var keycode = event.which || event.keyCode;
    if (keycode == 13) {
        if (action.left(1) == "#") {
            $(action).focus();
        } else {
            log("Handle action " + action);
            handlelogin(action);
        }
    }
}

var skiploadingscreen = false;
var skipunloadingscreen = false;
var skipmodalhide = false;
//overwrites javascript's alert and use the modal popup
(function () {
    var proxied = window.alert;
    window.alert = function () {
        skipmodalhide = true;
        var title = "Alert";
        if (arguments.length > 1) {
            title = arguments[1];
        }
        $("#exclame").hide();
        $("#alert-cancel").hide();
        $("#alert-ok").off("click");
        $("#alert-confirm").off("click");
        $("#alertmodalbody").html(arguments[0]);
        $("#alertmodallabel").html(title);
        $("#alertmodal").modal('show');
    };
})();

function reseturl(Why){
    //CloseModal("reseturl: " + Why);
    history.pushState("", document.title, window.location.pathname);
}

function validateform(formselector, validity){
    log("Validating form: " + formselector);
    if(formselector.length < 2){return false;}
    var ret = true;
    if(formselector == "#addform"){
        var selector = formselector + " input[name=formatted_address]";
        if($(formselector + " input[autocomplete=really-truly-off]").length > 0){
            selector = formselector + " input[autocomplete=really-truly-off]";
        }
        validity = isvalidaddress();// $(selector).val().length > 0;
        ret = validateselector(selector, validity, 2);
        if(ret){$("#reg_address-error").hide();}
        return ret;
    }
    ret = validateselector(formselector + " input:visible", validity);
    if(!ret) {flash();}
    return ret;
}
function validateselector(selector, validity, parentlevel){
    //log("Validating selector: " + selector + " validity: " + validity + " parentlevel: " + parentlevel);
    var ret = true;
    $(selector).each(function( index ) {
        if (!validateinput(this, validity, parentlevel)) {ret = false;}
    });
    return ret;
}
function validateinput(input, validity, parentlevel){
    if(isUndefined(input)){
        $("label.error").remove();
        $(".redhighlite").removeClass("redhighlite");
        return false;
    } else if(input == "googleaddress"){
        input = getGoogleAddressSelector();
    }
    if(isUndefined(parentlevel)){parentlevel=1;}
    var ID = $(input).attr("id");
    if($(input).length == 0){
        log("ERROR: " + ID);
        return false;
    }
    if($(input)[0].hasAttribute("parentlevel")){
        parentlevel = $(input).attr("parentlevel");
    }
    var target = $(input).parent();
    for(var i= 2; i <= parentlevel; i++){
        target = target.parent();
    }
    target = target.prev().find(".fa-stack");
    if(isUndefined(validity)){validity = $(input).valid();}
    console.log("ID: " + ID + " = " + validity + " found: " + target.length + " parentlevel: " + parentlevel);
    if(validity === true) {
        $(input).removeClass("error");
        target.removeClass("redhighlite");
        $("#" + ID + "-error").remove();
        return true;
    }
    target.addClass("redhighlite");
    if(validity !== false) {
        log("Error for: " + ID + ": " + validity);
        var HTML = '<label id="' + ID + '-error" class="error" for="' + ID + '">' + validity + '</label>';
        if($("#error-" + ID).length > 0) {
            $("#error-" + ID).html(HTML);
        } else if($("#" + ID + "-error").length == 0){
            $(input).after(HTML);
        } else {
            $("#" + ID + "-error").html(validity);
        }
    }
    if(parentlevel != 1){flash();}
    return false;
}

function getformid(element){
    var id = "";
    while(!element.is("form") && !element.is("body")){
        element = element.parent();
        id = element.attr("id");
    }
    if(element.is("form")){return element.attr("id");}
}

function clearphone(why) {
    log("clearphone: " + why);
    $('#order_phone').attr("style", "");
    ajaxerror();
}


function handlefirefox(why){
    /* see "why is this commented out?" in popups_address.blade.php
     if(why == "addresschanged:showcheckout"){return false;}
     if(is_firefox_for_android){
     log("handlefirefox Why: " + why);
     $("#ffaddress").show();
     $("#formatted_address").show();
     $("#checkoutmodal").modal("hide");
     $("#firefoxandroid").show();
     } */
}

//universal AJAX error handling
var blockerror = false;
$(document).ajaxComplete(function (event, request, settings) {
    loading(false, "ajaxComplete");
    if (request.status != 200 && request.status > 0 && !blockerror) {//not OK, or aborted
        var text = request.responseText;
        if (text.indexOf('Whoops, looks like something went wrong.') > -1 && text.indexOf('<span class="exception_title">') > -1) {
            text = text.between('<span class="exception_title">', '</h2>');
            text = text.replace(/<(?:.|\n)*?>/gm, '');
            if (text.indexOf('TokenMismatchException') > -1) {
                text = "Your session has expired. Starting a new one.";
                $.get(webroot + "auth/gettoken", function (data) {
                    token = data;
                });
            }
        } else {
            text = request.statusText;
        }
        var reload = false;
        try {
            var data = JSON.parse(request.responseText);
            if (!isUndefined(data["exception"]) && !isUndefined(data["message"])) {
                data["file"] = data["file"].replace(/\\/g,"/");
                if(data["line"] == 203 && data["file"].endswith("vendor/laravel/framework/src/Illuminate/Foundation/Exceptions/Handler.php")){
                    text = "Your session has expired.<BR>Refreshing the page automatically.";
                    reload = true;
                    login();
                } else if(debugmode){
                    text = data["message"] + '<BR>Line: ' + data["line"] + '<BR>File: ' + data["file"];
                } else {
                    text = data["message"];
                }
            }
        } catch (e) {
        }
        ajaxerror(text + "<BR><BR>URL: " + settings.url, "AJAX error code: " + request.status);
        if (reload){location.reload();}
    }
    blockerror = false;
});

function ajaxerror(errortext, title){
    if(isUndefined(title)){title = "Error";}
    var selector = ".ajaxprompt:visible";
    if (isUndefined(errortext)) {
        $(selector).removeClass("ajaxsuccess").removeClass("ajaxerror").html("");
        return false;
    } else if($(selector).length > 0){
        var fontawesome = "exclamation-triangle";
        if(title.contains("success")){
            $(selector).addClass("ajaxsuccess").removeClass("ajaxerror");
            fontawesome = "smile";
        } else {
            $(selector).addClass("ajaxerror").removeClass("ajaxsuccess");
        }
        $(selector).html('<DIV CLASS="ajaxtitle">&nbsp;<i class="fas fa-' + fontawesome + '"></i>&nbsp;' + title + '</DIV>' + errortext).show();
    } else {
        alert(errortext, title);
    }
    skipunloadingscreen=false;
    paydisabled=false;
    forceloading(false, "ajaxerror");
}

function toast(Text) {
    var x = document.getElementById("snackbar");
    x.className = "show snackbar-" + database;
    x.innerHTML = Text;
    //log("toast: " + Text);
    setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
    forceloading(false, "toast");
}

function rnd(min, max) {
    return Math.round(Math.random() * (max - min) + min);
}

function cantplaceorder(Where) {
    ajaxerror();
    if(isUndefined(Where)){Where = "Unknown";}
    if(!debugmode){Where = "";} else {Where = " - " + Where;}
    $(".red").removeClass("red");
    $("#red_card").removeClass("redhighlite");
    addressstatus(true, true, false, false, "cantplaceorder" + Where);
    if (!$("#saved-credit-info").val()) {
        var validcreditcard = isvalidcreditcard();
        if (validcreditcard != 0) {
            $("#red_card").addClass("redhighlite");
            switch (validcreditcard){//-1=unknown, 0=success, 1=bad card number, 2=bad expiry date, 3=bad CVV
                case 1: validateinput("#saved-credit-info", "Please select or enter a valid credit card" + Where); break;
                case 2: validateinput("#saved-credit-info", "Please select a valid expiry date" + Where); break;
                case 3: validateinput("#saved-credit-info", "Please enter a valid CVV number" + Where); break;
                case 4: validateinput("#saved-credit-info", "Please enter a Visa, MasterCard or American Express card number" + Where); break;
                case 5: validateinput("#saved-credit-info", "The credit card number is not the correct amount of digits" + Where); break;
            }
        }
    }
    if(!validphonenumber(userphonenumber())) {
        validateinput("#order_phone", "Please enter a valid phone number" + Where);
    }
    if(!validdeliverytime()){
        GenerateHours(generalhours);
        validateinput("#deliverytime", "Please select a future delivery time" + Where);
    }
    return false;
}

function random(min, max){
    if(min == max){return min;}
    return Math.floor((Math.random() * max) + min);
}

function flash(delay){
    return false;
    if(isUndefined(delay)){delay = 500;}
    $('.redhighlite').fadeTo(delay, 0.3, function() { $(this).fadeTo(delay, 1.0).fadeTo(delay, 0.3, function() { $(this).fadeTo(delay, 1.0).fadeTo(delay, 0.3, function() { $(this).fadeTo(delay, 1.0); }); }); });
}

//state: true=can't place orders, false=can place orders
function placeorderstate(state){
    if(state){//THIS SHOULD NOT BE COMMENTED OUT!!!!
      //  $(".payfororder").removeClass("disabled");
    } else {
      //  $(".payfororder").addClass("disabled");
    }
    paydisabled = state;
}

var paydisabled = false;
function payfororder() {
    log("PAYFORORDER");
    ajaxerror();
    validateinput();
    if (!canplaceanorder(true, "payfororder")) {
        flash();
        log("Can't pay for order");
        return cantplaceorder("payfororder");
    }
    placeorderstate(true);
    forceloading(true, "payfororder");
    var $form = $('#orderinfo');
    var stripetoken = changecredit(true, 'payfororder');
    log("Attempt to pay: " + stripetoken);
    $('input').blur();
    if (isnewcard()) {
        log("Stripe data");
        loading(true, "stripe");
        placeorderstate(false);
        Stripe.card.createToken($form, stripeResponseHandler);
        log("Stripe data - complete");
    } else {//saved card
        log("Use Saved data");
        placeorder();//no stripe token, use customer ID on the server side
    }
    $(".saveaddresses").removeClass("dont-show");
}

function forceloading(state, where){
    if(state){loading(true, where);}
    skiploadingscreen = state;
    lockloading = state;
    skipunloadingscreen = state;
    if(!state){loading(false, where);}
}

function stripeResponseHandler(status, response) {
    var errormessage = "";
    log("Stripe response");
    switch (status) {
        case 400: errormessage = "Bad Request:<BR>The request was unacceptable, often due to missing a required parameter."; break;
        case 401: errormessage = "Unauthorized:<BR>No valid API key provided."; break;
        case 402: errormessage = "Request Failed:<BR>The parameters were valid but the request failed."; break;
        case 404: errormessage = "Not Found:<BR>The requested resource doesn't exist."; break;
        case 409: errormessage = "Conflict:<BR>The request conflicts with another request (perhaps due to using the same idempotent key)."; break;
        case 429: errormessage = "Too Many Requests:<BR>Too many requests hit the API too quickly. We recommend an exponential backoff of your requests."; break;
        case 500: case 502: case 503: case 504: errormessage = "Server Errors:<BR>Something went wrong on Stripe's end."; break;
        case 200:// - OK	Everything worked as expected.
            if (response.error) {
                ajaxerror(response.error.message);
            } else {
                log("Stripe successful - token: " + response.id);
                loading(false, "stripe");
                placeorder(response.id);
            } break;
    }
    if (errormessage) {
        ajaxerror(response["error"]["message"]);
        forceloading(false, "stripe error");
    }
}

var closest = false;
function addresshaschanged(place) {
    if (!getcloseststore) {return;}
    var HTML = '<OPTION VALUE="0">' + makestring("{norestaurants}") + '</OPTION>';
    if(isUndefined(place)) {
        var value = $("#saveaddresses").val();
        if (value == "0" || value == "addaddress"){
            $("#restaurant").html(HTML).val(0);
            return;
        }
        var formdata = getform("#orderinfo");
    } else {//needs latitude and longitude, radius and limit optional
        var formdata = {latitude:  place.geometry.location.lat, longitude:  place.geometry.location.lng};
    }
    formdata.limit = 10;
    if (!formdata.latitude || !formdata.longitude) {return;}
    //skiploadingscreen = true;
    //canplaceorder = false;
    $.post(webroot + "placeorder", {
        _token: token,
        info: formdata,
        action: "closestrestaurant"
    }, function (result) {
        if (handleresult(result)) {
            closest = JSON.parse(result)["closest"];
            var smallest = "0";
            if (closest.length > 0) {//} closest.hasOwnProperty("id")) {
                HTML = '';
                var distance = -1;
                for (var i = 0; i < closest.length; i++) {
                    var restaurant = closest[i];
                    closest[i].restid = restaurant.restaurant.id;
                    restaurant.distance = parseFloat(restaurant.distance);
                    var distancetext = "";
                    if (distance == -1 || distance > restaurant.distance) {
                        smallest = restaurant.restaurant.id;
                        distance = restaurant.distance;
                        distancetext = ' (' + restaurant.distance.toFixed(2) + ' km)';
                    }
                    HTML += '<OPTION VALUE="' + restaurant.restaurant.id + '">' + restaurant.restaurant.name + '</OPTION>';
                }
            }
            if (!smallest) {
                smallest = 0;
            }
            $("#restaurant").html(HTML).val(smallest);
            restchange("addresshaschanged");
        }
    });
}

function testclosest() {
    var formdata = getform("#orderinfo");
    formdata.limit = 10;
    if (!formdata.latitude || !formdata.longitude) {
        alert(makestring("{long_lat}"));
        return;
    }
    $.post(webroot + "placeorder", {
        _token: token,
        info: formdata,
        action: "closestrestaurant"
    }, function (result) {
        if (handleresult(result)) {
            alert(result, makestring("{ten_closest}"));
        }
    });
}

function loadsavedcreditinfo() {
    if (userdetails.stripecustid.length > 0) {
        if(userdetails.hasOwnProperty("Stripe")) {
            return userdetails.Stripe.length > 0;
        }
    }
    return false;
}

function changecredit(focus, where) {
    ajaxerror();
    log("changecredit: " + where);
    $("#saved-credit-info").removeClass("red");
    $("[data-stripe=number]").removeClass("red");
    var val = $("#saved-credit-info").val();
    $("#red_card").removeClass("redhighlite");
    if (!val) {
        $(".credit-info").show();//let cust edit the card
        if(focus){focuson("input[data-stripe=number]");}
    } else {
        $(".credit-info").hide();//use saved card info
    }
    return val;
}

function creditcardstatus(disabled){
    if(is_android && is_chrome) {
        document.getElementById("saved-credit-info").disabled = disabled;
        if(disabled){
            $("#chromeccbutton").show();
        } else {
            $("#chromeccbutton").hide();
        }
    }
}

function selectaddress(address){
    if(isUndefined(address)) {
        if (userdetails["Addresses"].length == 0) {
            selectaddress("addaddress");
            addresshaschanged();
            $(getGoogleAddressSelector()).show();
            visible_address(true);
        } else if (userdetails["Addresses"].length == 1) {
            selectaddress(userdetails["Addresses"][0].id);
        } else {
            selectaddress(0);
        }
    } else {
        if (isNaN(address)) {
            setTimeout(function () {
                $("#saveaddresses").val(address);
            }, 100);
        } else {
            $("#saveaddresses").val(address);
        }
        addresschanged("showcheckout");
    }
}

function userisloggedin(){
    return userdetails.hasOwnProperty("id");
}

var needscheckout = false;
function showcheckout() {
    if(!userisloggedin()){
        needscheckout = true;
        return showlogin("showcheckout");
    }
    needscheckout = false;
    $(getGoogleAddressSelector()).val("");
    //placeorderstate(false);
    var HTML = $("#checkoutaddress").html();
    HTML = HTML.replace('class="', 'class="corner-top ');
    var needscreditrefresh = false;
    if (loadsavedcreditinfo()) {
        $(".credit-info").hide();
        var creditHTML = '<SELECT ID="saved-credit-info" name="creditcard" onchange="changecredit(true, ' + "'showcheckout1'" + ');" class="form-control proper-height"><OPTION value="">Add Card</OPTION>';
        for (var i = 0; i < userdetails.Stripe.length; i++) {
            var card = userdetails.Stripe[i];
            creditHTML += '<OPTION value="' + card.id + '" id="card_' + card.id + '"';
            if (i == userdetails.Stripe.length - 1) {
                creditHTML += ' SELECTED';
            }
            var cardtext = card.brand + ' x-' + card.last4 + ' Expires: ' + card.exp_month.pad(2) + '/20' + right(card.exp_year, 2);
            if(debugmode){cardtext += " (" + card.id.replace("card_", "ID: ") + ")";}
            log("Card: " + (i+1) + " of " + userdetails.Stripe.length + " = " + cardtext);
            creditHTML += '>' + cardtext + '</OPTION>';
        }
        $("#credit-info").html(creditHTML + '</SELECT>');
    } else {
        $("#credit-info").html('<INPUT TYPE="hidden" ID="saved-credit-info">');
        needscreditrefresh = true;
    }
    $("#checkoutaddress").html(HTML);
    $("#deliverytime").val($("#deliverytime option:first").val());

    visible("#userphone", !userdetails.phone);
    $("#order_phone").val(userdetails.phone);
    $("#checkoutmodal").modal("show");

    $(function () {
        $("#orderinfo").validate({
            submitHandler: function (form) {
                //handled by placeorder
            },
            onkeyup :false,
            onfocusout: false
        });
    });
    $("#restaurant").html('<option value="0">Select Restaurant</option>').val("0");
    refreshform("#saveaddresses");
    if(needscreditrefresh){changecredit(false, 'showcheckout2');}
    validateinput();
    selectaddress();
    refreshAddAddress();
}

function clearvalidation(specificselector){
    if(!isUndefined(specificselector)){specificselector = ".redhighlite";}
    $(specificselector).removeClass("redhighlite");
    $(".error").hide();
}

var daysofweek = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
var monthnames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

function now() {
    if(testing){return newtime;}
    var now = new Date();
    return now.getHours() * 100 + now.getMinutes();
}

function getToday() {
    return getNow(3);//doesn't take into account <= because it takes more than 1 minute to place an order
}

function GenerateTime(time) {
    var minutes = time % 100;
    var thehours = Math.floor(time / 100);
    var hoursAMPM = thehours % 12;
    if (hoursAMPM == 0) {
        hoursAMPM = 12;
    }
    var tempstr = hoursAMPM + ":";
    if (minutes < 10) {
        tempstr += "0" + minutes;
    } else {
        tempstr += minutes;
    }
    var extra = "";
    if (time == 0) {
        extra = " (Midnight)";
    } else if (time == 1200) {
        extra = " (Noon)";
    }
    if (time < 1200) {
        return tempstr + " AM" + extra;
    } else {
        return tempstr + " PM" + extra;
    }
}



function gettime(now, IncrementBig, IncrementSmall, OldTime){
    var minutes = now.getMinutes();
    minutes = minutes + IncrementBig;
    minutes = minutes + (IncrementSmall - (minutes % IncrementSmall));
    now.setMinutes(minutes);
    var time = now.getHours() * 100 + now.getMinutes();
    return [now, time, OldTime, IncrementBig, IncrementSmall];
}
function addtotime(time, increments){
    time = time + increments;
    if (time % 100 >= 60) {
        return (Math.floor(time / 100) + 1) * 100;
    }
    return time;
}

//Index: 0=hour, 1=minute, 2=24hr time, 3=day of week(0-6), 4=date, 5=tomorrow
function getNow(Index, AllowTesting){
    if(isUndefined(Index)){
        return Math.floor(Date.now() / 1000);//reduce to seconds
    }
    if(isUndefined(AllowTesting)){AllowTesting = true;}
    var now = new Date();
    switch (Index){
        case 0: //hour
            if(testing && AllowTesting){return Math.floor(newtime / 100);}
            return now.getHours();
            break;
        case 1: //minute
            if(testing && AllowTesting){return Math.floor(newtime % 100);}
            return now.getMinutes();
            break;
        case 2://hour+minute(24 hour)
            if(testing && AllowTesting){return newtime;}
            return now.getHours() * 100 + now.getMinutes();
            break;
        case 3: //day of week
            if(testing && AllowTesting){return newday;}
            return now.getDay();
            break;
        case 4: case 5: //date
            if(testing && AllowTesting){
                now.setHours(Math.floor(newtime / 100));
                now.setMinutes(Math.floor(newtime % 100));
                now.setSeconds(0);
                var currentday = now.getDay();
                if(currentday > newday){
                    now.add("day", 6 - currentday + newday);
                } else if (currentday < newday){
                    now.add("day", newday - currentday);
                }
            }
            if(Index == 5){
                return now.add("day", 1);
            }
            return now;
            break;
    }
}

function toTimestamp(strDate){
    var datum = Date.parse(strDate);
    return datum/1000;
}

function fromtimestamp(unix_timestamp){
    return new Date(unix_timestamp * 1000);
}
function totimestamp(time, wasnow){
    if (isUndefined(time)){return Math.floor(Date.now() / 1000);}
    var now = new Date(wasnow.getTime());
    var timezone = now.getTimezoneOffset() / 60;
    now.setUTCHours(time / 100 + timezone);
    now.setUTCMinutes(time % 100);
    return toTimestamp(now) + timestampoffset;
}

//getNow(Index){Index: 0=hour, 1=minute, 2=24hr time, 3=day of week(0-6), 4=date, 5=tomorrow
function isopen(hours, dayofweek, time) {
    var now = getNow(4);//doesn't take into account <= because it takes more than 1 minute to place an order
    if (isUndefined(dayofweek)) {dayofweek = getNow(3);}
    if (isUndefined(time)) {time = getNow(2);}//now.getHours() * 100 + now.getMinutes();
    var today = hours[dayofweek];
    if (!today.hasOwnProperty("open")){return false;}
    var yesterday = dayofweek - 1;
    if (yesterday < 0) {
        yesterday = 6;
    }
    var yesterdaysdate = yesterday;
    yesterday = hours[yesterday];
    today.open = Number(today.open);
    today.close = Number(today.close);
    yesterday.open = Number(yesterday.open);
    yesterday.close = Number(yesterday.close);
    var tempstr = "[isopen] dayofweek: " + dayofweek + " time: " + time + " Checking:";
    if (yesterday.open > -1 && yesterday.close > -1 && yesterday.close < yesterday.open) {
        tempstr += " Yesterday close: " + yesterday.close + " open: " + yesterday.open;
        if (yesterday.close >= time) {
            //log(tempstr + " True (" + yesterdaysdate + ")");
            return yesterdaysdate;
        }
    }
    if (today.open > -1 && today.close > -1) {
        tempstr += " Today close: " + today.close + " open: " + today.open;
        if (today.close < today.open) {
            if (time >= today.open || time <= today.close) {
                //log(tempstr + " = True (" + dayofweek + ")");
                return dayofweek;
            }
        } else {
            if (time >= today.open && time <= today.close) {
                //log(tempstr + " = True (" + dayofweek + ")");
                return dayofweek;
            }
        }
    }
    //log(tempstr + " = False");
    return -1;//closed
}

function visiblemodals() {
    return $('.modal:visible').map(function () {
        return this.id;
    }).get();
}

if (isUndefined(unikeys)) {
    var unikeys = {
        exists_already: "'[name]' already exists",
        cat_name: "What name would you like the category to be?\r\nIt will only be saved when you add an item to the category",
        not_placed: "Order was not placed!",
        error_login: "Error logging in",
        email_needed: "Please enter a valid email address",
        long_lat: "Longitude and/or latitude missing",
        ten_closest: "10 closest restaurants",
        clear_order: "Clear your order?",
        myaddress: "My Addresses",
        noaddresses: 'No Addresses Saved',
        mycreditcard: 'My Credit Cards',
        nocreditcards: 'No Credit Cards',
        norestaurants: 'No {storename} within range'
    };
}

var oneclick = true, currentstyle = 1, currentbasecost = 0, currentaddoncost = 0;
var currentaddontype = "", currentside = "", currentqualifier = "", addonname = "", item_name = "", hashalves = true;
var currentaddonlist = new Array, currentitemindex = 0, currentitemname = "";

function toclassname(text) {
    return text.toLowerCase().replaceAll(" ", "_");
}

function generateaddons(ItemIndex, ToppingIndex) {
    var HTML = '';
    var totaltoppings = 0;
    if (isUndefined(ItemIndex)) {
        ItemIndex = -1;
        ToppingIndex = -1;
    }
    switch (currentaddontype) {
        case "toppings":
            addonname = "Toppings";
            item_name = "Pizza ";
            break;
        case "wings_sauce":
            addonname = "Sauce";
            item_name = "Lb";
            break;
        default:
            addonname = "Error: " + currentaddontype;
    }

    var thisside = ' CLASS="thisside">';
    var showthisitem = 0;
    for (var itemindex = 0; itemindex < currentaddonlist.length; itemindex++) {
        var freetoppings = 0;
        var paidtoppings = 0;
        var tempstr = '';
        var classname = 'itemcontents itemcontents' + itemindex;

        HTML += '<DIV ONCLICK="selectitem(event, ' + itemindex + ');" CLASS="list-group-item d-flex flex-wrap receipt-addons currentitem currentitem' + itemindex;
        if (currentitemindex == itemindex) {
            HTML += ' thisside';
        }
        HTML += '">' + '<strong class="pr-3" id="item_' + itemindex + '">' + ucfirst(item_name) + ' #' + (itemindex + 1) + '</strong>';

        if(currentaddonlist[itemindex].length == 0){
            tempstr += ' No ' + addonname; //leave for the users who assume we'll pick toppings for them
        }
        for (var i = 0; i < currentaddonlist[itemindex].length; i++) {
            var currentaddon = currentaddonlist[itemindex][i];
            var qualifier = "";
            if(isfirstinstance(itemindex, i)) {
                tempstr += '<DIV CLASS="' + classname + '" id="topping_' + itemindex + '_' + i + '">' + countaddons(itemindex, i) + currentaddon.name ;
                //<!--span ONCLICK="removelistitem(' + itemindex + ', ' + i + ');">&nbsp; <i CLASS="fa fa-times"></i> </span-->
                if(!islasttopping(itemindex, i)){
                    tempstr += ',&nbsp;';
                }
                tempstr+= '</div>';
                if(ismatch(itemindex, ToppingIndex, i) && itemindex == ItemIndex){
                    ToppingIndex = i;
                }
            }
            qualifier = currentaddon.qual;
            if (qualifier == 0) {
                qualifier = 0.5;
            } else if (currentaddon.side != 1) {
                qualifier = qualifier * 0.5;
            }
            if (isaddon_free(currentaddontype, currentaddon.name)) {
                freetoppings += qualifier;
            } else {
                paidtoppings += qualifier;
            }
        }
        totaltoppings += Math.ceil(paidtoppings);
        if (debugmode) {
            HTML += '<SPAN TITLE="THIS IS ONLY SHOWN IN DEBUG MODE" CLASS="debugmode">(Paid: ' + paidtoppings + ' Free: ' + freetoppings + ')</SPAN> ';
        }
        HTML += tempstr + '</DIV>';
    }

    itemtotalprice(getcost(totaltoppings));
    $("#theaddons").html(HTML);
    $(".currentitem.thisside").trigger("click");
    refreshremovebutton();
    if (ItemIndex > -1) {
        log("FADE: #topping_" + ItemIndex + "_" + ToppingIndex);
        $("#topping_" + ItemIndex + "_" + ToppingIndex).hide().fadeTo(fade_speed, 1);
    }
}

function itemtotalprice(newprice, fade){
    if(isUndefined(fade)){fade = true;}
    newprice = Number(newprice).toFixed(2);
    var current = $("#modal-itemtotalprice").text();
    if(current != newprice) {
        if (fade) {
            $("#modal-itemtotalprice-all").stop(true, true).fadeTo(fade_speed, 0, function() {
                $("#modal-itemtotalprice").text(newprice);
                $("#modal-itemtotalprice-all").fadeTo(fade_speed, 1);
            });
        } else {
            $("#modal-itemtotalprice").text(newprice);
        }
    }
}

function ismatch(itemindex, toppingindex1, toppingindex2){
    log("itemindex: " + itemindex + " toppingindex1: " +  toppingindex1 + " toppingindex2: " + toppingindex2 + " currentaddonlist.length: " + currentaddonlist.length);
    if(itemindex >= currentaddonlist.length){return false;}
    if (toppingindex1 == -1 || toppingindex2 == -1 || toppingindex1 >= currentaddonlist[itemindex].length || toppingindex2 >= currentaddonlist[itemindex].length){return false;}
    var topping1 = currentaddonlist[itemindex][toppingindex1];
    var topping2 = currentaddonlist[itemindex][toppingindex2];
    if (topping1.qual == topping2.qual && topping1.side == topping2.side){
        if (topping1.type == topping2.type) {
            return topping1.name == topping2.name;
        }
    }
    return false;
}

function isfirstinstance(itemindex, toppingindex){
    for (var i = 0; i < toppingindex; i++) {
        if (ismatch(itemindex, toppingindex, i)){
            return false;
        }
    }
    return true;
}

function islasttopping(itemindex, toppingindex){
    for (var i = toppingindex+1; i < currentaddonlist[itemindex].length; i++) {
        if (!ismatch(itemindex, toppingindex, i)){
            return false;
        }
    }
    return true;
}

function countaddons(itemindex, toppingindex){
    var total = 0;
    for (var i = 0; i < currentaddonlist[itemindex].length; i++) {
        if (ismatch(itemindex, toppingindex, i)){
            total+=1;
        }
    }
    if (total < 2){return "";}
    return total + "x ";
}

function getcost(Toppings) {
    //itemcost, itemname, size, toppingcost
    if (currentitem.toppingcost) {
        var itemcost = parseFloat(currentitem.itemcost.replace("$", ""));
        itemcost += parseFloat(currentitem.toppingcost) * Number(Toppings);
        return itemcost.toFixed(2);// + " (" + Toppings + " addons)";
    }
    return $("#modal-itemprice").text();
}

function list_addons_quantity(quantity, tablename, halves, name, basecost, addoncost) {
    currentaddonlist = new Array();
    currentitemindex = 0;
    for (var i = 0; i < quantity; i++) {
        currentaddonlist.push([]);
    }
    currentitemname = name;
    currentbasecost = basecost;
    currentaddoncost = addoncost;
    list_addons(tablename, halves);
}

function list_addons(table, halves) {
    currentaddontype = table;
    var HTML = '<DIV class="receipt-addons-list"><DIV id="theaddons"></DIV></DIV>';
    if (currentstyle == 0) {
        HTML += '<DIV CLASS="addonlist" ID="addontypes">';
    }
    var types = Object.keys(alladdons[table]);
    if (currentstyle == 0) {
        $("#addonlist").html(HTML + '</DIV>');
    } else {
        HTML += '<div class="toppinglist">';

        var breaker_green = 0;
        var breaker_red = 0;
        for (var i = 0; i < types.length; i++) {
            for (var i2 = 0; i2 < alladdons[currentaddontype][types[i]].length; i2++) {
                var addon = alladdons[currentaddontype][types[i]][i2];
                var title = "";
                var breaker_css_green = "";
                var breaker_css_red = "";


                if(types[i] == 'Vegetable' && breaker_green == 0){
                    //   breaker_css_green = ' note_green ';
                    //   breaker_green = 1;
                }
                if(types[i] == 'Meat' && breaker_red == 0){
                    //    breaker_css_red = ' note_red ';
                    //   breaker_red = 1;
                }

                HTML += '<button class="fourthwidth bg-white2 bg-'+types[i]+ ' ' + breaker_css_green +  breaker_css_red + ' addon-addon list-group-item-action toppings_btn';
                if (isaddon_free(String(currentaddontype), String(addon))) {
                    title = "Free addon";
                }
                HTML += '" TITLE="' + title + '">' + addon +'</button>';
            }
        }

        HTML += '<button class="fourthwidth toppings_btn bg-white2 list-group-item-action" id="removeitemfromorder"><i style="font-size: 1rem !important;" class=" fa fa-arrow-left removeitemarrow" ></i></button>' +
            '<button class="btn-primary fourthwidth toppings_btn" data-popup-close="menumodal" data-dismiss="modal" id="additemtoorder" onclick="additemtoorder();">ADD</button>';

        $("#addonlist").html(HTML);
        $(".addon-addon").click(
            function (event) {
                list_addon_addon(event);
            }
        );
    }
    $(".addon-type").click(
        function (event) {
            list_addon_type(event);
        }
    );
    hashalves = halves;
    generateaddons();
}

function list_addon_type(e) {
    $(".addon-type").removeClass("addon-selected");
    $(e.target).addClass("addon-selected");
    $("#addonall").remove();
    $("#addonedit").remove();
    var HTML = '<DIV ID="addonall">';
    var addontype = $(e.target).text();
    for (var i = 0; i < alladdons[currentaddontype][addontype].length; i++) {
        var addon = alladdons[currentaddontype][addontype][i];
        HTML += '<DIV class="addon-addon">' + addon + '</DIV>';
    }
    $(e.target).after(HTML + '</DIV>');
    $(".addon-addon").click(
        function () {
            list_addon_addon(event);
        }
    );
}

function list_addon_addon(e) {
    addonname = $(e.target).text();
    if (oneclick) {
        currentqualifier = 1;
        return addtoitem();
    }
    $(".addon-addon").removeClass("addon-selected");
    $(e.target).addClass("addon-selected");
    $("#addonedit").remove();
    var HTML = '<DIV ID="addonedit">';
    if (isaddon_free(currentaddontype, addonname)) {
        HTML += '<DIV>This is a free addon</DIV>';
    }

    if (hashalves) {
        if (isaddon_onall(currentaddontype, addonname)) {
            HTML += '<DIV>This addon goes on the whole item</DIV>';
            currentside = 1;
        } else {
            HTML += makelist("Side", "addon-side", ["Left", "Whole", "Right"], 1);
        }
    }

    if (qualifiers[currentaddontype].hasOwnProperty(addonname)) {
        HTML += makelist("Qualifier", "addon-qualifier", qualifiers[currentaddontype][addonname], 1);
    } else {
        HTML += makelist("Qualifier", "addon-qualifier", qualifiers["DEFAULT"], 1);
    }

    HTML += '<BUTTON ONCLICK="addtoitem();"">Add to item</BUTTON>';
    $(e.target).after(HTML + '</DIV>');
}

function makelist(Title, classname, data, defaultindex) {
    var HTML = '<DIV><DIV>' + Title + ':</DIV>';
    var selected;
    for (var i = 0; i < data.length; i++) {
        selected = "";
        if (i == defaultindex) {
            selected = " addon-selected";
        }
        HTML += '<DIV CLASS="addon-list ' + classname + selected + '" ONCLICK="list_addon_list(event, ' + "'" + classname + "', " + i + ');">' + data[i] + '</DIV>';
    }
    switch (classname) {
        case "addon-qualifier":
            currentqualifier = defaultindex; break;
        case "addon-side":
            currentside = defaultindex; break;
    }
    return HTML + '</DIV>';
}

function list_addon_list(e, classname, index) {
    var listitemname = $(e.target).text();
    //if(classname == "addon-qualifier" && index == 0){index = "0.5";}
    $("." + classname).removeClass("addon-selected");
    $(e.target).addClass("addon-selected");
    switch (classname) {
        case "addon-qualifier":
            currentqualifier = index; break;
        case "addon-side":
            currentside = index; break;
    }
    log(classname + "." + listitemname + "=" + index);
}

function addtoitem() {
    if (!hashalves) {
        currentside = 1;
    }
    var removed = "";
    var group = getaddon_group(currentaddontype, addonname);
    currentaddonlist[currentitemindex].push({
        name: addonname,
        side: currentside,
        qual: currentqualifier,
        type: currentaddontype,
        group: group
    });
    if (group > 0) {
        for (var i = currentaddonlist[currentitemindex].length - 2; i > -1; i--) {
            if (currentaddonlist[currentitemindex][i]["group"] == group) {
                removed = currentaddonlist[currentitemindex][i]["name"];
                if (removed == addonname) {
                    removed = "";
                }
                removelistitem(currentitemindex, i);
            }
        }
    }
    if (!oneclick) {
        $(".addon-selected").removeClass("addon-selected");
        $("#addonall").remove();
        $("#addonedit").remove();
    }
    if (removed) {
        removed += " was removed";
    }
    // $("#removelist").text(removed);
    generateaddons(currentitemindex, currentaddonlist[currentitemindex].length - 1);
}

function selectitem(e, index) {
    $(".currentitem").removeClass("thisside");
    $(".currentitem" + index).addClass("thisside");
    currentitemindex = index;
    refreshremovebutton();
}

function removelistitem(index, subindex) {
    if (isUndefined(subindex)) {
        removeindex(currentaddonlist, index);
    } else {
        removeindex(currentaddonlist[index], subindex);
    }
    generateaddons();
}

function makeplural(value, singular, plural){
    if(value == 1){return singular;}
    if(isUndefined(plural)){return singular + "s";}
    return plural;
}

function ucfirst(text) {
    return text.left(1).toUpperCase() + text.right(text.length - 1);
}

function iif(value, iftrue, iffalse) {
    if (value) {return iftrue;}
    if (isUndefined(iffalse)) {return "";}
    return iffalse;
}

function ismodalvisible(){
    return $(".modal:visible").length > 0;
}
function CloseModal(Why){
    if(ismodalvisible()) {
        var modalname = $(".modal:visible").attr("id");
        log("Closing #" + modalname + " modal: " + Why);
        if(modalname == "loginmodal"){return false;}
        $(".modal:visible").modal("hide");
        return true;
    }
    log("No modal was open");
    return false;
}

function scrolltobottom() {
    $('html,body').animate({scrollTop: document.body.scrollHeight}, "slow");
}

//NProgress.start(); NProgress.set(0.4); NProgress.inc(); NProgress.done(); http://ricostacruz.com/nprogress/
;(function(root,factory){if(typeof define==='function'&&define.amd){define(factory);}else if(typeof exports==='object'){module.exports=factory();}else{root.NProgress=factory();}})(this,function(){var NProgress={};NProgress.version='0.2.0';var Settings=NProgress.settings={minimum:0.08,easing:'ease',positionUsing:'',speed:200,trickle:true,trickleRate:0.02,trickleSpeed:800,showSpinner:true,barSelector:'[role="bar"]',spinnerSelector:'[role="spinner"]',parent:'body',template:'<div class="bar" role="bar"><div class="peg"></div></div><div class="spinner" role="spinner"><div class="spinner-icon"></div></div>'};NProgress.configure=function(options){var key,value;for(key in options){value=options[key];if(value!==undefined&&options.hasOwnProperty(key))Settings[key]=value;}
    return this;};NProgress.status=null;NProgress.set=function(n){var started=NProgress.isStarted();n=clamp(n,Settings.minimum,1);NProgress.status=(n===1?null:n);var progress=NProgress.render(!started),bar=progress.querySelector(Settings.barSelector),speed=Settings.speed,ease=Settings.easing;progress.offsetWidth;queue(function(next){if(Settings.positionUsing==='')Settings.positionUsing=NProgress.getPositioningCSS();css(bar,barPositionCSS(n,speed,ease));if(n===1){css(progress,{transition:'none',opacity:1});progress.offsetWidth;setTimeout(function(){css(progress,{transition:'all '+ speed+'ms linear',opacity:0});setTimeout(function(){NProgress.remove();next();},speed);},speed);}else{setTimeout(next,speed);}});return this;};NProgress.isStarted=function(){return typeof NProgress.status==='number';};NProgress.start=function(){$("#loading").show();if(!NProgress.status)NProgress.set(0);var work=function(){setTimeout(function(){if(!NProgress.status)return;NProgress.trickle();work();},Settings.trickleSpeed);};if(Settings.trickle)work();return this;};NProgress.done=function(force){$("#loading").hide();if(!force&&!NProgress.status)return this;return NProgress.inc(0.3+ 0.5*Math.random()).set(1);};NProgress.inc=function(amount){var n=NProgress.status;if(!n){return NProgress.start();}else{if(typeof amount!=='number'){amount=(1- n)*clamp(Math.random()*n,0.1,0.95);}
    n=clamp(n+ amount,0,0.994);return NProgress.set(n);}};NProgress.trickle=function(){return NProgress.inc(Math.random()*Settings.trickleRate);};(function(){var initial=0,current=0;NProgress.promise=function($promise){if(!$promise||$promise.state()==="resolved"){return this;}
    if(current===0){NProgress.start();}
    initial++;current++;$promise.always(function(){current--;if(current===0){initial=0;NProgress.done();}else{NProgress.set((initial- current)/ initial);
    }});return this;};})();NProgress.render=function(fromStart){if(NProgress.isRendered())return document.getElementById('nprogress');addClass(document.documentElement,'nprogress-busy');var progress=document.createElement('div');progress.id='nprogress';progress.innerHTML=Settings.template;var bar=progress.querySelector(Settings.barSelector),perc=fromStart?'-100':toBarPerc(NProgress.status||0),parent=document.querySelector(Settings.parent),spinner;css(bar,{transition:'all 0 linear',transform:'translate3d('+ perc+'%,0,0)'});if(!Settings.showSpinner){spinner=progress.querySelector(Settings.spinnerSelector);spinner&&removeElement(spinner);}
    if(parent!=document.body){addClass(parent,'nprogress-custom-parent');}
    parent.appendChild(progress);return progress;};NProgress.remove=function(){removeClass(document.documentElement,'nprogress-busy');removeClass(document.querySelector(Settings.parent),'nprogress-custom-parent');var progress=document.getElementById('nprogress');progress&&removeElement(progress);};NProgress.isRendered=function(){return!!document.getElementById('nprogress');};NProgress.getPositioningCSS=function(){var bodyStyle=document.body.style;var vendorPrefix=('WebkitTransform'in bodyStyle)?'Webkit':('MozTransform'in bodyStyle)?'Moz':('msTransform'in bodyStyle)?'ms':('OTransform'in bodyStyle)?'O':'';if(vendorPrefix+'Perspective'in bodyStyle){return'translate3d';}else if(vendorPrefix+'Transform'in bodyStyle){return'translate';}else{return'margin';}};function clamp(n,min,max){if(n<min)return min;if(n>max)return max;return n;}
    function toBarPerc(n){return(-1+ n)*100;}
    function barPositionCSS(n,speed,ease){var barCSS;if(Settings.positionUsing==='translate3d'){barCSS={transform:'translate3d('+toBarPerc(n)+'%,0,0)'};}else if(Settings.positionUsing==='translate'){barCSS={transform:'translate('+toBarPerc(n)+'%,0)'};}else{barCSS={'margin-left':toBarPerc(n)+'%'};}
        barCSS.transition='all '+speed+'ms '+ease;return barCSS;}
    var queue=(function(){var pending=[];function next(){var fn=pending.shift();if(fn){fn(next);}}
        return function(fn){pending.push(fn);if(pending.length==1)next();};})();var css=(function(){var cssPrefixes=['Webkit','O','Moz','ms'],cssProps={};function camelCase(string){return string.replace(/^-ms-/,'ms-').replace(/-([\da-z])/gi,function(match,letter){return letter.toUpperCase();});}
        function getVendorProp(name){
            var style=document.body.style;if(name in style)return name;var i=cssPrefixes.length,capName=name.charAt(0).toUpperCase()+ name.slice(1),vendorName;while(i--){vendorName=cssPrefixes[i]+ capName;if(vendorName in style)return vendorName;}
            return name;}
        function getStyleProp(name){name=camelCase(name);return cssProps[name]||(cssProps[name]=getVendorProp(name));}
        function applyCss(element,prop,value){prop=getStyleProp(prop);element.style[prop]=value;}
        return function(element,properties){var args=arguments,prop,value;if(args.length==2){for(prop in properties){value=properties[prop];if(value!==undefined&&properties.hasOwnProperty(prop))applyCss(element,prop,value);}}else{applyCss(element,args[1],args[2]);}}})();function hasClass(element,name){var list=typeof element=='string'?element:classList(element);return list.indexOf(' '+ name+' ')>=0;}
    function addClass(element,name){var oldList=classList(element),newList=oldList+ name;if(hasClass(oldList,name))return;element.className=newList.substring(1);}
    function removeClass(element,name){var oldList=classList(element),newList;if(!hasClass(element,name))return;newList=oldList.replace(' '+ name+' ',' ');element.className=newList.substring(1,newList.length- 1);}
    function classList(element){return(' '+(element.className||'')+' ').replace(/\s+/gi,' ');}
    function removeElement(element){element&&element.parentNode&&element.parentNode.removeChild(element);}
    return NProgress;});

function isReady(){
    return document.readyState === 'complete';
}

function loading(state, where) {
    if (state) {
        ajaxerror();
        log("loading start " + where);
        NProgress.start();
    } else if(!skipunloadingscreen) {
        log("loading end " + where);
        NProgress.done();
    }
}

$.ajaxSetup({ xhr: function () {
    var xhr = new window.XMLHttpRequest();
    xhr.upload.addEventListener("progress", function(evt) {
        if (evt.lengthComputable) {
            var percentComplete = evt.loaded / evt.total * 0.9;//stop event will handle 100%
            NProgress.set(percentComplete);
        }
    }, false);

    xhr.addEventListener("progress", function(evt) {
        if (evt.lengthComputable) {
            var percentComplete = evt.loaded / evt.total * 0.9;//stop event will handle 100%
            NProgress.set(percentComplete);
        }
    }, false);
    return xhr;
} });

function checkblock(e) {
    var checked = $(e.target).is(':checked');
    BeforeUnload(checked);
}
function BeforeUnload(enable) {
    if (enable) {
        window.onbeforeunload = function (e) {
            return "Discard changes?";
        };
        log("Page transitions blocked");
    } else {
        window.onbeforeunload = null;
        log("Page transitions allowed");
    }
}