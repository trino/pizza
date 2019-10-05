@extends('layouts_app')
@section('content')
    <?php
    //vardump($GLOBALS["app"]["config"]["database"]["constants"]);
    //$admins = enumadmins(false); vardump($admins);

    $launchdate = "April 1, 2017";
    $datestamp = strtotime($launchdate);
    $SQLdate = date("Y-m-d", $datestamp);
    $launched = iif(time() > $datestamp, " (Launched)");
    if (!$launched) {
        $days = ceil(($datestamp - time()) / 86400);
        $launched = " (" . $days . " day" . iif($days > 1, "s") . " away)";
    }
    $orders = first('SELECT count(*) as count FROM orders WHERE status <> 2 AND status <> 4 AND placed_at > "' . $SQLdate . '"')["count"];
    $donation_per_order = 0.1;
    $units_donated = "Pizzas";
    //$donations = number_format((float)$orders * $donation_per_order, 0, '.', '');
    $donations = floor($orders * $donation_per_order);
    $email = '<A HREF="mailto:info@trinoweb.ca?subject=' . sitename . '">info@trinoweb.ca</A>';
    ?>
    <STYLE>
        li > .title {
            font-weight: bold;
        }

        .collapse, .collapsing {
            margin-left: 30px;
        }

        .btn:not(.btn-circle) {
        }

        .btn-wide {
            width: 150px !important;
        }

        jump, .jump {
            text-decoration: underline;
            cursor: pointer;
            color: blue;
        }

        jump.event {
            text-decoration: none !important;
            font-weight: bold;
            color: black !important;
        }

        .no-u {
            text-decoration: none !important;
        }

        #gotobottom {
            bottom: 0;
        }

        #expandall {
            bottom: 28px;
        }

        #contractall {
            bottom: 56px;
        }

        #gototop {
            bottom: 84px;
        }

        .footer {
            font-weight: bold;
            position: fixed;
            left: 0;
            display: table;
            margin: 0 auto;
            background-color: white;
            width: 150px !important;
            color: #0281E1;
            z-index: 999;
            border: 1px solid #0281E1 !important;
            text-align: left !important;
        }

        .bg-secondary {
            margin-bottom: 2px;
        }

        .fa-black {
            border-radius: 3px;
            background-color: #292B2C;
            color: white;
            width: 20px;
            height: 20px;
            text-align: center;
            padding-top: 1px;
        }

        .fa-black.fa-plus {
            padding-top: 2px;
            padding-left: 1px;
        }

        .reason {
            font-weight: bold;
            color: blue;
        }

        .tab {
            margin-left: 25px;
        }

        .btn-border {
            border: 1px solid black !important;
        }
    </STYLE>
    <SCRIPT>
        $(document).ready(function () {
            $("jump").click(function () {
                var target = "#item_" + toclassname($(this).text());
                var targetstarget = $(target).attr("data-target");
                if (!$(targetstarget).hasClass("show")) {
                    $(target).trigger("click");
                    scrollto($(target).offset().top);
                }
            });
            $("#profileinfo").remove();

            $('a[href=#top]').click(function (event) {
                event.preventDefault();
                scrollto(0);
            });

            $('a[href=#bottom]').click(function (event) {
                event.preventDefault();
                scrollto($(document).height());
            });

            var afterhash = window.location.hash.replace("#", "");
            if (afterhash) {
                $("#item_" + afterhash.toLowerCase().replaceAll(" ", "_")).click();
            }
        });

        function scrollto(Y) {
            $('html, body').animate({scrollTop: Y}, 'slow');
        }

        function expandall(expand) {
            if (expand) {
                $(".collapse").not(".show").prev().trigger("click");
            } else {
                $(".show").prev().trigger("click");
            }
        }
    </SCRIPT>
    <DIV class="row">
        <div class="col-sm-6 py-3">
            <h3><?= getsetting("aboutus"); ?></h3>
            <div class="card-block ">

                <h1>                <strong>On Demand Home Cleaning</strong></h1>
                <br>
                <p>
                    <?= sitename; ?> was founded with the simple belief that online food ordering doesn’t have to be so
                    complicated. We realize that {{storenames}} are paying enormous commissions to existing online food
                    service providers. Ultimately, it’s YOU, the customer, who ends up paying the bills. We want to put
                    that money back where it belongs…in your pocket! Not only do we save you money, but we also use 100%
                    of all our profits to give back and help out the local community.
                </p>
                <p>
                    How do we do it? We leverage an easy-to-use online platform, one universal menu for all users alike,
                    and local partnerships with {{storenames}} who share our vision. In an effort to be completely
                    transparent, we will post a summary all of our contributions below, updated on a monthly basis.
                </p>
                <p>
                    Join us in our mission to change the way we order our food online and make your first order today!
                </p>
                <br>
                <div class="btn-outlined-danger text-center pt-1">
                    <strong>August, 2018</strong>
                    <p> Orders: <?= $orders; ?>
                        <br> Donated: <?= $donations + 1 . " " . $units_donated; ?>
                        <br> Charity: Hamilton Food Centre</p>
                </div>


            </div>
        </div>
        <DIV CLASS="col-sm-6 bg-dark text-white" titledebug="popups_login">
            <?= view("popups_login", array("justright" => true))->render(); ?>
        </DIV>
    </DIV>



    <DIV class="row">
        <div class="col-sm-12 pt-3"><hr>
        </div>
        </div>


    <DIV class="row">
        <div class="col-sm-6 py-3">
        <div class="card-block ">




            <h1>                <strong>  United States and Canada: Terms &amp; Conditions</strong></h1>

<br>

                    <p style="margin: 0px 0px 10px;">
                        The terms and conditions stated herein (collectively, this "Agreement") constitute a legal agreement between you and Trino, Inc. (dba Cleaner Love), a Delaware corporation or the appropriate
                        entity (the "Company"). By using or receiving any services supplied to you by the Company (together with the website located at https://canbii.com/, collectively, the "Service"), and downloading,
                        installing or using any associated software supplied by the Company which purpose is to enable you to use the Service (collectively, the "Software"), you hereby expressly acknowledge and agree to
                        be bound by the terms and conditions of this Agreement, and any future amendments and additions to this Agreement as published from time to time at https://canbii.com/ or through the Service.</p>
                    <p style="margin: 0px 0px 10px;">
                        The Company reserves the right to modify the terms and conditions of this Agreement or its policies relating to the Service or Software at any time, effective upon posting of an updated version of
                        this Agreement on the Service or Software. You are responsible for regularly reviewing this Agreement. Continued use of the Service or Software after any such changes shall constitute your consent
                        to such changes. If you require any more information or have any questions about our Terms and Conditions, please feel free to contact us by email at info@canbii.com</p>
                    <p style="margin: 0px 0px 10px;">
                        THE COMPANY DOES NOT PROVIDE CLEANING SERVICES, AND THE COMPANY IS NOT A CLEANING SERVICE PROVIDER. IT IS UP TO THE THIRD PARTY CLEANING SERVICE PROVIDER TO OFFER CLEANING SERVICES WHICH MAY BE
                        SCHEDULED THROUGH USE OF THE SOFTWARE OR SERVICE. THE COMPANY OFFERS INFORMATION AND A METHOD TO OBTAIN SUCH THIRD PARTY CLEANING SERVICES, BUT DOES NOT AND DOES NOT INTEND TO PROVIDE CLEANING
                        SERVICES OR ACT IN ANY WAY AS A CLEANING SERVICE PROVIDER, AND HAS NO RESPONSIBILITY OR LIABILITY FOR ANY CLEANING SERVICES PROVIDED TO YOU BY SUCH THIRD PARTIES.</p>
                    <br>
                    <b>Cleaner Love is Only a Venue</b>
                    <p style="margin: 0px 0px 10px;">
                        The Service is a communications platform for enabling the connection between individuals seeking to obtain cleaning services and/or individuals seeking to provide cleaning services. The Company
                        checks the backgrounds of cleaning service providers via third party background check services; however, the Company does not guarantee or warrant, and makes no representations regarding, the
                        reliability, quality or suitability of such cleaning service providers. When interacting with cleaning service providers you should exercise caution and common sense to protect your personal
                        safety and property, just as you would when interacting with other persons whom you don't know. By using the Service, you agree to hold the Company free from the responsibility for any liability
                        or damage that might arise out of the transaction involved. NEITHER THE COMPANY NOR ITS AFFILIATES OR LICENSORS IS RESPONSIBLE FOR THE CONDUCT, WHETHER ONLINE OR OFFLINE, OF ANY USER OF THE
                        SERVICE. THE COMPANY AND ITS AFFILIATES AND LICENSORS WILL NOT BE LIABLE FOR ANY CLAIM, INJURY OR DAMAGE ARISING IN CONNECTION WITH YOUR USE OF THE SERVICE.</p>
                    <br>
                    <b>Representations and Warranties</b>
                    <p style="margin: 0px 0px 10px;">
                        By using the Software or Service, you expressly represent and warrant that you are legally entitled to enter this Agreement. If you reside in a jurisdiction which restricts the use of the Service
                        because of age, or restricts the ability to enter into agreements such as this one due to age, you must abide by such age limits and you must not use the Software and Service. Without limiting the
                        foregoing, the Service and Software is not available to children (persons under the age of 18) or others who are not capable of entering into binding contracts. By using the Software or Service,
                        you represent and warrant that you are at least 18 years old and otherwise capable of entering into binding contracts. By using the Software or the Service, you represent and warrant that you have
                        the right, authority and capacity to enter into this Agreement and to abide by the terms and conditions of this Agreement. Your participation in using the Service and/or Software is for your sole,
                        personal use. You may not authorize others to use your user status, and you may not assign or otherwise transfer your user account to any other person or entity. When using the Software or Service
                        you agree to comply with all applicable laws from the country, state and city in which you are present while using the Software or Service.</p>
                    <p style="margin: 0px 0px 10px;">
                        You may only access the Service using authorized means. It is your responsibility to check to ensure you download the correct Software for your device. The Company is not liable if you do not have
                        a compatible handset or if you have downloaded the wrong version of the Software for your handset.</p>
                    <p style="margin: 0px 0px 10px;">
                        By using the Software or the Service, you agree that:</p>
                    <ul style="padding: 0px; margin: 0px 0px 10px 25px;">
                        <li style="line-height: 20px;">
                            You will only use the Service or Software for lawful purposes; you will not use the Service for sending or storing any unlawful material or for fraudulent purposes.
                        </li>
                        <li style="line-height: 20px;">
                            You will not use the Service or Software to cause nuisance, annoyance or inconvenience.
                        </li>
                        <li style="line-height: 20px;">
                            You will not impair the proper operation of the network.
                        </li>
                        <li style="line-height: 20px;">
                            You will not try to harm the Service or Software in any way whatsoever.
                        </li>
                        <li style="line-height: 20px;">
                            You will not copy, or distribute the Software or other content without written permission from the Company.
                        </li>
                        <li style="line-height: 20px;">
                            You will only use the Software and Service for your own use and will not resell it to a third party.
                        </li>
                        <li style="line-height: 20px;">
                            You will keep secure and confidential your account password or any identification provided to you which allows access to the Service.
                        </li>
                        <li style="line-height: 20px;">
                            You will provide us with whatever proof of identity the Company may reasonably request.
                        </li>
                        <li style="line-height: 20px;">
                            You will only use an access point or data account which you are authorized to use.
                        </li>
                        <li style="line-height: 20px;">
                            When requesting cleaning services by SMS, you opt-in to receive text messages from the Company, and acknowledge that standard messaging charges from your mobile network service provider may
                            apply, and you represent and warrant that the number provided is your own cell phone number.
                        </li>
                    </ul>
                    <p style="margin: 0px 0px 10px;">
                        By submitting contact numbers and other information to Cleaner Love, you consent to:
                    </p>
                    <ul style="padding: 0px; margin: 0px 0px 10px 25px;">
                        <li style="line-height: 20px;">
                            Contact at the number(s) provided by Cleaner Love, Cleaner Love Cleaning Professionals, and its partners by telephone call and/or text message, including by calls or text messages made by an
                            automatic telephone dialing system or other automated technology, even if you have opted-out of such calls through the National Do Not Call Registry (or state equivalent) or the internal do
                            not call list of Cleaner Love or any other company, and
                        </li>
                        <li style="line-height: 20px;">
                            Calls or text messages can be revoked at any time by email to info@canbii.com.
                        </li>
                    </ul>
                    <br>
                    <b>License Grant &amp; Restrictions</b>
                    <p style="margin: 0px 0px 10px;">
                        The Company hereby grants you a non-exclusive, non-transferable, right to use the Software and Service, solely for your own personal, non-commercial purposes, subject to the terms and conditions
                        of this Agreement. All rights not expressly granted to you are reserved by the Company and its licensors.</p>
                    <p style="margin: 0px 0px 10px;">
                        You shall not (i) license, sublicense, sell, resell, transfer, assign, distribute or otherwise commercially exploit or make available to any third party the Service or the Software in any way;
                        (ii) modify or make derivative works based upon the Service or the Software; (iii) create Internet "links" to the Service or "frame" or "mirror" any Software on any other server or wireless or
                        Internet-based device; (iv) reverse engineer the Software; (v) access the Software in order to (a) build a competitive product or service, (b) build a product using similar ideas, features,
                        functions or graphics of the Service or Software, or (c) copy any ideas, features, functions or graphics of the Service or Software, or (vi) launch an automated program or script, including, but
                        not limited to, web spiders, web crawlers, web robots, web ants, web indexers, bots, viruses or worms, or any program which may make multiple server requests per second, or unduly burdens or
                        hinders the operation and/or performance of the Service or Software.</p>
                    <p style="margin: 0px 0px 10px;">
                        You may use the Software and Service only for your personal, non-commercial purposes and shall not: (i) send spam or otherwise duplicative or unsolicited messages in violation of applicable laws;
                        (ii) send or store infringing, obscene, threatening, libelous, or otherwise unlawful or tortious material, including material harmful to children or violative of third party privacy rights; (iii)
                        send or store material containing software viruses, worms, Trojan horses or other harmful computer code, files, scripts, agents or programs; (iv) interfere with or disrupt the integrity or
                        performance of the Software or Service or the data contained therein; or (v) attempt to gain unauthorized access to the Software or Service or its related systems or networks.</p>
                    <br>
                    <b>Payment Terms</b>
                    <p style="margin: 0px 0px 10px;">
                        Any fees which the Company may charge you for the Software or Service are due immediately upon completion of your cleaning and are non-refundable. This no refund policy shall apply at all times
                        regardless of your decision to terminate your usage, the Company's decision to terminate your usage, disruption caused to our Software or Service either planned, accidental or intentional, or any
                        reason whatsoever. The Company reserves the right to determine final prevailing pricing - Please note the pricing information published on the website may not reflect the prevailing pricing.</p>
                    <p style="margin: 0px 0px 10px;">
                        The Company, at its sole discretion, make promotional offers with different features and different rates to any of our customers. These promotional offers, unless made to you, shall have no
                        bearing whatsoever on your offer or contract. <span style="">You may be charged for your appointment in full if you cancel within 24 hours of the appointment start subject to our Last Minute Cancellation Policy or if your cleaner is unable to complete as a result of being locked out of your home. Please see our <a
                                    href="/help">Help Center</a> for Last Minute Cancellation and Lockout Policies.</span> The Company may change the fees for our Service as we deem necessary for our business. We
                        encourage you to check back at our website periodically if you are interested about how we charge for the Service.</p>
                    <br>
                    <b>Intellectual Property Ownership</b>
                    <p style="margin: 0px 0px 10px;">
                        The Company alone (and its licensors, where applicable) shall own all right, title and interest, including all related intellectual property rights, in and to the Software and the Service. To the
                        extent you provide any suggestions, ideas, enhancement requests, feedback, recommendations or other information regarding the Service or Software, you hereby assign to the Company all right, title
                        and interest thereto. This Agreement is not a sale and does not convey to you any rights of ownership in or related to the Software or the Service, or any intellectual property rights owned by the
                        Company. The Company name, the Company logo, and the product names associated with the Software and Service are trademarks of the Company or third parties, and no right or license is granted to
                        use them.</p>
                    <br>
                    <b>Privacy; DMCA</b>
                    <p style="margin: 0px 0px 10px;">
                        Please visit https://canbii.com//privacy to understand how the Company collects and uses personal information. The Digital Millennium Copyright Act of 1998 (the "DMCA") provides recourse for
                        copyright owners who believe that material appearing on the Internet infringes their rights under U.S. copyright law. If you believe in good faith that any content made available in connection
                        with the Service or Software infringes your copyright, you (or your agent) may send the Company a notice requesting that the content be removed, or access to it blocked. Notices and
                        counter-notices must meet the then-current statutory requirements imposed by the DMCA (see http://www.loc.gov/copyright for details). Notices and counter notices with respect to the Service or
                        Software should be sent to the Company at:</p>
                    <p style="margin: 0px 0px 10px;">
                        By Mail:<br>
                        Copyright Agent<br>
                        Trino, Inc.<br>
                        2711 Centerville Road, Suite 400, Wilmington, New Castle County, Delaware 19808<br>
                        <br>
                        By Email: info@canbii.com</p>
                    <br>
                    <b>Third Party Interactions</b>
                    <p style="margin: 0px 0px 10px;">
                        During use of the Software and Service, you may enter into correspondence with, purchase goods and/or services from, or participate in promotions of third party service providers, advertisers or
                        sponsors showing their goods and/or services through the Software or Service. Any such activity, and any terms, conditions, warranties or representations associated with such activity, is solely
                        between you and the applicable third-party. The Company and its licensors shall have no liability, obligation or responsibility for any such correspondence, purchase, transaction or promotion
                        between you and any such third-party. The Company does not endorse any sites on the Internet that are linked through the Service or Software, and in no event shall the Company or its licensors be
                        responsible for any content, products, services or other materials on or available from such sites or third party providers. The Company provides the Software and Service to you pursuant to the
                        terms and conditions of this Agreement. You recognize, however, that certain third-party providers of goods and/or services may require your agreement to additional or different terms and
                        conditions prior to your use of or access to such goods or services, and the Company disclaims any and all responsibility or liability arising from such agreements between you and the third party
                        providers.</p>
                    <p style="margin: 0px 0px 10px;">
                        The Company may rely on third party advertising and marketing supplied through the Software or Service and other mechanisms to subsidize the Software or Service. By agreeing to these terms and
                        conditions you agree to receive such advertising and marketing. If you do not want to receive such advertising you should notify us in writing. The Company may compile and release information
                        regarding you and your use of the Software or Service on an anonymous basis as part of a customer profile or similar report or analysis. You agree that it is your responsibility to take reasonable
                        precautions in all actions and interactions with any third party you interact with through the Service.</p>
                    <br>
                    <b>Indemnification</b>
                    <p style="margin: 0px 0px 10px;">
                        By entering into this Agreement and using the Software or Service, you agree to defend, indemnify and hold the Company, its licensors and each such party's parent organizations, subsidiaries,
                        affiliates, officers, directors, members, employees, attorneys and agents harmless from and against any and all claims, costs, damages, losses, liabilities and expenses (including attorneys' fees
                        and costs) arising out of or in connection with: (a) your violation or breach of any term of this Agreement or any applicable law or regulation, whether or not referenced herein; (b) your
                        violation of any rights of any third party, including providers of cleaning services arranged via the Service or Software, or (c) your use or misuse of the Software or Service.</p>
                    <p style="margin: 0px 0px 10px;">
                        IF YOU ARE A NEW JERSEY RESIDENT, THE ABOVE LANGUAGE IN THIS SECTION (INDEMNIFICATION) IS INTENDED TO BE ONLY AS BROAD AND INCLUSIVE AS IS PERMITTED UNDER NEW JERSEY LAW. IF ANY PORTION OF THIS
                        SECTION IS HELD TO BE INVALID UNDER NEW JERSEY LAW, THE INVALIDITY OF SUCH PORTION SHALL NOT AFFECT THE VALIDITY OF THE REMAINING PORTIONS OF THIS SECTION.
                    </p>
                    <br>
                    <b>Termination</b>
                    <p style="margin: 0px 0px 10px;">
                        The Company reserves the right to (i) modify or discontinue, temporarily or permanently, the Service (or any part thereof) and (ii) refuse any and all current and future use of the Service,
                        suspend or terminate your account (any part thereof) or use of the Service, for any reason, including if the Company believes that you have violated this Agreement. The Company shall not be liable
                        to you or any third party for any modification, suspension or discontinuation of the Service. The Company will use good faith efforts to contact you to warn you prior to suspension or termination
                        of your account by the Company.</p>
                    <br>
                    <b>Communications</b>
                    <p style="margin: 0px 0px 10px;">
                        You consent to the monitoring and/or recording of any incoming or outgoing calls, text SMS messages, and other communications transmitted to or through the Services, and hereby waive any
                        notification requirement at the time such recording to the maximum extent permitted under applicable law.</p>
                    <br>
                    <b>Disclaimer of Warranties</b>
                    <p style="margin: 0px 0px 10px;">
                        THE COMPANY MAKES NO REPRESENTATION, WARRANTY, OR GUARANTY AS TO THE RELIABILITY, TIMELINESS, QUALITY, SUITABILITY, AVAILABILITY, ACCURACY OR COMPLETENESS OF THE SERVICE OR SOFTWARE. THE COMPANY
                        DOES NOT REPRESENT OR WARRANT THAT (A) THE USE OF THE SERVICE OR SOFTWARE WILL BE SECURE, TIMELY, UNINTERRUPTED OR ERROR-FREE OR OPERATE IN COMBINATION WITH ANY OTHER HARDWARE, SOFTWARE, SYSTEM OR
                        DATA, (B) THE SERVICE OR SOFTWARE (INCLUDING ANY CLEANING SERVICES) WILL MEET YOUR REQUIREMENTS OR EXPECTATIONS, (C) ANY STORED DATA WILL BE ACCURATE OR RELIABLE, (D) THE QUALITY OF ANY PRODUCTS,
                        SERVICES, INFORMATION, OR OTHER MATERIAL PURCHASED OR OBTAINED BY YOU THROUGH THE SERVICE (INCLUDING ANY CLEANING SERVICES) WILL MEET YOUR REQUIREMENTS OR EXPECTATIONS, (E) ERRORS OR DEFECTS IN
                        THE SERVICE OR SOFTWARE WILL BE CORRECTED, OR (F) THE SERVICE OR THE SERVER(S) THAT MAKE THE SERVICE AVAILABLE ARE FREE OF VIRUSES OR OTHER HARMFUL COMPONENTS. THE SERVICE AND SOFTWARE IS PROVIDED
                        TO YOU STRICTLY ON AN "AS IS" BASIS. ALL CONDITIONS, REPRESENTATIONS AND WARRANTIES, WHETHER EXPRESS, IMPLIED, STATUTORY OR OTHERWISE, INCLUDING, WITHOUT LIMITATION, ANY IMPLIED WARRANTY OF
                        MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE, OR NON-INFRINGEMENT OF THIRD PARTY RIGHTS, ARE HEREBY DISCLAIMED TO THE MAXIMUM EXTENT PERMITTED BY APPLICABLE LAW BY THE COMPANY. THE COMPANY
                        MAKES NO REPRESENTATION, WARRANTY, OR GUARANTY AS TO THE RELIABILITY, SAFETY, TIMELINESS, QUALITY, SUITABILITY OR AVAILABILITY OF ANY SERVICES, PRODUCTS OR GOODS OBTAINED BY THIRD PARTIES THROUGH
                        THE USE OF THE SERVICE OR SOFTWARE. YOU ACKNOWLEDGE AND AGREE THAT THE ENTIRE RISK ARISING OUT OF YOUR USE OF THE SOFTWARE AND SERVICE, AND ANY THIRD PARTY SERVICES OR PRODUCTS, REMAINS SOLELY
                        WITH YOU, TO THE MAXIMUM EXTENT PERMITTED BY LAW.</p>
                    <p style="margin: 0px 0px 10px;">
                        IF YOU ARE A NEW JERSEY RESIDENT, THE ABOVE LANGUAGE IN THIS SECTION (DISCLAIMER OF WARRANTIES) IS INTENDED TO BE ONLY AS BROAD AND INCLUSIVE AS IS PERMITTED UNDER NEW JERSEY LAW. IF ANY PORTION
                        OF THIS SECTION IS HELD TO BE INVALID UNDER NEW JERSEY LAW, THE INVALIDITY OF SUCH PORTION SHALL NOT AFFECT THE VALIDITY OF THE REMAINING PORTIONS OF THIS SECTION.
                    </p>

                    <br>
                    <b>Network Delays</b>
                    <p style="margin: 0px 0px 10px;">
                        THE COMPANY'S SERVICE AND SOFTWARE MAY BE SUBJECT TO LIMITATIONS, DELAYS, AND OTHER PROBLEMS INHERENT IN THE USE OF THE INTERNET, TELECOMMUNICATIONS NETWORKS AND ELECTRONIC COMMUNICATIONS. THE
                        COMPANY IS NOT RESPONSIBLE FOR ANY DELAYS, DELIVERY FAILURES, OR OTHER DAMAGE RESULTING FROM SUCH PROBLEMS.</p>
                    <br>
                    <b>Limitation of Liability</b>
                    <p style="margin: 0px 0px 10px;">
                        IN NO EVENT SHALL THE COMPANY'S AGGREGATE LIABILITY EXCEED THE AMOUNTS ACTUALLY PAID BY AND/OR DUE FROM YOU IN THE SIX (6) MONTH PERIOD IMMEDIATELY PRECEDING THE EVENT GIVING RISE TO SUCH CLAIM.
                        IN NO EVENT SHALL THE COMPANY AND/OR ITS LICENSORS BE LIABLE TO ANYONE FOR ANY INDIRECT, PUNITIVE, SPECIAL, EXEMPLARY, INCIDENTAL, CONSEQUENTIAL OR OTHER DAMAGES OF ANY TYPE OR KIND (INCLUDING
                        PERSONAL INJURY, LOSS OF DATA, REVENUE, PROFITS, USE OR OTHER ECONOMIC ADVANTAGE). THE COMPANY AND/OR ITS LICENSORS SHALL NOT BE LIABLE FOR ANY LOSS, DAMAGE OR INJURY WHICH MAY BE INCURRED BY YOU,
                        INCLUDING BY NOT LIMITED TO LOSS, DAMAGE OR INJURY ARISING OUT OF, OR IN ANY WAY CONNECTED WITH THE SERVICE OR SOFTWARE, INCLUDING BUT NOT LIMITED TO THE USE OR INABILITY TO USE THE SERVICE OR
                        SOFTWARE, ANY RELIANCE PLACED BY YOU ON THE COMPLETENESS, ACCURACY OR EXISTENCE OF ANY ADVERTISING, OR AS A RESULT OF ANY RELATIONSHIP OR TRANSACTION BETWEEN YOU AND ANY THIRD PARTY SERVICE
                        PROVIDER, ADVERTISER OR SPONSOR WHOSE ADVERTISING APPEARS ON THE WEBSITE OR IS REFERRED BY THE SERVICE OR SOFTWARE, EVEN IF THE COMPANY AND/OR ITS LICENSORS HAVE BEEN PREVIOUSLY ADVISED OF THE
                        POSSIBILITY OF SUCH DAMAGES.</p>
                    <p style="margin: 0px 0px 10px;">
                        THE COMPANY MAY INTRODUCE YOU TO THIRD PARTY CLEANING SERVICE PROVIDERS FOR THE PURPOSES OF PROVIDING CLEANING SERVICES. WE WILL NOT ASSESS THE SUITABILITY, LEGALITY OR ABILITY OF ANY THIRD PARTY
                        CLEANING SERVICE PROVIDERS AND YOU EXPRESSLY WAIVE AND RELEASE THE COMPANY FROM ANY AND ALL ANY LIABILITY, CLAIMS OR DAMAGES ARISING FROM OR IN ANY WAY RELATED TO THE THIRD PARTY CLEANING SERVICE
                        PROVIDER. THE COMPANY WILL NOT BE A PARTY TO DISPUTES, NEGOTIATIONS OF DISPUTES BETWEEN YOU AND SUCH THIRD PARTY PROVIDERS. RESPONSIBILITY FOR THE DECISIONS YOU MAKE REGARDING SERVICES OFFERED VIA
                        THE SOFTWARE OR SERVICE (WITH ALL ITS IMPLICATIONS) RESTS SOLELY WITH YOU. WE WILL NOT ASSESS THE SUITABILITY, LEGALITY OR ABILITY OF ANY SUCH THIRD PARTIES AND YOU EXPRESSLY WAIVE AND RELEASE THE
                        COMPANY FROM ANY AND ALL LIABILITY, CLAIMS, CAUSES OF ACTION, OR DAMAGES ARISING FROM YOUR USE OF THE SOFTWARE OR SERVICE, OR IN ANY WAY RELATED TO THE THIRD PARTIES INTRODUCED TO YOU BY THE
                        SOFTWARE OR SERVICE. YOU EXPRESSLY WAIVE AND RELEASE ANY AND ALL RIGHTS AND BENEFITS UNDER SECTION 1542 OF THE CIVIL CODE OF THE STATE OF CALIFORNIA (OR ANY ANALOGOUS LAW OF ANY OTHER STATE),
                        WHICH READS AS FOLLOWS: "A GENERAL RELEASE DOES NOT EXTEND TO CLAIMS WHICH THE CREDITOR DOES NOT KNOW OR SUSPECT TO EXIST IN HIS FAVOR AT THE TIME OF EXECUTING THE RELEASE, WHICH, IF KNOWN BY HIM,
                        MUST HAVE MATERIALLY AFFECTED HIS SETTLEMENT WITH THE DEBTOR."</p>
                    <p style="margin: 0px 0px 10px;">
                        THE QUALITY OF THE CLEANING SERVICES SCHEDULED THROUGH THE USE OF THE SERVICE OR SOFTWARE IS ENTIRELY THE RESPONSIBILITY OF THE THIRD PARTY PROVIDER WHO ULTIMATELY PROVIDES SUCH CLEANING SERVICES
                        TO YOU. YOU UNDERSTAND, THEREFORE, THAT BY USING THE SOFTWARE AND THE SERVICE, YOU MAY BE EXPOSED TO CLEANING SERVICES THAT ARE POTENTIALLY DANGEROUS, OFFENSIVE, HARMFUL TO MINORS, UNSAFE OR
                        OTHERWISE OBJECTIONABLE, AND THAT YOU USE THE SOFTWARE AND THE SERVICE, AND SUCH THIRD PARTY CLEANING SERVICES, AT YOUR OWN RISK.</p>
                    <p style="margin: 0px 0px 10px;">
                        NOTHING ON THIS WEBSITE CONSTITUTES, OR IS MEANT TO CONSTITUTE, ADVICE OF ANY KIND. IF YOU REQUIRE ADVICE IN RELATION TO ANY LEGAL, FINANCIAL OR MEDICAL MATTER YOU SHOULD CONSULT AN APPROPRIATE
                        PROFESSIONAL.</p>
                    <p style="margin: 0px 0px 10px;">
                        BY USING THE SERVICE OR SOFTWARE, YOU AGREE THAT THE EXCLUSIONS AND LIMITATIONS OF LIABILITY SET OUT IN THIS AGREEMENT ARE REASONABLE. IF YOU DO NOT THINK THEY ARE REASONABLE, YOU MUST NOT USE THE
                        SERVICE OR SOFTWARE.</p>
                    <p style="margin: 0px 0px 10px;">
                        IF YOU ARE A NEW JERSEY RESIDENT, THE ABOVE LANGUAGE IN THIS SECTION (LIMITATIONS OF LIABILITY) IS INTENDED TO BE ONLY AS BROAD AND INCLUSIVE AS IS PERMITTED UNDER NEW JERSEY LAW. IF ANY PORTION
                        OF THIS SECTION IS HELD TO BE INVALID UNDER NEW JERSEY LAW, THE INVALIDITY OF SUCH PORTION SHALL NOT AFFECT THE VALIDITY OF THE REMAINING PORTIONS OF THIS SECTION.
                    </p>
                    <br>
                    <b>Notice</b>
                    <p style="margin: 0px 0px 10px;">
                        The Company may give notice to you by means of a general notice on the Service, electronic mail to your email address on record in the Company's account information, or by written communication
                        sent by first class mail or pre-paid post to your address on record in the Company's account information. Such notice shall be deemed to have been given upon the expiration of 48 hours after
                        mailing or posting (if sent by first class mail or pre-paid post) or 12 hours after sending (if sent by email). You may give notice to the Company (such notice shall be deemed given when received
                        by the Company) at any time by any of the following: letter sent by confirmed facsimile to the Company at the following fax numbers (whichever is appropriate): 1-855-569-8783; letter delivered by
                        nationally recognized overnight delivery service or first class postage prepaid mail to the Company at the following addresses (whichever is appropriate): Cleaner Love, 2711 Centerville Road,
                        Suite 400, Wilmington, New Castle County, Delaware 19808, addressed to the attention of: Chief Executive Officer.</p>
                    <br>
                    <b>Controlling Law and Jurisdiction</b>
                    <p style="margin: 0px 0px 10px;">
                        This Agreement will be interpreted in accordance with the laws of the State of California and the United States of America, without regard to its conflict-of-law provisions. You and the Company
                        agree to submit to the personal jurisdiction of a state court located in Santa Clara, California or a United States District Court, Northern District of California located in Santa Clara,
                        California, for any actions for which the parties retain the right to seek injunctive or other equitable relief in a court of competent jurisdiction to prevent the actual or threatened
                        infringement, misappropriation or violation of a party's copyrights, trademarks, trade secrets, patents, or other intellectual property rights, as set forth in the Dispute Resolution provision
                        below.</p>
                    <br>
                    <b>Dispute Resolution</b>
                    <p style="margin: 0px 0px 10px;">
                        INFORMAL NEGOTIATIONS. To expedite resolution and reduce the cost of any dispute, controversy or claim related to this Agreement ("Dispute"), you and the Company agree to first attempt to
                        negotiate any Dispute (except those Disputes expressly excluded below) informally for at least thirty (30) days before initiating any arbitration or court proceeding. Such informal negotiations
                        will commence upon written notice, as set forth above.</p>
                    <p style="margin: 0px 0px 10px;">
                        BINDING ARBITRATION. If you and the Company are unable to resolve a Dispute through informal negotiations, all claims arising from use of the Service or Software (except those Disputes expressly
                        excluded below) will be finally and exclusively resolved by binding arbitration. Any election to arbitrate by one party will be final and binding on the other. YOU UNDERSTAND THAT IF EITHER PARTY
                        ELECTS TO ARBITRATE, NEITHER PARTY WILL HAVE THE RIGHT TO SUE IN COURT OR HAVE A JURY TRIAL. The arbitration will be commenced and conducted under the Commercial Arbitration Rules (the "AAA
                        Rules") of the American Arbitration Association ("AAA") and, where appropriate, the AAA's Supplementary Procedures for Consumer Related Disputes ("AAA Consumer Rules"), both of which are available
                        at the AAA website www.adr.org. Your arbitration fees and your share of arbitrator compensation will be governed by the AAA Rules (and, where appropriate, limited by the AAA Consumer Rules). If
                        your claim for damages does not exceed $10,000, the Company will pay all such fees unless the arbitrator finds that either the substance of your claim or the relief sought in your Demand for
                        Arbitration was frivolous or was brought for an improper purpose (as measured by the standards set forth in Federal Rule of Civil Procedure 11(b)). The arbitration may be conducted in person,
                        through the submission of documents, by phone or online. The arbitrator will make a decision in writing, but need not provide a statement of reasons unless requested by a party. The arbitrator
                        must follow applicable law, and any award may be challenged if the arbitrator fails to do so. Except as otherwise provided in this Agreement, you and the Company may litigate in court to compel
                        arbitration, stay proceeding pending arbitration, or to confirm, modify, vacate or enter judgment on the award entered by the arbitrator.</p>
                    <p style="margin: 0px 0px 10px;">
                        EXCEPTIONS TO ALTERNATIVE DISPUTE RESOLUTION. Each party retains the right to bring an individual action in small claims court or to seek injunctive or other equitable relief on an individual
                        basis in a federal or state court in Santa Clara County, California, with respect to any dispute related to the actual or threatened infringement, misappropriation or violation of a party's
                        intellectual property or proprietary rights.</p>
                    <p style="margin: 0px 0px 10px;">
                        WAIVER OF RIGHT TO BE A PLAINTIFF OR CLASS MEMBER IN A PURPORTED CLASS ACTION OR REPRESENTATIVE PROCEEDING. You and the Company agree that any arbitration will be limited to the Dispute between
                        the Company and you individually. YOU ACKNOLWEDGE AND AGREE THAT YOU AND THE COMPANY ARE EACH WAIVING THE RIGHT TO PARTICIPATE AS A PLAINTIFF OR CLASS MEMBER IN ANY PURPORTED CLASS ACTION OR
                        REPRESENTATIVE PROCEEDING. Further, unless both you and the Company otherwise agree, the arbitrator may not consolidate more than one person's claims, and may not otherwise preside over any form
                        of any class or representative proceeding. If this specific paragraph is held unenforceable, then the entirety of this "Dispute Resolution" Section will be deemed null and void.</p>
                    <p style="margin: 0px 0px 10px;">
                        LOCATION OF ARBITRATION. Arbitration will take place in Santa Clara County, California. You and the Company agree that for any Dispute not subject to arbitration (other than claims proceeding in
                        any small claims court), or where no election to arbitrate has been made, the California state and Federal courts located in Santa Clara, California have exclusive jurisdiction and you and the
                        Company agree to submit to the personal jurisdiction of such courts.</p>
                    <br>
                    <b>Governing Law</b>
                    <p style="margin: 0px 0px 10px;">
                        You and the Company agree that, other than as set forth under the subsection entitled "Waiver Of Right To Be A Plaintiff Or Class Member In A Purported Class Action Or Representative Proceeding"
                        above, if any portion of the section entitled "Dispute Resolution" is found illegal or unenforceable, that portion will be severed and the remainder of the section will be given full force and
                        effect. Notwithstanding the foregoing, if the subsection entitled "Exceptions to Alternative Dispute Resolution" above is found to be illegal or unenforceable, neither you nor the Company will
                        elect to arbitrate any Dispute falling within that portion of that subsection that is found to be illegal or unenforceable and such Dispute will be decided by a court of competent jurisdiction
                        within Santa Clara, California, and you and the Company agree to submit to the personal jurisdiction of that court.</p>
                    <p style="margin: 0px 0px 10px;">
                        Except as expressly provided otherwise, this Agreement will be is governed by, and will be construed under, the laws of the State of California, without regard to choice of law principles.</p>
                    <br>
                    <b>Assignment</b>
                    <p style="margin: 0px 0px 10px;">
                        This Agreement may not be assigned by you without the prior written approval of the Company but may be assigned without your consent by the Company to (i) a parent or subsidiary, (ii) an acquirer
                        of assets, or (iii) any other successor or acquirer. Any purported assignment in violation of this section shall be void.</p>
                    <br>
                    <b>General</b>
                    <p style="margin: 0px 0px 10px;">
                        No joint venture, partnership, employment, or agency relationship exists between you, the Company or any third party provider as a result of this Agreement or use of the Service or Software. If
                        any provision of the Agreement is held to be invalid or unenforceable, such provision shall be struck and the remaining provisions shall be enforced to the fullest extent under law. The failure of
                        the Company to enforce any right or provision in this Agreement shall not constitute a waiver of such right or provision unless acknowledged and agreed to by the Company in writing. This Agreement
                        comprises the entire agreement between you and the Company and supersedes all prior or contemporaneous negotiations, discussions or agreements, whether written or oral, between you and the Company
                        regarding the subject matter contained herein.</p>
                    <br>
                    <b>Other Parties</b>
                    <p style="margin: 0px 0px 10px;">
                        You accept that, as a corporation, the Company has an interest in limiting the personal liability of its officers and employees. You agree that you will not bring any claim personally against the
                        Company's officers or employees in respect of any losses you suffer in connection with the Service or Software. Without prejudice to the foregoing, you agree that the limitations of warranties and
                        liability set out in this Agreement will protect the Company's officers, employees, agents, subsidiaries, successors, assigns and sub-contractors as well as the Company.</p>
                    <br>
                    <b>Breaches of these terms and conditions</b>
                    <p style="margin: 0px 0px 10px;">
                        Without prejudice to the Company's other rights under these terms and conditions, if you breach these terms and conditions in any way, the Company may take such action as the Company deems
                        appropriate to deal with the breach, including suspending your access to the Service or Software, prohibiting you from accessing the Service or Software, blocking computers using your IP address
                        from accessing the Service or Software, contacting your internet service provider to request that they block your access to the Service or Software and/or bringing court proceedings against
                        you.</p>


        </div>
        </div>
        <DIV CLASS="col-sm-6 py-3">


            <div class="card-block ">
                <h1>
                    <strong>Privacy Policy</strong></h1>
<br>
                <p>
                    <strong>Last updated: October, 2019</strong></p>

                <p>
                    <strong>Our Policy</strong></p>
                <p>
                    Welcome to the web site (the "Site") of Trino, Inc. (doing business as Cleaner Love). This Site is operated by Cleaner Love and has been created to provide information about our company and the
                    Cleaner Love house cleaning services, whether accessible to you via web, mobile app or other platform (our services, together with the Site, are the "Services") by visitors and users of the Services
                    ("you" and/or "your").</p>
                <p>
                    This Privacy Policy sets out Cleaner Love's policy with respect to information, including in particular information which identifies you personally (known as 'personally identifiable information' in
                    the USA which we'll call "Personal Data") and other information that is collected from visitors and users of the Services.</p>
                <p>
                    Please read this privacy policy carefully so that you understand how we will treat your information.&nbsp; By using any of our Services, you confirm that you have read, understood and agree to this
                    privacy policy.&nbsp; If you do not agree to this policy, please do not use any of the Services.&nbsp; If you have any queries, please email us at info@canbii.com</p>
                <p>
                    <strong>Who we are:</strong></p>
                <p>
                    In the USA, we are Trino, Inc. (doing business as Cleaner Love), a Delaware corporation with our head office at 2711 Centerville Road, Suite 400, Wilmington, New Castle County, Delaware 19808.</p>
                <p>
                    We will refer to these companies together as "Cleaner Love", "we", "us" and/or "our".</p>
                <p>
                    <strong>Our legal status under applicable data privacy laws</strong></p>
                <p>
                    <strong>Information We Collect:</strong></p>
                <p>
                    When you interact with us through the Services, we may collect Personal Data and other information from you, as further described below:</p>
                <p>
                    <strong>Personal Data That You Provide Through the Services:</strong>&nbsp;We collect Personal Data from you when you voluntarily provide such information, such as when you contact us with inquiries,
                    respond to one of our surveys, register for access to the Services or use certain Services, which typically includes your: (i) name; (ii) telephone number; (iii) email address; (iv) home address; (v)
                    information about your home which you give us; (v) your payment details; (iv) your IP address; and (vi) any other personal information which you give us in connection with the Services.</p>
                <p>
                    Wherever Cleaner Love collects Personal Data we make an effort to provide a link to this Privacy Policy.</p>
                <p>
                    <strong>By voluntarily providing us with Personal Data, you are consenting to our use of it in connection with the Services and in accordance with this Privacy Policy.</strong></p>
                <p>
                    <strong>Non-Identifiable Data:</strong>&nbsp;When you interact with Cleaner Love through the Services, we receive and store certain information which does not identify you personally (referred to as
                    personally non-identifiable information in the USA).&nbsp; Such information is collected passively using various technologies.&nbsp; This includes:</p>
                <p>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;i.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Technical or other details about any device which you use to access the Services, including: Internet and/or
                    network connection; your Media Access Control (MAC); any device Unique Device Identifier (UDID) or equivalent; your operating system, browser type or other software; your hardware or mobile device
                    details (including your mobile device type and number and mobile carrier details), if applicable; or other technical details.&nbsp; This is technical data about our users and their actions and
                    patterns, which we do not believe identifies any individual;</p>
                <p>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ii.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Details of your use of our Services including, but not limited to: metrics information about when and how you use
                    the Services; traffic data; and your geographical location data.</p>
                <p>
                    Cleaner Love may store such information itself or such information may be included in databases owned and maintained by Cleaner Love's affiliates, agents or service providers. The Services may use
                    such information and pool it with other information on an anonymised and generalised basis to track, for example, the total number of users of our Services, the number of visitors to each page of our
                    Site and the domain names of our visitors' Internet service providers. It is important to note that no Personal Data is available or used in this process.</p>
                <p>
                    <strong>Platform Communications</strong></p>
                <p>
                    In addition to appointment reminders sent to the email address &amp; phone number provided during booking, you may contact us at info@canbii.com. Additionally, we may provide an optional phone number
                    that connects you with your Cleaning Professional(s).</p>
                <p>
                    By providing your phone number and using the Cleaner Love Platform, you agree that we may, to the extent permitted by applicable law, use your mobile phone number for calls and, if such phone number
                    is a mobile number, for text (SMS) messages, in order to assist with facilitating the requested Professional Services. Standard call or message charges or other charges from your phone carrier may
                    apply to calls or text (SMS) messages we send you. You may opt-out of receiving text (SMS) messages from us by replying with the word "STOP" to a text message from us. You acknowledge that opting out
                    of text (SMS) messages may impact your ability to use the Cleaner Love Platform.
                </p>

                <p>
                    By providing your phone number, you expressly consent that your numbers provided will be used to communicate with between you, Cleaner Love and your customers/cleaners unless and until you opt-out via
                    email to info@canbii.com.
                </p>
                <p>
                    You agree to Cleaner Love's use of a service provider to mask your telephone number when you call or exchange text (SMS) messages with a Service Provider or Service Requestor using a telephone number
                    provided by Cleaner Love. During this process, Cleaner Love and its service provider will receive in real time and store call data, including the date and time of the call or text (SMS) message, the
                    parties’ phone numbers, and the content of the text (SMS) messages. You agree to the masking process described above and to Cleaner Love's use and disclosure of this call data for its legitimate
                    business purposes.
                </p>
                <p>
                    By using the Services, you acknowledge and agree that any incoming or outgoing calls, text SMS messages, and other communications transmitted to or through the Services may be monitored and/or
                    recorded for quality assurance purposes, including but not limited to assisting in the resolution of any disputes you may have with the Services.
                </p>
                <p>
                    <br>

                </p>
                <p>
                    <strong>Use of cookies:</strong></p>
                <p>
                    In operating the Services, we may use a technology called "cookies."</p>
                <p>
                    <br>
                    <strong>Our Use of Your Personal Data and Other Information:</strong></p>
                <p>
                    By providing us with the information about you discussed above, you consent for us and our subsidiaries and affiliates (the "Cleaner Love Related Companies to use that information in the following
                    ways:</p>
                <p>
                    (1) to implement and monitor any Cleaner Love bookings which you make using our Services; (2) to share your Personal Data with Cleaner Love professionals in order to carry out your Cleaner Love
                    bookings using our Serviecs; (3) to ensure that content from our Services is presented in the most effective manner for you and for your computer or other device from which you access the Services;
                    (4) to provide you with information, products or services that you request from us or which we feel may interest you; (5) to carry out our obligations arising from any contracts between you and us;
                    (6) to allow you to participate in interactive features of our Services, when you choose to do so; (7) to notify you about changes to our Services; (8) to improve or modify the Services, for example
                    based on how you use our Services; (9) to calculate conversion rates and other elements of Services' performance; and (10) for marketing purposes (which we discuss further below).</p>
                <p>
                    &nbsp;</p>
                <p>
                    <br>
                    <strong>Our Disclosure of Your Personal Data and Other Information:</strong></p>
                <p>
                    Cleaner Love is not in the business of selling your information. We consider this information to be a vital part of our relationship with you. There are, however, certain circumstances in which we may
                    share your Personal Data with certain third parties, as set out below:</p>
                <p>
                    <strong>Cleaning Services:</strong>&nbsp;We will share your Personal Data with cleaning service providers as necessary for them to provide their cleaning services to you.</p>
                <p>
                    <strong>Business Transfers:</strong>&nbsp;As we develop our business, we might sell or buy businesses or assets. In the event of a corporate sale, merger, reorganization, dissolution or similar event,
                    Personal Data may be part of the transferred assets.</p>
                <p>
                    <strong>Related Companies:</strong>&nbsp;We may also share your Personal Data with the Cleaner Love Related Companies &nbsp;if we need to do so to fulfil this Privacy Policy.</p>
                <p>
                    <strong>Agents, Consultants and Related Third Parties:</strong>&nbsp;Cleaner Love, like many businesses, sometimes hires other companies to perform certain business-related functions. Examples of such
                    functions include mailing information, maintaining databases and processing payments. When we employ another company to perform a function of this nature, we only provide them with the information
                    that they need to perform their specific function and under the same standards and protections as in this privacy policy.</p>
                <p>
                    <strong>Legal Requirements:</strong>&nbsp;Cleaner Love may disclose your Personal Data if required to do so by law or in the good faith belief that such action is necessary to (i) comply with a legal
                    obligation, (ii) protect and defend the rights or property of Cleaner Love, (iii) act in urgent circumstances to protect the personal safety of users of the Services or the public, or (iv) protect
                    against legal liability.</p>
                <p>
                    <strong>Aggregated Personal Data:</strong>&nbsp;In an ongoing effort to better understand and serve the users of the Services, Cleaner Love conducts research on its user demographics, interests and
                    behavior based on the Personal Data and other information provided to us. This research will be compiled and analyzed on an aggregate basis, and Cleaner Love may share this aggregate data with its
                    affiliates, agents and business partners. This aggregate information does not identify you personally. Cleaner Love may also disclose aggregated user statistics in order to describe our Services to
                    current and prospective business partners, and to other third parties for other lawful purposes in fulfilment of this privacy policy.</p>
                <p>
                    <strong>Marketing and advertising:</strong>&nbsp;Cleaner Love and its affiliates may use Personal Data to contact you in the future to tell you about services we believe will be of interest to you. If
                    we do so, each communication we send you will contain instructions permitting you to "opt-out" of receiving future communications. In addition, if at any time you wish not to receive any future
                    communications or you wish to have your name deleted from our mailing lists, please contact us as indicated below.</p>
                <p>
                    <br>
                    We do not disclose personal information about identifiable individuals to advertisers, but we may provide them with aggregate and/or anonymised information about our users to help advertisers reach
                    the kind of audience they want to target. We may make use of the information we have collected from you to enable us to comply with our advertisers' wishes by displaying their advertisement to that
                    target audience.</p>
                <p>
                    <strong>Your Choices:</strong></p>
                <p>
                    You can visit the Services without providing any Personal Data. If you choose not to provide any Personal Data, you may not be able to use certain Cleaner Love Services.</p>
                <p>
                    <strong>Where and how we store your personal information:</strong></p>
                <p>
                    The information that we collect from you will be held in servers in the USA and/or potentially in the European Union.&nbsp; If data is collected from you in the European Union then it may be
                    transferred to, stored and processed at a destination outside the European Economic Area (including the USA).&nbsp; By submitting your personal information, you agree to this transfer, storing or
                    processing. We will take all steps reasonably necessary to ensure that your information is treated securely and in accordance with this privacy policy.<strong></strong></p><strong>
                </strong>
                <p><strong>Unsolicited information and public forums:</strong></p>
                <p>
                    Please be aware that we do not accept unsolicited Personal Data unconnected with the Services and we will delete it as soon as we become aware you have sent us any such Personal Data (unless we are
                    required to keep that Personal Data under this Privacy Policy).</p>
                <p>
                    You are responsible for any public publishing of information including Personal Data (for example on a discussion forum).&nbsp; Please remember that this may mean third parties gaining access to that
                    information, for which you will be responsible.</p>
                <p>
                    <strong>Children:</strong></p>
                <p>
                    Cleaner Love does not knowingly collect Personal Data from children under the age of 13. If you are under the age of 13, please do not submit any Personal Data through the Services. We encourage
                    parents and legal guardians to monitor their children's Internet usage and to help enforce our Privacy Policy by instructing their children never to provide Personal Data on the Services without their
                    permission. If you have reason to believe that a child under the age of 13 has provided Personal Data to Cleaner Love through the Services, please contact us, and we will endeavor to delete that
                    information from our databases.</p>
                <p>
                    <br>
                    <strong>Links to Other Web Sites:</strong></p>
                <p>
                    This Privacy Policy applies only to the Services. The Services may contain links to other web sites not operated or controlled by Cleaner Love (the "Third Party Sites"). The policies and procedures we
                    described here do not apply to the Third Party Sites. The links from the Services do not imply that Cleaner Love endorses or has reviewed the Third Party Sites. We suggest contacting those sites
                    directly for information on their privacy policies.</p>
                <p>
                    <br>
                    <strong>Security:</strong></p>
                <p>
                    Cleaner Love takes reasonable steps to protect the Personal Data provided via the Services from loss, misuse, and unauthorized access, disclosure, alteration, or destruction. However, no Internet or
                    e-mail transmission is ever fully secure or error free; any transmission is at your own risk. In particular, e-mail sent to or from the Services may not be secure. Therefore, you should take special
                    care in deciding what information you send to us via e-mail. Please keep this in mind when disclosing any Personal Data to Cleaner Love via the Internet. Once we have received your information, we
                    will use strict procedures and security features to try to prevent unauthorised access.</p>
                <p>
                    Registered Cleaner Love users will have an account name and password which enables you to access certain parts of our Services.&nbsp; You &nbsp;are responsible for keeping them confidential. Please
                    don't share them with anyone.</p>
                <p>
                    <br>
                    <strong>Other Terms and Conditions:</strong></p>
                <p>
                    Your access to and use of the Services is also subject to the Terms of Use at canbii.com</p>
                <p>
                    <strong>Your rights:</strong></p>
                <p>
                    <em>In the European Union</em></p>
                <p>
                    If you are a European Union citizen, you have the right to ask us not to process your personal information for marketing purposes by contacting us at info@canbii.com.&nbsp; EU data protection
                    legislation gives EU citizens the right to access information held about you.&nbsp; Any access request may be subject to a small administrative fee to meet our costs in providing you with details of
                    the information we hold about you.&nbsp; You may also email us at info@canbii.com to request that we delete your personal information from our database. We will use commercially reasonable efforts to
                    honour your request. We may retain an archived copy of your records as required by law or for legitimate business purposes (and if so we will explain this to you at the time).</p>
                <p>
                    <em>Your California privacy rights</em></p>
                <p>
                    Under California law, California residents who have an established business relationship with us may choose to opt out of disclosure of personal information about them to third parties for direct
                    marketing purposes. If you choose to opt-out at any time after granting approval, email info@canbii.com.</p>
                <p>
                    <br>
                    <strong>Changes to Cleaner Love's Privacy Policy:</strong></p>
                <p>
                    The Services and our business may change from time to time. As a result, at times it may be necessary for Cleaner Love to make changes to this Privacy Policy. Therefore, we may update this privacy
                    policy from time to time (for example, to reflect changes in our business or the law).&nbsp; Any changes we may make will be posted on this page and we will highlight to you any material changes which
                    we make.&nbsp; This Privacy Policy was last updated on the date indicated above. Your continued use of the Services after any changes or revisions to this Privacy Policy shall indicate your agreement
                    with the revised terms.</p>
                <p>
                    <br>
                    <strong>Access to Information; Contacting Cleaner Love:</strong></p>
                <p>
                    To keep your Personal Data accurate, current, and complete, please contact us as specified below. We will take reasonable steps to update or correct Personal Data in our possession that you have
                    previously submitted via the Services.</p>
                <p>
                    Please also feel free to contact us if you have any questions about Cleaner Love’s Privacy Policy or the information practices of the Services.</p>
                <p>
                    You may contact us as follows: info@canbii.com</p>
                <p>
                    &nbsp;</p>
            </div>


        </DIV>
    </DIV>









    @if(false)
        <DIV class="row bg-white">
            <div class="col-sm-12 list-padding list-card">
                <h3 class="mb-2">FAQ</h3>
                <?php
                $minimum = first("SELECT price FROM additional_toppings WHERE size = 'Minimum'")["price"];

                function toclass($text)
                {
                    $text = str_replace('/', '_', $text);
                    $text = strtolower(str_replace(" ", "_", trim(strip_tags($text))));
                    return str_replace(array("?"), "", $text);
                }

                function newID()
                {
                    if (isset($GLOBALS["lastid"])) {
                        $GLOBALS["lastid"] += 1;
                    } else {
                        $GLOBALS["lastid"] = 0;
                    }
                    return lastID();
                }

                function lastID()
                {
                    return "section_" . $GLOBALS["lastid"];
                }

                function newlist($Title)
                {
                    if (isset($GLOBALS["startlist"])) {
                        echo '</UL>';
                    }
                    $GLOBALS["startlist"] = true;
                    echo '<H2>' . $Title . '</H2><UL>';
                }

                function newitem($Title, $Text, $Class = "")
                {
                    $Title = str_replace("[sitename]", sitename, $Title);
                    $Text = str_replace("[sitename]", sitename, $Text);
                    echo '<LI data-toggle="collapse" data-target="#' . newID() . '" ID="item_' . toclass($Title) . '">';
                    echo '<SPAN CLASS="title cursor-pointer ' . $Class . '">' . $Title . '</SPAN></LI>';
                    echo '<div id="' . lastID() . '" class="collapse">' . $Text . '</div>';
                }

                function actionitem($action, $text = '')
                {
                    $actions = actions($action);
                    $parties = ["User", "Admin", "Restaurant"];
                    $tempstr = "";
                    if (!count($actions)) {
                        $tempstr = "<BR>No actions are assigned to this event";
                    }
                    foreach ($actions as $actiond) {
                        $tempstr2 = "The " . $parties[$actiond["party"]] . " is: ";
                        $actione = array();
                        if ($actiond["sms"]) {
                            $actione[] = "texted";
                        }
                        if ($actiond["phone"]) {
                            $actione[] = "called";
                        }
                        if ($actiond["email"]) {
                            $actione[] = "emailed";
                        }
                        $tempstr2 .= join("/", $actione) . ' with the message/subject "' . str_replace("[reason]", '<span class="reason">[reason]</span>', $actiond["message"]) . '"';
                        $tempstr .= '<BR>' . $tempstr2;
                    }
                    newitem($action, 'Occurs when: ' . $text . $tempstr);
                }

                newlist("General");
                newitem("Why can’t I order for pickup?", "We are committed to providing a premier end-to-end customer experience. In order to promote simplicity and ease-of-use, we only provide delivery service at this time. Please check our site regularly for updates on new service offerings.");
                newitem("Can I track my order once submitted?", "Once your order is submitted, it is accepted and confirmed by the choosen " . storename . " immediately. If there are any issues in preparing or delivering your order on time, we will contact you directly. Unfortunately, we do not currently have the ability to track the status of your order while it is being prepared and/or delivered.");
                newitem("How do I know if the " . storename . " has accepted my order?", "All orders placed on [sitename] are instantly confirmed and accepted. You will receive an email receipt of your order details, including the contact information for the " . storename . " fulfilling your order.");
                newitem("What do I do if I need to make changes after submitting an order?", "You will receive an email receipt of your order details, including the contact information for the " . storename . " fulfilling your order. Please contact the restaurant directly by phone should you wish to make any changes.");
                newitem("I never received my order. Who do I contact?", "You may call our support line at [sitename} or call the " . storename . " for immediate assistance.");//Support line?
                newitem("Why can’t I see certain items on the menu?", "Since we use a universal menu, certain items offered by particular " . storenames . " may not be available through our service. Once you receive your order receipt via email, you may contact the restaurant directly should you wish to make any specific additions to your order.");
                newitem("Can I choose the " . storename . " that prepares my order?", "Yes, during check-out you have the ability to choose from any restaurant that is within your delivery range. By default, we choose the " . storename . " closest to your location to fulfill your order.");
                newitem("Can I pay with cash or credit/debit once I receive my order?", "No. Unfortunately, all of our orders require pre-payment via debit/credit card. This allows us to instantly confirm and start preparing orders placed through [sitename].");
                newitem("Do you store my credit card information?", "We do not keep this information on our servers, but rather via our secure payment processing partner: Stripe. It is requested from Stripe when you sign in and stored on your browser, not our servers.");

                newlist("Your Account");
                newitem("Signing in", "Enter your email address and password in the <A HREF='" . webroot("/") . "'>Log In</A> page and click <button class='btn btn-sm " . btncolor . "'>LOG IN</button>");
                newitem("Forgot password", "Enter the email address you registered with, click <button class='btn " . btncolor . " btn-wide btn-sm'>Forgot Password</button> and a new password will be emailed to you");
                newitem("Registering", "Click the 'Signup' tab, enter a valid Hamilton address into the 'Delivery Address' field (use 'Apt/Buzzer' for things like apartment/unit/back door/etc), enter your name/email/password and click <Button class='btn btn-sm " . btncolor . "'>Register</button>");
                newitem('<i class="fa fa-fw fa-bars"></i> button', "A dropdown menu with various options, located in the top-left corner");
                if (read("id")) {
                    newitem('<i class="fa fa-fw fa-user"></i> <SPAN CLASS="session_name"></SPAN>', "A popup to edit your user name/phone number/password/credit card numbers/addresses");
                }
                newitem('<i class="fa fa-fw fa-clock"></i> Past Orders', "A popup that shows a list of your previous orders. Clicking <button class='btn btn-sm " . btncolor . "'>Load Order</button> will overwrite the contents of your cart with that order");
                newitem('<i class="fa fa-fw fa-sign-out-alt"></i> Log Out', "Logs you out and returns to the login/register page");
                newitem('Why do I need an account?', 'The main reason is the convenience of storing your address and phone number for repeat visits. But the secondary reason is our credit card handler (Stripe) requires an account to associate credit card info with');

                newlist('Your Order');
                newitem("Add an item to your cart", "Click the item on the menu. If it has a + next to the price, there will be a popup allowing you to edit the item options before adding it to the receipt");

                if (database == "ai") newitem("Topping/sauces popup", 'If the menu item contains more than 1 item (ie: 2 ' . product . '), there will be a list at the top of this popup to select which item to edit. Clicking any of the options from the list will add it to the selected item. Some options are part of a group and only 1 option in that group can be added to an item (ie: well done and lightly done will conflict, so only 1 can be added to a ' . product . '). The price will update automatically when you add options.<BR><button class="btn btn-sm mt-0 toppings_btn bg-success flat-border"><i class="fa fa-check"></i><SPAN CLASS="pull-right">$X.XX</SPAN></button> will add the item with the options you selected to the receipt.<BR><button class="btn btn-sm bg-success toppings_btn"><i class="fa fa-fw fa-arrow-left"></i></button> will remove the last option added to the selected item, if it is not dimmed');
                newitem("Editing an item in your cart", 'Click <button class="btn-sm"><i class="fa fa-pencil-alt"></i></button> to the right of the item in the receipt, the same popup you used to add the item will appear');
                newitem("Remove an item from your cart", 'Click <button class="btn-sm"><i class="fa fa-minus"></i></button> to the right of the item in the receipt');
                newitem("Duplicating an item in your cart", 'Click <button class="btn-sm"><i class="fa fa-plus"></i></button> to the right of the item in the receipt (if it is a simple item without any addons/toppings)');
                newitem("Empty your cart", 'Click <i class="fa fa-times"></i> at the top-left corner of your receipt');
                newitem('<i class="fa fa-fw fa-shopping-cart"></i> CHECKOUT', "Click this when you're done placing your order. You'll need to enter your <jump>Payment Information</jump>, <jump>Delivery Address</jump>, <jump>Preferred " . ucfirst(storename) . "</jump>, <jump>Delivery Time</jump>, then click <BUTTON CLASS='btn " . btncolor . " btn-sm'>Place order</BUTTON>.<BR>This button will only be visible once your order meets the minumum of: $" . $minimum . " before taxes and delivery", "btn btn-sm btn-block btn-wide " . btncolor);
                newitem("Payment Information", "If you have a saved card (note: Cards are saved with Stripe, not our servers) you can select it from the dropdown, or use 'Add Card' to add a new one. Otherwise just enter your credit card information");
                newitem("Delivery Address", "If you have a saved address you can select it from the dropdown, or select 'Add Address' to add a new address. Otherwise just enter a valid Hamilton address");
                newitem("Preferred " . ucfirst(storename), "Select which " . storename . " you want to recieve your order from");
                newitem("Delivery time", "Leave as 'Deliver Now' to have the store deliver it ASAP. Otherwise they'll try to deliver as close to your selected time as possible.");

                if (read("id") && read("profiletype") > 0) {
                    newlist(ucfirst(storename));
                    newitem("Registering", "You can only register as a regular user. To get escalated to a " . storename . " account requires you to contact an admin at: " . $email);
                    newitem('<i class="fa fa-fw fa-user-plus"></i> Orders List', "Shows a list of orders for your " . storename);
                    newitem("View", "View the contents of the order, a map showing the customer's address, and gives the options to Confirm, Email and Decline the order", "btn btn-sm btn-border btn-wide " . btncolor);
                    newitem("Delete", "Trigger the <jump class='event'>order_declined</jump> event and delete the order from the system", "btn btn-sm btn-border btn-wide " . btncolor);
                    newitem("Confirmed", "Mark the order as confirmed and trigger the <jump class='event'>order_confirmed</jump> event", "btn btn-sm btn-border btn-wide " . btncolor);
                    newitem('<i class="fa fa-fw fa-envelope"></i> Email', "Re-send the receipt to customer via the <jump class='event'>order_placed</jump> event", "btn btn-sm red btn-border btn-wide " . btncolor);
                    newitem("Declined", 'Mark the order as declined and trigger the <jump class="event">order_declined</jump> event', "btn btn-sm btn-border btn-wide");
                    newitem("Delivered", 'Mark the order as delivered and trigger the <jump class="event">order_delivered</jump> event', "btn btn-sm btn-warning btn-border btn-wide");
                    newitem("FILE NOT FOUND", "The order file is missing. Delete the order as the order itself is useless");

                    newlist("Communication Actions");
                    newitem("Editing actions", 'This can only done in <B><i class="fa fa-fw fa-user-plus"></i> Actions list</B>. This tells the system who to contact and how depending on specific events.<BR><SPAN class="reason">[reason]</SPAN> is replaced with the message entered by the ' . storename . '<BR><SPAN class="reason">[name]</SPAN> is replaced with the name of the party<BR><SPAN class="reason">[url]</SPAN> with a link to the receipt that doesn&apos;t require logging in<BR><SPAN class="reason">[sitename]</SPAN> with &apos;' . sitename . '&apos;<BR>and the [tags] must be lower-cased');
                    actionitem("order_placed", "the order is placed");
                    actionitem("order_delivered", 'the <jump class="btn btn-sm btn-warning btn-border no-u">Delivered</jump> button is clicked');
                    actionitem("order_confirmed", 'the <jump class="btn btn-sm btn-primary btn-border no-u">Confirmed</jump> button is clicked');
                    actionitem("order_declined", 'the <jump class="btn btn-sm btn-border no-u">Declined</jump> or <jump class="btn btn-sm btn-danger btn-border no-u">Deleted</jump> buttons are clicked.');
                    actionitem("user_registered", 'a new user is registered. (Since no ' . storename . ' is involved in this event, do not set the party of this event to the Restaurant)');
                    actionitem("cron_job/cron_job_final", 'unconfirmed orders are in the system, waiting for the store to confirm receipt. cron_job_final is for the admin after max_attempts(settings) have been made<BR>[#] is the number of orders<BR>[restaurant] is the name of the' . storename . '<BR>[s] is the s added to the word order if there is more than one order<BR>[from] is a list of the user names who placed an order');

                    if (read("profiletype") == 1) {
                        newlist("Administrators");
                        newitem("Escalating a user account to a " . storename, 'Go to <B><i class="fa fa-fw fa-user-plus"></i> Users list</B>, click the Profiletype column for that user, and click "Restaurant" from the drop-down menu');
                        newitem("Changing the price of a topping or the delivery fee", 'Go to <B><i class="fa fa-fw fa-user-plus"></i> Additional_toppings list</B>, click the price column for that item, then change the text in the text box');
                        newlist('<i class="fa fa-fw fa-user-plus"></i> Edit Menu');
                        if (database == "ai") newitem("Size Costs", "Edit the cost of toppings for each size of " . product . ", and the delivery fee");
                        if (database == "ai") newitem(product . " Toppings/Wing Sauces", "Edit toppings/wing sauces, which category they belong to, if they are free toppings or not, and their group ID # (if the ID # is above 0, only 1 item from this group can be added to a menu item)");
                        newitem("[New Category]", "Add a new menu item category to the list below");
                        newitem("Category list", "Edit menu items. The Toppings/Wings_sauce numbers refer to how many lists of toppings they must select. ie: 2 Toppings would mean they have to select toppings for 2 pizzas");
                    }
                }

                echo '</UL>';
                ?>
            </DIV>
        </DIV>
    @endif

    <!--div class="btn-group" CLASS="dont-show">
    <button id="gototop" class="btn btn-sm btn-primary "><A HREF="#top"><i class="fa fa-arrow-up"></i> Go to the top</A></button>
    <button id="expandall" class="btn btn-sm btn-primary footer" onclick="expandall(true);"><i class="fa fa-expand"></i> Expand all</button>
    <button id="contractall" class="btn btn-sm btn-primary footer" onclick="expandall(false);"><i class="fa fa-compress"></i> Contract all</button>
    <button id="gotobottom" class="btn btn-sm btn-primary footer"><A HREF="#bottom"><i class="fa fa-arrow-down"></i> Go to the bottom</A></button>
    </div-->
@endsection