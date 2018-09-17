@extends('layouts_app')
@section('content')
    <?php
        startfile("index");
        $minimal = true;//also change in layouts_app.blade.php
        function bool($value){
            return iif($value, "true", "false");
        }
        function filemtime2($Filename){
            if (file_exists($Filename)) {
                return filemtime($Filename);
            }
            return 0;
        }
        function isFileUpToDate2($SettingKey, $Filename = false){
            if (file_exists($Filename)) {
                $SettingKey = getsetting($SettingKey, "0");
                $lastFILupdate = filemtime($Filename);
                return $lastFILupdate <= $SettingKey;
            }
        }

        if(!read("id") && !$minimal){
            echo view("popups_login")->render();
        } else {
            if(debugmode || read("profiletype") == 1){
                echo view("popups_time")->render();
            }
            ?>
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
                    if(debugmode){
                        $GLOBALS["debugdata"]["docache"] = bool($doCache);
                        $GLOBALS["debugdata"]["usecache"] = bool($menucache_uptodate && $doCache);
                        $GLOBALS["debugdata"]["menucache setting"] = isFileUpToDate("menucache");
                        $GLOBALS["debugdata"]["menucache_uptodate"] = bool($menucache_uptodate) . " (" . filemtime2($menucache_filename) . ")";
                        $GLOBALS["debugdata"]["menublade_uptodate"] = bool($menublade_uptodate) . " (" . filemtime2($menublade_filename) . ")";
                    }
                ?>
                <div class="col-lg-3 col-md-12 bg-inverse bg-grey">
                    @include("popups_checkout")
                </div>
            </div>
        <?php } ?>
    @include("popups_editprofile_modal")
    <div class="fixed-action-btn hidden-lg-up sticky-footer d-lg-none">
        <button class="circlebutton bg-success dont-show" onclick="window.scrollTo(0,document.body.scrollHeight);" TITLE="Scroll to the top of the page">
            <span class="white" id="checkout-total"></span>
        </button>
    </div>
    <?php endfile("index"); ?>
@endsection