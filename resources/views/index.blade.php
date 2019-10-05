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

    @if(!read("id"))

    <div id="home-section" class="jumbotron image-bg vertical-align" style="background-image:url({{webroot("public/images/banner.jpg")}});height:450px;border-radius: 0;">

        <div class="container-fluid">
            <div class="btn-group-sm">

                <h1 class="display-4 banner-text text-normal" style="">Hello, Hamilton!</h1>


                <h1 style="font-size: 2.25rem;" class="display-4 text-normal banner-text">The easiest way to book a home cleaning expert</h1>
                <p class="lead  text-normal banner-text" style=";">Canbii Cleaning is the easiest way to book house cleaners near you</p>
                <p class="lead" >
                    <a class="btn btn-primary btn-lg" href="#booknow" >Book now</a>
                    <a class="btn btn-secondary btn-lg" href="#" role="button">Learn more</a>

                </p>
            </div>
        </div>
    </div>



    <div class="container-fluid">


    <div class="row my-4">
        <div class="col-lg-3 pa-3">
            <div class="   list-group-item">
                <div class="card-body bg-white">
                    <h3 class="mb-3 text-center"><i class="fa fa-leaf mr-1 " style="color:  #d7d7d7;"></i></h3>

                    <h5 class="card-title">Trustworthy housekeeping</h5>
                    <p class="card-text">Each cleaner is interviewed and background checked We have teams that do 100% green cleaning</p>
                    <a href="#" class="card-link">Card link</a>
                </div>
            </div>
        </div>
        <div class="col-lg-3 pa-3 ">
            <div class="   list-group-item">
                <div class="card-body bg-white">
                    <h3 class="mb-3 text-center"><i class="fa fa-leaf mr-1 " style="color:  #d7d7d7;"></i></h3>

                    <h5 class="card-title"> Book in 60 seconds</h5>
                    <p class="card-text"> Review total price and book online instantly We have teams that do 100% green cleaning
                    </p>
                    <a href="#" class="card-link">Card link</a>
                </div>
            </div>
        </div>
        <div class="col-lg-3 pa-3 ">
            <div class="   list-group-item">
                <div class="card-body bg-white">
                    <h3 class="mb-3 text-center"><i class="fa fa-leaf mr-1 " style="color:  #d7d7d7;"></i></h3>

                    <h5 class="card-title">Hamilton native</h5>
                    <p class="card-text"> Local service you can trust We have teams that do 100% green cleaning
                    </p>
                    <a href="#" class="card-link">Card link</a>
                </div>
            </div>
        </div>
        <div class="col-lg-3 pa-3  ">
            <div class=" list-group-item">
                <div class="card-body bg-white">
                    <h3 class="mb-3 text-center"><i class="fa fa-leaf mr-1 " style="color:  #d7d7d7;"></i></h3>
                    <h5 class="card-title">Eco-Friendly Cleaning Products</h5>
                    <p class="card-text"> We have teams that do 100% green cleaning We have teams that do 100% green cleaning
                    </p>
                    <a href="#" class="card-link">Another link</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row my-3">
    </div>
    <div class="row  list-group-item">
        <div class="col-lg-2  text-center"></div>
        <div class="col-lg-8  text-center">
            <h1 class="mb-2" name="booknow">Ready to book your first cleaning?</h1>
            <p>We offer simple flat pricing based on the size of the home. All flat rate pricing includes your bedrooms,
                bathrooms, kitchen, and common areas. We offer our hourly package for any Custom Jobs, Large Apartments/Homes,
                and Offices. No surprises. Cancel anytime!
            </p>
        </div>
        <div class="col-lg-2  text-center"></div>

    </div>
    <div class="row my-3">
    </div>
    </div>

    @endif
    <div class="container-fluid">
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
            <div class="col-lg-3 col-md-12 bg-inverse" titledebug="popups_checkout">
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