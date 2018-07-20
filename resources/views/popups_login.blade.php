<?php
    startfile("popups_login");
    $Additional_Toppings = first("SELECT * FROM additional_toppings", false);
    function getVal($Additional_Toppings, $size) {
        $it = getiterator($Additional_Toppings, "size", $size, false);
        return $Additional_Toppings[$it]["price"];
    }
    $minimum = number_format(getVal($Additional_Toppings, "Minimum"), 2);
    $delivery = number_format(getVal($Additional_Toppings, "Delivery"), 2);
    $time = getVal($Additional_Toppings, "DeliveryTime");
    $hours = first("SELECT * FROM hours WHERE restaurant_id = 0");
?>
<div class="row">
    <DIV CLASS="col-lg-4 col-md-5 bg-white">
        <DIV CLASS="py-3 px-3">
            <ul class="nav nav-tabs mb-1" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active bold" href="#profile" role="tab" data-toggle="tab" id="logintab" onclick="skiploadingscreen = false; ajaxerror();">Log in</a>
                </li>
                <A class="nav-link">
                    &nbsp;or&nbsp;
                </A>
                <li class="nav-item">
                    <a class="nav-link bold" href="#buzz" role="tab" data-toggle="tab" id="signuptab" onclick="skiploadingscreen = true; ajaxerror();">Sign up</a>
                </li>
            </ul>
            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane fade in active" id="profile">
                    <FORM id="signform" name="signform">
                        <div class="input_left_icon">
                            <span class="fa-stack fa-2x"><i class="fa fa-circle fa-stack-2x"></i><i class="fa fa-envelope text-white fa-stack-1x"></i></span>
                        </div>
                        <div class="input_right">
                            <INPUT TYPE="text" id="login_email" name="login_email" placeholder="Email" class="form-control session_email_val" onkeydown="enterkey(event, '#login_password');" required>
                        </div>
                        <div class="input_left_icon">
                            <span class="fa-stack fa-2x"><i class="fa fa-circle fa-stack-2x"></i><i class="fa fa-lock text-white fa-stack-1x"></i></span>
                        </div>
                        <div class="input_right">
                            <INPUT TYPE="password" id="login_password" placeholder="Password" class="form-control" onkeydown="enterkey(event, 'login');" required>
                        </div>
                        <div class="clearfix py-2"></div>
                        <BUTTON CLASS="btn-block btn btn-primary" href="#" onclick="handlelogin('login'); return false;">LOG IN</BUTTON>
                        <!--div class="clearfix py-2"></div>
                        <A CLASS="btn-block btn-sm btn btn-link btn-secondary" href="<?= webroot("help"); ?>#Why do I need an account">Why do I need an account?</A-->
                        <div class="clearfix py-2"></div>
                        <BUTTON CLASS="btn-sm pl-0 text-muted btn-link" href="#" onclick="handlelogin('forgotpassword'); return false;">Forgot Password</BUTTON>
                    </FORM>
                </div>
                <div role="tabpanel" class="tab-pane fade" id="buzz">
                    <FORM id="addform">
                        <?php
                            if (!read("id")) {
                                echo view("popups_address", array("style" => 1, "required" => true, "icons" => true, "firefox" => false))->render();
                            }
                        ?>
                    </FORM>
                    <FORM Name="regform" id="regform">
                        <?= view("popups_edituser", array("phone" => false, "autocomplete" => "new-password", "required" => true, "icons" => true))->render(); ?>
                    </FORM>
                    <div class="clearfix py-2"></div>
                    <button class="btn btn-block btn-primary" onclick="register();">
                        SIGN UP
                    </button>
                </div>
            </div>
            <DIV CLASS="clearfix"></DIV>
        </DIV>

        <DIV class="ajaxprompt margin-sm"></DIV>

        <div class="pb-3">
            <center>
                <img src="<?= webroot("images/delivery.jpg"); ?>" class="width-50"/>
                <h2 class="text-danger mt-3 pull-center">Only the Best Pizza in <?= cityname; ?></h2>
                ${{ $minimum }} Minimum<br>
                ${{ $delivery }} Delivery<br>
                Credit/Debit Only
            </center>
        </div>

    </DIV>
    <div class="col-lg-8 col-md-7 py-3 bg-inverse padding-lr-15">
        <span class="bold bigtext"> <?= strtoupper(cityname); ?> PIZZA DELIVERY</span><br><br>
        <p>The art of delivery is in the team, local restaurants at your footstep within <?= $time; ?> minutes.</p>
        <p class="text-danger">Feed yourself plus someone else... <!--a class="btn-link" href="<?= webroot("help"); ?>" role="button"><?= makestring("{aboutus}"); ?></a--><br>Our restaurants will donate 1 pizza for every 10 ordered to feed the needy!</p>

        <div class="row">
            <div class="col-md-6 padleftright15">
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



            <div class="col-md-6 padleftright15 dont-show">
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
    </div>
</div>




<SCRIPT>
    redirectonlogin = true;
    var minlength = 5;
    var getcloseststore = false;
    lockloading = true;
    blockerror = true;

    function register() {
        var addform2 = isvalidaddress();
        if (addform2) {
            $("#reg_address-error").remove();
        } else if ($("#reg_address-error").length == 0) {
            $('<label id="reg_address-error" class="error" for="reg_name">Please check your address</label>').insertAfter("#gmapc");
        }
        redirectonlogin = false;
        var addform = validateform("#addform") && addform2;
        if(validateform('#regform') && addform) {
            loading(true, "register");
            $('#regform').submit();
        }
    }

    $(".session_email_val").on("keydown", function (e) {
        return e.which !== 32;
    });

    $(document).click(function() {
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
            onkeyup :false,
            onfocusout: false
        });

        $("form[name='regform']").validate({
            rules: {
                name: "required",
                formatted_address: {
                    validaddress: true,
                    required: true
                },
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
                if (isvalidaddress()) {
                    var formdata = getform("#regform");
                    formdata["action"] = "registration";
                    formdata["_token"] = token;
                    formdata["address"] = serializeaddress("#addform");
                    skipunloadingscreen = true;
                    $.post(webroot + "auth/login", formdata, function (result) {
                        if (result) {
                            try {
                                @if(!islive())
                                    if(formdata["name"] == "test") {
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
                        }
                    });
                }
                return false;
            },
            onkeyup :false,
            onfocusout: false
        });
    });
    $(document).ready(function () {
        $("#profile").removeClass("fade").removeClass("in");
    });
</SCRIPT>
<?php endfile("popups_login"); ?>