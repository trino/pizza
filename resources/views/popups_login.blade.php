<?php
startfile("popups_login");
$Additional_Toppings = first("SELECT * FROM additional_toppings", false);
if (!function_exists("getVal")) {
    function getVal($Additional_Toppings, $size)
    {
        $it = getiterator($Additional_Toppings, "size", $size, false);
        return $Additional_Toppings[$it]["price"];
    }
}
$minimum = number_format(getVal($Additional_Toppings, "Minimum"), 2);
$delivery = number_format(getVal($Additional_Toppings, "Delivery"), 2);
$time = getVal($Additional_Toppings, "DeliveryTime");
$hours = first("SELECT * FROM hours WHERE restaurant_id = 0");
if (!isset($minimal)) {
    $minimal = false;
}
if (!isset($justright)) {
    $justright = false;
}
if (!isset($noclose)) {
    $noclose = false;
}
if (!isset($dohours)) {
    $dohours = true;
}
?>

@if(!$justright)
    @if($minimal)
        <div class="modal" id="loginmodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"
             data-keyboard="false" data-backdrop="static">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                <!--div class="modal-header">
                        <DIV CLASS="col-lg-6 offset-lg-6">
                            <ul class="nav nav-tabs mb-1 row" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active bold" href="#profile" role="tab" data-toggle="tab"
                                       id="logintab" onclick="skiploadingscreen = false; ajaxerror();">Log in</a>
                                </li>
                                <A class="nav-link">
                                    &nbsp;or&nbsp;
                                </A>
                                <li class="nav-item">
                                    <a class="nav-link bold" href="#buzz" role="tab" data-toggle="tab" id="signuptab"
                                       onclick="skiploadingscreen = true; ajaxerror();">Sign up</a>
                                </li>
                            </ul>
                        </DIV>
                        @if(!$noclose)
                    <button data-dismiss="modal" class="btn btn-sm ml-auto align-middle"><i
                                class="fa fa-times"></i></button> @endif
                        </div-->
                    <div class="modal-body modal-blue">
                        <div class="row">
                            <DIV CLASS="col-lg-6 bg-primary text-white modal-blue-content">
                                <?= view("popups_login", array("justright" => true, "dohours" => false))->render(); ?>
                                <DIV CLASS="modal-blue-div"></DIV>
                            </DIV>
                            <DIV CLASS="col-lg-6">
                                @else
                                    <div class="row">
                                        <DIV CLASS="col-lg-4 col-md-5 bg-white">
                                            @endif
                                            @endif

                                            @if(!$justright)
                                                <DIV CLASS="py-3 px-3">
                                                    <ul class="nav nav-tabs mb-1" role="tablist">
                                                        <li class="nav-item">
                                                            <a class="nav-link active bold" href="#profile" role="tab"
                                                               data-toggle="tab" id="logintab"
                                                               onclick="skiploadingscreen = false; ajaxerror();">Login</a>
                                                        </li>
                                                        <A class="nav-link">
                                                            &nbsp;or&nbsp;
                                                        </A>
                                                        <li class="nav-item">
                                                            <a class="nav-link bold" href="#buzz" role="tab"
                                                               data-toggle="tab" id="signuptab"
                                                               onclick="skiploadingscreen = true; ajaxerror();">Signup</a>
                                                        </li>
                                                    </ul>

                                                    <!-- Tab panes -->
                                                    <div class="tab-content">
                                                        <div role="tabpanel" class="tab-pane fade in active"
                                                             id="profile">
                                                            <FORM id="signform" name="signform">
                                                                <div class="input_left_icon">
                        <span class="fa-stack fa-2x">
                            <i class="fa fa-circle fa-stack-2x"></i>
                            <i class="fa fa-envelope text-white fa-stack-1x"></i>
                        </span>
                                                                </div>
                                                                <div class="input_right">
                                                                    <INPUT TYPE="email" id="login_email"
                                                                           name="login_email" placeholder="Email"
                                                                           class="form-control session_email_val"
                                                                           onkeydown="enterkey(event, '#login_password');"
                                                                           required>
                                                                </div>
                                                                <div class="input_left_icon">
                        <span class="fa-stack fa-2x">
                            <i class="fa fa-circle fa-stack-2x"></i>
                            <i class="fa fa-lock text-white fa-stack-1x"></i>
                        </span>
                                                                </div>
                                                                <div class="input_right">
                                                                    <INPUT TYPE="password" id="login_password"
                                                                           placeholder="Password" class="form-control"
                                                                           onkeydown="enterkey(event, 'login');"
                                                                           required>
                                                                </div>

                                                                @if(debugmode)
                                                                    <div class="input_left_icon">
                                                                        <span class="fa-stack fa-2x"><i
                                                                                    class="fa fa-circle fa-stack-2x"></i><i
                                                                                    class="fa fa-user-secret text-white fa-stack-1x"></i></span>
                                                                    </div>
                                                                    <div class="input_right">
                                                                        <SELECT CLASS="form-control"
                                                                                TITLE="Only visible in debug mode, assumes password is 'admin'"
                                                                                ONCHANGE="selectuser(this);">
                                                                            <OPTION>User list</OPTION>
                                                                            <?php
                                                                            $users = Query("SELECT name, profiletype, email FROM users ORDER BY profiletype", true);
                                                                            $profiletype = -1;
                                                                            foreach ($users as $user) {
                                                                                if ($profiletype != $user["profiletype"]) {
                                                                                    switch ($user["profiletype"]) {
                                                                                        case 0:
                                                                                            $profiletype = "Users";
                                                                                            break;
                                                                                        case 1:
                                                                                            $profiletype = "Admins";
                                                                                            break;
                                                                                        case 2:
                                                                                            $profiletype = "Restaurants";
                                                                                            break;
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
                                                                <BUTTON CLASS="btn-block btn btn-primary" href="#"
                                                                        onclick="handlelogin('login'); return false;">
                                                                    LOG IN
                                                                </BUTTON>
                                                            <!--div class="clearfix py-2"></div>
<A CLASS="btn-block btn-sm btn btn-link btn-secondary" href="<?= webroot("help"); ?>#Why do I need an account">Why do I need an account?</A-->
                                                                <div class="clearfix py-2"></div>
                                                                <BUTTON CLASS="btn-sm pl-0 text-muted btn-link"
                                                                        style="font-weight: normal !important;" href="#"
                                                                        onclick="handlelogin('forgotpassword'); return false;">
                                                                    Forgot Password
                                                                </BUTTON>
                                                            </FORM>
                                                        </div>
                                                        <div role="tabpanel" class="tab-pane fade" id="buzz">
                                                            <FORM id="addform">
                                                                <?php
                                                                if ($minimal) {
                                                                    echo '<div class="input_left_icon"><span class="fa-stack fa-2x"><i class="fa fa-circle fa-stack-2x"></i><i class="fa fa-car text-white fa-stack-1x"></i></span></div><div class="input_right">';
                                                                    echo view("popups_second_address", array("name" => "signupaddress", "unit" => true))->render();
                                                                    echo '</DIV>';
                                                                } else {
                                                                    echo view("popups_address", array("style" => 1, "required" => true, "icons" => true, "firefox" => false))->render();
                                                                }
                                                                ?>
                                                            </FORM>
                                                            <FORM Name="regform" id="regform">
                                                                <?= view("popups_edituser", array("phone" => false, "autocomplete" => "new-password", "required" => true, "icons" => true))->render(); ?>
                                                            </FORM>
                                                            <div class="clearfix py-2"></div>
                                                            <button class="btn btn-block btn-primary"
                                                                    onclick="register();">
                                                                SIGN UP
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <DIV CLASS="clearfix"></DIV>
                                                </DIV>

                                                <DIV class="ajaxprompt margin-sm"></DIV>

                                                <div class="pb-3">
                                                    <center>
                                                        <img src="<?= webroot("images/delivery.jpg"); ?>"
                                                             class="width-50"/>
                                                        <h2 class="text-danger mt-3 pull-center">Pizza Delivery Made
                                                            Simple</h2>
                                                        ${{ $minimum }} Minimum<br>
                                                        ${{ $delivery }} Delivery<br>
                                                        Credit/Debit Only
                                                    </center>
                                                </div>
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
                <div class="col-lg-8 col-md-7 bg-inverse ">
                    @endif

                    <div class="py-3 px-3">
                        <span class="bold bigtext"> <?= cityname ?>'s Premier Pizza Delivery Service</span><br><br>

                    <!--p class="pull-center">
            <img style="max-width:100%;border:7px solid #eceeef!important" src="<?= webroot("images/ultimatecombo.png"); ?>"/>
        </p-->


                        <p>        <!--i class="fa fa-gavel fa-2x"></i-->

                            We’ve partnered up with some of your favourite local pizza restaurants to bring you a
                            one-stop
                            online pizza shop. What makes us different? We offer the lowest prices in town and only use
                            one
                            menu – no more wasting time browsing through restaurants. Once you complete your order,
                            we’ll
                            immediately connect with our closest partner restaurant and start preparing your meal right
                            away. Sounds pretty fast and easy right?
                        </p>

                        <p>
                            Best of all, we are a 100% not-for-profit service. Every pizza ordered through our website
                            helps
                            us feed those in need within our local community. Join our movement by making an order today
                            and
                            feeding not just yourself, but someone else! Still not convinced? – Check out our
                            <A HREF="<?= webroot("help"); ?>" CLASS="link-white">About Us</A> for more info.
                        </p>

                        <p><a CLASS="link-white btn btn-danger" href="https://hammerpizza.ca/blog/pizza-giveaway/">Want to win some free pizzas?</a></p>
                    </div>

                    @if($dohours)
                        <div class="col-md-12 ">
                            <div class="pb-2 px-3">

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

                    function checkaddress() {
                        if (isvalidaddress2("#gmap_signupaddress")) {
                            return true;
                        }
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
                                }
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
                                }
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

                    @if($minimal && $noclose)
                    CheckLoggedIn();
                    @endif
                    $(document).ready(function () {
                        $("#profile").removeClass("fade").removeClass("in");
                    });

                    function CheckLoggedIn() {
                        if (!userisloggedin() && !$('#loginmodal').is(':visible') && currentRoute == "/") {
                            showlogin("document ready: " + currentRoute);
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