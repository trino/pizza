<TITLE>CRON!</TITLE>
<STYLE>
    .finalattempt{
        color: red;
        font-weight: bold;
    }
</STYLE>
<TABLE BORDER="1" STYLE="margin: auto;">
    <TR><TH>ID</TH><TH>{{ucfirst(storename)}}</TH><TH>Orders</TH><TH>Attempt</TH><TH>Call</TH></TR>
    <?php //  http://localhost/pizza/public/cron
        $max_attempts = getsetting("max_attempts", 3);
        $enabled = isset($_GET["call"]) || !debugmode || islive();
        $delivery_delay = getdeliverytime() * 60;//minutes * 60 * 1000 // (60 seconds per minute, 1000 milliseconds per second)

        $orders = query("SELECT * FROM orders WHERE deliver_at = '0000-00-00 00:00:00'", true, "CRON");
        if($orders){
            vardump($orders);
            printline(count($orders) . " order(s) found/corrected with invalid delivery dates");
            foreach($orders as $order){
                $time = delivery_at($order["placed_at"], $order["deliverytime"], $delivery_delay);
                $query = "UPDATE orders SET deliver_at = '" . $time . "' WHERE id = " . $order["id"];
                Query($query);//didn't work
                //vardump($query);
            }
        }

        $query = "SELECT *, min(attempts) as new_attempts, count(*) as count FROM orders WHERE stripeToken <> '' AND paid = 1 AND status = 0 AND attempts < " . ($max_attempts+1) . " GROUP BY restaurant_id ORDER BY attempts ASC";
        $orders = query($query, true, "CRON");
        if($orders){
            foreach($orders as $index => $order){
                $orders[$index]["attempts"] = $order["new_attempts"];
                unset($orders[$index]["new_attempts"]);
            }
        }

        //vardump($query); vardump($orders); die();

        $restaurants = query("SELECT * FROM restaurants", true, "CRON");
        if(!$enabled){
            printline('<A HREF="' . Request::url() . '?call" TITLE="click to enable it">Calling system is disabled</A>');
        } else if(!debugmode) {
            printline('CRON: ' . count($orders) . ' ' . storenames . ' have unconfirmed orders');
        }

        function printline($text, $sidetext = false){
            if($sidetext){
                echo '<TR><TD COLSPAN="3" ALIGN="CENTER"><STRONG>' . $text . '</STRONG></TD><TD COLSPAN="2" ALIGN="CENTER"><STRONG>' . $sidetext . '</STRONG></TD></TR>';
            } else {
                echo '<TR><TD COLSPAN="5" ALIGN="CENTER"><STRONG>' . $text . '</STRONG></TD></TR>';
            }
        }

        function processorder($OrderID, $isfinal = false){
            //$orderid, &$info = false, $party = -1, $event = "order_placed", $Reason = "", $RetActions = false
            $info = false;
            $party = -1;//0=customer, 1=admin, 2=restaurant
            $event = iif($isfinal, "cron_job_final", "cron_job");
            return App::make('App\Http\Controllers\HomeController')->order_placed($OrderID, $info, $party, $event, "CRON", true);
            //return $ID;
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
            echo '<TD ALIGN="center"><INPUT TYPE="checkbox" DISABLED TITLE="Will only call if the system is enabled, and the order has a valid ' . storename . '"';
            $data = "[" . $order["restaurant_id"] . "NOT SENT]";
            if($enabled && $restaurant !== false){
                $data = processorder($order["id"], $isfinal);
                $calls+=1;
                echo ' CHECKED';
            }
            echo '></TR>';
            //vardump($data);
        }
        if($count == 0){
            echo '<TR><TD COLSPAN="5">No orders found with a stripeToken, status=0(pending), and attempts < ' . ($max_attempts+1) .'</TD></TR>';
        } else {
            echo '<TR><TD COLSPAN="2" ALIGN="right"><STRONG>Total:</STRONG></TD><TD ALIGN="right">' . $count . '</TD><TD COLSPAN="2" ALIGN="right">' . $calls . ' call(s)</TD></TR>';
        }
    ?>
</TABLE>
