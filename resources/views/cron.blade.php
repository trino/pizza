CRON!

<TABLE>
    <?php
        function processorder($ID){
            App::make('App\Http\Controllers\HomeController')->order_placed($ID);
            return $ID;
        }
        $orders = query("SELECT * FROM orders WHERE stripeToken <> '' AND status = 0", true, "CRON");
        foreach($orders as $order){
            echo '<TR>';
            echo '<TD>' . processorder($order["id"]) . '</TD>';
            echo '</TR>';
        }
    ?>
</TABLE>
