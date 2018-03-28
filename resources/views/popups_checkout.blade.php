<?php startfile("popups_checkout"); ?>

<div class="list-group-item">
    <h2 CLASS="float-left">My Order</h2>
    <span style="visibility: hidden;" class="align-middle item-icon rounded-circle sprite sprite-drinks sprite-crush-orange sprite-medium"></span>
    <button class="ml-auto bg-transparent" ONCLICK="confirmclearorder();" id="confirmclearorder"><i class="fa fa-times"></i></button>
</div>

<div id="myorder" style='font-family:sans Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace !important;'></div>

<button id="checkout-btn" class="list-padding btn btn-primary btn-block" onclick="showcheckout();">
    <i class="fa fa-shopping-basket mr-2"></i> CHECKOUT
</button>

<div class="modal" id="checkoutmodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"
     data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="myModalLabel" class="align-middle" style="text-transform: uppercase;">Hi, <SPAN CLASS="session_name"></SPAN></h2>
                <button data-dismiss="modal" data-popup-close="checkoutmodal" class="btn btn-sm ml-auto align-middle bg-transparent"><i class="fa fa-times"></i>
                </button>
            </div>
            <FORM ID="orderinfo" name="orderinfo">
                <div class="modal-body">
                    <?php
                        $order = ["deliverytime", "useraddress", "restaurant", "userphone", "creditcard", "notes"];
                        foreach($order as $key){
                            switch($key){
                                case "deliverytime": ?>
                                    <div class="input_left_icon">
                                        <span class="fa-stack fa-2x">
                                           <i class="fa fa-circle fa-stack-2x"></i>
                                           <i class="fa fa-clock text-white fa-stack-1x"></i>
                                        </span>
                                    </div>
                                    <div class="input_right">
                                        <div>
                                            <SELECT id="deliverytime" TITLE="Delivery Time" class="form-control" parentlevel="2"/>
                                                <OPTION>Deliver ASAP</OPTION>
                                            </SELECT>
                                        </div>
                                    </div>
                                <?php break; case "useraddress": ?>
                                    <div class="input_left_icon" id="red_address">
                                        <span class="fa-stack fa-2x">
                                           <i class="fa fa-circle fa-stack-2x"></i>
                                           <i class="fa fa-map-marker text-white fa-stack-1x"></i>
                                        </span>
                                    </div>
                                    <div class="input_right">
                                        <div class="clear_loggedout addressdropdown proper-height" id="checkoutaddress"></div>
                                        <?php
                                            if (read("id")) {
                                                echo view("popups_address", array("dontincludeAPI" => true, "style" => 1, "saveaddress" => true, "form" => false, "findclosest" => true, "autored" => "red_address"))->render();
                                            }
                                        ?>
                                        <div class="clearfix"></div>
                                        <DIV ID="error-saveaddresses"></DIV>
                                    </div>
                                <?php break; case "userphone": ?>
                                    <div class="input_left_icon redhighlite" id="red_phone">
                                        <span class="fa-stack fa-2x">
                                           <i class="fa fa-circle fa-stack-2x"></i>
                                           <i class="fa fa-mobile text-white fa-stack-1x" style="font-size: 1.5rem !important;"></i>
                                        </span>
                                    </div>
                                    <div class="input_right">
                                        <input type="tel" name="phone" id="order_phone" class="form-control session_phone_val" placeholder="Cell Phone" required="true" autored="red_phone" aria-required="true" value="<?= read('phone'); ?>">
                                    </div>
                                <?php break; case "restaurant": ?>
                                    <div class="input_left_icon" id="red_rest">
                                        <span class="fa-stack fa-2x">
                                           <i class="fa fa-circle fa-stack-2x"></i>
                                           <i class="fa fa-utensils text-white fa-stack-1x"></i>
                                        </span>
                                    </div>
                                    <div class="input_right">
                                        <SELECT class="form-control" ID="restaurant" ONCHANGE="restchange('elementchange');">
                                            <OPTION VALUE="0" SELECTED>Select Restaurant</OPTION>
                                        </SELECT>
                                    </div>
                                <?php break; case "notes": ?>
                                    <div class="input_left_icon">
                                        <span class="fa-stack fa-2x">
                                            <i class="fa fa-circle fa-stack-2x"></i>
                                            <i class="fa fa-pencil-alt text-white fa-stack-1x"></i>
                                        </span>
                                    </div>
                                    <div class="input_right">
                                        <textarea placeholder="Order Notes" id="cookingnotes" class="form-control" maxlength="255"></textarea>
                                    </div>
                                <?php break; case "creditcard": ?>
                                    <div class="input_left_icon" id="red_card">
                                        <span class="fa-stack fa-2x">
                                           <i class="fa fa-circle fa-stack-2x"></i>
                                           <i class="fa fa-credit-card text-white fa-stack-1x"></i>
                                        </span>
                                    </div>
                                    <div class="input_right">
                                        <DIV ID="credit-info"></DIV>
                                    </div>
                                    <div class="input_right">
                                        <input type="text" size="20" class="form-control credit-info" autored="red_card" data-stripe="number" placeholder="Card Number">
                                    </div>
                                    <div class="input_left_icon"></div>
                                    <div class="input_right">
                                        <div class="thirdwidth pr-1">
                                            <SELECT style="margin-top: 0 !important;" CLASS="credit-info form-control" data-stripe="exp_month">
                                                <OPTION VALUE="01">01/Jan</OPTION>
                                                <OPTION VALUE="02">02/Feb</OPTION>
                                                <OPTION VALUE="03">03/Mar</OPTION>
                                                <OPTION VALUE="04">04/Apr</OPTION>
                                                <OPTION VALUE="05">05/May</OPTION>
                                                <OPTION VALUE="06">06/Jun</OPTION>
                                                <OPTION VALUE="07">07/Jul</OPTION>
                                                <OPTION VALUE="08">08/Aug</OPTION>
                                                <OPTION VALUE="09">09/Sep</OPTION>
                                                <OPTION VALUE="10">10/Oct</OPTION>
                                                <OPTION VALUE="11">11/Nov</OPTION>
                                                <OPTION VALUE="12">12/Dec</OPTION>
                                            </SELECT>
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="thirdwidth pr-1">
                                            <SELECT style="margin-top: 0 !important;" CLASS="credit-info form-control" data-stripe="exp_year">
                                                <?php
                                                    $CURRENT_YEAR = date("Y");
                                                    $TOTAL_YEARS = 6;
                                                    for ($year = $CURRENT_YEAR; $year < $CURRENT_YEAR + $TOTAL_YEARS; $year++) {
                                                        echo '<OPTION VALUE="' . right($year, 2) . '">' . $year . '</OPTION>';
                                                    }
                                                ?>
                                            </SELECT>
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="thirdwidth">
                                            <input style="margin-top: 0 !important;" type="text" size="4" data-stripe="cvc" CLASS="credit-info form-control" autored="red_card" PLACEHOLDER="CVC" style="padding: .54rem .75rem;">
                                            <INPUT style="margin-top: 0 !important;" class="credit-info" TYPE="hidden" name="istest" id="istest">
                                            @if(!islive() || read("profiletype") == 1)
                                                <a class="credit-info float-right btn" onclick="testcard();"
                                                   TITLE="Don't remove this, I need it!">Test Card</a> <a class="credit-info float-right btn"
                                                   onclick="$('#restaurant').html('<OPTION VALUE=0>No restaurant within range</OPTION>');">Clear
                                                    Restaurant</a>
                                            @endif
                                            <div class="clearfix"></div>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="input_left_icon"></div>
                                    <div class="input_right">
                                        <DIV ID="error-saved-credit-info"></DIV>
                                    </DIV>
                                <?php break; default:
                                    echo 'ERROR: ' . $key . ' is not defined!';
                                }
                            }
                    ?>
                    <div class="clearfix"></div>
                </div>

                <DIV class="ajaxprompt"></DIV>

                <div class="modal-body" style="padding: 0 !important;">
                    <button class="btn-block list-padding radius-bottom btn btn-primary text-white payfororder" onclick="payfororder(); return false;">
                        <i class="fa fa-check mr-2"></i> ORDER
                    </button>
                    <span class="payment-errors error"></span>
                    <div class="clearfix"></div>
                </div>
            </FORM>
        </div>
    </div>
</div>
<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
<SCRIPT>
    //https://stripe.com/docs/custom-form
    @if(read("id"))
    $(document).ready(function () {
        getcloseststore = true;
        visible_address(false);
        $("#saveaddresses").append('<OPTION VALUE="addaddress" ID="addaddress">Add Address</OPTION>');
        $(".credit-info").change(function () {
            if (isvalidcreditcard()) {
                ajaxerror();
            }
        });
    });
    //$('#reg_phone').keypress(function () {if ($('#reg_phone').valid()) {clearphone('keypress');}});
            @endif

    var shortitems = [];

    function validdeliverytime() {
        return totimestamp() < $("#deliverytime option:selected").attr("timestamp");
    }

    function restchange(where) {
        var abort = false;
        switch(where){
            case "addresshaschanged": abort = true; break;
        }
        log("restchange: " + where + iif(abort, " [ABORTED]"));
        if(abort){return;}
        var value = $("#restaurant").val();
        var index = findwhere(closest, "restid", value);
        addressstatus();
        if (closest.length > 0) {
            GenerateHours(closest[index].hours);
            // shortitems = CheckforShortage(closest[index].shortage);
            alertshortage();
        }
    }

    function alertshortage() {
        if (shortitems.length) {
            var otherstores = " or select a different restaurant to continue";
            if (closest.length == 1) {
                otherstores = "";
            }
            ajaxerror("Sorry, but this restaurant is currently out of:<BR><UL><LI>" + shortitems.join("</LI><LI>") + "</LI></UL><BR>Please remove them from your order" + otherstores, "Product Shortage");
            return true;
        }
        return false;
    }

    function CheckforShortage(shortage) {
        var shortitems = [];
        for (var i = 0; i < theorder.length; i++) {
            if (isShort(shortage, "menu", theorder[i].itemid)) {
                shortitems.push(theorder[i].itemname);
            }
            if (theorder[i].hasOwnProperty("itemaddons")) {
                for (var subitem = 0; subitem < theorder[i].itemaddons.length; subitem++) {
                    var addons = theorder[i].itemaddons[subitem].addons;
                    var tablename = theorder[i].itemaddons[subitem].tablename;
                    for (var addon = 0; addon < addons.length; addon++) {
                        if (isShort(shortage, tablename, addons[addon].text)) {
                            shortitems.push("'" + addons[addon].text + "' for the '" + theorder[i].itemname + "'");
                        }
                    }
                }
            }
        }
        return shortitems;
    }

    function isShort(shortage, tablename, ID) {
        if (tablename != "menu") {
            ID = getKeyByValue(alladdons[tablename + "_id"], ID);
        }
        for (var i = 0; i < shortage.length; i++) {
            if (shortage[i].item_id == ID && shortage[i].tablename == tablename) {
                return true;
            }
        }
        return false;
    }

    function fffa() {
        $("#ffaddress").text($("#formatted_address").val());
        $('#checkoutmodal').modal('show');
        $("#firefoxandroid").hide();
    }

    /*
    $('#orderinfo input').each(function () {
        $(this).click(function () {
            refreshform(this)
        }).blur(function () {
            refreshform(this)
        });
        log("Autored: " + refreshform(this).attr("id"));
    });
    */

    function refreshform(t) {
        var ID = t;
        if (!$(t).is(":visible")) {
            return $(ID);
        }
        var ActualID = $(t).attr("id");
        var value = $(t).val();
        var tagname = $(t).prop("tagName").toUpperCase();
        if (tagname == "SELECT" && value == 0) {
            value = false;
        }
        switch (tagname + "." + ActualID) {
            case "SELECT.saveaddresses":
                if (value == "addaddress") {
                    value = false;
                }
                break;
        }
        var classname = "red";
        if ($(t).hasAttr("autored")) {
            ID = "#" + $(t).attr("autored").replaceAll('"', "");
            classname = "redhighlite";
        }
        if ($(t).hasAttr("autored") || $(t).hasClass("autored")) {
            if (value) {
                $(ID).removeClass(classname);
            } else {
                value = "[EMPTY]";
                $(ID).addClass(classname);
            }
            log(tagname + "." + ActualID + " Autored value: " + value);
        }
        return $(ID);
    }
</SCRIPT>
<?php endfile("popups_checkout"); ?>

<DIV ID="firefoxandroid" class="fullscreen grey-backdrop dont-show">
    <DIV CLASS="centered firefox-child bg-white">
        <i class="fab fa-firefox"></i> Firefox Address editor
        <DIV ID="gmapffac" class="bg-white"></DIV>
        <BUTTON ONCLICK="fffa();" CLASS="btn btn-primary radius0 btn-full pull-down-right">OK</BUTTON>
    </DIV>
</DIV>