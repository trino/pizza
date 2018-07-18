@extends('layouts_app')
@section('content')

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="/resources/demos/style.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <STYLE>
        label{
            margin-bottom: 00px;
        }

        .half-width{
            width: 45%;
        }

        input[type=text], input[type=number]{
            padding-left: 5px !important;
            padding-right: 5px !important;
            display: inline-block;
        }

        select{
            font-family: FontAwesome, sans-serif;
        }

        .float-bottom{
            position: absolute;
            bottom: 0px;
        }
    </STYLE>

    <DIV CLASS="row">

        @if(read("profiletype") == 2)
            <INPUT TYPE="hidden" ID="rest_id" VALUE="<?= findrestaurant(); ?>">
        @else
            <DIV CLASS="col-md-2">
                Restaurant:
                <SELECT ID="rest_id" CLASS="form-control">
                    <OPTION VALUE="0">All</OPTION>
                    <?php
                        $restaurants = Query("SELECT * FROM restaurants ORDER BY name", true, "home_search");
                        foreach($restaurants as $restaurant){
                            echo '<OPTION VALUE="' . $restaurant["id"] . '">' . $restaurant["name"] . '</OPTION>';
                        }
                    ?>
                </SELECT>
            </DIV>
        @endif

        @if(read("profiletype") == 0)
            <INPUT TYPE="hidden" ID="user_id" VALUE="<?= read("id"); ?>">
        @else
            <DIV CLASS="col-md-2">
                User:
                <SELECT ID="user_id" CLASS="form-control">
                    <OPTION VALUE="0">All</OPTION>
                    <?php
                        $users = Query("SELECT * FROM users ORDER BY profiletype", true, "home_search");
                        foreach($users as $user){
                            echo '<OPTION VALUE="' . $user["id"] . '">';
                            switch ($user["profiletype"]){
                                case 0://customer
                                    echo '&#xf007;';
                                    break;
                                case 1://admin
                                    echo '&#xf234;';
                                    break;
                                case 2://restaurant
                                    echo '&#xf07a;';
                                    break;
                            }
                            echo $user["name"] . '</OPTION>';
                        }
                    ?>
                </SELECT>
            </DIV>
        @endif

        <DIV CLASS="col-md-2">
            Starting Date:
            <INPUT TYPE="TEXT" CLASS="datepicker form-control" ID="datepicker">
        </DIV>

        <DIV CLASS="col-md-2">
            <LABEL><INPUT TYPE="checkbox" ID="useenddate"> Use Ending Date:</LABEL>
            <INPUT TYPE="TEXT" CLASS="datepicker form-control" ID="datepicker_end">
        </DIV>

        <DIV CLASS="col-md-2">
            Search which date:<BR>
            <LABEL><INPUT TYPE="radio" NAME="datetype" VALUE="placed_at" CHECKED>Placed At</LABEL><BR>
            <LABEL><INPUT TYPE="radio" NAME="datetype" VALUE="deliver_at">Deliver At</LABEL>
        </DIV>

        <DIV CLASS="col-md-2">
            Price Range:<BR>
            <INPUT TYPE="NUMBER" MIN="0" MAX="1000" ID="minimum" CLASS="form-control half-width">
            <INPUT TYPE="NUMBER" MIN="0" MAX="1000" ID="maximum" CLASS="form-control half-width">
        </DIV>

        <DIV CLASS="col-md-10">
            Search Term:
            <INPUT TYPE="TEXT" CLASS="form-control" ID="searchterm" ONKEYPRESS="enterbutton(event);">
        </DIV>

        <DIV CLASS="col-md-2">
            <BUTTON CLASS="btn btn-primary float-bottom form-control" ONCLICK="search();">Search</BUTTON>
        </DIV>

        <DIV CLASS="col-md-2" ID="orders_list"></DIV>
        <DIV CLASS="col-md-10" ID="orders_content"></DIV>
    </DIV>

    <SCRIPT>
        var APIURL = "<?= webroot('public/list/orders'); ?>";

        function toMMDDYYY(today){
            if(isUndefined(today)){today = new Date();}
            var dd = today.getDate();
            var mm = today.getMonth()+1; //January is 0!
            var yyyy = today.getFullYear();
            if(dd<10) {dd = '0'+dd;}
            if(mm<10) {mm = '0'+mm;}
            return mm + '/' + dd + '/' + yyyy;
        }

        function toHMMSS(today){
            if(isUndefined(today)){today = new Date();}
            var h = today.getHours();
            var am = "AM";
            var mm = today.getMinutes();
            var ss = today.getSeconds();
            if(mm<10) {mm = '0'+mm;}
            if(ss<10) {ss = '0'+mm;}
            if(h == 0){
                h = 12;
            } else if (h > 11){
                am = "PM";
                if (h > 12){h = h - 12;}
            }
            return h + ":" + mm + ":" + ss + " " + am;
        }


        function loadorder(OrderID){
            var html = $("#order_" + OrderID).html();
            if(html) {
                $("#orders_content").html(html);
            } else {
                $.post(APIURL, {
                    _token: token,
                    action: "getreceipt",
                    orderid: OrderID,
                    JSON: false
                }, function (html) {
                    $("#order_" + OrderID).html(html);
                    $("#orders_content").html(html);
                });
            }
        }

        $( function() {
            $(".datepicker").datepicker();
            setDate(".datepicker");
        } );

        function getDate(picker_selector){
            return toMMDDYYY($(picker_selector).datepicker("getDate"));
        }

        function setDate(picker_selector, Date){
            if(isUndefined(Date)){
                Date = toMMDDYYY();
            }
            $(picker_selector).datepicker('setDate', Date);
        }

        function enterbutton(e){
            if (e.keyCode == 13) {
                search();
            }
        }

        function selectedradio(name){
            return $("input[name=" + name + "]").filter(":checked").val();
        }

        function search(){
            var today = getDate("#datepicker");
            var actualdate = new Date();
            var is_yesterday = false;
            var currenttime = actualdate.getHours() * 100 + actualdate.getMinutes();
            actualdate = $( "#datepicker" ).datepicker( "getDate" );
            var enddate = toMMDDYYY($( "#datepicker_end" ).datepicker( "getDate" ));
            var dayofweek = actualdate.getDay();
            var useend = $("#useenddate").prop("checked");
            var searchterm = $("#searchterm").val().trim();
            var currentrestaurant = $( "#rest_id").val();

            if(testing){
                currenttime = newtime;
                dayofweek = newday;
            }
            dayofweek = dayofweek - 1;
            if(dayofweek == -1){dayofweek = 6;}
            var closingtime = $("#rest_" + currentrestaurant).attr(dayofweek + "_close");
            if(currenttime <= closingtime){
                actualdate.setDate(actualdate.getDate() - 1);
                today = toMMDDYYY(actualdate);
                is_yesterday = true;
            }
            //end yesterday

            $("#orders_list").html("");
            $("#orders_content").html("Loading...");
            $.post(APIURL, {
                _token: token,
                action: "getorders",
                restaurant: currentrestaurant,
                userid: $("#user_id").val(),
                date: today,
                enddate: enddate,
                useend: useend,
                search: searchterm,
                minimum: $("#minimum").val(),
                maximum: $("#maximum").val(),
                datetype: selectedradio("datetype")
            }, function (result) {
                result = JSON.parse(result);
                var listHTML = 'Checked at: ' + toHMMSS() + '<BR>';
                var contentHTML = '';
                if(result.data.length == 0){
                    if(useend){
                        contentHTML += "No orders found between " + result.startdate + " and " + result.enddate;
                    } else {
                        contentHTML += "No orders found on " + result.startdate;
                    }
                    if(searchterm.length > 0){contentHTML += " containing '" + searchterm + "'";}
                    contentHTML += " at " + $("#rest_name").text() + "<BR>";
                    if(result.hasOwnProperty("query")) {contentHTML += "SQL: " + result.query + "<BR>";}
                }
                if(is_yesterday){
                    contentHTML += "(Checking orders for yesterday since it's close to closing for that day)<BR>";
                }

                var actualindex = 1;
                for(var index = 0; index < result.data.length; index++){
                    //if(result.data[index].hasOwnProperty("html")) {
                    @if(debugmode) actualindex = result.data[index].id; @endif
                        listHTML += '<A HREF="javascript:loadorder(' + result.data[index].id + ');">Order: ' + actualindex + " ($" + result.data[index].price + ')<DIV ID="order_' + result.data[index].id + '" CLASS="order" STYLE="display: none;"></DIV></A><BR>';
                    //result.data[index].html
                    actualindex++;
                    //}
                }
                $("#orders_list").html(listHTML);
                $("#orders_content").html(contentHTML);
            });
        }
    </SCRIPT>
@endsection