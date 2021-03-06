<?php
    startfile("popups_login");
    $Additional_Toppings = first("SELECT * FROM additional_toppings", false);
    if (!function_exists("getVal")) {
        function getVal($Additional_Toppings, $size) {
            $it = getiterator($Additional_Toppings, "size", $size, false);
            return $Additional_Toppings[$it]["price"];
        }
    }
    $minimum = number_format(getVal($Additional_Toppings, "Minimum"), 2);
    $delivery = number_format(getVal($Additional_Toppings, "Delivery"), 2);
    $time = getVal($Additional_Toppings, "DeliveryTime");
    $hours = first("SELECT * FROM hours WHERE restaurant_id = 0");
    if (!isset($minimal)) {$minimal = false;}
    if (!isset($justright)) {$justright = false;}
    if (!isset($noclose)) {$noclose = false;}
    if (!isset($dohours)) {$dohours = true;}
    if (!isset($showlogin)) {$showlogin = false;}
    $iscanbii = database == "canbii";
    $minimumage19 = false; //database == "canbii"
?>

@if(!$justright)
    @if($minimal)
        <div class="modal" id="loginmodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <ul class="nav nav-tabs mb-1" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active bold" href="#profile" role="tab" data-toggle="tab" id="logintab" onclick="skiploadingscreen = false; ajaxerror();">Log in</a>
                            </li>
                            <A class="nav-link">&nbsp;or&nbsp;</A>
                            <li class="nav-item">
                                <a class="nav-link bold" href="#buzz" role="tab" data-toggle="tab" id="signuptab" onclick="resetsignup();">Sign up</a>
                            </li>
                        </ul>
                        @if(!$noclose)
                            <button data-dismiss="modal" class="btn ml-auto align-middle bg-transparent"><i class="fa fa-times"></i></button>
                        @endif
                    </div>
                <div class="modal-body" oldclass="modal-blue">
    @else
        <div class="row">
            <DIV CLASS="col-lg-4 col-md-5 bg-white">
    @endif
@endif

@if(!$justright)
    <DIV>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane fade in active" id="profile">
                <FORM id="signform" name="signform">
                    <div class="input_left_icon">
                        <span class="fa-stack fa-2x">
                            <i class="fa fa-circle fa-stack-2x"></i>
                            <i class="fa fa-envelope text-white fa-stack-1x"></i>
                        </span>
                    </div>
                    <div class="input_right">
                        <INPUT TYPE="email" id="login_email" name="login_email" placeholder="Email" class="form-control session_email_val" onkeydown="enterkey(event, '#login_password');" required>
                    </div>
                    <div class="input_left_icon">
                        <span class="fa-stack fa-2x">
                            <i class="fa fa-circle fa-stack-2x"></i>
                            <i class="fa fa-lock text-white fa-stack-1x"></i>
                        </span>
                    </div>
                    <div class="input_right">
                        <INPUT TYPE="password" id="login_password" placeholder="Password" class="form-control" onkeydown="enterkey(event, 'login');" required>
                    </div>

                    @if(debugmode)
                        <div class="input_left_icon">
                            <span class="fa-stack fa-2x"><i class="fa fa-circle fa-stack-2x"></i><i class="fa fa-user-secret text-white fa-stack-1x"></i></span>
                        </div>
                        <div class="input_right">
                            <SELECT CLASS="form-control" TITLE="Only visible in debug mode, assumes password is 'admin'" ONCHANGE="selectuser(this);">
                                <OPTION>User list</OPTION>
                                <?php
                                    $users = Query("SELECT name, profiletype, email FROM users ORDER BY profiletype", true);
                                    $profiletype = -1;
                                    foreach ($users as $user) {
                                        if ($profiletype != $user["profiletype"]) {
                                            switch ($user["profiletype"]) {
                                                case 0: $profiletype = "Users"; break;
                                                case 1: $profiletype = "Admins"; break;
                                                case 2: $profiletype = ucfirst(storename); break;
                                            }
                                            echo '<OPTION DISABLED>' . $profiletype . '</OPTION>';
                                            $profiletype = $user["profiletype"];
                                        }
                                        echo '<OPTION VALUE="' . $user["email"] . '">&nbsp;&nbsp;&nbsp;&nbsp;' . $user["name"] . '</OPTION>';
                                    }
                                ?>
                            </SELECT>
                        </div>
                    @endif

                    <div class="clearfix py-2"></div>
                    <BUTTON CLASS="btn-block btn {{btncolor}}" href="#" onclick="handlelogin('login'); return false;">LOG IN</BUTTON>
                    <div class="clearfix py-2"></div>
                    <BUTTON CLASS="btn-sm pl-0 text-muted btn-link" style="" href="#" onclick="handlelogin('forgotpassword'); return false;">Forgot Password</BUTTON>
                </FORM>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="buzz">
                <FORM id="addform">
                    <?php
                        if ($minimal) {
                            echo '<div class="input_left_icon"><span ID="addressicon" class="fa-stack fa-2x"><i class="fa fa-circle fa-stack-2x"></i><i class="fa fa-home text-white fa-stack-1x"></i></span></div><div class="input_right">';
                            echo view("popups_second_address", array("name" => "signupaddress", "unit" => true))->render();
                            echo '</DIV>';
                        } else {
                            echo view("popups_address", array("style" => 1, "required" => true, "icons" => true, "firefox" => false))->render();
                        }
                    ?>
                </FORM>
                <FORM Name="regform" id="regform">
                    <?= view("popups_edituser", array("phone" => true, "autocomplete" => "new-password", "required" => true, "icons" => true, "age" => $minimumage19))->render(); ?>
                </FORM>
                <div class="clearfix py-2"></div>
                <button class="btn btn-block {{btncolor}}" onclick="register();">
                    SIGN UP
                </button>
                <div class="clearfix py-2"></div>

               <label>By creating an account, I agree to receive updates and exclusive offers from Canbii. For more details see our <a HREF="<?= webroot("help"); ?>">Help Page</a>.</label>



            </div>





        </div>
        <DIV CLASS="clearfix"></DIV>
    </DIV>

    <DIV class="ajaxprompt margin-sm"></DIV>

    <div class="row py-1"  style="font-size: .875rem">
        <!--div class="col-md-6" style="padding-top: 1rem">
            <center>
                <img src="<?= webroot("images/delivery.jpg"); ?>" class="width-50"/>
                <h2 class="text-danger mt-3 pull-center">Online <?= product ?> Delivery</h2>
                ${{ $minimum }} Minimum<br>
                ${{ $delivery }} Delivery<br>
                Credit/Debit Only
            </center>
        </div>
        <div class="col-md-6">
            <div style="padding: 1rem; border:0px solid #eceeef">
                @if(database == "ai")
                    <h2 class="text-danger">Our Partners</h2>
                    Famo Pizza & Wings
                    <br>Queens Pizza & Wings
                    <br>Le Bella Pizza (Fennell)
                    <br>Bruno's Pizza & Wings (Main St)
                    <br>National Pizza & Wings (Upper James)
                    <br>Bella Pizza (King St)
                    <br>Pizza Italia
                @else

                @endif
            </div>
        </div-->
@endif

@if($minimal)
                    </div>
                </div>
            </div>
        </div>
    </div>
@else
    @if(!$justright)
            </DIV>
        <div class="col-lg-8 col-md-7 bg-inverse">
    @endif

    <div class="py-3 px-3">

        <h1>                <strong> Hamilton, we're always ready to serve you</strong></h1>


 <br>
        <!--p class="pull-center">
        <img style="max-width:100%;border:7px solid #eceeef!important" src="<?= webroot("images/ultimatecombo.png"); ?>"/>
        </p-->
        @if(database == "ai")
            <p>
                <!--i class="fa fa-gavel fa-2x"></i-->
                We’ve partnered up with some of your favourite local {{product . " " . storenames}} to bring you a one-stop
                online {{product}} shop. What makes us different? We offer the lowest prices in town and only use one
                menu – no more wasting time browsing through {{storenames}}. Once you complete your order, we’ll
                immediately connect with our closest partner {{storename}} and start preparing your meal right
                away. Sounds pretty fast and easy right?
            </p>
            <p>
                Best of all, we are a 100% not-for-profit service. Every pizza ordered through our website helps
                us feed those in need within our local community. Join our movement by making an order today and
                feeding not just yourself, but someone else! Still not convinced? – Check out our
                <A HREF="<?= webroot("help"); ?>" CLASS="link-white">About Us</A> for more info.
            </p>
        @else
                We’ve partnered up with some of your favourite local cleaners to bring you a one-stop ahop for all your cleaning needs.
        @endif
    </div>

    @if($dohours)
        <div class="col-md-12 ">
            <div class="px-3">
                <TABLE class="inline">
                    <TR>
                        <TD COLSPAN="3"><p class="lead strong">HOURS OF OPERATION</p></TD>
                    </TR>
                    <?php
                        $daysofweek = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
                        for ($day = 0; $day < 7; $day++) {
                            echo '<TR><TD>' . $daysofweek[$day] . "&nbsp;&nbsp;&nbsp; </TD>";
                            $open = $hours[$day . "_open"];
                            $close = $hours[$day . "_close"];
                            if ($open == "-1" || $close == "-1") {
                                echo '<TD COLSPAN="2"">Closed';
                            } else {
                                echo '<TD>' . GenerateTime($open) . ' to&nbsp;</TD><TD>' . GenerateTime($close);
                            }
                            echo '</TD></TR>';
                        }
                    ?>
                </TABLE>




                <p></p> <br>
                <h2>Canbii At A Glance</h2>
                Maid Cleaning Service in Hamilton
                <br> Easy Online Booking (Takes only 60 seconds!)
                <br> Certified Professional Cleaners
                <br> ALL Cleaning Supplies Included
                <br> 100% Satisfaction Guarantee

            </div>

            <div class="col-md-6  dont-show">
                <TABLE class="inline">
                    <TR>
                        <TD COLSPAN="2"><p class="lead strong">DISCOUNTS</p></TD>
                    </TR>
                    <?php
                        $discounts = select_field_where("additional_toppings", "size like 'over$%'", false);
                        foreach ($discounts as $discount) {
                            echo '<TR><TD>Orders ' . str_replace("$", " $", $discount["size"]) . "</TD><TD>&nbsp;get " . $discount["price"] . '% off</TD></TR>';
                        }
                    ?>
                </TABLE>
            </div>
        </div>
    @endif

    @if(!$justright)
            </div>
        </div>
    @endif
@endif


@if(!$justright)
    <SCRIPT>
        redirectonlogin = true;
        var minlength = 5;
        var getcloseststore = false;
        lockloading = true;
        blockerror = true;

        function resetsignup(){
            skiploadingscreen = true;
            ajaxerror();
            $("#reg_minimumage").val("");
        }

        function checkaddress() {
            if (isvalidaddress2("#gmap_signupaddress")) {
                $("#addressicon").removeClass("redhighlite");
                return true;
            }
            $("#addressicon").addClass("redhighlite");
            if (debugmode) {
                toast("Address is missing or invalid");
            }
        }

        function register() {
            validateinput();
            var addform2 = checkaddress();
            if (addform2) {
                $("#gmap_signupaddress-error").remove();
            } else if ($("#gmap_signupaddress-error").length == 0) {
                $('<label id="gmap_signupaddress-error" class="error" for="reg_name">Please check your address</label>').insertAfter("#gmac_signupaddress");
            }
            redirectonlogin = false;
            var addform = validateform("#addform") && addform2;
            addform = checkage() && addform;
            if (validateform('#regform') && addform) {
                loading(true, "register");
                firstsignin = true;
                $('#regform').submit();
            }
        }

        $(".session_email_val").on("keydown", function (e) {
            return e.which !== 32;
        });

        $(document).click(function () {
            $("#dropdown-menu").hide();
        });

        function checkage(){
            var ret = true;
            @if($minimumage19)
                $("#reg_minimumage-error").remove();
                $("#cminimumage").removeClass("redhighlite");
                ret = $("#reg_minimumage").val() === "yes";
                if(!ret){
                    $('<label id="reg_minimumage-error" class="error" for="reg_age">You must be over 19 to use this site</label>').insertAfter("#reg_minimumage");
                    $("#cminimumage").addClass("redhighlite");
                }
            @endif
            return ret;
        }

        $.validator.addMethod('radioage', function (Data, element) {
            return Data == "yes";
        }, "You must be over 19 to use this site");

        $(function () {
            $("#signform").validate({
                rules: {
                    login_email: {
                        required: true,
                        email: true
                    }
                },
                messages: {
                    login_email: {
                        required: "Please enter a valid email address",
                        email: "Please enter a valid email address"
                    }
                },
                onkeyup: false,
                onfocusout: false
            });

            $("form[name='regform']").validate({
                rules: {
                    name: "required",
                    @if(!$minimal)
                        formatted_address: {
                            validaddress: true,
                            required: true
                        },
                        phone: {
                            phonenumber: true,
                            required: true
                        },
                    @endif
                    email: {
                        required: true,
                        email: true,
                        remote: {
                            url: '<?= webroot('public/user/info'); ?>',
                            type: "post",
                            data: {
                                action: "testemail",
                                _token: token,
                                email: function () {
                                    return $('#reg_email').val();
                                },
                                user_id: "0"
                            }
                        }
                    },
                    password: {
                        minlength: minlength
                    },
                    @if($iscanbii)
                        minimumage:{
                            radioage: true
                        }
                    @endif
                },
                messages: {
                    name: "Please enter your name",
                    password: {
                        required: "Please provide a password",
                        minlength: "Your new password must be at least " + minlength + " characters long"
                    },
                    email: {
                        required: "Please enter your email address",
                        email: "Please enter a valid email address",
                        remote: "Please enter a unique email address"
                    },
                    phone: "Please enter a valid phone number"
                    },
                submitHandler: function (form) {
                    if (checkaddress()) {
                        var formdata = getform("#regform");
                        formdata["action"] = "registration";
                        formdata["_token"] = token;
                        formdata["address"] = "";
                        var address = getAddress("#gmap_signupaddress");//serializeaddress("#addform")
                        if (address) {
                            formdata["address"] = address;
                        }
                        skipunloadingscreen = true;
                        $.post(webroot + "auth/login", formdata, function (result) {
                            if (result) {
                                result = JSON.parse(result);
                                if (result.Status) {
                                    try {
                                        @if(!islive())
                                        if (formdata["name"] == "test") {
                                            formdata["email"] = "roy@trinoweb.com";
                                            formdata["password"] = "admin";
                                        }
                                        @endif
                                        $("#login_email").val(formdata["email"]);
                                        $("#login_password").val(formdata["password"]);
                                        redirectonlogin = true;
                                        handlelogin('login');
                                    } catch (e) {
                                        skipunloadingscreen = false;
                                        loading(false, "register");
                                        alert(result, "Registration Error");
                                    }
                                } else {
                                    toast(result.Reason);
                                }
                            }
                        });
                    }
                    return false;
                },
                onkeyup: false,
                onfocusout: false
            });
        });

        $(document).ready(function () {
            $("#profile").removeClass("fade").removeClass("in");
            @if($showlogin) CheckLoggedIn("popups_login"); @endif
        });

        function CheckLoggedIn(Where) {
            if (!userisloggedin() && !$('#loginmodal').is(':visible') && isIndex()) {
            showlogin("document ready: " + currentRoute + " Where: " + Where);
            }
        }

        function selectuser(element) {
            $("#login_email").val(element.value);
            $("#login_password").val("admin");
        }

        function getAddress(selector) {
            selector = $(selector);
            var ret = {
                number: selector.attr("address_street_number"),
                unit: $("#signupaddress_unit").val(),
                buzzcode: "",
                street: selector.attr("address_route"),
                postalcode: selector.attr("address_postal_code"),
                city: selector.attr("address_locality"),
                province: selector.attr("address_administrative_area_level_1"),
                latitude: selector.attr("place_geometry_location_lat"),
                longitude: selector.attr("place_geometry_location_lng")
            };
            log("Address: " + JSON.stringify(ret));
            return ret;
        }
    </SCRIPT>
@endif
<?php endfile("popups_login"); ?>