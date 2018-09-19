<?php
    startfile("popups_menu");
    if (!function_exists("getsize")) {
        //gets the size of the pizza
        function getsize($itemname, &$isfree){
            $currentsize = "";
            foreach ($isfree as $size => $cost) {
                if (!is_array($cost)) {
                    if (textcontains($itemname, $size) && strlen($size) > strlen($currentsize)) {
                        $currentsize = $size;
                    }
                }
            }
            return $currentsize;
        }

        //process addons, generating the option group dropdown HTML, enumerating free toppings and qualifiers
        function getaddons($Table, &$isfree, &$qualifiers, &$addons, &$groups){
            $toppings = Query("SELECT * FROM " . $Table . " WHERE enabled = '1' ORDER BY id asc, type ASC, name ASC", true, "popups_menu.getaddons");
            $toppings_display = '';
            $currentsection = "";
            $isfree[$Table] = array();
            foreach ($toppings as $ID => $topping) {
                if ($currentsection != $topping["type"]) {
                    if ($toppings_display) {
                        $toppings_display .= '</optgroup>';
                    }
                    $toppings_display .= '<optgroup label="' . $topping["type"] . '">';
                    $currentsection = $topping["type"];
                }
                $addons[$Table][$topping["type"]][] = explodetrim($topping["name"]);
                $addons[$Table . "_id"][$topping["id"]] = $topping["name"];
                $topping["displayname"] = $topping["name"];
                if ($topping["isfree"]) {
                    $isfree[$Table][] = $topping["name"];
                    $topping["displayname"] .= " (free)";
                }
                if ($topping["qualifiers"]) {
                    $qualifiers[$Table][$topping["name"]] = explodetrim($topping["qualifiers"]);
                }
                if ($topping["isall"]) {
                    $isfree["isall"][$Table][] = $topping["name"];
                }
                if ($topping["groupid"] > 0) {
                    $groups[$Table][$topping["name"]] = $topping["groupid"];
                }
                $toppings_display .= '<option value="' . $topping["id"] . '" type="' . $topping["type"] . '">' . $topping["displayname"] . '</option>';
            }
            return $toppings_display . '</optgroup>';
        }

        //same as explode, but makes sure each cell is trimmed
        function explodetrim($text, $delimiter = ",", $dotrim = true){
            if (is_array($text)) {
                return $text;
            }
            $text = explode($delimiter, $text);
            if (!$dotrim) {
                return $text;
            }
            foreach ($text as $ID => $Word) {
                $text[$ID] = trim($Word);
            }
            return $text;
        }

        //converts a string to a class name (lowercase, replace spaces with underscores)
        function toclass($text){
            $text = strtolower(str_replace([" ", "/"], "_", trim($text)));
            return $text;
        }

        function endwith($Text, $WithWhat){
            return strtolower(right($Text, strlen($WithWhat))) == strtolower($WithWhat);
        }

        function maketip($tip, $cols = false){
            if($cols) {echo '<DIV CLASS="col-md-4">';}
            echo '<button class="cursor-pointer list-group-item list-group-item-action hoveritem d-flex justify-content-start item_tip" ONCLICK="addtip(' . $tip . ');">';
            echo '<i class="fas fa-dollar-sign rounded-circle align-middle item-icon bg-warning sprite-tip sprite-medium"></i>
                <span class="align-middle item-name">Tip</span>
                <span class="ml-auto align-middle btn-sm-padding item-cost"> $' . $tip . '</span>
            </button>';
            if($cols) {echo '</DIV>';}
        }

        function makecategory(&$ID, $Name){
            echo '<div class="' . textcolor . ' strong list-group-item" ID="category_' . $ID . '" catname="' . toclass($Name) . '">
<h2 CLASS="pull-left align-middle h2-middle">' . $Name . '</h2><span class="align-middle hidden item-icon rounded-circle sprite sprite-drinks sprite-crush-orange sprite-medium"></span></div>';
            $ID +=1;
        }

        function geticon($fontawesomeicons, $category, $color = false){
            foreach($fontawesomeicons as $icon => $categories){
                if(in_array($category, $categories["categories"])){
                    if($color){return $categories["color"];}
                    return '<i class="fa-5x caticon ' . $icon . '"></i>';
                }
            }
        }
    }

    $CSS = iif(database == "canbii", "canbii", "sprite");

    $qualifiers = array("DEFAULT" => array("1/2", "1x", "2x", "3x"));
    $categories = Query("SELECT * FROM menu WHERE enabled = '1' GROUP BY category ORDER BY id", true, "popups_menu.categories");
    $isfree = collapsearray(Query("SELECT * FROM additional_toppings", true, "popups_menu.isfree"), "price", "size");
    foreach($GLOBALS["settings"] as $key => $value){
        if(is_numeric($value)){$isfree[$key] = $value;}
    }

    $deliveryfee = $isfree["Delivery"];
    $minimum = $isfree["Minimum"];
    $addons = array();
    $classlist = array();
    $groups = array();
    $toppings_display = getaddons("toppings", $isfree, $qualifiers, $addons, $groups);
    $wings_display = getaddons("wings_sauce", $isfree, $qualifiers, $addons, $groups);
    $notaxes = flattenarray(Query("SELECT id FROM menu WHERE hastax = '0' AND enabled = '1' ORDER BY id asc", true, "popups_menu.getaddons"), "id");

    $tables = array("toppings", "wings_sauce");
    $totalmenuitems = countSQL("menu");
    $maxmenuitemspercol = $totalmenuitems / 3; //17
    $itemsInCol = 0;
    $CurrentCol = 1;
    $CurrentCat = 0;

    $newcolumns = ["dips", "sides", "1oz_indica", "indica_basic", "sativa_premium"];//which categories to start a new column on
    $fontawesomeicons = [
        "far fa-moon"   => ["color" => "bg-dark-blue",  "categories" => ["1oz_indica", "1g_indica", "1_4_indica", "1_2_indica"]],
        "far fa-sun"    => ["color" => "bg-light-blue", "categories" => ["sativa_basic", "sativa_high_quality", "sativa_premium"]],
        "fas fa-cloud"  => ["color" => "bg-green",      "categories" => ["hybrid_basic", "hybrid_high_quality", "hybrid_premium"]]
    ];
    echo '<!-- menu cache generated at: ' . my_now() . ' --> ';
?>
<DIV CLASS="col-md-9">
    <DIV CLASS="row no-margin">
        <div class="col-lg-4 col-md-12 bg-white ismenu">
            @foreach ($categories as $category)
                <?php
                    $toppings_extra = '+';
                    $catclass = toclass($category['category']);
                    $classlist[] = $catclass;
                    $menuitems = Query("SELECT * FROM menu WHERE category = '" . $category['category'] . "' order by id", true, "popups_menu.foreach");
                    $menuitemcount = count($menuitems);
                    if ($itemsInCol + $menuitemcount > $maxmenuitemspercol && $CurrentCol < 3) {
                        $itemsInCol = 0;
                        $CurrentCol += 1;
                    }
                    $itemsInCol += $menuitemcount;
                    makecategory($CurrentCat, $category['category']);
                ?>
                @foreach ($menuitems as $menuitem)
                    <button class="cursor-pointer list-group-item list-group-item-action hoveritem d-flex justify-content-start item_{{ $catclass }}"
                         itemid="{{$menuitem["id"]}}"
                         itemname="{{trim($menuitem['item'])}}"
                         itemprice="{{$menuitem['price']}}"
                         itemsize="{{getsize($menuitem['item'], $isfree)}}"
                         itemcat="{{$menuitem['category']}}"
                         calories="{{$menuitem['calories']}}"
                         allergens="{{$menuitem['allergens']}}"
                         <?php
                            $itemclass = $catclass;
                            if ($itemclass == "sides") {
                                $itemclass = str_replace("_", "-", toclass($menuitem['item']));
                                if (endwith($itemclass, "lasagna")) {
                                    $itemclass = "lasagna";
                                } else if (endwith($itemclass, "chicken-nuggets")) {
                                    $itemclass = "chicken-nuggets";
                                } else if (endwith($itemclass, "salad")) {
                                    $itemclass = "salad";
                                } else if ($itemclass == "panzerotti") {
                                    $icon = $toppings_extra;
                                }
                            } else if ($itemclass == "pizza") {
                                if (left($menuitem['item'], 1) == "2") {
                                    $itemclass = "241_pizza";
                                }
                                $icon = $toppings_extra;
                            }
                            $itemclass .= " " . $CSS . "-" . str_replace(".", "", str_replace("_", "-", toclass($menuitem['item'])));

                            $total = 0;
                            foreach ($tables as $table) {
                                echo $table . '="' . $menuitem[$table] . '" ';
                                $total += $menuitem[$table];
                            }
                            if ($total) {
                                $HTML = ' data-toggle="modal" data-backdrop="static" data-target="#menumodal" onclick="loadmodal(this);"';
                            } else {
                                $HTML = ' onclick="additemtoorder(this, -1);"';
                                $icon = '';
                            }
                            echo $HTML;
                            ?>
                        >
                        <span class="align-middle item-icon rounded-circle bg-warning {{$CSS . " " . $CSS . "-" . $itemclass . " " . geticon($fontawesomeicons, $catclass, true)}} sprite-medium" style="max-height:100%">
                            <?php
                                if(database == "canbii"){
                                    echo '<img src="' . webroot("public/images/weed.jpg") . '" />';
                                }
                            ?>
                        </span>
                        <?php
                            if(database == "ai"){
                                echo '<span class="align-middle item-icon rounded-circle bg-warning ' . $CSS . " " . $CSS . "-" . $itemclass . " " . geticon($fontawesomeicons, $catclass, true);                                  echo ' sprite-medium">';
                                echo geticon($fontawesomeicons, $catclass);
                                echo '</span>';
                            }
                        ?>
                        <span class="align-middle item-name">{{ str_replace(array("[", "]"), "", $menuitem['item']) }} </span>
                        <span class="ml-auto align-middle btn-sm-padding item-cost"> ${{number_format($menuitem["price"], 0)}}<?= $icon; ?></span>
                    </button>
                @endforeach
                @if(in_array($catclass, $newcolumns))
                        </div>
                    <div class="col-lg-4 col-md-12 bg-white ismenu">
                @endif
            @endforeach
        </div>
    </DIV>
</DIV>


<!-- order menu item Modal -->
<div class="modal modal-fullscreen force-fullscreen" id="menumodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="dont-show" id="modal-hiddendata">
                <SPAN ID="modal-itemprice"></SPAN>
                <SPAN ID="modal-itemid"></SPAN>
                <SPAN ID="modal-itemsize"></SPAN>
                <SPAN ID="modal-itemcat"></SPAN>
            </div>

            <div class="list-group-item toppinglistitem">
                <h2 class="text-normal" id="myModalLabel">
                    <SPAN ID="modal-itemname"></SPAN><br>
                    <small ID="toppingcost" class="nowrap">$<SPAN id="modal-toppingcost"></SPAN> per topping</small>
                </h2>
                <button data-dismiss="modal" class="btn align-middle ml-auto bg-transparent"><i class="fa fa-times"></i></button>
            </div>

            <div class="modal-body menumodal">
                <DIV ID="addonlist" class="addonlist"></DIV>
            </div>
        </div>
    </div>
</div>

<script>
    var tables = <?= json_encode($tables); ?>;
    var alladdons = <?= json_encode($addons); ?>;
    var freetoppings = <?= json_encode($isfree); ?>;
    var qualifiers = <?= json_encode($qualifiers); ?>;
    var groups = <?= json_encode($groups); ?>;
    var theorder = new Array;
    var deliveryfee = <?= $deliveryfee; ?>;
    var minimumfee = <?= $minimum; ?>;
    var classlist = <?= json_encode($classlist); ?>;
    var ordinals = ["1st", "2nd", "3rd", "4th", "5th", "6th", "7th", "8th", "9th", "10th"];
    var notaxes = <?= json_encode($notaxes); ?>;

    $(".hoveritem").hover(
        function(e){
            var calories = $(this).attr("calories");
            var allergens = $(this).attr("allergens");
            if(calories || allergens) {
                var position = $(this).position();
                position.left = $(this).offset().left - $("#category_0").offset().left;
                var height = $(this).outerHeight();
                var bottom = position.top + height;
                var tooltipheight = height * 2 - 1;
                var onecolumnwidth = $(this).outerWidth();
                var twocolumnwidth = onecolumnwidth * 2 - 2;
                var containerwidth = $(".container-fluid").width();

                if(containerwidth < onecolumnwidth){//rare mobile device with hover ability
                    position.top = position.top + $(this).parent().position().top;
                    bottom = position.top + height;
                    twocolumnwidth = onecolumnwidth - 1;
                } else if (position.left > 400) {//last col
                    position.left = position.left - onecolumnwidth;
                }
                if (isbelowhalf(bottom)) {//below middle of screen
                    bottom = position.top - tooltipheight - 1;
                }
                $("#nutritiontooltip").css({
                    position: "absolute",
                    left: position.left,
                    top: bottom,
                    width: twocolumnwidth,
                    height: tooltipheight
                }).stop().show(100);
                var HTML = "";
                if(calories){
                    HTML += "Calories: " + calories;
                }
                if(allergens){
                    allergens = allergens.split(",");
                    for(var i=0;i<allergens.length;i++){
                        var allergen = allergens[i];
                        var quantity = false;
                        var indexOf = allergen.indexOf("=");
                        if(indexOf > -1){
                            quantity = allergen.right( allergen.length - indexOf - 1);
                            allergen = allergen.left(indexOf);
                        }
                        if(HTML){HTML += ", ";}
                        HTML += ucfirst(allergen);
                        if(quantity) {
                            HTML += ": " + quantity;
                        }
                    }
                }
                $("#nutritioninfo").html(HTML);
                visible("#nutritionnote", $(this).attr("calories"));
            }
        },
        function(e){
            $("#nutritiontooltip").hide();
        }
    );

    function isbelowhalf(Y){
        return Y - $(window).scrollTop() > $( window ).height() * 0.5;
    }
</script>

<DIV ID="nutritiontooltip" class="custom-tooltip">
    <DIV ID="nutritioninfo"></DIV>
    <SPAN CLASS="nutritionnote">2,000 calories a day is used for general nutrition advice, but calorie needs vary</SPAN>
</DIV>
<!-- end order menu item Modal -->
<!-- end menu cache -->
<?php endfile("popups_menu"); ?>