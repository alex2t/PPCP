<?php

/*
	* Config for PayPal specific values
*/

// Urls
if(isset($_SERVER['SERVER_NAME'])) {
    $url = @($_SERVER["HTTPS"] != 'on') ? 'http://' . $_SERVER["SERVER_NAME"] : 'https://' . $_SERVER["SERVER_NAME"];
    $url .= ($_SERVER["SERVER_PORT"] !== 80) ? ":" . $_SERVER["SERVER_PORT"] : "";
    $url .= $_SERVER["REQUEST_URI"];
}
else {
    $url = "";
}

define("URL", array(

    "current" => $url,

    "services" => array(
        "orderCreate" => 'api/createOrder.php',
        "orderGet" => 'api/getOrderDetails.php',
		"orderPatch" => 'api/patchOrder.php',
		"orderCapture" => 'api/captureOrder.php',
		"verification3d"=> 'api/verification.php'
    ),

	"redirectUrls" => array(
        "returnUrl" => 'pages/success.php',
		"cancelUrl" => 'pages/cancel.php',
    )
));

// PayPal Environment 
define("PAYPAL_ENVIRONMENT", "sandbox");

// PayPal REST API endpoints
define("PAYPAL_ENDPOINTS", array(
	"sandbox" => "https://api-m.sandbox.paypal.com",
	"production" => "https://api.paypal.com"
));

// PayPal REST App credentials
//App Pro
//email merchant alexuk.seller@outlook.com
define("PAYPAL_CREDENTIALS", array(
	"sandbox" => [
		"client_id" => "AdUXfl1sBpAcilku_H0KZXPjLCas-FXlCl95n5ILs0rUs0lC_O-zom_TU-wcZC48ZFOgPCCQYjPQ_R5q",
		"client_secret" => "EPav8MSHtb7NyxdQEQDLZcdAR0YMAZqGlIXg0OnEWYImTckgSKAywtrciUwCEfmi-92beQVowI4SlJc5"
	],
	"production" => [
		"client_id" => "",
		"client_secret" => ""
	]
));



// PayPal REST API version
define("PAYPAL_REST_VERSION", "v2");

