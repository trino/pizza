<TITLE>CRON!</TITLE>
<STYLE>
    .finalattempt{
        color: red;
        font-weight: bold;
    }
</STYLE>
<TABLE BORDER="1" STYLE="margin: auto;">
    <TR><TH>ID</TH><TH>Restaurant</TH><TH>Orders</TH><TH>Attempt</TH><TH>Call</TH></TR>
    <?php //  http://localhost/pizza/public/cron
        $max_attempts = getsetting("max_attempts", 3);
        $enabled = isset($_GET["call"]) || !debugmode;
        $orders = query("SELECT *, count(*) as count FROM orders WHERE stripeToken <> '' AND status = 0 AND attempts < " . ($max_attempts+1) . " GROUP BY restaurant_id", true, "CRON");
        $restaurants = query("SELECT * FROM restaurants", true, "CRON");
        if(!$enabled){
            echo '<TR><TD COLSPAN="5" ALIGN="CENTER"><STRONG><A HREF="' . Request::url() . '?call" TITLE="click to enable it">Calling system is disabled</A></STRONG></TD></TR>';
        } else if(!debugmode) {
            log("CRON: " . count($orders) . " restaurants have unconfirmed orders");
        }
        function processorder($ID, $isfinal = false){
            //$orderid, &$info = false, $party = -1, $event = "order_placed", $Reason = ""
            App::make('App\Http\Controllers\HomeController')->order_placed($ID, false, -1, iif($isfinal, "cron_job_final", "cron_job"));
            return $ID;
        }
        function findkeyvalue($array, $key, $value, $retdata = false){
            foreach($array as $index => $data){
                if($data[$key] == $value){
                    if($retdata){return $data;}
                    return $index;
                }
            }
            return false;
        }


        $calls=0;
        $count = 0;
        foreach($orders as $order){
            $isfinal = $order["attempts"] == $max_attempts;
            $restaurant = findkeyvalue($restaurants, "id", $order["restaurant_id"], true);
            echo '<TR>';
            echo '<TD ALIGN="right">' . $order["id"] . '</TD>';
            if($restaurant === false){
                echo '<TD CLASS="finalattempt">' . $order["restaurant_id"] . ' (NOT FOUND)</TD>';
            } else {
                echo '<TD>' . $order["restaurant_id"] . ' (' . $restaurant["name"] . ')</TD>';
            }
            $count += $order["count"];
            echo '<TD ALIGN="right">' . $order["count"] . '</TD>';
            echo '<TD' . iif($isfinal, ' CLASS="finalattempt"') . ' ALIGN="right">' . getordinal($order["attempts"]) . '</TD>';
            echo '<TD ALIGN="center"><INPUT TYPE="checkbox" DISABLED TITLE="Will only call if the system is enabled, and the order has a valid restaurant"';
            if($enabled && $restaurant !== false){
                processorder($order["restaurant_id"], $isfinal);
                $calls+=1;
                echo ' CHECKED';
            }
            echo '></TR>';
        }
        if($count == 0){
            echo '<TR><TD COLSPAN="5">No orders found with a stripeToken, status=0(pending), and attempts < ' . ($max_attempts+1) .'</TD></TR>';
        } else {
            echo '<TR><TD COLSPAN="2" ALIGN="right"><STRONG>Total:</STRONG></TD><TD ALIGN="right">' . $count . '</TD><TD COLSPAN="2" ALIGN="right">' . $calls . ' call(s)</TD></TR>';
        }
    ?>
</TABLE>
