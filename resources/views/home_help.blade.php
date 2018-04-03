@extends('layouts_app')
@section('content')
    <?php
        $site_name = sitename;
        $email = '<A HREF="mailto:info@trinoweb.ca?subject=' . $site_name . '">info@trinoweb.ca</A>';
    ?>
    <STYLE>
        li > .title {
            font-weight: bold;
        }

        .collapse, .collapsing {
            margin-left: 30px;
        }

        .btn:not(.btn-circle) {
        }

        .btn-wide{
            width: 150px !important;
        }

        jump, .jump {
            text-decoration: underline;
            cursor: pointer;
            color: blue;
        }

        jump.event{
            text-decoration: none !important;
            font-weight: bold;
            color: black !important;
        }

        .no-u{
            text-decoration: none !important;
        }

        #gotobottom{
            bottom: 0;
        }
        #expandall{
            bottom: 28px;
        }
        #contractall{
            bottom: 56px;
        }
        #gototop {
            bottom: 84px;
        }

        .footer{
            font-weight: bold;
            position: fixed;
            left: 0;
            display: table;
            margin: 0 auto;
            background-color: white;
            width: 150px !important;
            color: #0281E1;
            z-index: 999;
            border: 1px solid #0281E1 !important;
            text-align: left !important;
        }

        .bg-secondary {
            margin-bottom: 2px;
        }

        .fa-black {
            border-radius: 3px;
            background-color: #292B2C;
            color: white;
            width: 20px;
            height: 20px;
            text-align: center;
            padding-top: 1px;
        }

        .fa-black.fa-plus{
            padding-top: 2px;
            padding-left: 1px;
        }

        .reason{
            font-weight: bold;
            color: blue;
        }

        .tab{
            margin-left: 25px;
        }

        .btn-border{
            border: 1px solid black !important;
        }
    </STYLE>
    <SCRIPT>
        $(document).ready(function () {
            $("jump").click(function () {
                var target = "#item_" + toclassname($(this).text());
                var targetstarget = $(target).attr("data-target");
                if (!$(targetstarget).hasClass("show")) {
                    $(target).trigger("click");
                    scrollto($(target).offset().top);
                }
            });
            $("#profileinfo").remove();
            //$(".sticky-footer").remove();

            $('a[href=#top]').click(function(event){
                event.preventDefault();
                scrollto(0);
            });

            $('a[href=#bottom]').click(function(event){
                event.preventDefault();
                scrollto($(document).height());
            });

            var afterhash = window.location.hash.replace("#", "");
            if(afterhash){
                $("#item_" + afterhash.toLowerCase().replaceAll(" ", "_")).click();
            }
        });

        function scrollto(Y){
            $('html, body').animate({scrollTop: Y}, 'slow');
        }

        function expandall(expand){
            if(expand){
                $(".collapse").not(".show").prev().trigger("click");
            } else {
                $(".show").prev().trigger("click");
            }
        }
    </SCRIPT>
    <DIV class="row py-3 bg-white">
        <div class="col-sm-12 list-padding list-card">


        <h3 class="mb-2">FAQ</h3>
    <?php
        $minimum = first("SELECT price FROM additional_toppings WHERE size = 'Minimum'")["price"];

        function toclass($text) {
            $text = str_replace('/', '_', $text);
            $text = strtolower(str_replace(" ", "_", trim(strip_tags($text))));
            return str_replace(array("?"), "", $text);
        }
        function newID() {
            if (isset($GLOBALS["lastid"])) {
                $GLOBALS["lastid"] += 1;
            } else {
                $GLOBALS["lastid"] = 0;
            }
            return lastID();
        }
        function lastID() {
            return "section_" . $GLOBALS["lastid"];
        }
        function newlist($Title) {
            if (isset($GLOBALS["startlist"])) {
                echo '</UL>';
            }
            $GLOBALS["startlist"] = true;
            echo '<H2>' . $Title . '</H2><UL>';
        }
        function newitem($Title, $Text, $Class = "") {
            echo '<LI data-toggle="collapse" data-target="#' . newID() . '" ID="item_' . toclass($Title) . '">';
            echo '<SPAN CLASS="title cursor-pointer ' . $Class . '">' . $Title . '</SPAN></LI>';
            echo '<div id="' . lastID() . '" class="collapse">' . $Text . '</div>';
        }
        function actionitem($action, $text = '') {
            $actions = actions($action);
            $parties = ["User", "Admin", "Restaurant"];
            $tempstr = "";
            if(!count($actions)){
                $tempstr = "<BR>No actions are assigned to this event";
            }
            foreach ($actions as $actiond) {
                $tempstr2 = "The " . $parties[$actiond["party"]] . " is: ";
                $actione = array();
                if ($actiond["sms"]) {
                    $actione[] = "texted";
                }
                if ($actiond["phone"]) {
                    $actione[] = "called";
                }
                if ($actiond["email"]) {
                    $actione[] = "emailed";
                }
                $tempstr2 .= join("/", $actione) . ' with the message/subject "' . str_replace("[reason]", '<span class="reason">[reason]</span>', $actiond["message"]) . '"';
                $tempstr .= '<BR>' . $tempstr2;
            }
            newitem($action, 'Occurs when: ' . $text . $tempstr);
        }

        newlist("Your Account");
        newitem("Signing in", "Enter your email address and password in the <A HREF='" . webroot("/") . "'>Log In</A> page and click <button class='btn btn-sm btn-primary'>LOG IN</button>");
        newitem("Forgot password", "Enter the email address you registered with, click <button class='btn btn-secondary btn-wide btn-sm'>Forgot Password</button> and a new password will be emailed to you");
        newitem("Registering", "Click the 'Signup' tab, enter a valid London address into the 'Delivery Address' field (use 'Apt/Buzzer' for things like apartment/unit/back door/etc), enter your name/email/password and click <Button class='btn btn-sm btn-primary'>Register</button>");
        newitem('<i class="fa fa-fw fa-bars"></i> button', "A dropdown menu with various options, located in the top-left corner");
        if(read("id")){
            newitem('<i class="fa fa-fw fa-user"></i> <SPAN CLASS="session_name"></SPAN>', "A popup to edit your user name/phone number/password/credit card numbers/addresses");
        }
        newitem('<i class="fa fa-fw fa-clock"></i> Past Orders', "A popup that shows a list of your previous orders. Clicking <button class='btn btn-sm btn-primary'>Load Order</button> will overwrite the contents of your cart with that order");
        newitem('<i class="fa fa-fw fa-sign-out-alt"></i> Log Out', "Logs you out and returns to the login/register page");
        newitem('Why do I need an account?', 'The main reason is the convenience of storing your address and phone number for repeat visits. But the secondary reason is our credit card handler (Stripe) requires an account to associate credit card info with');

        newlist('Your Order');
        newitem("Add an item to your cart", "Click the item on the menu. If it has a + next to the price, there will be a popup allowing you to edit the item options before adding it to the receipt");
        newitem("Topping/sauces popup", 'If the menu item contains more than 1 item (ie: 2 pizzas), there will be a list at the top of this popup to select which item to edit. Clicking any of the options from the list will add it to the selected item. Some options are part of a group and only 1 option in that group can be added to an item (ie: well done and lightly done will conflict, so only 1 can be added to a pizza). The price will update automatically when you add options.<BR><button class="btn btn-sm mt-0 toppings_btn bg-secondary flat-border"><i class="fa fa-check"></i><SPAN CLASS="pull-right">$X.XX</SPAN></button> will add the item with the options you selected to the receipt.<BR><button class="btn btn-sm bg-secondary toppings_btn"><i class="fa fa-fw fa-arrow-left"></i></button> will remove the last option added to the selected item, if it is not dimmed');
        newitem("Editing an item in your cart", 'Click <button class="btn-sm"><i class="fa fa-pencil-alt"></i></button> to the right of the item in the receipt, the same popup you used to add the item will appear');
        newitem("Remove an item from your cart", 'Click <button class="btn-sm"><i class="fa fa-minus"></i></button> to the right of the item in the receipt');
        newitem("Duplicating an item in your cart", 'Click <button class="btn-sm"><i class="fa fa-plus"></i></button> to the right of the item in the receipt (if it is a simple item without any addons/toppings)');
        newitem("Empty your cart", 'Click <i class="fa fa-times"></i> at the top-left corner of your receipt');
        newitem('<i class="fa fa-fw fa-shopping-basket"></i> CHECKOUT', "Click this when you're done placing your order. You'll need to enter your <jump>Payment Information</jump>, <jump>Delivery Address</jump>, <jump>Preferred Restaurant</jump>, <jump>Delivery Time</jump>, then click <BUTTON CLASS='btn btn-primary btn-sm'>Place order</BUTTON>.<BR>This button will only be visible once your order meets the minumum of: $" . $minimum . " before taxes and delivery", "btn btn-primary btn-sm btn-block btn-wide");
        newitem("Payment Information", "If you have a saved card (note: Cards are saved with Stripe, not our servers) you can select it from the dropdown, or use 'Add Card' to add a new one. Otherwise just enter your credit card information");
        newitem("Delivery Address", "If you have a saved address you can select it from the dropdown, or select 'Add Address' to add a new address. Otherwise just enter a valid London address");
        newitem("Preferred Restaurant", "Select which restaurant you want to recieve your order from");
        newitem("Delivery time", "Leave as 'Deliver Now' to have the store deliver it ASAP. Otherwise they'll try to deliver as close to your selected time as possible.");

        if (read("id") && read("profiletype") > 0) {
            newlist("Restaurants");
            newitem("Registering", "You can only register as a regular user. To get escalated to a restaurant account requires you to contact an admin at: " . $email);
            newitem('<i class="fa fa-fw fa-user-plus"></i> Orders List', "Shows a list of orders for your restaurant");
            newitem("View", "View the contents of the order, a map showing the customer's address, and gives the options to Confirm, Email and Decline the order", "btn btn-sm btn-success btn-border btn-wide");
            newitem("Delete", "Trigger the <jump class='event'>order_declined</jump> event and delete the order from the system", "btn btn-sm btn-border btn-wide");
            newitem("Confirmed", "Mark the order as confirmed and trigger the <jump class='event'>order_confirmed</jump> event", "btn btn-sm btn-primary btn-border btn-wide");
            newitem('<i class="fa fa-fw fa-envelope"></i> Email', "Re-send the receipt to customer via the <jump class='event'>order_placed</jump> event", "btn btn-sm btn-secondary red btn-border btn-wide");
            newitem("Declined", 'Mark the order as declined and trigger the <jump class="event">order_declined</jump> event', "btn btn-sm btn-border btn-wide");
            newitem("Delivered", 'Mark the order as delivered and trigger the <jump class="event">order_delivered</jump> event', "btn btn-sm btn-warning btn-border btn-wide");
            newitem("FILE NOT FOUND", "The order file is missing. Delete the order as the order itself is useless");

            newlist("Communication Actions");
            newitem("Editing actions", 'This can only done in <B><i class="fa fa-fw fa-user-plus"></i> Actions list</B>. This tells the system who to contact and how depending on specific events.<BR><SPAN class="reason">[reason]</SPAN> is replaced with the message entered by the restaurant<BR><SPAN class="reason">[name]</SPAN> is replaced with the name of the party<BR><SPAN class="reason">[url]</SPAN> with a link to the receipt that doesn&apos;t require logging in<BR><SPAN class="reason">[sitename]</SPAN> with &apos;' . $site_name . '&apos;<BR>and the [tags] must be lower-cased');
            actionitem("order_placed", "the order is placed");
            actionitem("order_delivered", 'the <jump class="btn btn-sm btn-warning btn-border no-u">Delivered</jump> button is clicked');
            actionitem("order_confirmed", 'the <jump class="btn btn-sm btn-primary btn-border no-u">Confirmed</jump> button is clicked');
            actionitem("order_declined", 'the <jump class="btn btn-sm btn-border no-u">Declined</jump> or <jump class="btn btn-sm btn-danger btn-border no-u">Deleted</jump> buttons are clicked.');
            actionitem("user_registered", 'a new user is registered. (Since no restaurant is involved in this event, do not set the party of this event to the Restaurant)');

            if (read("profiletype") == 1) {
                newlist("Administrators");
                newitem("Escalating a user account to a restaurant", 'Go to <B><i class="fa fa-fw fa-user-plus"></i> Users list</B>, click the Profiletype column for that user, and click "Restaurant" from the drop-down menu');
                newitem("Changing the price of a topping or the delivery fee", 'Go to <B><i class="fa fa-fw fa-user-plus"></i> Additional_toppings list</B>, click the price column for that item, then change the text in the text box');
                newlist('<i class="fa fa-fw fa-user-plus"></i> Edit Menu');
                newitem("Size Costs", "Edit the cost of toppings for each size of pizza, and the delivery fee");
                newitem("Pizza Toppings/Wing Sauces", "Edit toppings/wing sauces, which category they belong to, if they are free toppings or not, and their group ID # (if the ID # is above 0, only 1 item from this group can be added to a menu item)");
                newitem("[New Category]", "Add a new menu item category to the list below");
                newitem("Category list", "Edit menu items. The Toppings/Wings_sauce numbers refer to how many lists of toppings they must select. ie: 2 Toppings would mean they have to select toppings for 2 pizzas");
            }
        }

        echo '</UL>';
    ?>
    </DIV>
    </DIV>
    <div class="btn-group" CLASS="dont-show">
        <button id="gototop" class="btn btn-sm btn-primary "><A HREF="#top"><i class="fa fa-arrow-up"></i> Go to the top</A></button>
        <button id="expandall" class="btn btn-sm btn-primary footer" onclick="expandall(true);"><i class="fa fa-expand"></i> Expand all</button>
        <button id="contractall" class="btn btn-sm btn-primary footer" onclick="expandall(false);"><i class="fa fa-compress"></i> Contract all</button>
        <button id="gotobottom" class="btn btn-sm btn-primary footer"><A HREF="#bottom"><i class="fa fa-arrow-down"></i> Go to the bottom</A></button>
    </div>
@endsection