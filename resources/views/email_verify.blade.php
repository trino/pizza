@extends('layouts_email')
@section('header')
    Welcome to <?= sitename; ?>
@endsection
@section('content')
    Hi {{ $name }}, thank you for registering at Canbii.com
    <br><br>
    We will go above and beyond to ensure you get the best possible experience.<br><br>
    <?php /*Your password is $password <br><br> */ ?>
    <A HREF="<?= serverurl; ?>">Click here to book a cleaning now.</A>
    <br>
    @if($requiresauth)
        <A HREF="<?= webroot('auth/login', true) . '?action=verify&code=' . $authcode; ?>">Click here to verify your email</A>
    @endif
@endsection


