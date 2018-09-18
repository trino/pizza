<?php
    startfile("popups_edituser");
    $currentURL = webroot("public/user/info");
    if (isset($user_id)) {
        $user = first("SELECT * FROM users WHERE id=" . $user_id);
        echo '<INPUT TYPE="HIDDEN" NAME="id" VALUE="' . $user_id . '">';
        if (!isset($name)) {$name = "user";}
    } else {
        $user = array("name" => "", "phone" => "", "email" => "");
        if (!isset($name)) {$name = "reg";}
    }
    if (!function_exists("printarow")) {
        function printarow($Name, $Prepend, $field) {
            $field["type"] = strtolower($field["type"]);
            if($GLOBALS["icons"]){
                if(!isset($field["icon"])){$field["icon"] = "fa-user";}
                $STYLE="";
                if($field["type"] == "html"){
                    $STYLE=' STYLE="height: 49px; display: table"';
                }
                echo '<div class="input_left_icon"><span class="fa-stack fa-2x" ID="c' . $Name . '"><i class="fa fa-circle fa-stack-2x"></i><i class="fa ' . $field["icon"] . ' text-white fa-stack-1x"></i></span></div><div class="input_right"' . $STYLE . '>';
            }
            if($field["type"] == "html"){
                echo '<DIV STYLE="display:table-cell; vertical-align: middle;">' . $field["html"] . '</DIV>';
                //if(isset($field["outsidehtml"])){echo '<DIV CLASS="clearfix"></DIV>' . $field["outsidehtml"];}
            } else if($field["type"] == "select") {
                echo '<SELECT NAME="' . $field["name"] . '" ID="' . $Prepend . '_' . $field["name"] . '"';
                    if (isset($field["class"]))                                 {echo ' CLASS="' . trim($field["class"]) . '" ';}
                echo '>';
                if (isset($field["placeholder"]))                               {echo '<OPTION DISABLED SELECTED VALUE="">' . $field["placeholder"] . '</OPTION>';}
                foreach($field["options"] as $value => $option){
                    echo '<OPTION VALUE="' . $value . '">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $option . '</OPTION>';
                }
                echo '</SELECT>';
            } else {
                echo '<INPUT TYPE="' . $field["type"] . '" NAME="' . $field["name"] . '" ID="' . $Prepend . '_' . $field["name"] . '"';
                if (isset($field["class"]))                                     {echo ' CLASS="' . trim($field["class"]) . '" ';}
                if (isset($field["value"]))                                     {echo ' value="' . $field["value"] . '" ';}
                if (isset($field["min"]))                                       {echo ' min="' . $field["min"] . '" ';}
                if (isset($field["maxlen"]))                                    {echo ' min="' . $field["maxlen"] . '" ';}
                if (isset($field["max"]))                                       {echo ' max="' . $field["max"] . '" ';}
                if (isset($field["readonly"]))                                  {echo ' readonly';}
                if (isset($field["autocomplete"]) && $field["autocomplete"])    {echo ' autocomplete="' . $field["autocomplete"] . '"';}
                if (isset($field["title"]) && $field["title"])                  {echo ' title="' . $field["title"] . '"';}
                if (isset($field["placeholder"]))                               {echo ' placeholder="' . $field["placeholder"] . '" ';}
                if (isset($field["corner"]))                                    {echo ' STYLE="border-' . $field["corner"] . '-radius: 5px;"';}
                if (isset($field["required"]) && $field["required"])            {echo ' REQUIRED';}
                echo '>';
            }
            if($GLOBALS["icons"]){ echo '</DIV>';}
        }
    }

    if (!isset($password))      {$password = true;}
    if (!isset($age))           {$age = false;}
    if (!isset($email))         {$email = true;}
    if (!isset($autocomplete))  {$autocomplete = "";}
    if (!isset($required))      {$required = false;}
    $GLOBALS["icons"] = isset($icons) && $icons;
    if(isset($class)){$class .= " form-control ";} else {$class = "form-control ";}

    echo '<DIV>';
    if (!isset($profile1) || $profile1) {
        printarow("Name", $name, array("name" => "name", "value" => $user["name"], "type" => "text", "placeholder" => "Name", "class" => $class . "session_name_val", "required" => $required));
    }
    if (!isset($phone) || $phone) {
        if (!isset($phone)) {$phone = false;}
        printarow("Phone", $name, array("name" => "phone", "value" => formatphone($user["phone"]), "type" => "tel", "placeholder" => "Cell Phone", "class" => $class . "session_phone_val", "required" => $phone || $required, "icon" => "fa-15 fas fa-mobile"));
    }
    if ($email) {
        printarow("Email", $name, array("name" => "email", "value" => $user["email"], "type" => "email", "placeholder" => "Email", "class" => $class . "session_email_val", "required" => $required, "icon" => "fa-envelope"));
    }
    if (isset($user_id) || isset($showpass)) {
        printarow("Old Password", $name, array("name" => "oldpassword", "type" => "password", "class" => $class, "placeholder" => "Old Password", "autocomplete" => $autocomplete, "required" => $required, "icon" => "fa-lock"));
        $type = islive() ? "password" : "text";
        $title = islive() ? "" : "This is only a text field in testing mode. Otherwise it's a password field";
        printarow("New Password", $name, array("name" => "newpassword", "type" => $type, "class" => $class, "placeholder" => "New Password", "autocomplete" => $autocomplete, "required" => $required, "icon" => "fa-lock", "title" => $title));
    } else if ($password) {
        printarow("Password", $name, array("name" => "password", "type" => "password", "class" => $class, "placeholder" => "Password", "autocomplete" => $autocomplete, "required" => $required, "icon" => "fa-lock"));
    }
    if (isset($address) && $address) {
        echo view("popups_address", array("style" => 1))->render();
    }
    if ($age) {
        printarow("minimumage", $name, array("name" => "minimumage", "type" => "select", "class" => "required valid " . $class, "required" => true, "icon" => "fa-birthday-cake",
            "placeholder" => "Are you over 19?", "options" => ["yes" => "I am over 19", "no" => "I am under 19"]
        ));
    }
    echo '</DIV>';
?>
<SCRIPT>
    var minlength = 5;
    redirectonlogout = true;

    function userform_submit(isSelf) {
        if(validateform("#userform")) {
            var formdata = getform("#userform");
            $("#edituser_error").text("");
            var keys = ["name", "phone"];//"email",
            for (var keyid = 0; keyid < keys.length; keyid++) {
                var key = keys[keyid];
                var val = formdata[key];
                createCookieValue("session_" + key, val);
                $(".session_" + key).text(val);
                $(".session_" + key + "_val").val(val);
            }
            $.post("<?= $currentURL; ?>", {
                action: "saveitem",
                _token: token,
                value: formdata
            }, function (result) {
                if (result) {
                    if (result == "Data saved") {
                        result = "Changes to your profile have been saved";
                    }
                    if (result.contains("password mismatch")) {
                        validateselector("#reg_oldpassword", result, -1);
                    }
                    toast(result);
                    return true;
                }
            });
        }
        return false;
    }

    $(function () {
        $("form[name='user']").validate({
            /*onfocusout: function(element) {
                validateform("form[name='user']");
                ajaxerror();
            },*/
            onfocusout: false,
            onkeyup: false,
            rules: {
                name: "required",
                phone: {
                    phonenumber: true,
                    required: <?= $phone == "required" ? "true" : "false"; ?>
                },
                email: {
                    required: true,
                    email: true,
                    remote: {
                        url: "<?= $currentURL; ?>",
                        type: "post",
                        data: {
                            action: "testemail",
                            _token: token,
                            email: function () {
                                return $('#user_email').val();
                            },
                            user_id: userdetails["id"]
                        }
                    }
                },
                oldpassword: {
                    required: function (element) {
                        return $("form[name='user']").find("input[name=newpassword]").val().length > 0;
                    },
                    minlength: minlength
                },
                newpassword: {
                    required: function (element) {
                        return $("form[name='user']").find("input[name=oldpassword]").val().length > 0;
                    },
                    minlength: minlength
                },
            },
            messages: {
                name: "Please enter your name",
                phone: {
                    required: "Please provide an up-to-date phone number",
                    phonenumber: "Please provide a valid phone number"
                },
                oldpassword: {
                    required: "Please provide your old password",
                    minlength: "Your old password is at least " + minlength + " characters long"
                },
                newpassword: {
                    required: "Please provide a new password",
                    minlength: "Your new password must be at least " + minlength + " characters long"
                },
                email: {
                    required: "Please enter your email address",
                    email: "Please enter a valid email address",
                    remote: "Please enter a unique email address"
                }
            }
        });

        $("#orderinfo").validate({
            onkeyup :false,
            onfocusout: false,
            rules: {
                name: "required",
                phone: {
                    phonenumber: true,
                    required: <?= $phone == "required" ? "true" : "false"; ?>
                }
            },
            messages: {
                name: "Please enter your name",
                phone: "Please enter a valid phone number"
            }
        });
    });

    $(document).ready(function () {
        setTimeout(function () {
            $("#orderinfo").removeAttr("novalidate");
        }, 100);
    });
</SCRIPT>
<?php endfile("popups_edituser"); ?>
