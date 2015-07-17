<?php
require_once dirname(dirname(dirname(__FILE__))).'/openpay-php/Openpay.php';

try{
	

$openpay = Openpay::getInstance('mgvxcww4nbopuaimkkgw', 'sk_e9747dfe55b94e64805221563eb37874');
error_log("Prueba log");
$customerData = array(
     'external_id' => '1',
     'name' => 'customer name',
     'last_name' => '',
     'email' => 'customer_email@me.com',
     'requires_account' => false,
     'phone_number' => '44209087654',
     'address' => array(
         'line1' => 'Calle 10',
         'line2' => 'col. san pablo',
         'line3' => 'entre la calle 1 y la 2',
         'state' => 'Queretaro',
         'city' => 'Queretaro',
         'postal_code' => '76000',
         'country_code' => 'MX'
      )
   );

$customer = $openpay->customers->add($customerData);
} catch (Exception $e) {
	$msg = sprintf("Descripcion %s, codifo de error %s", $e -> getDescription(), $e -> getErrorCode());
	print_r($msg);
	//print_r($e);
}
?>