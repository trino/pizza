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

        <div id="home-section" class=" image-bg vertical-align" style="background-image:url({{webroot("public/images/banner.jpg")}});height:400px;border-radius: 0;">
            <div class="container ">
                <div class="pt-5 pb-4">
                    <h1 style="color: #666 !important;font-size: 1.75rem;font-weight: bold">Hello, Hamilton!</h1>
                    <h1 style="color: #666 !important;font-size: 1.75rem;font-weight: normal">The easiest way to book a cleaner</h1>
                    <h1 style="font-weight:bold;color: #666 !important;font-size: 1rem;font-wei4ght: bold;padding:2rem 0 .15rem 0;">Re-launch Special</h1>
                    <h1 class="display-4 banner-text text-normal" style="color: #666 !important;font-size: 1.75rem;font-weight: bold;font-weight: normal">25% Off All Services!</h1>

                    <div style="margin-top:1rem; "></div>
                    <a class="btn btn-primary" href="#booknow">Book Now</a>
                    <a class="btn btn-secondary" HREF="<?= webroot("help"); ?>" role="button">Learn More</a>
                </div>
            </div>
        </div>


        <div class="container ">


            <div class="row">
                <div class="col-lg-12">
                    <div class="list-group-item" style="padding-top: 2rem !important;padding-bottom: 0 !important;">

                        <div class="alert alert-danger" role="alert" style="margin-top: 0 !important;margin-bottom: 0 !important;">
                            <strong>COVID-19: Supplies No Longer Included</strong><br>To avoid cross-contamination from home to home; we ask that you supply the cleaning products, vacuum & mop.
                            If you do not have supplies, we can bring it for an additional charge of $15. Thank you for your patience.
                        </div>
                    </div>
                </div>
            </div>

            <div class="row my-4">
                <div class="col-lg-3">
                    <div class="   list-group-item">
                        <div class="card card-body bg-white">
                            <h3 class="mb-3 text-center"><i class="fa fa-leaf mr-1 " style="color:  #d7d7d7;"></i></h3>

                            <strong class="card-title text-primary text-center"> Choose Your Time
                            </strong>
                            <p class="card-text"> Pick your date and cleaning hours required. We'll take care of the rest!
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 pa-3 ">
                    <div class="   list-group-item">
                        <div class="card card-body bg-white">
                            <h3 class="mb-3 text-center"><i class="fa fa-leaf mr-1 " style="color:  #d7d7d7;"></i></h3>

                            <strong class="card-title text-primary text-center"> Pay Securely Online
                            </strong>
                            <p class="card-text"> Canbii uses the same technology that bank uses for secure online payments.

                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 pa-3 ">
                    <div class="   list-group-item">
                        <div class="card card-body bg-white">
                            <h3 class="mb-3 text-center"><i class="fa fa-leaf mr-1 " style="color:  #d7d7d7;"></i></h3>

                            <strong class="card-title text-primary text-center"> Sit Back & Relax
                            </strong>
                            <p class="card-text"> We'll arrive at the scheduled date and have your home spic and span.

                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 pa-3  ">
                    <div class=" list-group-item">
                        <div class="card card-body bg-white">
                            <h3 class="mb-3 text-center"><i class="fa fa-leaf mr-1 " style="color:  #d7d7d7;"></i></h3>
                            <strong class="card-title text-primary text-center"> The Canbii Promise
                            </strong>
                            <p class="card-text"> With our 100% Satisfaction Guarantee, we will re-clean for free. No questions asked.

                            </p>
                        </div>
                    </div>
                </div>
            </div>
            @if(false)
                <div class="row my-4">


                    <div class="col-md-4 text-center" id="">
                        <a class="icon-box" href="tel:2896831944"> <i class="fa  fa-phone  fa-3x"></i>
                            <div class="icon-box__text">
                                <h4 class="">289.683.1944</h4> <span class="icon-box__subtitle">CALL NOW!</span>
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
            @endif<br>
            <div class="row">

                <div class="col-lg-2"></div>
                <div class="col-lg-8 ">
                    <div class="list-group-item">
                        <div class="">
                            <br>
                            <h1 class="text-center"><strong>What's Included in a Basic Cleaning?</strong></h1>
                            <br>
                            <p>The following outlines common tasks perform for a basic cleaning. If you'd like for us to focus on specific areas, please add a note to your appointment with
                                relevant instructions.

                            </p>
                            <strong>Bathrooms</strong>
                            <ul>
                                <li> Cleaning toilets, showers, and sinks
                                </li>
                                <li> Dusting all surfaces
                                </li>
                                <li> Wiping down all mirrors and glass surfaces/furniture
                                </li>
                                <li> Vacuuming/Cleaning floors
                                </li>
                                <li> Taking out garbage
                                </li>
                            </ul>

                            <strong>Bedrooms & Common Areas</strong>
                            <ul>
                                <li> General dusting of all surfaces
                                </li>
                                <li> Wiping down mirrors and glass surfaces/furniture
                                </li>
                                <li> Vacuuming / Cleaning floors
                                </li>
                                <li> Taking out garbage
                                </li>
                            </ul>

                            <strong>Kitchen</strong>
                            <ul>
                                <li> General dusting of all surfaces
                                </li>
                                <li> Washing dishes / loading dishwasher
                                </li>
                                <li> Cleaning exterior surfaces (stove, cabinets, fridge)
                                </li>
                                <li> Vacuuming / Cleaning floors
                                </li>
                                <li> Taking out garbage
                                </li>
                            </ul>
                            <p> We pay special attention to detail on every project to ensure the complete satisfaction of each client. ​Our goal is to make sure that we leave your home with a sparkling kitchen,
                                bathrooms and floors, organized and tidy living spaces, and refreshing aromas of cleanliness!
                            </p>


                        </div>
                    </div>
                </div>
                <div class="col-lg-2"></div>
            </div>
            <br>
            <br>
            <div class="row">
                <div class="col-lg-2  text-center"></div>
                <div class="col-lg-8 px-3 text-center">


                    <h1 class="mb-2" id="booknow"><strong>Ready to Book a Canbii Cleaner?</strong></h1>

                    <p> We offer simple flat pricing by the hour. You provide cleaning supplies. No surprises. Cancel anytime! </p>


                    <div class="alert alert-primary" role="alert">
                        Re-launch Special — <strong>25% Off All Services!</strong>
                    </div>

                </div>
                <div class="col-lg-2"></div>

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
            <div class="col-lg-4  bg-inverse" titledebug="popups_checkout">
                @include("popups_checkout")
            </div>
        </div>
    </div>
    <div class="container d-none d-sm-block">
        <div class="row">
            <div class="col-md-12">
                <label class=" d-block text-center py-2 mt-4">Canbii &copy; 2013 - 2020 / <a HREF="<?= webroot("help");?>" class="text-muted">About</a> / <a HREF="<?= webroot("help");?>" class="text-muted">Terms</a></label>
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