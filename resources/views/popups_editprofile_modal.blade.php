<!-- edit profile Modal -->
<div class="modal" id="profilemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="myModalLabel" class="align-middle">My Profile</h2>
                <button data-popup-close="profilemodal" data-dismiss="modal" class="btn ml-auto align-middle bg-transparent"><i class="fa fa-times"></i></button>
            </div>

            <div class="modal-body">
                <FORM NAME="user" id="userform">
                    @include("popups_edituser", array("showpass" => true, "email" => false, "icons" => true, "name" => "user"))

                    <div class="input_left_icon"><span class="fa-stack fa-2x"><i class="fa fa-circle fa-stack-2x"></i><i class="fa fa-envelope text-white fa-stack-1x"></i></span></div>

                    <div class="input_right"><input type="text" readonly class="form-control session_email_val"></div>

                    <DIV class="clearfix mt-1"></DIV>
                    <DIV CLASS="error" id="edituser_error"></DIV>
                    <DIV class="clearfix mt-1"></DIV>

                    <DIV CLASS="pull-center"><BUTTON CLASS="btn {{btncolor}} alert-button" onclick="return userform_submit(true);">SAVE</BUTTON></DIV>
                </FORM>

                <P><DIV CLASS="ajaxprompt"></DIV><P>

                <div CLASS="editprofilediv">
                    <DIV ID="addresslist"></DIV>
                </div>

                <div CLASS="editprofilediv">
                    <DIV ID="creditcardlist"></DIV>
                </div>

                <DIV CLASS="editprofilediv dont-show">
                    <H2>Newsletter</H2>
                    <DIV ID="newsletter"></DIV>
                </DIV>

                <div class="alert alert-info mt-3 mb-0 font-size-85rem">
                     Add new address or credit card on checkout
                    <br>  <a href="help" class="btn-link">More info</a>
                </div>
                <div CLASS="editprofilediv mt-2 dont-show">
                    <button ONCLICK="handlelogin('logout');" CLASS="btn {{btncolor}} pull-left" href="#">LOG OUT</button>
                    <button ONCLICK="orders();" CLASS="btn {{btncolor}} pull-right" href="#">PAST ORDERS</button>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<!-- end edit profile Modal -->