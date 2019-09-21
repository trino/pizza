@extends('layouts_app')
@section('content')
    <?php
    startfile("index");
    $minimal = true;//also change in layouts_app.blade.php
    function bool($value)
    {
        return iif($value, "true", "false");
    }
    function filemtime2($Filename)
    {
        if (file_exists($Filename)) {
            return filemtime($Filename);
        }
        return 0;
    }
    function isFileUpToDate2($SettingKey, $Filename = false)
    {
        if (file_exists($Filename)) {
            $SettingKey = getsetting($SettingKey, "0");
            $lastFILupdate = filemtime($Filename);
            return $lastFILupdate <= $SettingKey;
        }
    }

    if(!read("id") && !$minimal){
        echo view("popups_login")->render();
    } else {
    if (debugmode || read("profiletype") == 1) {
        echo view("popups_time")->render();
    }
    ?>



    <div id="home-section" class="image-bg vertical-align" style="background-image:url(http://localhost/pizza/public/images/general-labour.jpg);height:400px">


        <div class="container ">
            <div class="row py-3">

                <div class="col-lg-12 py-3">
                    <h1 style="text-shadow: black 0px 0px 10px;" class="home-head-primary">Find house cleaning services near you</h1>

                    <h1 style="text-shadow: black 0px 0px 10px;font-size: 2rem;" class="home-head-secondary">Servicing Hamilton & The GTA</1>

                </div>


            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row my-4 py-3">

            <div class="col-lg-3">
                <div class="trust-card-header">
                    <h3>Trustworthy housekeeping</h3></div>
                <div class="trust-card-content">Each cleaner is interviewed and background checked.</div>
            </div>
            <div class="col-lg-1">
            </div>
            <div class="col-lg-3">
                <div class="trust-card-header">
                    <h3>Book in 60 seconds</h3></div>
                <div class="trust-card-content">Review total price and book online instantly.</div>
            </div>
            <div class="col-lg-1">
            </div>
            <div class="col-lg-3">
                <div class="trust-card-header">
                    <h3><i class="fa fa-file"></i>Hamilton local</h3></div>
                <div class="trust-card-content">Local service you know you can trust</div>
            </div>


        </div>

        <div class="row py-3">

            <div class="col-lg-12 py-1">
                <h3>Ready to book your first service?</h3>

            </div>


        </div>


        <div class="row shadow">


            <?php
            $doCache = $GLOBALS["settings"]["domenucache"];
            $menucache_filename = public_path() . "/menucache.html";
            $menublade_filename = resource_path() . "/views/popups_menu.blade.php";
            $menublade_uptodate = isFileUpToDate2("menucache", $menublade_filename);
            $menucache_uptodate = isFileUpToDate2("menucache", $menucache_filename);
            if ($menucache_uptodate && $menublade_uptodate && $doCache) {
                echo '<!-- menu cache pre-generated at: ' . filemtime($menucache_filename) . " it is " . time() . ' now --> ' . file_get_contents($menucache_filename);
            } else {
                $menu = view("popups_menu")->render();
                if ($doCache) {
                    file_put_contents($menucache_filename, $menu);
                    setsetting("menucache", filemtime($menucache_filename));
                }
                echo '<!-- menu cache generated at: ' . my_now() . ' --> ' . $menu;
            }
            if (debugmode) {
                $GLOBALS["debugdata"]["docache"] = bool($doCache);
                $GLOBALS["debugdata"]["usecache"] = bool($menucache_uptodate && $doCache);
                $GLOBALS["debugdata"]["menucache setting"] = isFileUpToDate("menucache");
                $GLOBALS["debugdata"]["menucache_uptodate"] = bool($menucache_uptodate) . " (" . filemtime2($menucache_filename) . ")";
                $GLOBALS["debugdata"]["menublade_uptodate"] = bool($menublade_uptodate) . " (" . filemtime2($menublade_filename) . ")";
            }
            ?>
            <div class="col-lg-3 col-md-12 bg-inverse bg-grey" titledebug="popups_checkout">
                @include("popups_checkout")
            </div>
        </div>
    </div>
    <?php } ?>

    @include("popups_editprofile_modal")
    <div class="fixed-action-btn hidden-lg-up sticky-footer d-lg-none">
        <button class="circlebutton bg-primary dont-show" onclick="window.scrollTo(0,document.body.scrollHeight);" TITLE="Scroll to the top of the page">
            <span class="white" id="checkout-total"></span>
        </button>
    </div>
    <?php endfile("index"); ?>
@endsection