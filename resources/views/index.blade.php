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


    <!--h1>Smooth Scroll</h1>

    <div class="main" id="section1">
        <h2>Section 1</h2>
        <p>Click on the link to see the "smooth" scrolling effect.</p>
        <a href="#section2">Click Me to Smooth Scroll to Section 2 Below</a>
        <p>Note: Remove the scroll-behavior property to remove smooth scrolling.</p>
    </div>

    <div class="main" id="section2">
        <h2>Section 2</h2>
        <a href="#section1">Click Me to Smooth Scroll to Section 1 Above</a>
    </div-->

<!-- !read("id") &&  -->
    @if(true)

        <div id="home-section" class=" image-bg vertical-align" style="background-image:url({{webroot("public/images/banner.jpg")}});height:500px;border-radius: 0;">

            <div class="container list-group-item" style="border: 0 !important;">


                <div class="">
                    <div class="pt-5 pb-5" style=" ">
                        <h1 class="display-4 banner-text text-normal" style="color: rgba(0,0,0,0.6);text-shadow: 2px 8px 6px rgba(0,0,0,0.2), 0px -5px 35px rgba(255,255,255,0.3);font-size: 2.75rem;">Hello, Hamilton!</h1>
                        <h1 style="color: #616161;text-shadow: #e0e0e0 1px 1px 0;font-size: 1.75rem;" class="display-4 text-normal banner-text">The easiest way to book a home cleaning expert</h1>
                        <p class="lead  text-normal banner-text" style="color: rgba(0,0,0,0.6);text-shadow: 2px 8px 6px rgba(0,0,0,0.2), 0px -5px 35px rgba(255,255,255,0.3);">Five star service at an affordable price!</p>
                    </div>


                    <div class="">
                        <a class="btn btn-primary btn-lg" href="#booknow">Book now</a>
                        <a class="btn btn-secondary btn-lg" HREF="<?= webroot("help"); ?>" role="button">Learn more</a>
                    </div>
                </div>

            </div>
        </div>



        <div class="container">

            <div class="row my-4">
                <div class="col-lg-3 pa-3">
                    <div class="   list-group-item">
                        <div class="card-body bg-white">
                            <h3 class="mb-3 text-center"><i class="fa fa-leaf mr-1 " style="color:  #d7d7d7;"></i></h3>

                            <h5 class="card-title"> CHOOSE YOUR TIME
                            </h5>
                            <p class="card-text"> Choose your ideal date/time and package. We'll take care of the rest!
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 pa-3 ">
                    <div class="   list-group-item">
                        <div class="card-body bg-white">
                            <h3 class="mb-3 text-center"><i class="fa fa-leaf mr-1 " style="color:  #d7d7d7;"></i></h3>

                            <h5 class="card-title"> PAY SECURELY ONLINE
                            </h5>
                            <p class="card-text"> Canbii uses the same technology that bank uses for secure online payments

                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 pa-3 ">
                    <div class="   list-group-item">
                        <div class="card-body bg-white">
                            <h3 class="mb-3 text-center"><i class="fa fa-leaf mr-1 " style="color:  #d7d7d7;"></i></h3>

                            <h5 class="card-title"> SIT BACK & RELAX
                            </h5>
                            <p class="card-text"> Your cleaner will arrive at your scheduled date and get your home spic and span

                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 pa-3  ">
                    <div class=" list-group-item">
                        <div class="card-body bg-white">
                            <h3 class="mb-3 text-center"><i class="fa fa-leaf mr-1 " style="color:  #d7d7d7;"></i></h3>
                            <h5 class="card-title"> THE CANBII PROMISE
                            </h5>
                            <p class="card-text"> With our 100% Satisfaction Guarantee, we will re-clean for free

                            </p>
                        </div>
                    </div>
                </div>
            </div>
            @if(false)
            <div class="row my-4">


            <div class="col-md-4 text-center" id="">
                <a class="icon-box" href="tel:9055315331"> <i class="fa  fa-phone  fa-3x"></i>
                    <div class="icon-box__text">
                        <h4 class="">(905) 531-5331</h4> <span class="icon-box__subtitle">CALL NOW!</span>
                    </div>
                </a></div>
                <div class="col-md-4 text-center" id="">
                <a class="icon-box" href="http://zwt.co/sms?t=9055315331&amp;body=Hi%20Maid%20Sailors%21%20My%20name%20is"> <i
                            class="fa  fa-comments  fa-3x"></i>
                    <div class="icon-box__text">
                        <h4 class="">TEXT US</h4> <span class="icon-box__subtitle">MOBILE ONLY</span></div>
                </a></div>
                <div class="col-md-4 text-center" id="">
                <a class="icon-box" href=""> <i class="fa  fa-laptop  fa-3x"></i>
                    <div class="icon-box__text"><h4 class="">LOGIN</h4> <span class="icon-box__subtitle">EXISTING CUSTOMERS</span></div>
                </a></div>

            </div>
            @endif
            <div class="row my-3">
            </div>
            <div class="row  list-group-item">
                <div class="col-lg-2  text-center"></div>
                <div class="col-lg-8  text-center">


                    <h3 class="mb-2" name="booknow">What's included in a cleaning?</h3>
                    <p> All Canbii Cleaning Professionals bring cleaning supplies and a vacuum to appointments. If you'd like your cleaner to use your own products, please add a note to your appointment with relevant
                        instructions. See what's included in a cleaning service <a class="" HREF="<?= webroot("help"); ?>" role="button">here</a>.

                    </p>
                    <br>

                    <h2 style="font-size: 1.25rem !important;" class="mb-2" id="booknow">Ready to book your first cleaning?</h2>
                    <p>We offer simple flat pricing by the hour. No surprises. Cancel anytime!
                    </p>


                </div>
                <div class="col-lg-2  text-center"></div>

            </div>
            <div class="row my-3">
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
            <div class="col-lg-4 col-md-12 bg-inverse" titledebug="popups_checkout">
                @include("popups_checkout")
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <label class=" d-block text-center py-2 mt-2">
                    Copyright &copy; 2019 / Canbii Cleaners / <a HREF="<?= webroot("help");?>" class="text-muted">Terms</a>
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