@extends('layouts_email')
@section('header')
    <?= $mail_subject; ?>
@endsection
@section('content')
<?php
    //Remember, no CSS can be used
    if(isset($body)){
        echo $body;
    }
?>
@endsection
