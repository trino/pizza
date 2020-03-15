<?php
    function startswith($text, $test){
        return left($text, strlen($test)) == $test;
    }

    function left($text, $length){
        return substr($text, 0, $length);
    }

    function right($text, $length){
        return substr($text, -$length);
    }

    function mid($text, $start, $length){
        return substr($text, $start, $length);
    }

    function getbetween($text, $start, $end = false){
        $startpos = strpos($text, $start);
        if($startpos === false){return false;}
        $text = right($text, strlen($text) - ($startpos + strlen($start)));
        if($end === false){return $text;}
        $startpos = strpos($text, $end);
        if($startpos === false){return false;}
        $text = left($text, $startpos);
        return $text;
    }

    function clean($string){
        $string = str_replace(PHP_EOL, '', $string);
        $string = preg_replace('/[\s]+/mu', ' ', $string);
        return trim($string);
    }

    function getwebsite($url, $mode = false){
        $website = getbetween($url, "://", "/");
        if($mode){
            $tempstr = strpos($website, "/");
            if($tempstr !== false){
                $website = left($url, $tempstr);
            }
            return $website;
        }
        $tempstr = getbetween($website, ".", ".");
        if($tempstr !== false){
            return $tempstr;
        }
        return $website;
    }

    if(isset($_GET["url"]) && isset($_GET["action"])){
        $url = $_GET["url"];
        $website = getwebsite($url);
        $HTML = false;
        switch($website){
            case "ubereats":
                echo $website . ' blocks downloads';
                break;
            default:
                downloadstate($url);
                $HTML = file_get_contents($url);
                downloadstate();
        }
        if($HTML) {
            switch ($_GET["action"]) {
                case "storelist":
                    $stores = enumstores($url, $HTML);
                    echo '<TITLE>Harvesting: ' . $website . '</TITLE>';
                    foreach ($stores as $store) {
                        if(needsstore($website, $store)) {
                            $store["menu"] = enummenu($store["url"], $website);
                            processstore($website, $store);
                            if (!isset($_GET["all"])) {
                                vardump($store);
                                footer();
                            }
                        } else {
                            echo '<DIV CLASS="skipped">' . $website . ": " . $store["name"] . " is already saved to the HTML file. Skipping</DIV>";
                        }
                    }
                    break;
            }
            echo '<H2>' . $_GET["action"] . " - " . $website . '</H2>' . printhtml($HTML);
        }
    }

    function harvesterfile(){
        return getcwd() . "/harvester.html";
    }

    function iniharvesterhtml(){
        appendtofile('<link rel="stylesheet" href="harvester.css">');
    }

    function processfile(){
        if(!isset($GLOBALS["harvester"])) {
            if (file_exists(harvesterfile())) {
                $HTML = file_get_contents(harvesterfile());
                if (strpos($HTML, "stylesheet") === false){iniharvesterhtml();}
                $HTML = explode('<TABLE BORDER="1">', $HTML);
                foreach($HTML as $store){
                    $website = getbetween($store, "<TR><TD>Source:</TD><TD>", '</TD></TR>');
                    $name = getbetween($store, "<TR><TD>Name:</TD><TD>", '</TD></TR>');
                    $GLOBALS["harvester"][$website . ":" . $name] = true;
                }
            } else {
                iniharvesterhtml();
            }
        }
    }

    function appendtofile($text){
        file_put_contents(harvesterfile(), $text, FILE_APPEND);
    }

    function needsstore($website, $store){
        processfile();
        if(!isset($GLOBALS["harvester"][$website . ":" . $store["name"]])){
            return true;
        }
        return false;
    }

    function processstore($website, $store){
        $GLOBALS["harvester"][$website . ":" . $store["name"]] = true;

        $HTML = '<TABLE BORDER="1"><THEAD>';
        $HTML .= '<TR><TD>Source:</TD><TD>' . $website . '</TD></TR>';
        $HTML .= '<TR><TD>Name:</TD><TD>' . $store["name"] . '</TD></TR>';
        $HTML .= '<TR><TD>URL:</TD><TD><A HREF="' . $store["url"] . '">' . $store["url"] . '</A></TD></TR>';
        $HTML .= '<TR><TD COLSPAN="2"><HR></TD></TR>';
        $HTML .= '</THEAD><TBODY>';
        foreach($store["menu"] as $item){
            $HTML .= '<TR><TD TITLE="' . $item['description'] . '">' . $item['title'] . '</TD><TD ALIGN="RIGHT">' . $item['price'] . '</TD></TR>';
        }
        $HTML .= '</TBODY></TABLE>';
        appendtofile($HTML);
    }

    function basehref($URL){
        return left($URL, stripos($URL, "/", stripos($URL, ".")));
    }

    function enumstores($URL, $HTML){
        $stores = [];
        $website = getwebsite($URL);
        $basehref = basehref($URL);
        $isAHREFs = false;

        switch($website){
            case "skipthedishes":
                $HTML = getbetween($HTML, '<div id="restaurant-list-container">');
                $isAHREFs = 'restaurant-list show-loading';
                break;
            case "just-eat":
                $HTML = getbetween($HTML, '<div class="listing-group listing-group--noSpacingTop card" data-test-id="listingGroupOpen">');
                $isAHREFs = "mediaElement listing-item-link";
                break;
        }

        if($isAHREFs){
            $tags = explode('<a', $HTML);
            foreach($tags as $tag){
                $class = getbetween($tag, 'class="', '"');
                if($class == $isAHREFs) {
                    $storename = "";
                    $address = "";
                    switch($website){
                        case "skipthedishes":
                            $storename = getbetween($tag, '<div class="truncated-name text-dark">', '</div>');
                            $address = getbetween($tag, '<meta itemprop="streetAddress" content="', '">');
                            break;
                        case "just-eat":
                            $storename = html_entity_decode(getbetween($tag, '<h3 class="listing-item-title" itemprop="name">', '</h3>'));
                            $address = getbetween($tag, '<p class="infoText" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">', '</p>');
                            break;
                    }
                    $tag = getbetween($tag, 'href="', '"');
                    if ($tag !== false) {
                        $stores[] = [
                            "url" => $basehref . $tag,
                            "name" => $storename,
                            "address" => $address
                        ];
                    }
                }
            }
        }
        return $stores;
    }

    function downloadstate($url = false){
        if($url){
            echo '<DIV ID="downloading"><IMG SRC="images/loader.gif"><BR>Downloading page</DIV>';
        } else {
            ?>
                <SCRIPT LANGUAGE="JAVASCRIPT">
                    var elem = document.getElementById("downloading");
                    elem.parentElement.removeChild(elem);
                </SCRIPT>
            <?php
        }
        ob_flush();
        ob_implicit_flush(1);
        flush();
    }

    function get_data($url, $cookie = false) {
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $header = [];// array('Content-type: application/x-www-form-urlencoded;charset=UTF-8');
        $header[] = "User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.167 Safari/537.36";
        if($cookie !== false){
            $header[] = "Cookie: " . $cookie;
        }
        //$header[] = "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8";
        //$header[] = "Accept-Encoding: gzip, deflate, br";
        //$header[] = "Accept-Language: en-US,en;q=0.9";
        //$header[] = "Cache-Control: max-age=0";
        //$header[] = "Connection: keep-alive";
        //$header[] = "Host: " . getwebsite($url, true);
        //$header[] = "Upgrade-Insecure-Requests: 1";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        downloadstate($url);
        $data = curl_exec($ch);
        downloadstate();
        curl_close($ch);
        if($data === false){
            die($url . " took too long to download");
        }
        return $data;
    }

    function enummenu($URL, $website = false){
        if($website === false){$website = getwebsite($URL);}
        $cookie = false;
       /* switch($website){
            case "ubereats":
                GLOBAL $ubereatscookie;
                $cookie = $ubereatscookie;
                break;
        }*/
        $HTML = get_data($URL, $cookie);//requires manual downloading
        $products = [];
        switch($website){
            case "skipthedishes":
                $HTML = getbetween($HTML, '<div class="restaurant-menu-list flex-block">');
                $HTML = explode('<div class="menu-item"', $HTML);
                foreach($HTML as $tag){
                    $product["price"] = clean(getbetween($tag, '<strong itemprop="price">', '</strong>'));
                    if(startswith($product["price"], "$")){
                        $product["title"] = clean(getbetween($tag, '<span itemprop="name">', '</span>'));
                        $product["description"] = clean(getbetween($tag, '<meta itemprop="description" content="', '">'));
                        $products[] = $product;
                    }
                }
                break;
            case "just-eat":
                $HTML = getbetween($HTML, '<div id="container-menu--card" class="menuCard-contents">');
                $HTML = explode('<div class="menu-product', $HTML);
                foreach($HTML as $tag){
                    $product["title"] = clean(getbetween($tag, '<h4 class="product-title">', '</h4>'));
                    $product["description"] = clean(getbetween($tag, '<div class="product-description">', '</div>'));
                    $product["price"] = false;
                    if(strpos($tag, "has-synonyms") !== false){
                        $tag = explode('<div class="product-synonym"', $tag);
                        foreach($tag as $tag2){
                            $product2["title"] = clean($product["title"] . " " . getbetween($tag2, '<div class="product-synonym-name">', '</div>'));
                            $product2["price"] = clean(getbetween($tag2, '<div class="product-price u-noWrap">', '</div>'));
                            if($product2["title"] && $product2["price"]) {
                                $products[] = $product2;
                            }
                        }
                    } else {
                        $product["price"] = clean(getbetween($tag, '<div class="product-price u-noWrap">', '</div>'));
                        if($product["title"] && $product["price"]) {
                            $products[] = $product;
                        }
                    }
                }
                break;
            default:
                printhtml($HTML, $website . " IS NOT SUPPORTED");
        }
        return $products;
    }

    function printhtml($HTML, $title = "", $iframe = false){
        echo '<TEXTAREA CLASS="html" TITLE="' . $title . '">' . htmlspecialchars($HTML) . '</TEXTAREA>';
        if ($iframe){
            echo '<DIV CLASS="html" TITLE="' . $title . ' HTML">' . $HTML . '</DIV>';
        }
    }

    function footer(){
        $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        if(strpos($actual_link, "?") !== false){
            $actual_link = left($actual_link, strpos($actual_link, "?"));
        }
        ?>
            <STYLE>
                .html{
                    width: 100%;
                    height: 400px;
                }
            </STYLE>
            <H2>Source list:</H2>
            <UL>
                <LI><A HREF="?url=https://www.skipthedishes.com/hamilton/restaurants/all?gclid=Cj0KCQjw-uzVBRDkARIsALkZAdkCaIOtVqPUOjdXeXMvxMM-fWEgVT5oH1840DCaLzffxBC6_XY7jMwaAphHEALw_wcB&action=storelist">skipthedishes hamilton (single store)</A></LI>
                <LI><A HREF="?url=https://www.skipthedishes.com/hamilton/restaurants/all?gclid=Cj0KCQjw-uzVBRDkARIsALkZAdkCaIOtVqPUOjdXeXMvxMM-fWEgVT5oH1840DCaLzffxBC6_XY7jMwaAphHEALw_wcB&action=storelist&all">skipthedishes hamilton (all stores)</A></LI>
                <LI><A HREF="?url=https://www.just-eat.ca/area/l8m-hamilton/?lat=43.2426058&long=-79.8210626&action=storelist">just-eat hamilton (single store)</A></LI>
                <LI><A HREF="?url=https://www.just-eat.ca/area/l8m-hamilton/?lat=43.2426058&long=-79.8210626&action=storelist&all">just-eat hamilton (all stores)</A></LI>
                <LI>ubereats does not allow downloading via this method<!--A HREF="?url=https://www.ubereats.com/stores/&action=storelist">ubereats</A--></LI>
                <?php
                if (file_exists(harvesterfile())) {
                    echo '<LI><A HREF="' . str_replace(".php", ".html", $actual_link) . '">Harvested data</A></LI>';
                }
                ?>
                <LI><A HREF="<?= $actual_link; ?>">Back to the main screen</A></LI>
            </UL>
        <?php
        die();
    }

    footer();
?>