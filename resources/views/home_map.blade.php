@extends('layouts_app')
@section('content')
    <?php
        $markers = array();
        if(read("profiletype") == 1){
            $restaurants = Query("SELECT * FROM restaurants WHERE address_id > 0", true, "home_map");
            $addressIDs = array();
            foreach($restaurants as $restaurant){
                if(!in_array($restaurant["address_id"], $addressIDs)){
                    $addressIDs[] = $restaurant["address_id"];
                }
            }
            $addressIDs = implode(",", $addressIDs);
            $addresses = Query("SELECT * FROM useraddresses WHERE id IN (" . $addressIDs . ")", true, "home_map");
            $hours = Query("SELECT * FROM hours WHERE restaurant_id IN (0," . $addressIDs . ")", true, "home_map");

            function findwhere($array, $key, $value, $retdata = false){
                foreach($array as $index => $data){
                    if($data[$key] == $value){
                        if($retdata){return $data;}
                        return $index;
                    }
                }
                return false;
            }

            function restaurantdata($restaurant, $includeName = true, $newline = '<BR>'){
                $HTML = iif($includeName, $restaurant["name"]);
                if (strlen(filternonnumeric($restaurant["email"])) == 10 && !textcontains($restaurant["email"], "@")){
                    $restaurant["phone"] = $restaurant["email"];
                    $restaurant["email"] = "";
                }
                if($restaurant["email"]){$HTML .= $newline . "Email: " . $restaurant["email"];}
                if($restaurant["phone"]){$HTML .= $newline . "Phone: " . formatphone($restaurant["phone"]);}
                $HTML .= $newline . "Address: " . addressdata($restaurant["address"]);
                $HTML .= $newline . "Hours: " . hourdata($restaurant["hours"]);
                $HTML .= $newline . "Is live: " . iif($restaurant["is_delivery"], "Yes", "No");
                if(startswith($HTML, $newline)){$HTML = right($HTML, strlen($HTML) - strlen($newline));}
                return $HTML;
            }

            function addressdata($address){
                if($address["number"] == 0){
                    return $address["street"];
                } else {
                    return $address["number"] . " " . iif($address["unit"], " (Unit" . $address["unit"] . ")") . $address["street"] . ", " . $address["city"] . ", " . $address["province"] . " " . $address["postalcode"];
                }
            }

            function hourdata($hours){
                $dayofweek = date("w");
                $time = date("Gi");
                $today_open = $hours[$dayofweek . "_open"];
                $today_close = $hours[$dayofweek . "_close"];
                $dayofweek = $dayofweek - 1;
                if($dayofweek == -1){$dayofweek = 6;}
                $yesterday_open = $hours[$dayofweek . "_open"];
                $yesterday_close = $hours[$dayofweek . "_close"];
                if($today_close > $today_open && $today_open < $time && $today_close > $time ){
                    return "Open: " . GenerateTime($today_open) . " - Close: " . GenerateTime($today_close);
                } else if ( $today_close < $today_open && $today_open < $time) {
                    return "Open: " . GenerateTime($today_open) . " - Close: " . GenerateTime($today_close);
                } else if ( $yesterday_close < $yesterday_open && $yesterday_close > $time){
                    return "Open: " . GenerateTime($yesterday_open) . " - Close: " . GenerateTime($yesterday_close);
                } else if ( $time > $today_close && $today_close > 0 ) {
                    return "Closed at: " . GenerateTime($today_close);
                }
                return "Closed";
            }

            echo '<DIV CLASS="row"><DIV CLASS="col-md-2">Restaurants:';
            foreach($restaurants as $index => $restaurant){
                $address = findwhere($addresses, "id", $restaurant["address_id"], true);
                $hoursID = findwhere($hours, "restaurant_id", $restaurant["id"]);
                if($hoursID == false){$hoursID = findwhere($hours, "restaurant_id", 0);}
                $restaurant["hours"] = $hours[$hoursID];
                $restaurant["address"] = $address;
                if($address !== false){//0                  1                            2                     3                      4
                    $markers[] = array($restaurant["name"], restaurantdata($restaurant), $address["latitude"], $address["longitude"], iif($restaurant["is_delivery"], "1", "0"));
                    echo '<BR><A CLASS="rest-' . iif($restaurant["is_delivery"], "live", "dead") . '" lat="' . $address["latitude"] . '" long="' . $address["longitude"] . '" HREF="#" ONCLICK="return clickrest(this);" marker="' . (count($markers)-1) . '" TITLE="' . restaurantdata($restaurant, false, ' - ') . '">' . $restaurant["name"] . '</A>';
                }
            }

            echo '</DIV><DIV CLASS="col-md-10">';
            echo view("popups_googlemaps");
            echo '</DIV></DIV>';
        } else {
            echo view("popups_accessdenied");
        }

        $dead_color = "#FF0000";
        $live_color = "#006400";
    ?>
    <STYLE>
        .rest-dead{
            color: <?= $dead_color; ?>;
        }
        .rest-live{
            color: <?= $live_color; ?>;
        }
    </STYLE>
    <SCRIPT>
        var locations = <?= json_encode($markers); ?>;
        var islive_icon, isntlive_icon;

        function getIcon(pinColor, google) {
            var data = new google.maps.MarkerImage("http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|" + pinColor.replace("#", ''),
                    new google.maps.Size(21, 34), new google.maps.Point(0,0), new google.maps.Point(10, 34));
            data.labelOrigin = {x: 10, y: -10};
            return data;
        }

        function iconcolor(islive){
            if(islive == 1){return islive_icon;}
            return isntlive_icon;
        }

        function getColor(islive){
            if(islive == 1){return "<?= $live_color; ?>";}
            return "<?= $dead_color; ?>";
        }

        $(window).load(function () {
            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 10,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            });

            islive_icon = getIcon(getColor(1), google);
            isntlive_icon = getIcon(getColor(0), google);
            var infowindow = new google.maps.InfoWindow();

            var marker, i;
            var bounds = new google.maps.LatLngBounds();
            for (i = 0; i < locations.length; i++) {
                marker = new google.maps.Marker({
                    label: {
                        text: locations[i][0],
                        fontSize: "16px",
                        fontWeight: "bold",
                        color: getColor(locations[i][4]),
                        strokeColor: "black",
                        strokeWeight: 1
                    },
                    position: new google.maps.LatLng(locations[i][2], locations[i][3]),
                    map: map,
                    icon: iconcolor(locations[i][4])
                });
                locations[i][4] = marker;
                bounds.extend(marker.getPosition());

                google.maps.event.addListener(marker, 'click', (function(marker, i) {
                    return function() {
                        infowindow.setContent(locations[i][1]);
                        infowindow.open(map, marker);
                    }
                })(marker, i));
            }
            map.fitBounds(bounds);
        });

        function clickrest(element){
            new google.maps.event.trigger( locations[element.getAttribute("marker")][4], 'click' );
            return false;
        }
    </SCRIPT>
@endsection