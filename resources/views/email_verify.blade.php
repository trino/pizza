@extends('layouts_email')
@section('header')
    Welcome to <?= sitename; ?>
@endsection
@section('content')
    Hi {{ $name }}, thank you for registering at <?= sitename; ?>.
    <br><br>
    <?php /*Your password is $password <br><br> */ ?>
    <A HREF="<?= serverurl; ?>">Click here to start ordering</A>
    <br>
    @if($requiresauth)
        <A HREF="<?= webroot('auth/login', true) . '?action=verify&code=' . $authcode; ?>">Click here to verify your email</A>
    @endif
@endsection


