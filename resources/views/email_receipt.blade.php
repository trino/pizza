<HTML>
    <table border="0" cellpadding="0" cellspacing="0" style="background-color: #fdfdfd; border: 1px solid #dcdcdc; border-radius: 3px !important; margin: auto; max-width: 600px;">
        <tr>
            <td style="padding: 36px 48px; display:block; background-color: red;">
                <h1 style="color:#ffffff;font-family: Helvetica Neue,Helvetica,Roboto,Arial,sans-serif; font-size: 30px; font-weight: 300; line-height: 150%; margin: 0; text-align: left;">
                    Customer order
                </h1>
            </td>
        </tr>
        <TR>
            <TD style="padding: 48px;" valign="top">
                <?php
                    //hack to put CSS inline for emails
                    echo 'Thank you for your order.<br>';
                    $HTML = view("popups_receipt", array("orderid" => $orderid, "inline" => true, "place" => "email", "style" => 2, "includeextradata" => true, "party" => $party))->render();

                    $Styles = array(
                        "TD" => "border: 1px solid #eceeef; padding: .3rem; display: table-cell;",
                        "TH" => "border-color: #55595c; border-bottom: 2px solid #eceeef; padding: .3rem; display: table-cell; border-right: 2px solid #eceeef;",
                        "th" => "border-color: #55595c; border-bottom: 2px solid #eceeef; padding: .3rem; display: table-cell;"//hack for last TH in a TR
                    );
                    foreach($Styles as $Tag => $Style){
                        $HTML = str_replace('<' . $Tag, '<' . $Tag . ' STYLE="' . $Style . '"', $HTML);
                    }
                    echo $HTML . "<P>" . view("email_test");
                ?>
            </TD>
        </TR>
    </TABLE>
</HTML>
