<SPAN ID="gmac_<?= $name; ?>">
    <INPUT class="form-control google-address" TYPE="text" <?php
        if(!isset($function)){$function = false;}
        echo 'ID="gmap_' . $name . '" name="' . $name . '"';
    ?> >
    @if($unit)
        <INPUT TYPE="text" NAME="<?= $name; ?>_unit" ID="<?= $name; ?>_unit" PLACEHOLDER="Apt/Buzzer" CLASS="form-control address" TITLE="ie: Apt/Unit, buzz code, which door to go to">
    @endif
</SPAN>
<SCRIPT>
    var <?= $name; ?>_address;
    window.onload = function () {
        <?= $name; ?>_address = initAutocomplete('gmap_<?= $name; ?>', function(){
            var place = <?= $name; ?>_address.getPlace();
            var address = formataddress(place);
            $("#gmap_<?= $name; ?>").val(address["streetformat"]);
            savedata("#gmap_<?= $name; ?>", "address_", address);
            savedata("#gmap_<?= $name; ?>", "place_", place);
            @if($function) <?= $function; ?>(place); @endif
        }, false);
    };

    function savedata(Selector, DataName, Data){
        for (var key in Data){
            if(isArray(Data[key]) || isObject(Data[key])){
                savedata(Selector, DataName + key + "_", Data[key]);
            } else {
                $(Selector).attr(DataName + key, Data[key]);
            }
        }
    }

    function isvalidaddress2(ElementID){
        ElementID = $(ElementID);
        var Attributes = ["address_streetformat", "place_geometry_location_lat", "place_geometry_location_lng"];
        for(var i = 0; i < Attributes.length; i++){
            var Attribute = Attributes[i];
            if(!ElementID.hasAttr(Attribute) || ! ElementID.attr(Attribute)){return false;}
        }
        return ElementID.attr("address_streetformat") == ElementID.val();
    }
</SCRIPT>