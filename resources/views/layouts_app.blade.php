<!DOCTYPE html>
<html lang="en" class="full">
<head>
    <?php
        $time = microtime(true);
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
        $minimal = true;//also change in index.blade.php
        $noclose = false;//!read("id");
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
    <!--meta name="viewport" content="width=380, user-scalable=no"-->
    <meta name="viewport" content="width=device-width, user-scalable=no">
    <meta http-equiv="content-language" content="en-CA">
    <meta name="mobile-web-app-capable" content="yes">
    <!--title><?= cityname . " " . product ?> Delivery</title-->
    <title>Canbii Cleaners Hamilton</title>
    <link rel="icon" sizes="128x128" href="<?= webroot("images/" . strtolower(product) . "128.png"); ?>">
    <link rel="icon" sizes="192x192" href="<?= webroot("images/" . strtolower(product) . "192.png"); ?>">
    <script defer src="https://use.fontawesome.com/releases/v5.0.6/js/all.js"></script>

    <link rel="stylesheet" href="<?= $css; ?>/bootstrap.min.css">
    <?php
        $CSS = "public/css/";
        includefile($CSS . "custom5.css");
        includefile($CSS . "sprite128.css");//128 pixel version of the sprite sheet
        includefile($CSS . "google.css");
        includefile($CSS . "toast.css");
    ?>
    <script src="<?= $scripts; ?>/jquery.min.js"></script>
    <script src="<?= $scripts; ?>/tether.min.js"></script>
    <script src="<?= $scripts; ?>/bootstrap.min.js"></script>
    <SCRIPT SRC="<?= $scripts; ?>/jquery.validate.min.js"></SCRIPT>
    @include("popups_alljs")
</head>
<body>
<div id="snackbar"></div>
<div ID="loading" class="fullscreen grey-backdrop dont-show"></div>


<style>
    *{bo90rder:1px solid black !important;}
</style>
<div class=" contai3ner-fluid {{ headercolor }}" style="padding-right: 0">
<div style="border:0 !important;z-index: 999;padd3ing:0 !important;" class="list-group-item container">
    <?php
        if(defined("logo")){
            echo  '<a HREF="' .webroot("") .'"><IMG CLASS="sitelogo" SRC="' . webroot("images/" . logo) . '"></a>';
        }
    ?>

    <button data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="margin-right: 0" class="btn btn-settings bg-transparent togglemenu" ONCLICK="$('#dropdown-menu').toggle();">
        <i class="fa fa-bars loggedout {{ headertextcolor }}"></i>
        <i class="fa fa-user loggedin {{ headertextcolor }}"></i>
    </button>

    <ul class="dropdown-menu dropdown-menu-right" ID="dropdown-menu">
        <SPAN class="loggedin profiletype profiletype1">
            <?php
                foreach (array("users", "restaurants", "useraddresses", "orders", "additional_toppings", "actions", "shortage", "settings", "hours") as $table) {
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
                <li class="profiletype_not profiletype_not2"><A CLASS="dropdown-item" HREF="javascript:orders();"><i class="fa fa-clock icon-width"></i> My Bookings</A></li>
            @endif
        </SPAN>

        <SPAN class="loggedout">
            <LI><A CLASS="dropdown-item" HREF="javascript:showlogin('login');"><i class="fa fa-user icon-width"></i> Login / Signup</A></LI>
        </SPAN>

        @if($routename == "help")
            <LI><A CLASS="dropdown-item" HREF="<?= webroot("", true); ?>"><i class="fa fa fa-shopping-cart icon-width"></i> Menu</A></LI>
        @else
            <LI><A CLASS="dropdown-item" HREF="<?= webroot("help", true); ?>"><i class="fa fa-question-circle icon-width"></i> About Us</A></LI>
        @endif

        <!--LI><A CLASS="dropdown-item" HREF="#"><i class="fa fa-star icon-width"></i> Weekly Giveaway</A></LI-->
        <LI class="loggedin"><A CLASS="dropdown-item" href="javascript:handlelogin('logout');"><i class="fa fa-sign-out-alt icon-width"></i> Log Out</A></LI>

        @if(false)
            <LI><A CLASS="dropdown-item" HREF="<?= webroot("map", true); ?>"><i class="fa fa-question-circle icon-width"></i> Map</A></LI>
            <LI><A CLASS="dropdown-item" HREF="<?= webroot("ourstory"); ?>"><?= makestring("{aboutus}"); ?></A></LI>
            <LI><A CLASS="dropdown-item" HREF="<?= webroot("help"); ?>">About Us</A></LI>
            <LI><A CLASS="dropdown-item" HREF="<?= webroot("tos"); ?>">TOS</A></LI>
            <LI><A CLASS="dropdown-item" HREF="<?= webroot("privacy"); ?>">Privacy Policy</A></LI>
            <LI><A CLASS="dropdown-item" HREF="<?= webroot("contact"); ?>">Contact Us</A></LI>
        @endif
    </ul>

    <a style="text-decoration: none;" HREF="<?= webroot(""); ?>" class="{{ headertextcolor }} "><span style="color: white; text-decoration: none;font-weight: bold" ><?= sitename; ?></span><br>
        <!--span style="color: #efefef;font-weight: normal;fo3nt-size: 90%;" >On-Demand Home Cleaning</span-->

    </a>

    <?php
        if (debugmode) {
            echo '<SPAN ID="debugbar" TITLE="This will not show on the live server">&emsp;IP: <B>' . $_SERVER['SERVER_ADDR'] . "</B> ROUTE: <B>" . $routename . '</B>';
            $user = first("SELECT * FROM users WHERE profiletype = 1", true, "layouts_app");
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
            echo '<BUTTON CLASS="float-right btn-danger" ONCLICK="$(' . "'#debugbar'" . ').remove();"><I CLASS="fas fa-times cursor-pointer"></I></BUTTON></SPAN>';
        }
    ?>
</div>
</div>
<div class="contai6ner menu">
    @yield('content')
</div>

@if($routename != "help" && false)
    <div class="container-fluid d-none d-sm-block list-group-item">
        <div class="row">
            <div class="col-sm-12">
                <A CLASS="btn btn-sm text-muted" href="<?= webroot("help"); ?>">
                    <i class="fa fa-question-circle icon-width helpbtn"></i>
                    FAQs
                </A>
                @if(isset($_GET["time"]))
                    <SPAN id="servertime" CLASS="text-muted pull-right">Server time: <?= my_now(); ?></SPAN>
                @endif
            </div>
        </div>
    </div>
@endif
</body>

<script type="text/javascript">
    var newtime = -1, newday = -1, testing = false;
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
        if ($("#QUICKLOGIN").length) {
            $("#QUICKLOGIN").click();
        }
    });
    log("Page has loaded at: " + Date.now());
</script>

@if(islive())

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-61032538-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'UA-61032538-1');
    </script>


@endif

<?php
    function findSQL($SQLquery) {
        foreach ($GLOBALS["SQL"] as $index => $query) {
            if ($query["Query"] == $SQLquery) {
                return $index;
            }
        }
    }

    if (debugmode) {
        echo '<STYLE>.sqlduplicate{color:red;}.centertable{margin-left: auto; margin-right: auto;}</STYLE>';
        if (isset($GLOBALS["SQL"])) {
            echo '<TABLE BORDER="1" CLASS="centertable">';
            echo '<TR><TD COLSPAN="4" ALIGN="CENTER"><STRONG>SQL Debug data. (<SPAN CLASS="sqlduplicate">red text</SPAN> = duplicate SQL query)</STRONG></TD></TR>';
            echo '<TR><TH>#</TH><TH>Time</TH><TH>SQL Query</TH><TH>Where</TH></TR>';
            $total = 0;
            $duplicates = 0;
            foreach ($GLOBALS["SQL"] as $index => $query) {
                $total += $query["Time"];
                echo '<TR><TD>' . $index . '</TD><TD>' . $query["Time"] . ' ms</TD><TD>' . $query["Query"] . '</TD><TD';
                if (findSQL($query["Query"]) < $index) {
                    echo ' CLASS="sqlduplicate"';
                    $duplicates += 1;
                }
                echo '>' . $query["Where"] . '</TD></TR>';
            }
            echo '<TR><TD COLSPAN="3" ALIGN="RIGHT"><STRONG>Total Time:</STRONG></TD><TD><STRONG>' . $total . ' ms</STRONG></TD></TR>';
            echo '<TR><TD COLSPAN="3" ALIGN="RIGHT"><STRONG>Total Queries:</STRONG></TD><TD><STRONG>' . count($GLOBALS["SQL"]) . '</STRONG></TD></TR>';
            echo '<TR><TD COLSPAN="3" ALIGN="RIGHT"><STRONG>Total Duplicates:</STRONG></TD><TD><STRONG>' . $duplicates . '</STRONG></TD></TR>';
            echo '</TABLE>';
        }
        if (isset($GLOBALS["filetimes"])) {
            echo '<TABLE BORDER="1" CLASS="centertable"><TR><TH COLSPAN="3">File times</TH></TR>';
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
                echo '</TD><TD' . iif($Values["times"] > 1, ' CLASS="sqlduplicate"') . '>x' . $Values["times"] . '</TD></TR>';
            }
            $total = str_pad(round($total, 4), 5, "0");
            echo '<TR><TD>Total</TD><TD COLSPAN="2">' . $total . 's</TD></TR>';
            echo '<TR><TD>DOM Loaded</TD><TD COLSPAN="2" ID="td_loaded"></TD></TR>';
            echo '<TR><TD>DOM Ready</TD><TD COLSPAN="2" ID="td_ready"></TD></TR>';
            echo '</TABLE>';
        }
        if (isset($GLOBALS["debugdata"])) {
            echo '<TABLE BORDER="1" CLASS="centertable"><TR><TH COLSPAN="2">Debug data</TH></TR>';
            foreach ($GLOBALS["debugdata"] as $index => $value) {
                echo '<TR><TD>' . $index . '</TD><TD>' . $value . '</TD></TR>';
            }
            echo '</TABLE>';
        }
    }
    if (!read("id" && $minimal)) {
        echo view("popups_login", array("minimal" => $minimal, "noclose" => $noclose))->render();
    }
?>






</html>