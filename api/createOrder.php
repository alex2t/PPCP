<?php 

include_once('Config/Config.php');
include_once('Helpers/PayPalHelper.php');

$paypalHelper = new PayPalHelper;

$orderData = '{
    "intent": "CAPTURE",
    "payer": {
        "name": {
            "given_name": "PayPal",
            "surname": "Customer"
        },
        "address": {
            "address_line_1": "Via Palermo 1",
            "admin_area_2": "Brannenburg",
            "admin_area_1": "Freistaat Bayern",
            "postal_code": "83095",
            "country_code": "FR"
        }
        
    },
    "purchase_units": [
        {
            "reference_id": "1",
            "amount": {
                "currency_code": "EUR",
                "value": "50.00",
                "breakdown": {
                    "item_total": {
                        "currency_code": "EUR",
                        "value": "40.00"
                    },
                    "shipping": {
                        "currency_code": "EUR",
                        "value": "10.00"
                    }
                }
            },
            "items": [
                {
                    "name": "box",
                    "quantity": "1",
                    "unit_amount": {
                        "currency_code": "EUR",
                        "value": "40.00"
                    }
                }
            ],
            "shipping": {
                "name": {
                    "full_name": "herbert lawn"
                },
                "address": {
                    "address_line_1": "my street 1",
                    "admin_area_1": "my state",
                    "admin_area_2": "my town",
                    "postal_code": "12345678",
                    "country_code": "FR"
                }
            },
            "description": "Payment for order",
            "custom_id": "1234567890"
        }
    ]
}';




header('Content-Type: application/json');
echo json_encode($paypalHelper->orderCreate($orderData));