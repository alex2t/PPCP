<?php 
include_once('Config/Config.php');
include_once('Helpers/PayPalHelper.php');


$data = json_decode(file_get_contents('php://input'), true);


$paypalHelper = new PayPalHelper;

header('Content-Type: application/json');
echo json_encode($paypalHelper->orderCapture($data['orderID']));