@if($style==1)
    <TABLE cellspacing="0" cellpadding="0" <?= inline("table table-sm"); ?>>
        <TR>
            <TH>#</TH>
            <TH>Item</TH>
            <TH align="right"> Sub-total</TH>
            <TH>Addons</TH>
            @if($debugmode)
                <TH TITLE="<?= $onlydebug; ?>">Count</TH>
            @endif
            <th align="right">Price</th>
        </TR>
@else
    <TABLE WIDTH="100%" class="mb-2" style="border-collapse: collapse; border: none !important;">
@endif

<?php
    startfile("popups_receiptdata");
    $Bold = '<SPAN style="font-weight: bold;">';
    $ordinals = array("1st", "2nd", "3rd", "4th", "5th", "6th", "7th", "8th", "9th", "10th");
    $integrity = true;
    if (!function_exists("findkey")) {
        function findkey($arr, $key, $value){
            return array_search($value, array_column($arr, $key));
        }

        //finds the size of the item
        function getsize($itemname, $isfree){
            $currentsize = "";
            foreach ($isfree as $value) {
                $size = $value["size"];
                $cost = $value["price"];
                if (!is_array($cost)) {
                    if (textcontains($itemname, $size) && strlen($size) > strlen($currentsize)) {
                        $currentsize = $size;
                    }
                }
            }
            return $currentsize;
        }

        function textcontains999($text, $searchfor){
            return strpos(strtolower($text), strtolower($searchfor)) !== false;
        }

        function hasaddons($menuitem, $tables){
            foreach ($tables as $table => $value) {
                if (isset($menuitem[$table])) {
                    if ($menuitem[$table]) {
                        return true;
                    }
                }
            }
            return false;
        }

        function checkforclones(&$items, $OriginalID, $OriginalItem, $menuitem){
            $Quantity = 1;
            foreach ($items as $ID => $item) {
                if ($ID != $OriginalID && $item->itemid == $OriginalItem->itemid) {
                    $Quantity += 1;
                    $items[$ID]->clone = true;
                }
            }
            return $Quantity;
        }

        function iconexists($imagefile){
            return file_exists(public_path() . '/images/icon-' . $imagefile . ".png");
        }

        function showifabove1($quantity, $text = ""){
            if ($quantity > 1) {
                return $quantity . $text;
            }
        }

        function counttoppings(&$toppings, $toppingtocount){
            $count = 0;
            foreach ($toppings as $topping) {
                if (isset($topping->id)) {//search by id
                    if($toppingtocount->id == $topping->id){
                        $count++;
                        $topping->counted = true;
                    }
                } else if($toppingtocount->text == $topping->text){
                    $count++;
                    $topping->counted = true;
                }
            }
            if($count>1){
                return $count . "x ";
            }
        }

        function hastax($item, $notaxlist){
            return !in_array($item, $notaxlist);
        }
    }

    //check all data again, do not trust the prices from the user!!
    $tables = array("toppings", "wings_sauce", "additional_toppings");
    foreach ($tables as $ID => $table) {
        $tables[$table] = Query("SELECT * FROM " . $table, true, "popups_receipt.foreach");
        unset($tables[$ID]);
    }

    $deliveryfee = findkey($tables["additional_toppings"], "size", "Delivery");
    $deliveryfee = $tables["additional_toppings"][$deliveryfee]["price"];
    foreach($tables["additional_toppings"] as $key => $value){
        if (startswith($value["size"], "over$")){
            $GLOBALS["discounts"][$value["size"]] = $value["price"];
        }
    }
    $notaxlist = flattenarray(Query("SELECT id FROM menu WHERE hastax = '0' AND enabled = '1' ORDER BY id asc", true, "popups_receiptdata"), "id");
    $filefound = true;

    if(is_string($filename)){
        if (file_exists($filename)) {
            $filename = file_get_contents($filename);
            try{
                $items = json_decode($filename);
            } catch (exception $e) {
                echo 'Caught exception: ', $e->getMessage() . " on line " . $e->getLine() . "<BR>" . $filename;
                $filefound = false;
            }
        } else {
            echo '<TR><TD COLSPAN="' . $colspan . '" ALIGN="CENTER"><B TITLE="' . $filename . '">ORDER ' . $orderid . ' NOT FOUND</B></TD></TR>';
            $filefound = false;
        }
    } else {//data format no longer matches, attempt to convert
        $items = [];
        foreach ($filename as $index => $item) {
            if(isset($item["itemaddons"])){
                foreach($item["itemaddons"] as $itemindex => $addon){
                    $item["itemaddons"][$itemindex] = (object) $addon;
                    if(isset($item["itemaddons"][$itemindex]->addons)){
                        $addons = [];
                        foreach($item["itemaddons"][$itemindex]->addons as $addon){
                            $addons[] = (object) $addon;
                        }
                        $item["itemaddons"][$itemindex]->addons = $addons;
                    }
                }
            }
            $items[] = (object) $item;
        }
        //vardump($items); die();
    }

    if($filefound){
        $itemIDs = array();
        foreach ($items as $index => $item) {
            if (isset($item->itemid)) {
                $itemIDs[] = $item->itemid;
            }
        }
        $itemIDs = implode(",", array_unique($itemIDs));
        if (!$itemIDs) {
            vardump($items);
            die("Order is empty");
        }

        $menu = Query("SELECT * FROM menu WHERE id IN(" . $itemIDs . ")", true, "popups_receipt.menu");
        $localdir = webroot("public/images/icon-");
        if ($place == "email" && !islive()) {
            $localdir = "http://" . serverurl . "/public/images/icon-";
        }

        //convert the JSON into an HTML receipt, using only item/addon IDs, reobtaining cost/names from the database for security
        $subtotal = 0;
        $subtotal_notax = 0;

        if(!is_array($items)){
            $items =  (array) $items;
        }
        foreach ($items as $ID => $item) {
            unset($items[$ID]->clone);
        }
        foreach ($items as $ID => $item) {
            $quantity = 1;
            $menukey = findkey($menu, "id", $item->itemid);
            if (!isset($item->clone)) {
                $menuitem = $menu[$menukey];
                if (!hasaddons($menuitem, $tables)) {
                    $quantity = checkforclones($items, $ID, $item, $menuitem);
                }
                $size = getsize($menuitem["item"], $tables["additional_toppings"]);
                $addonscost = "0.00";
                if ($size) {
                    $addonscost = findkey($tables["additional_toppings"], "size", $size);
                    $addonscost = $tables["additional_toppings"][$addonscost]["price"];
                }
                $itemtotal = $menuitem["price"];
                $paidtoppings = 0;
                $freetoppings = 0;

                $totaladdons = 0;
                foreach ($tables as $name => $data) {
                    if (isset($menuitem[$name])) {
                        $totaladdons += $menuitem[$name];
                    }
                }

                $itemname = str_replace(array("[", "]"), "", $item->itemname);
                $units = "";
                if($quantity > 1){
                    //if(isset($item->units)){
                    //    $units = " (" . $item->units . ")";
                    //} else if (textcontains($item->itemname, "[") && textcontains($item->itemname, "]")) {
                        $units = getbetween($item->itemname, "[", "]");
                        if(textcontains($units, "/")){
                            $top = getbetween("[" . $units, "[", "/");
                            $bottom = getbetween($units . "]", "/", "]");
                            $unit = filternumeric($bottom);
                            $bottom = filternonnumeric($bottom);
                            $top = $top * $quantity;
                            $whole = floor( $top / $bottom );
                            $top = $top % $bottom;
                            if($whole == 0){$whole = "";}
                            if($top == 0){
                                $value = $whole;
                            } else {
                                $value = $whole . '<SUP>' . $top . '</SUP>/<SUB>' . $bottom . '</SUB>';
                            }
                        } else {
                            $value = filternonnumeric($units) * $quantity;
                            $unit = filternumeric($units);
                        }
                        $units = " (" . $value . $unit . ")";
                    //}
                }

                switch ($style) {
                    case 1:
                        if ($debugmode) {
                            $debug = ' TITLE="' . $onlydebug . var_export($item, true) . '"';
                        }
                        echo '<TR><TD>' . showifabove1($quantity) . '</TD><TD' . $debug . '>' . $itemname . $units . '</TD>';
                        if ($debugmode) {
                            $debug = ' TITLE="' . $onlydebug . print_r($menuitem, true) . '"';
                        }
                        echo '<TD ALIGN="RIGHT"' . $debug . '>$' . number_format($menuitem["price"], 2) . '</TD><TD>';
                        break;
                    case 2:
                        $imagefile = str_replace(" ", "-", strtolower($menuitem["category"]));
                        if (right($imagefile, 5) == "pizza" || !iconexists($imagefile)) {
                            $imagefile = str_replace(" ", "-", strtolower($itemname));
                            if (!iconexists($imagefile)) {
                                $imagefile = "pizza";

                                if (strtolower(right(trim($itemname), 5)) == "salad") {
                                    $imagefile = "salad";
                                }
                            }
                        }
                        $colspan = $colspan - 2;
                        echo '<TR><TD valign="middle">' . $Bold . showifabove1($quantity, 'x&nbsp;') . $itemname . $units . '</SPAN></TD><TD ALIGN="RIGHT" WIDTH="5%">';
                        break;
                }

                $HTML = "";
                if (isset($item->itemaddons)) {
                    if ($style == 1) {
                        $HTML = '<TABLE style="border:1px solid #eceeef;!important;" WIDTH="100%">';
                    }
                    $addoncount = count($item->itemaddons);
                    foreach ($item->itemaddons as $addonID => $addon) {
                        $toppings = array();
                        $none = "UNKNOWN NONE (notable)";
                        $itemtype = "UNKNOWN ITEMTYPE (notable)";
                        if (isset($addon->tablename)) {
                            $tablename = $addon->tablename;
                            $none = "UNKNOWN NONE (" . $tablename . ")";
                            $itemtype = "UNKNOWN ITEMTYPE (" . $tablename . ")";
                            switch ($tablename) {
                                case "toppings":
                                    $itemtype = "Pizza";
                                    $none = "No Toppings";
                                    break;
                                case "wings_sauce":
                                    $itemtype = "lb";
                                    $none = "No Sauce";
                                    break;
                            }
                            if (isset($addon->addons)) {
                                $toppings = $addon->addons;
                            }
                        }
                        $newtoppings = array();
                        foreach ($toppings as $topping) {
                            if (isset($topping->id)) {//search by id
                                $id = $topping->id;
                                $toppingkey = findkey($tables[$tablename], "id", $topping->id);
                            } else {//search by name
                                $toppingkey = findkey($tables[$tablename], "name", $topping->text);
                            }
                            $counted = isset($topping->counted);
                            $count = counttoppings($toppings, $topping);
                            $topping = $tables[$tablename][$toppingkey];
                            if ($topping["isfree"]) {
                                $freetoppings++;
                                $topping["name"] = '<I>' . $topping["name"] . '</I>';
                            } else {
                                $paidtoppings++;
                            }
                            if ($debugmode) {
                                $debug = ' TITLE="' . $onlydebug . print_r($topping, true) . '"';
                            }
                            if(!$counted){
                                $newtoppings[] = '<SPAN' . $debug . '>' . $count . $topping["name"] . '</SPAN>';
                            }
                        }
                        if (!$newtoppings) {
                            $newtoppings[] = $none;
                        }

                        if ($style == 1) {
                            $itemtitle = $itemtype . ' #' . ($addonID + 1);
                            $HTML .= '<TR><TH NOWRAP>' . $itemtitle . '</TH></TR><TR><TD>' . implode(", ", $newtoppings) . '</TD></TR>';
                        } else {
                            $itemtitle = "";
                            if ($addoncount > 1) {
                                $itemtitle = $ordinals[$addonID] . " " . $itemtype . ": ";
                            }
                            $HTML .= $itemtitle . implode(", ", $newtoppings) . "<BR>";
                        }
                    }
                    if ($style == 1) {
                        echo $HTML . '</TABLE>';
                    }
                }

                $toppingscost = $addonscost * $paidtoppings;
                $itemtotal = ($menuitem["price"] + $toppingscost) * $quantity;

                if ($style == 1) {
                    echo '</TD>';
                    if ($debugmode) {
                        echo '<TD NOWRAP>';
                        if ($totaladdons) {
                            echo $paidtoppings . ' paid<BR>' . $freetoppings . ' free';
                            echo '<BR>$' . number_format($addonscost, 2) . '<BR>each';//'<BR>' . $size .
                        }
                        if ($debugmode) {
                            $debug = ' TITLE="' . $onlydebug . 'User side: $' . $item->itemprice . '"';
                        }
                        echo '</TD>';
                    }
                    echo '<TD ALIGN="RIGHT"' . $debug . '>';
                }
                echo '$' . number_format($itemtotal, 2) . '</TD></TR>';
                if ($style == 2 && $HTML) {
                    echo '<TR><TD COLSPAN="' . $colspan . '">' . $HTML . '</TD></TR>';
                }

                if(hastax($item->itemid, $notaxlist)){
                    $subtotal += $itemtotal;
                } else {
                    $subtotal_notax += $itemtotal;
                }
            }

        }

        $tax_percent = 0.13;
        $colspanminus1 = $colspan - 1;
        echo '<TR><TD COLSPAN="' . $colspanminus1 . '" ALIGN="RIGHT">Sub-total &nbsp;</TD><TD ALIGN="RIGHT"> $' . number_format($subtotal + $subtotal_notax, 2) . '</TD></TR>';
        $discountpercent = getdiscount($subtotal);
        if($discountpercent > 0){
            $discount = number_format($discountpercent * 0.01 * $subtotal, 2);
            echo '<TR><TD COLSPAN="' . $colspanminus1 . '" ALIGN="RIGHT">Discount (' . $discountpercent . '%) &nbsp;</TD><TD ALIGN="RIGHT"> $' . $discount . '</TD></TR>';
            $subtotal = $subtotal - $discount;
        }
        $tax = ($subtotal + $deliveryfee) * $tax_percent;
        $total = $subtotal + $subtotal_notax + $deliveryfee + $tax;
        if($deliveryfee>0){echo '<TR><TD COLSPAN="' . $colspanminus1 . '" ALIGN="RIGHT">Delivery &nbsp;</TD><TD ALIGN="RIGHT"> $' . number_format($deliveryfee, 2) . '</TD></TR>';}
        echo '<TR><TD COLSPAN="' . $colspanminus1 . '" ALIGN="RIGHT">Tax &nbsp;</TD><TD ALIGN="RIGHT"> $' . number_format($tax, 2) . '</TD></TR>';
        if($Order["tip"] > 0){
            echo '<TR><TD COLSPAN="' . $colspanminus1 . '" ALIGN="RIGHT">Tip &nbsp;</TD><TD ALIGN="RIGHT"> $' . number_format($Order["tip"], 2) . '</TD></TR>';
            $total += $Order["tip"];
        }
        echo '<TR><TD COLSPAN="' . $colspanminus1 . '" ALIGN="RIGHT">' . $Bold . 'Total &nbsp;</SPAN></TD><TD ALIGN="RIGHT">' . $Bold . ' $';
        echo '<SPAN ID="total">' . number_format($total, 2) . '</SPAN></TD></TR>';

        echo '<TR><TD COLSPAN="' . abs($colspan) . '"  ALIGN="RIGHT">(LAST4)</TD></TR>';
        if($orderid) {insertdb("orders", array("id" => $orderid, "price" => $total));}//saved for stripe

        if ($Order["cookingnotes"]){
            echo '<TR><TD COLSPAN="' . 2 . '"><h2>Notes </h2>' . $Order["cookingnotes"] . '</TD></TR>';
        }
    }
    echo '</TABLE>';
    endfile("popups_receiptdata");
?>