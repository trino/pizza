<TITLE>CRON!</TITLE>

<TABLE BORDER="1">
    <TR><TH>ID</TH><TH>Restaurant</TH><TH>Attempt</TH></TR>
    <?php //  http://localhost/pizza/public/cron
        function processorder($ID){
            //$orderid, &$info = false, $party = -1, $event = "order_placed", $Reason = ""
            //App::make('App\Http\Controllers\HomeController')->order_placed($ID, false, -1, "cron_job");
            return $ID;
        }
        $orders = query("SELECT * FROM orders WHERE stripeToken <> '' AND status = 0 GROUP BY restaurant_id", true, "CRON");
        foreach($orders as $order){
            echo '<TR>';
            echo '<TD>' . processorder($order["id"]) . '</TD>';
            echo '<TD>' . $order["restaurant_id"] . '</TD>';
            echo '</TR>';
        }

        $orders =
        vardump($orders);
    ?>
</TABLE>
