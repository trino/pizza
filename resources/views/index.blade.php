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
        $SettingKey = getsetting($SettingKey, "0");
        if (file_exists($Filename)) {
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

    <style>
        html {
            scroll-behavior: smooth;
        }
    </style>
    @if(true)
@if(true)
        <div id="home-section" class=" image-bg vertical-align" style="background-image:url({{webroot("public/images/banner.jpg")}});height:500px;border-radius: 0;">
            <div class="container list-group-item" style="border: 0 !important;">
                <div class="col-md-6">
                    <div class="pt-5 pb-4" style=" ">
                        <h1 class="display-4 banner-text text-normal" style="color: #666 !important;font-size: 2rem;font-weight: bold;padding-bottom:1rem;"></h1>
                        <h1 style="color: #666 !important;text-shadow: #e0e0e0 1px 1px 0;font-size: 1.75rem;font-we4ight: bold" class="display-4 text-normal banner-text">The easiest way to book a Canbii Helper</h1>
                        <h1 style="font-weight:bold;color: #666 !important;text-shadow: #e0e0e0 1px 1px 0;font-size: 1rem;font-wei4ght: bold;padding-top:1rem;" class="display-4 text-normal banner-text">Starting at
                            $24/hour daily</h1>
                    </div>
                    <div class="">
                        <a class="btn btn-primary b3tn-lg" href="#booknow">Book now</a>
                        <a class="btn btn-secondary b3tn-lg" HREF="<?= webroot("help"); ?>" role="button">Learn more</a>
                    </div>
                    <h1 style="color: #666 !important;text-sh7adow: #dadada 1px 1px 0;font-size: 1rem;font-wei4ght: bold" class="display-4 text-normal banner-text"><br>Contact Us<br>info@canbii.com</h1>
                <!--div class="alert alert-success py-4 mt-4" role="alert" style="border:0 !important;">
                        <p>Billed by the hour • We bring all the supplies • Cancel anytime</p>
                        <strong>100% Satisfaction Guarantee</strong>
                        <br>
                        If you're unhappy with the service, we will send a new cleaner and re-clean for free!
                        <BR>
                        <a class="btn btn-success btn-sm mt-3" HREF="<?= webroot("help"); ?>" role="button">Still not convinced?</a>
                    </div-->
                </div>
            </div>
        </div>
        @endif
        <div class="container">
            <div class="row my-2">
            </div>



            <div class="row my-3">

                <div class="col-lg-12" style="padding:1rem;">
                    <h1 id="booknow" class="text-center"><strong>Hamilton, how can we help you today?</strong></h1>
                </div>




            </div>

        </div>
            @endif
            <div class="container">
                <div class="row">

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
                    <div class="col-lg-4 bg-inverse cool-border cool-border-left" titledebug="popups_checkout">
                        @include("popups_checkout")
                    </div>
                </div>
            </div>
            <div class="container d-none d-sm-block">
                <div class="row">
                    <div class="col-md-12">
                        <label class=" d-block text-center py-2 mt-4">
                            Copyright &copy; 2013 - 2020 / Canbii Services/ <a HREF="<?= webroot("help");?>" class="text-muted">Terms of Use</a>
                        </label>
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