<HTML><!-- Remember, no CSS can be used -->
    <table border="0" cellpadding="0" cellspacing="0" style="border: 1px solid #dcdcdc; border-radius: 3px !important; margin: auto; width: 400px;">
        <tr>
            <td style="padding: 20px; display:block; background-color: <?= emailheadercolor ?>;">
                <h1 style="color:#ffffff;font-family: Helvetica Neue,Helvetica,Roboto,Arial,sans-serif; font-size: 30px; font-weight: 300; line-height: 150%; margin: 0; text-align: left;">
                    @yield('header')
                    <?php
                        if(defined("testing")){
                            if(testing){
                                echo "[TESTING MODE]";
                            }
                        }
                    ?>
                </h1>
            </td>
        </tr>
        <TR>
            <TD style="padding: 20px;" valign="top">
                @yield('content')
            </TD>
        </TR>
        <TR>
            <TD style="padding: 20px;" valign="top">
                Thank you,
                <br>The <A HREF="<?= serverurl ?>"><?= sitename; ?> Team</A>
            </TD>
        </TR>
    </TABLE>
</HTML>