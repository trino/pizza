<?php
    startfile("popups_checkout");
    $allowPickup = false;
    $allowCash = false;
?>

<div class="list-group-item" style="border:0 !important;">
    <h2 CLASS="float-left">My Booking</h2>
    <span class="align-middle item-icon rounded-circle sprite sprite-drinks sprite-crush-orange sprite-medium hidden"></span>
    <button class="ml-auto bg-transparent" ONCLICK="confirmclearorder();" id="confirmclearorder"><i class="fa fa-times"></i></button>
</div>

<div id="myorder" class="orderfont"></div>

<button id="checkout-btn" class="list-padding btn {{ btncolor }} btn-block" onclick="showcheckout();" style="display: none;">
    <i class="fa fa-shopping-cart mr-2"></i> CHECKOUT
</button>

<div class="modal" id="checkoutmodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="myModalLabel" class="align-middle"><SPAN style="display: none" CLASS="session_name"></SPAN>Checkout</h2>
                <button data-dismiss="modal" data-popup-close="checkoutmodal" class="btn ml-auto align-middle bg-transparent">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <FORM ID="orderinfo" name="orderinfo">
                <div class="modal-body">
                    <?php
                        $testing = false;
                        if(defined("testing")){
                            $testing = testing;
                        }

                        $order = [ "useraddress", "restaurant","userphone", "deliverytime",  "creditcard", "notes"];
                        $needsphone = true;//needsphonenumber();
                        $index = array_search("userphone", $order);
                        if (!$needsphone && $index !== false) {
                            unset($order[$index]);
                        }
                        foreach($order as $key){
                            switch($key){
                                case "deliverytime": ?>
                                    <div class="input_left_icon" titledebug="deliverytime">
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
                                    <div class="input_left_icon" id="red_address" titledebug="useraddress">
                                        <span class="fa-stack fa-2x">
                                           <i class="fa fa-circle fa-stack-2x"></i>
                                           <i class="fa fa-home text-white fa-stack-1x"></i>
                                        </span>
                                    </div>
                                    <div class="input_right" titledebug="popups_address">
                                        <div class="clear_loggedout addressdropdown proper-height" id="checkoutaddress"></div>
                                        <?php
                                            echo view("popups_address", array("dontincludeAPI" => true, "style" => 1, "saveaddress" => true, "form" => false, "findclosest" => true, "autored" => "red_address"))->render();
                                        ?>
                                        <div class="clearfix"></div>
                                        <DIV ID="error-saveaddresses"></DIV>
                                    </div>
                                <?php break; case "userphone": if($needsphone){ ?>
                                    <DIV ID="userphone" titledebug="userphone">
                                        <div class="input_left_icon redhighlite" id="red_phone">
                                            <span class="fa-stack fa-2x">
                                               <i class="fa fa-circle fa-stack-2x"></i>
                                               <i class="fa fa-mobile text-white fa-stack-1x medtext"></i>
                                            </span>
                                        </div>
                                        <div class="input_right">
                                            <input type="tel" name="phone" id="order_phone" class="form-control session_phone_val"
                                                   placeholder="Cell Phone" required="true" autored="red_phone" aria-required="true" value="<?= read('phone'); ?>">
                                        </div>
                                    </DIV>
                                <?php } break; case "restaurant": ?>

                                    <div class="input_left_icon display-none" id="red_rest" titledebug="restaurant">
                                        <span class="fa-stack fa-2x">
                                           <i class="fa fa-circle fa-stack-2x"></i>
                                           <i class="fa fa-utensils text-white fa-stack-1x"></i>
                                        </span>
                                    </div>
                                    <div class="input_right">
                                        <SELECT class="form-control display-none" ID="restaurant" ONCHANGE="restchange('elementchange');">
                                            <OPTION VALUE="0" SELECTED>Select {{ucfirst(storename)}}</OPTION>
                                        </SELECT>
                                    </div>




                                <?php break; case "notes": ?>
                                    <div class="input_left_icon" titledebug="notes">
                                        <span class="fa-stack fa-2x">
                                            <i class="fa fa-circle fa-stack-2x"></i>
                                            <i class="fa fa-pencil-alt text-white fa-stack-1x"></i>
                                        </span>
                                    </div>
                                    <div class="input_right">
                                        <textarea style="height: 90px !important;" placeholder="Notes for the cleaner" id="cookingnotes" class="form-control" maxlength="999"></textarea>
                                    </div>
                                <?php break; case "creditcard": ?>
                                    <div class="input_left_icon" id="red_card" titledebug="creditcard">
                                        <span class="fa-stack fa-2x">
                                           <i class="fa fa-circle fa-stack-2x"></i>
                                           <i class="fa fa-credit-card text-white fa-stack-1x"></i>
                                        </span>
                                    </div>
                                    <div class="input_right">
                                        <DIV ID="credit-info"></DIV>
                                    </div>
                                    <div class="input_right">
                                        <input type="text" size="20" class="form-control credit-info" autored="red_card"
                                               data-stripe="number" placeholder="Card Number" autocomplete="cc-number"
                                               onfocus="creditcardstatus(true);" onblur="creditcardstatus(false);">
                                        <BUTTON ID="chromeccbutton-disabled" style="display: none;" ONCLICK="creditcardstatus(false);">
                                            <i class="fas fa-unlock"></i>
                                        </BUTTON>
                                    </div>
                                    <div class="input_left_icon"></div>
                                    <div class="input_right">
                                        <div class="thirdwidth pr-1">
                                            <SELECT CLASS="credit-info form-control no-top-margin" data-stripe="exp_month">
                                                <OPTION VALUE="01">01</OPTION>
                                                <OPTION VALUE="02">02</OPTION>
                                                <OPTION VALUE="03">03</OPTION>
                                                <OPTION VALUE="04">04</OPTION>
                                                <OPTION VALUE="05">05</OPTION>
                                                <OPTION VALUE="06">06</OPTION>
                                                <OPTION VALUE="07">07</OPTION>
                                                <OPTION VALUE="08">08</OPTION>
                                                <OPTION VALUE="09">09</OPTION>
                                                <OPTION VALUE="10">10</OPTION>
                                                <OPTION VALUE="11">11</OPTION>
                                                <OPTION VALUE="12">12</OPTION>
                                            </SELECT>
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="thirdwidth pr-1">
                                            <SELECT CLASS="credit-info form-control no-top-margin" data-stripe="exp_year">
                                                <?php
                                                    $CURRENT_YEAR = date("Y");
                                                    $TOTAL_YEARS = 8;
                                                    for ($year = $CURRENT_YEAR; $year < $CURRENT_YEAR + $TOTAL_YEARS; $year++) {
                                                        echo '<OPTION VALUE="' . right($year, 2) . '">' . $year . '</OPTION>';
                                                    }
                                                ?>
                                            </SELECT>
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="thirdwidth">
                                            <input type="text" size="4" data-stripe="cvc" CLASS="credit-info form-control no-top-margin" autored="red_card" PLACEHOLDER="CVC" style="padding: .54rem .75rem;">
                                            <INPUT class="credit-info no-top-margin" TYPE="hidden" name="istest" id="istest">
                                        </div>
                                        @if(!islive() || read("profiletype") == 1 || $testing)
                                            <?php
                                                //Dont remove this, I need it!
                                                $reason = [];
                                                if(!islive())               {   $reason[] = "Is not live";      }
                                                if(read("profiletype") == 1){   $reason[] = "Admin detected";   }
                                                if($testing)                {   $reason[] = "Testing mode";     }
                                                if(!$reason){$reason = "Unknown";} else {$reason = join(", ", $reason);}
                                            ?>
                                            <div class="thirdwidth credit-info" TITLE="{{ $reason }}">
                                                <a class="float-right btn" onclick="$('#restaurant').html('<OPTION VALUE=0>No {{storename}} within range</OPTION>');">
                                                    Clear {{ucfirst(storename)}}
                                                </a>
                                            </DIV>
                                            <div class="thirdwidth credit-info" TITLE="{{ $reason }}">
                                                <a class="float-right btn" onclick="testcard();">Test Card</a>
                                            </DIV>
                                            <div class="thirdwidth credit-info" TITLE="{{ $reason }}">
                                                <SELECT ID="testresult" CLASS="form-control" ONCHANGE="testcard();">
                                                    <OPTION VALUE="">Successful card</OPTION>
                                                    <OPTION VALUE="4000000000005126">expired or canceled card</OPTION>
                                                    <OPTION VALUE="4000000000000101">cvc check failed</OPTION>
                                                    <OPTION VALUE="4000000000009235">risk level elevated</OPTION>
                                                    <OPTION VALUE="4100000000000019">risk level highest</OPTION>
                                                    <OPTION VALUE="4000000000000002">card declined</OPTION>
                                                    <OPTION VALUE="4000000000009995">insufficient funds</OPTION>
                                                    <OPTION VALUE="4000000000000127">incorrect cvc</OPTION>
                                                    <OPTION VALUE="4000000000000069">expired card</OPTION>
                                                    <OPTION VALUE="4000000000000119">processing error</OPTION>
                                                    <OPTION VALUE="4242424242424241">incorrect number</OPTION>
                                                </SELECT>
                                            </DIV>
                                        @endif
                                        <div class="clearfix"></div>
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

                <div class="modal-body">

                    <label>Your billing information is not stored by us. It is transferred over an encrypted connection to Stripe payment processing.
                        The technology is the same used by online banking, so you can rest assured that it's safe.</label>



                </div>
                <div class="modal-body no-padding">


                    <button class="btn-block list-padding radius-bottom btn {{btncolor}} text-white payfororder" onclick="payfororder(); return false;">

<div class="clearfix"></div>
                        <img class="float-left" style="width:105px;" src="<?=webroot("images/stripe_icon.png")?>" />
                        <span style="float: right">
                            <strong>BOOK NOW</strong>


                        </span>
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
    $(document).ready(function () {
        getcloseststore = true;
        visible_address(false);
        $(".credit-info").change(function () {
            log(".credit-info change");
            creditcardstatus(false);
            if (isvalidcreditcard()) {
                ajaxerror();
            }
        });
    });

    var shortitems = [];
    var allowPickup = '<?= $allowPickup; ?>';
    var allowCash = '<?= $allowCash; ?>';

    function validdeliverytime() {
        return totimestamp() < $("#deliverytime option:selected").attr("timestamp");
    }

    function restchange(where) {
        var abort = false;
        switch (where) {
            case "addresshaschanged":
                abort = true;
                break;
        }
        log("restchange: " + where + iif(abort, " [ABORTED]"));
        if (abort) {
            return;
        }
        var value = $("#restaurant").val();
        var index = findwhere(closest, "restid", value);
        addressstatus(true, true, false, false, "restchange " + where);
        if (closest.length > 0) {
            GenerateHours(closest[index].hours);
        }
    }

    function alertshortage() {
        if (shortitems.length) {
            var otherstores = " or select a different {{storename}} to continue";
            if (closest.length == 1) {
                otherstores = "";
            }
            ajaxerror("Sorry, but this {{storename}} is currently out of:<BR><UL><LI>" + shortitems.join("</LI><LI>") + "</LI></UL><BR>Please remove them from your order" + otherstores, "Product Shortage");
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
/*
    function fffa() {
        $("#ffaddress").text($("#formatted_address").val());
        $('#checkoutmodal').modal('show');
        $("#firefoxandroid").hide();
    }
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

<!--DIV ID="firefoxandroid" class="fullscreen grey-backdrop dont-show">
    <DIV CLASS="centered firefox-child bg-white">
        <i class="fab fa-firefox"></i> Firefox Address editor
        <DIV ID="gmapffac" class="bg-white"></DIV>
        <BUTTON ONCLICK="fffa();" CLASS="btn {{btncolor}} radius0 btn-full pull-down-right">OK</BUTTON>
    </DIV>
</DIV-->