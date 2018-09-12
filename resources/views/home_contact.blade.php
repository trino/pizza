@extends('layouts_app')
@section('content')
    <STYLE>
        H4{
            margin-bottom: 5px;
        }
    </STYLE>
    <div class="row">
        <DIV CLASS="col-lg-12 bg-white list-padding list-card">
            <H4 CLASS="title">Contact Us</H4>
            @if(isset($_POST["contact_text"]))
                Your message has been sent
            @else
                <FORM id="contact_form" name="contact_form" method="post">
                    <INPUT TYPE="hidden" ID="token" NAME="_token">
                    @if(read("email"))
                        <div class="input_right"><INPUT TYPE="HIDDEN" id="contact_email" name="contact_email" VALUE="<?= read("email"); ?>"></DIV>
                    @else
                        <div class="input_left_icon">
                            <span class="fa-stack fa-2x"><i class="fa fa-circle fa-stack-2x"></i><i class="fa fa-envelope text-white fa-stack-1x"></i></span>
                        </div>
                        <div class="input_right">
                            <INPUT TYPE="text" id="contact_email" name="contact_email" placeholder="Email" class="form-control session_email_val" required>
                        </div>
                    @endif
                    <div class="input_left_icon">
                        <span class="fa-stack fa-2x"><i class="fa fa-circle fa-stack-2x"></i><i class="fa far fa-edit text-white fa-stack-1x"></i></span>
                    </div>
                    <div class="input_right">
                        <TEXTAREA CLASS="width-full" rows="10" NAME="contact_text" id="contact_text"></TEXTAREA>
                    </div>
                    <INPUT TYPE="submit" CLASS="btn-block btn {{btncolor}}" VALUE="SEND">
                </FORM>
            @endif
        </DIV>
    </DIV>
    <SCRIPT>
        $(function () {
            $("#token").val(token);
            $("#contact_form").validate({
                rules: {
                    contact_email: {
                        required: true,
                        email: true
                    },
                    contact_text: {
                        required: true,
                    }
                },
                messages: {
                    login_email: {
                        required: "Please enter a valid email address",
                        email: "Please enter a valid email address"
                    },
                    contact_text: {
                        required: "Please enter a message"
                    }
                },
                onkeyup: false,
                onfocusout: false
            });
        });
    </SCRIPT>
@endsection