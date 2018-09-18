@extends('layouts_app')
@section('content')
    <STYLE>
        H3{
            margin-bottom: 5px;
        }
    </STYLE>
    <?php
        $site_name = sitename;
        $email = '<A HREF="mailto:info@trinoweb.ca?subject=' . $site_name . '">info@trinoweb.ca</A>';
        $GLOBALS["currentnumber"] = 1;
        $launchdate = "April 1, 2017";
        $datestamp = strtotime($launchdate);
        $SQLdate = date("Y-m-d", $datestamp);
        $launched = iif(time() > $datestamp, " (Launched)");
        if(!$launched){
            $days = ceil(($datestamp - time()) / 86400) ;
            $launched = " (" . $days . " day" . iif($days > 1, "s") . " away)";
        }
        $orders =  first('SELECT count(*) as count FROM orders WHERE status <> 2 AND status <> 4 AND placed_at > "' . $SQLdate . '"')["count"];
    ?>
    <div class="row">
        <DIV CLASS="col-lg-12 bg-white list-padding list-card">
            <h3><?= makestring("{aboutus}"); ?></h3>
            <p>
                <?= $site_name; ?> is a {{ product }} delivery service that's "faster than picking up the phone".

                Created by Van and Roy of Hamilton; we've seen what's out there for online ordering and we're confident that we can do better.
                <br><br>

                We believe in the community and we must give back at all cost.
                That's why we're pledging to donate $0.25 from every order the local food bank. With your support; we will help many people for many years. This is the lifetime commitment of <?= $site_name; ?>.
                <br><br>

                Thank you for your support.
            </p>

            <hr>
            <div class="btn-outlined-danger text-center pt-1">
                <strong>June, 2017</strong>
                <p>
                    Orders: <?= $orders; ?>
                    <br> Donated: $<?= number_format((float)$orders * 0.25, 2, '.', ''); ?>
                    <br> Charity: London Food Bank
                </p>
            </div>
        </div>
    </div>
</div>
@endsection