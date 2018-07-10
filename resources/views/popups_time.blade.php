<STYLE>
    #timeselect{
        position: absolute;
        top: 0;
        left: 0;
        width: 300px;
        height: 200px;
        border: 2px solid red;
    }

    .timecaption, #timeselect {
        z-index: -1;
    }

    .timecaption{
        background-color: red;
        color: white;
    }

    .debug_text{
        width: 60px;
    }

    .debug_cmd {
        padding-left: 5px !important;
        padding-right: 5px !important;
        min-width: 40px;
        margin-right: 5px;
        margin-left: 5px;
    }

    .debug_check{
        width: 20px;
        height: 20px;
    }

    #debug_time{
        position: absolute;
        bottom: 0px;
        left: 0px;
        right: 0px;
        text-align: center;
    }

    .strong{
        font-weight: bold;
    }

    .colon::before{
        content: ":";
    }

    @media screen and (max-width: 400px) {
        #timeselect{
            display: none;
        }
    }
</STYLE>
<DIV ID="timeselect">
    <DIV CLASS="timecaption strong">Debug Time</DIV>
    <FORM>
        <LABEL>
            <INPUT TYPE="checkbox" ID="debug_use" CHECKED CLASS="debug_check" ONCLICK="resettime(false);">Use <i class="far fa-clock"></i> instead of debug time
        </LABEL><BR>
        <LABEL>
            Day of Week:
            <SELECT ID="debug_dayofweek">
                <?php
                    $currentday = date("w");
                    $daysofweek = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
                    foreach($daysofweek as $index => $day){
                        echo '<OPTION VALUE="' . $index . '"';
                        if($currentday == $index){
                            echo ' SELECTED';
                        }
                        echo '>' . $day . '</OPTION>';
                    }
                ?>
            </SELECT>
        </LABEL><BR>
        <LABEL>
            Time:
            <INPUT TYPE="number" ID="debug_hour" VALUE="<?= date("G"); ?>" CLASS="debug_text" MIN="1" MAX="12">
            <SPAN CLASS="colon"></SPAN>
            <INPUT TYPE="number" ID="debug_min" VALUE="<?= date("i"); ?>" CLASS="debug_text" MIN="-1" MAX="60" ONCHANGE="neg(this, true);" ONKEYUP="neg(this);" ONCLICK="neg(this);">
            <INPUT TYPE="button" ID="debug_ampm" VALUE="<?= date("A"); ?>" ONCLICK="changeAMPM();" CLASS="debug_cmd">
        </LABEL><BR>
        <INPUT TYPE="button" VALUE="Noon" ONCLICK="changeAMPM(true);" CLASS="debug_cmd" TITLE="Set debug time to noon (12:00 PM)">
        <INPUT TYPE="button" VALUE="Midnight" ONCLICK="changeAMPM(false);" CLASS="debug_cmd" TITLE="Set debug time to midnight (12:00 AM)">
        <INPUT TYPE="button" VALUE="Save" ONCLICK="resettime(true);" CLASS="debug_cmd float-right">
    </FORM>
    <DIV CLASS="timecaption" ID="debug_time" STYLE="display: none;" TITLE="This is the saved debug time"></DIV>
</DIV>
<SCRIPT>
    function changeAMPM(value){
        var current = $("#debug_ampm").val();
        if(isUndefined(value)) {
            if (current == "AM") {current = "PM";} else {current = "AM";}
        } else {
            $("#debug_hour").val("12");
            $("#debug_min").val("00");
            //12am = midnight(false), 12pm = noon(true)
            if(value){current = "PM";} else {current = "AM";}
        }
        $("#debug_ampm").val(current);
    }

    function neg(input, ischange) {
        if(isUndefined(ischange)){ischange = false;}
        if(!isNaN(input.value)) {
            if(input.value<0) {
                input.value = 59;
            } else if (input.value > 59){
                input.value = 0;
            } else if (ischange && input.value.length < 2){
                input.value = "0" + input.value;
            }
        }
    }

    function resettime(value){
        log("resettime: " + value);
        if(value){//set display time
            var day_number = $("#debug_dayofweek").val();
            var day_text = $("#debug_dayofweek :selected").text();
            var hour_12 = Number($("#debug_hour").val());
            var hour_24 = Number(hour_12);
            var minute = Number($("#debug_min").val());
            if(minute < 10){minute = "0" + minute;}
            var AMPM = $("#debug_ampm").val();
            if(AMPM == "AM"){
                if(hour_24 == 12){hour_24 = 0;}
            } else if(hour_12 < 12) {
                hour_24 += 12;
            }
            newtime = Number(hour_24) * 100 + Number(minute);
            newday = day_number;
            log("hour_24: " + hour_24 + " minute: " + minute + " newtime: " + newtime + " newday: " + newday);
            $("#debug_time").text(day_text + " (" + day_number + ") at " + hour_12 + ":" + minute + " " + AMPM + " (" + hour_24 + minute + ")").show();
            setCookie("testtime", newtime);
            setCookie("testday", newday);
            if($("#debug_use").is(':checked')){return;}
        } else {
            testing = !$("#debug_use").is(':checked');
        }
        setCookie("testing", testing);
        GenerateHours(generalhours);
    }

    if(!isUndefined(getCookie("testtime"))){
        newtime = Number(getCookie("testtime"));
        newday = Number(getCookie("testday"));
        var hour_12 = Math.floor(newtime / 100);

        log("newtime: " + newtime + " newday: " + newday + " hour12: " + hour_12);


        $("#debug_dayofweek").val(newday);
        $("#debug_min").val( newtime % 100 );
        if(newtime > 1159){
            $("#debug_ampm").val("PM");
        } else {
            $("#debug_ampm").val("AM");
        }
        if(hour_12 == 0){
            hour_12 = 12;
        } else if (hour_12 > 12){
            hour_12 = hour_12 - 12;
        }
        $("#debug_hour").val( hour_12 );
        setTimeout(function() {
            resettime(true);
        }, 100);
    }
</SCRIPT>