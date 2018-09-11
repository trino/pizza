@extends('layouts_email')
@section('header')
    Forgot Password
@endsection
@section('content')
<!-- Remember, no CSS can be used -->
Your new password is <?= $password; ?>
@endsection
