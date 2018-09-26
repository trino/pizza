@extends('layouts_app')
@section('content')
    <?php
        //vardump($GLOBALS["app"]["config"]["database"]["constants"]);
        //$admins = enumadmins(false); vardump($admins);

        $launchdate = "April 1, 2017";
        $datestamp = strtotime($launchdate);
        $SQLdate = date("Y-m-d", $datestamp);
        $launched = iif(time() > $datestamp, " (Launched)");
        if (!$launched) {
            $days = ceil(($datestamp - time()) / 86400);
            $launched = " (" . $days . " day" . iif($days > 1, "s") . " away)";
        }
        $orders = first('SELECT count(*) as count FROM orders WHERE status <> 2 AND status <> 4 AND placed_at > "' . $SQLdate . '"')["count"];
        $donation_per_order = 0.1;
        $units_donated = "Pizzas";
        //$donations = number_format((float)$orders * $donation_per_order, 0, '.', '');
        $donations = floor($orders * $donation_per_order);
        $email = '<A HREF="mailto:info@trinoweb.ca?subject=' . sitename . '">info@trinoweb.ca</A>';
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

        .btn-wide {
            width: 150px !important;
        }

        jump, .jump {
            text-decoration: underline;
            cursor: pointer;
            color: blue;
        }

        jump.event {
            text-decoration: none !important;
            font-weight: bold;
            color: black !important;
        }

        .no-u {
            text-decoration: none !important;
        }

        #gotobottom {
            bottom: 0;
        }

        #expandall {
            bottom: 28px;
        }

        #contractall {
            bottom: 56px;
        }

        #gototop {
            bottom: 84px;
        }

        .footer {
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

        .fa-black.fa-plus {
            padding-top: 2px;
            padding-left: 1px;
        }

        .reason {
            font-weight: bold;
            color: blue;
        }

        .tab {
            margin-left: 25px;
        }

        .btn-border {
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

            $('a[href=#top]').click(function (event) {
                event.preventDefault();
                scrollto(0);
            });

            $('a[href=#bottom]').click(function (event) {
                event.preventDefault();
                scrollto($(document).height());
            });

            var afterhash = window.location.hash.replace("#", "");
            if (afterhash) {
                $("#item_" + afterhash.toLowerCase().replaceAll(" ", "_")).click();
            }
        });

        function scrollto(Y) {
            $('html, body').animate({scrollTop: Y}, 'slow');
        }

        function expandall(expand) {
            if (expand) {
                $(".collapse").not(".show").prev().trigger("click");
            } else {
                $(".show").prev().trigger("click");
            }
        }
    </SCRIPT>
    <DIV class="row">
        <div class="col-sm-6 py-3 bg-dark">
            <h3><?= getsetting("aboutus"); ?></h3>
            <div class="card-block text-white">
                @if(database == "ai")
                    <span class="bold bigtext"> Feed Yourself + Someone Else</span>
                    <br><br>
                    <p>
                        <?= sitename; ?> was founded with the simple belief that online food ordering doesn’t have to be so
                        complicated. We realize that {{storenames}} are paying enormous commissions to existing online food
                        service providers. Ultimately, it’s YOU, the customer, who ends up paying the bills. We want to put
                        that money back where it belongs…in your pocket! Not only do we save you money, but we also use 100%
                        of all our profits to give back and help out the local community.
                    </p>
                    <p>
                        How do we do it? We leverage an easy-to-use online platform, one universal menu for all users alike,
                        and local partnerships with {{storenames}} who share our vision. In an effort to be completely
                        transparent, we will post a summary all of our contributions below, updated on a monthly basis.
                    </p>
                    <p>
                        Join us in our mission to change the way we order our food online and make your first order today!
                    </p>
                    <br>
                    <div class="btn-outlined-danger text-center pt-1">
                        <strong>August, 2018</strong>
                        <p> Orders: <?= $orders; ?>
                        <br> Donated: <?= $donations+1 . " " . $units_donated; ?>
                        <br> Charity: Hamilton Food Centre</p>
                    </div>
                @else
                    <span class="bold bigtext"> The higher, the fewer</span>
                @endif
            </div>
        </div>
        <DIV CLASS="col-sm-6 bg-dark text-white">
            <?= view("popups_login", array("justright" => true))->render(); ?>
        </DIV>
    </DIV>
    <DIV class="row bg-white">
        <div class="col-sm-12 list-padding list-card">
            <h3 class="mb-2">FAQ</h3>
            <?php
            $minimum = first("SELECT price FROM additional_toppings WHERE size = 'Minimum'")["price"];

            function toclass($text){
                $text = str_replace('/', '_', $text);
                $text = strtolower(str_replace(" ", "_", trim(strip_tags($text))));
                return str_replace(array("?"), "", $text);
            }

            function newID(){
                if (isset($GLOBALS["lastid"])) {
                    $GLOBALS["lastid"] += 1;
                } else {
                    $GLOBALS["lastid"] = 0;
                }
                return lastID();
            }

            function lastID(){
                return "section_" . $GLOBALS["lastid"];
            }

            function newlist($Title){
                if (isset($GLOBALS["startlist"])) {
                    echo '</UL>';
                }
                $GLOBALS["startlist"] = true;
                echo '<H2>' . $Title . '</H2><UL>';
            }

            function newitem($Title, $Text, $Class = ""){
                $Title = str_replace("[sitename]", sitename, $Title);
                $Text = str_replace("[sitename]", sitename, $Text);
                echo '<LI data-toggle="collapse" data-target="#' . newID() . '" ID="item_' . toclass($Title) . '">';
                echo '<SPAN CLASS="title cursor-pointer ' . $Class . '">' . $Title . '</SPAN></LI>';
                echo '<div id="' . lastID() . '" class="collapse">' . $Text . '</div>';
            }

            function actionitem($action, $text = ''){
                $actions = actions($action);
                $parties = ["User", "Admin", "Restaurant"];
                $tempstr = "";
                if (!count($actions)) {
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

            newlist("General");
            newitem("Why can’t I order for pickup?", "We are committed to providing a premier end-to-end customer experience. In order to promote simplicity and ease-of-use, we only provide delivery service at this time. Please check our site regularly for updates on new service offerings.");
            newitem("Can I track my order once submitted?", "Once your order is submitted, it is accepted and confirmed by the choosen " . storename . " immediately. If there are any issues in preparing or delivering your order on time, we will contact you directly. Unfortunately, we do not currently have the ability to track the status of your order while it is being prepared and/or delivered.");
            newitem("How do I know if the " . storename . " has accepted my order?", "All orders placed on [sitename] are instantly confirmed and accepted. You will receive an email receipt of your order details, including the contact information for the " . storename . " fulfilling your order.");
            newitem("What do I do if I need to make changes after submitting an order?", "You will receive an email receipt of your order details, including the contact information for the " . storename . " fulfilling your order. Please contact the restaurant directly by phone should you wish to make any changes.");
            newitem("I never received my order. Who do I contact?", "You may call our support line at [sitename} or call the " . storename . " for immediate assistance.");//Support line?
            newitem("Why can’t I see certain items on the menu?", "Since we use a universal menu, certain items offered by particular " . storenames . " may not be available through our service. Once you receive your order receipt via email, you may contact the restaurant directly should you wish to make any specific additions to your order.");
            newitem("Can I choose the " . storename . " that prepares my order?", "Yes, during check-out you have the ability to choose from any restaurant that is within your delivery range. By default, we choose the " . storename . " closest to your location to fulfill your order.");
            newitem("Can I pay with cash or credit/debit once I receive my order?", "No. Unfortunately, all of our orders require pre-payment via debit/credit card. This allows us to instantly confirm and start preparing orders placed through [sitename].");
            newitem("Do you store my credit card information?", "We do not keep this information on our servers, but rather via our secure payment processing partner: Stripe. It is requested from Stripe when you sign in and stored on your browser, not our servers.");

            newlist("Your Account");
            newitem("Signing in", "Enter your email address and password in the <A HREF='" . webroot("/") . "'>Log In</A> page and click <button class='btn btn-sm " . btncolor . "'>LOG IN</button>");
            newitem("Forgot password", "Enter the email address you registered with, click <button class='btn " . btncolor . " btn-wide btn-sm'>Forgot Password</button> and a new password will be emailed to you");
            newitem("Registering", "Click the 'Signup' tab, enter a valid Hamilton address into the 'Delivery Address' field (use 'Apt/Buzzer' for things like apartment/unit/back door/etc), enter your name/email/password and click <Button class='btn btn-sm " . btncolor . "'>Register</button>");
            newitem('<i class="fa fa-fw fa-bars"></i> button', "A dropdown menu with various options, located in the top-left corner");
            if (read("id")) {
                newitem('<i class="fa fa-fw fa-user"></i> <SPAN CLASS="session_name"></SPAN>', "A popup to edit your user name/phone number/password/credit card numbers/addresses");
            }
            newitem('<i class="fa fa-fw fa-clock"></i> Past Orders', "A popup that shows a list of your previous orders. Clicking <button class='btn btn-sm " . btncolor . "'>Load Order</button> will overwrite the contents of your cart with that order");
            newitem('<i class="fa fa-fw fa-sign-out-alt"></i> Log Out', "Logs you out and returns to the login/register page");
            newitem('Why do I need an account?', 'The main reason is the convenience of storing your address and phone number for repeat visits. But the secondary reason is our credit card handler (Stripe) requires an account to associate credit card info with');

            newlist('Your Order');
            newitem("Add an item to your cart", "Click the item on the menu. If it has a + next to the price, there will be a popup allowing you to edit the item options before adding it to the receipt");

            if (database == "ai") newitem("Topping/sauces popup", 'If the menu item contains more than 1 item (ie: 2 ' . product . '), there will be a list at the top of this popup to select which item to edit. Clicking any of the options from the list will add it to the selected item. Some options are part of a group and only 1 option in that group can be added to an item (ie: well done and lightly done will conflict, so only 1 can be added to a ' . product . '). The price will update automatically when you add options.<BR><button class="btn btn-sm mt-0 toppings_btn bg-success flat-border"><i class="fa fa-check"></i><SPAN CLASS="pull-right">$X.XX</SPAN></button> will add the item with the options you selected to the receipt.<BR><button class="btn btn-sm bg-success toppings_btn"><i class="fa fa-fw fa-arrow-left"></i></button> will remove the last option added to the selected item, if it is not dimmed');
            newitem("Editing an item in your cart", 'Click <button class="btn-sm"><i class="fa fa-pencil-alt"></i></button> to the right of the item in the receipt, the same popup you used to add the item will appear');
            newitem("Remove an item from your cart", 'Click <button class="btn-sm"><i class="fa fa-minus"></i></button> to the right of the item in the receipt');
            newitem("Duplicating an item in your cart", 'Click <button class="btn-sm"><i class="fa fa-plus"></i></button> to the right of the item in the receipt (if it is a simple item without any addons/toppings)');
            newitem("Empty your cart", 'Click <i class="fa fa-times"></i> at the top-left corner of your receipt');
            newitem('<i class="fa fa-fw fa-shopping-cart"></i> CHECKOUT', "Click this when you're done placing your order. You'll need to enter your <jump>Payment Information</jump>, <jump>Delivery Address</jump>, <jump>Preferred " . ucfirst(storename) . "</jump>, <jump>Delivery Time</jump>, then click <BUTTON CLASS='btn " . btncolor . " btn-sm'>Place order</BUTTON>.<BR>This button will only be visible once your order meets the minumum of: $" . $minimum . " before taxes and delivery", "btn btn-sm btn-block btn-wide " . btncolor);
            newitem("Payment Information", "If you have a saved card (note: Cards are saved with Stripe, not our servers) you can select it from the dropdown, or use 'Add Card' to add a new one. Otherwise just enter your credit card information");
            newitem("Delivery Address", "If you have a saved address you can select it from the dropdown, or select 'Add Address' to add a new address. Otherwise just enter a valid Hamilton address");
            newitem("Preferred " . ucfirst(storename), "Select which " . storename . " you want to recieve your order from");
            newitem("Delivery time", "Leave as 'Deliver Now' to have the store deliver it ASAP. Otherwise they'll try to deliver as close to your selected time as possible.");

            if (read("id") && read("profiletype") > 0) {
                newlist(ucfirst(storename));
                newitem("Registering", "You can only register as a regular user. To get escalated to a " . storename . " account requires you to contact an admin at: " . $email);
                newitem('<i class="fa fa-fw fa-user-plus"></i> Orders List', "Shows a list of orders for your " . storename);
                newitem("View", "View the contents of the order, a map showing the customer's address, and gives the options to Confirm, Email and Decline the order", "btn btn-sm btn-border btn-wide " . btncolor);
                newitem("Delete", "Trigger the <jump class='event'>order_declined</jump> event and delete the order from the system", "btn btn-sm btn-border btn-wide " . btncolor);
                newitem("Confirmed", "Mark the order as confirmed and trigger the <jump class='event'>order_confirmed</jump> event", "btn btn-sm btn-border btn-wide " . btncolor);
                newitem('<i class="fa fa-fw fa-envelope"></i> Email', "Re-send the receipt to customer via the <jump class='event'>order_placed</jump> event", "btn btn-sm red btn-border btn-wide " . btncolor);
                newitem("Declined", 'Mark the order as declined and trigger the <jump class="event">order_declined</jump> event', "btn btn-sm btn-border btn-wide");
                newitem("Delivered", 'Mark the order as delivered and trigger the <jump class="event">order_delivered</jump> event', "btn btn-sm btn-warning btn-border btn-wide");
                newitem("FILE NOT FOUND", "The order file is missing. Delete the order as the order itself is useless");

                newlist("Communication Actions");
                newitem("Editing actions", 'This can only done in <B><i class="fa fa-fw fa-user-plus"></i> Actions list</B>. This tells the system who to contact and how depending on specific events.<BR><SPAN class="reason">[reason]</SPAN> is replaced with the message entered by the ' . storename . '<BR><SPAN class="reason">[name]</SPAN> is replaced with the name of the party<BR><SPAN class="reason">[url]</SPAN> with a link to the receipt that doesn&apos;t require logging in<BR><SPAN class="reason">[sitename]</SPAN> with &apos;' . sitename . '&apos;<BR>and the [tags] must be lower-cased');
                actionitem("order_placed", "the order is placed");
                actionitem("order_delivered", 'the <jump class="btn btn-sm btn-warning btn-border no-u">Delivered</jump> button is clicked');
                actionitem("order_confirmed", 'the <jump class="btn btn-sm btn-primary btn-border no-u">Confirmed</jump> button is clicked');
                actionitem("order_declined", 'the <jump class="btn btn-sm btn-border no-u">Declined</jump> or <jump class="btn btn-sm btn-danger btn-border no-u">Deleted</jump> buttons are clicked.');
                actionitem("user_registered", 'a new user is registered. (Since no ' . storename . ' is involved in this event, do not set the party of this event to the Restaurant)');
                actionitem("cron_job/cron_job_final", 'unconfirmed orders are in the system, waiting for the store to confirm receipt. cron_job_final is for the admin after max_attempts(settings) have been made<BR>[#] is the number of orders<BR>[restaurant] is the name of the' . storename . '<BR>[s] is the s added to the word order if there is more than one order<BR>[from] is a list of the user names who placed an order');

                if (read("profiletype") == 1) {
                    newlist("Administrators");
                    newitem("Escalating a user account to a " . storename, 'Go to <B><i class="fa fa-fw fa-user-plus"></i> Users list</B>, click the Profiletype column for that user, and click "Restaurant" from the drop-down menu');
                    newitem("Changing the price of a topping or the delivery fee", 'Go to <B><i class="fa fa-fw fa-user-plus"></i> Additional_toppings list</B>, click the price column for that item, then change the text in the text box');
                    newlist('<i class="fa fa-fw fa-user-plus"></i> Edit Menu');
                    if (database == "ai") newitem("Size Costs", "Edit the cost of toppings for each size of " . product . ", and the delivery fee");
                    if (database == "ai") newitem(product . " Toppings/Wing Sauces", "Edit toppings/wing sauces, which category they belong to, if they are free toppings or not, and their group ID # (if the ID # is above 0, only 1 item from this group can be added to a menu item)");
                    newitem("[New Category]", "Add a new menu item category to the list below");
                    newitem("Category list", "Edit menu items. The Toppings/Wings_sauce numbers refer to how many lists of toppings they must select. ie: 2 Toppings would mean they have to select toppings for 2 pizzas");
                }
            }

            echo '</UL>';
            ?>
        </DIV>
    </DIV>
    <!--div class="btn-group" CLASS="dont-show">
    <button id="gototop" class="btn btn-sm btn-primary "><A HREF="#top"><i class="fa fa-arrow-up"></i> Go to the top</A></button>
    <button id="expandall" class="btn btn-sm btn-primary footer" onclick="expandall(true);"><i class="fa fa-expand"></i> Expand all</button>
    <button id="contractall" class="btn btn-sm btn-primary footer" onclick="expandall(false);"><i class="fa fa-compress"></i> Contract all</button>
    <button id="gotobottom" class="btn btn-sm btn-primary footer"><A HREF="#bottom"><i class="fa fa-arrow-down"></i> Go to the bottom</A></button>
    </div-->
@endsection