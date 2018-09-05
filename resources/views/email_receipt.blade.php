@extends('layouts_email')
@section('header')
Customer Order
@endsection
@section('content')
<?php
    //hack to put CSS inline for emails cause no CSS can be used!!!
    echo '<br title="' . $party . '">';
    $HTML = view("popups_receipt", array(
            "orderid" => $orderid,
            "inline" => true,
            "place" => "email",
            "style" => 2,
            "includeextradata" => true,
            "party" => $party
    ))->render();
    $Styles = array(
        "TD" => "border: none !important; display: table-cell;",
        "TH" => "border-color: #55595c; border-bottom: 0px solid #eceeef; padding:0rem; display: table-cell; border-right: 0px solid #eceeef;",
        "th" => "border-color: #55595c; border-bottom: 0px solid #eceeef; padding: 0rem; display: table-cell;"//hack for last TH in a TR
    );
    foreach($Styles as $Tag => $Style){
        $HTML = str_replace('<' . $Tag, '<' . $Tag . ' STYLE="' . $Style . '"', $HTML);
    }
?>
@endsection