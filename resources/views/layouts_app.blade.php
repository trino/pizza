<!DOCTYPE html>
<html lang="en" class="full">
<head>
    <?php
        $time = microtime(true);
        // Gets microseconds
        if (read("id")) {
            $user = getuser(false);
            if (!$user) {
                //check for deleted user
                unset($user);
                write("id", false);
            } else {
                unset($user["password"]);
            }
        }
        $scripts = webroot("public/scripts");
        $css = webroot("public/css");
        $routename = Route::getCurrentRoute()->uri();
    ?>

    <script type="text/javascript">
        var timerStart = Date.now();
        var currentURL = "<?= Request::url(); ?>";
        var token = "<?= csrf_token(); ?>";
        var webroot = "<?= webroot("public/"); ?>";
        var redirectonlogout = false;
        var redirectonlogin = false;
        var addresskeys = ["id", "value", "user_id", "number", "unit", "buzzcode", "street", "postalcode", "city", "province", "latitude", "longitude", "phone"];
        var userdetails = false;
        var currentRoute = "<?= $routename ?>";
    </script>

    <meta charset="utf-8">
    <meta name="theme-color" content="#d9534f">
    <meta name="viewport" content="width=device-width, user-scalable=no">
    <meta name="viewport" content="width=device-width, user-scalable=no">
    <meta http-equiv="content-language" content="en-CA">
    <meta name="mobile-web-app-capable" content="yes">
    <title><?= sitename; ?> - Pizza Delivery</title>
    <link rel="icon" sizes="128x128" href="<?= webroot("public/images/pizza128.png"); ?>">
    <link rel="icon" sizes="192x192" href="<?= webroot("public/images/pizza192.png"); ?>">
    <script defer src="https://use.fontawesome.com/releases/v5.0.6/js/all.js"></script>
    <link href='<?= $css; ?>/Roboto.css' rel='stylesheet' type='text/css'>
    <link href='<?= $css; ?>/Roboto-slab.css' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="<?= $css; ?>/bootstrap.min.css">
    <link rel="stylesheet" href="<?= $css . "/custom4.css?v=" . time(); ?>">
    <link rel="stylesheet" href="<?= $css . "/google.css?v=" . time(); ?>">
    <script src="<?= $scripts; ?>/jquery.min.js"></script>
    <script src="<?= $scripts; ?>/tether.min.js"></script>
    <script src="<?= $scripts; ?>/bootstrap.min.js"></script>
    <SCRIPT SRC="<?= $scripts; ?>/jquery.validate.min.js"></SCRIPT>
    @include("popups_alljs")
</head>
<body>
<div ID="loading" class="fullscreen grey-backdrop dont-show"></div>
<div ID="headerbar" class="list-group-item container-fluid shadow" style="background-color: <?= getsetting("headercolor") ?>;">
    <button data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="btn bg-transparent" ONCLICK="$('#dropdown-menu').toggle();">
        <i class="fa fa-bars text-white"></i>
    </button>
    <ul class="dropdown-menu dropdown-menu-left" ID="dropdown-menu">
        @if(read("id"))
            <SPAN class="loggedin profiletype profiletype1">
                <?php
                    foreach (array("users", "restaurants", "useraddresses", "orders", "additional_toppings", "actions", "shortage", "settings") as $table) {
                        echo '<li><A HREF="' . webroot("list/" . $table, true) . '" CLASS="dropdown-item"><i class="fa fa-user-plus icon-width"></i> ' . str_replace("_", " ", ucfirst($table)) . ' list</A></li>';
                    }
                ?>
                <li><A HREF="<?= webroot("editmenu", true); ?>" CLASS="dropdown-item"><i class="fa fa-user-plus icon-width"></i> Edit Menu</A></li>
                <li><A HREF="<?= webroot("list/debug", true); ?>" CLASS="dropdown-item"><i class="fa fa-user-plus icon-width"></i> Debug log</A></li>
            </SPAN>
            <SPAN class="loggedin">
                <li id="profileinfo">
                    <A data-toggle="modal" data-target="#profilemodal" href="#" class="dropdown-item">
                    <i class="fa fa-user icon-width"></i> My Profile</A>
                </li>
                @if($routename != "help")
                    <li class="profiletype_not profiletype_not2"><A ONCLICK="orders();" class="dropdown-item" href="#"><i class="fa fa-clock icon-width"></i> Past Orders</A></li>
                @endif
            </SPAN>
        @endif
        @if($routename != "/")
            <SPAN class="loggedout">
                <LI><A CLASS="dropdown-item" href="<?= webroot("", true); ?>"><i class="fa fa-user icon-width"></i> Log In</A></LI>
            </SPAN>
        @endif
        @if($routename == "help")
            <LI><A CLASS="dropdown-item" href="<?= webroot("", true); ?>"><i class="fa fa fa-shopping-basket icon-width"></i> Order Now</A></LI>
        @else
            <LI><A CLASS="dropdown-item" href="<?= webroot("help", true); ?>"><i class="fa fa-question-circle icon-width"></i> FAQs</A></LI>
        @endif
        @if(read("id"))
            <LI><A ONCLICK="handlelogin('logout');" CLASS="dropdown-item" href="#"><i class="fa fa-sign-out-alt icon-width"></i> Log Out</A></LI>
        @endif
    </ul>

    <a HREF="<?= webroot("index"); ?>" class="align-left align-middle text-white" style="margin-left:22px;font-weight: bold;font-size: 1rem !important;" href="/"><?= strtoupper(sitename); ?></a>

    <A HREF="<?= webroot("ourstory"); ?>" class="align-left align-middle text-white"><?= getsetting("aboutus"); ?>,</A>&nbsp;
    <A HREF="<?= webroot("help"); ?>" class="align-left align-middle text-white">FAQs,</A>&nbsp;
    <A HREF="<?= webroot("tos"); ?>" class="align-left align-middle text-white">TOS,</A>
    <A HREF="<?= webroot("privacy"); ?>" class="align-left align-middle text-white">Privacy Policy</A>

    <?php
        if (!islive()) {
            echo '<BR><SPAN TITLE="This will not show on the live server">&emsp;IP: <B>' . $_SERVER['SERVER_ADDR'] . "</B> ROUTE: <B>" . $routename . '</B>';
            $user = first("SELECT * FROM users WHERE profiletype = 1");
            $ispass = \Hash::check("admin", $user["password"]);

            $currtime = millitime();
            $lasttime = getsetting("lastupdate", 0);
            $delay = $currtime - $lasttime;
            echo " Curr: " . $currtime . " Last: " . $lasttime . " Between: " . $delay;
            if ($delay < 1000) {
                echo " - Likely refreshed!";
            }
            setsetting("lastupdate", $currtime);

            if ($ispass) {
                echo ' <SPAN ID="QUICKLOGIN" ONCLICK="' . "$('#login_email').val('" . $user["email"] . "');$('#login_password').val('admin');" . '"' . ">Admin: '" . $user["email"] . " PW: admin'</SPAN>";
            } else {
                echo ' <SPAN ID="QUICKLOGIN" ONCLICK="' . "$('#login_email').val('" . $user["email"] . "');" . '"' . ">Admin: '" . $user["email"] . " PW: [UNKNOWN]'</SPAN>";
            }
            if (!isset($_SERVER['HTTPS']) or $_SERVER['HTTPS'] == 'off') {
                echo " - Not HTTPS";
            }
            echo '</SPAN>';
        }
    ?>
</div>

<div class="container-fluid shadow">
    @yield('content')
</div>

@if($routename != "help" && false)
    <div class="container-fluid d-none d-sm-block list-group-item">
        <div class="row">
            <div class="col-sm-12">
                <a CLASS="btn btn-sm text-muted" href="<?= webroot("help"); ?>">
                    <i style="font-size: 1rem !important;" class="fa fa-question-circle icon-width"></i>
                    FAQs
                </a>
                @if(isset($_GET["time"]))
                    <SPAN id="servertime" CLASS="text-muted pull-right">Server time: <?= my_now(); ?></SPAN>
                @endif
            </div>
        </div>
    </div>
@endif
</body>

<script type="text/javascript">
    var newtime = -1, newday = -1;
    @if(isset($_GET["time"]) && is_numeric($_GET["time"]) && $_GET["time"] >= 0 && $_GET["time"] < 2400)
        newtime = Number("<?= $_GET["time"]; ?>");
    @endif
    @if(isset($_GET["day"]) && is_numeric($_GET["day"]) && $_GET["day"] >= 0 && $_GET["day"] <= 6)
        newday = Number("<?= $_GET["day"]; ?>");
    @endif
    $(window).load(function () {
        var time = Date.now() - timerStart;
        $("#td_loaded").text(time / 1000 + "s");
        console.log("Time until everything loaded: ", time);
    });
    $(document).ready(function () {
        var time = Date.now() - timerStart;
        $("#td_ready").text(time / 1000 + "s");
        console.log("Time until DOMready: ", time);
        $("#navbar-text").text("<?= "" . round((microtime(true) - $time), 5) . "s"; ?>");
        $("#servertime").text($("#servertime").text() + " - Javascript time: " + getNow(4));
        if($("#QUICKLOGIN").length){$("#QUICKLOGIN").click();}
    });
    log("Page has loaded at: " + Date.now());
</script>

<div style="display: none;">
    <?php
        if (isset($GLOBALS["filetimes"])) {
            // && !islive()){
            echo '<TABLE><TR><TH COLSPAN="2">File times</TH></TR>';
            $total = 0;
            foreach ($GLOBALS["filetimes"] as $Index => $Values) {
                echo '<TR><TD>' . $Index . '</TD><TD>';
                if (isset($Values["start"]) && isset($Values["end"])) {
                    $val = round($Values["end"] - $Values["start"], 4);
                    if (strpos($val, ".") === false) {
                        $val .= ".000";
                    } else {
                        $val = str_pad($val, 4, "0");
                    }
                    echo $val . "s";
                    $total += $val;
                } else {
                    echo "Unended";
                }
                echo '</TD></TR>';
            }
            $total = str_pad(round($total, 4), 5, "0");
            echo '<TR><TD>Total</TD><TD>' . $total . 's</TD></TR>';
            echo '<TR><TD>DOM Loaded</TD><TD ID="td_loaded"></TD></TR>';
            echo '<TR><TD>DOM Ready</TD><TD ID="td_ready"></TD></TR>';
            echo '</TABLE>';
        }
    ?>
</div>

@if(islive())
    <script>
        (function (i, s, o, g, r, a, m) {
            i['GoogleAnalyticsObject'] = r;
            i[r] = i[r] || function () {
                (i[r].q = i[r].q || []).push(arguments)
            }, i[r].l = 1 * new Date();
            a = s.createElement(o),
                m = s.getElementsByTagName(o)[0];
            a.async = 1;
            a.src = g;
            m.parentNode.insertBefore(a, m)
        })(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');

        ga('create', 'UA-39190394-2', 'auto');
        ga('send', 'pageview');

    </script>
@endif
</html>