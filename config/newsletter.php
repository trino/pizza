<?php
    $MAILCHIMP_LIST_ID = [
        "ai"        => "99bea84729",
        "canbii"    => "4d0c86e78b"
    ];
    $MAILCHIMP_LIST_ID = $MAILCHIMP_LIST_ID[database];
    return [
        // The API key of a MailChimp account. You can find yours at https://us10.admin.mailchimp.com/account/api-key-popup/.
        'apiKey' => "60e052da248f2a0a4289f47e76eb0cdc-us19",

        // The listName to use when no listName has been specified in a method.
        'defaultListName' => 'subscribers',

        // Here you can define properties of the lists.
        'lists' => [
            //This key is used to identify this list. It can be used as the listName parameter provided in the various methods.
            //You can set it to any string you want and you can add as many lists as you want.
            'subscribers' => [
                // A MailChimp list id. Check the MailChimp docs if you don't know how to get this value:
                // http://kb.mailchimp.com/lists/managing-subscribers/find-your-list-id.
                'id' => $MAILCHIMP_LIST_ID,
            ],
        ],

        // If you're having trouble with https connections, set this to false.
        'ssl' => true,
    ];
?>