<?php
require_once(dirname(dirname(dirname(__FILE__)))."/dependencies/vendor/autoload.php");
require_once dirname(dirname(dirname(__FILE__))).'/openpay-php/Openpay.php';
use \Slim\Slim;


Slim::registerAutoloader();
$app = new Slim();
/*
$app = new \Slim\Slim(
		array(
    		'debug' => TRUE,
    		'log.level' => \Slim\Log::DEBUG,
    		'log.enabled' => TRUE,
    		'log.writer' => new \Slim\Extras\Log\DateTimeFileWriter(array("path" => "../logs/"
			                                                             ))
		)
	);
*/
$app->get('/hello/:name', function ($name) {
    echo "Hello, $name";
});

$app->post('/cargos', "cargos");

function cargos() {
	$app = Slim::getInstance();
	$response = array('message' => "Error inesperado intente mas tarde"
						,'codigo'	=> 0
						,'status'	=> "fallo"
						);
	try{
		
	$openpay = Openpay::getInstance('mgvxcww4nbopuaimkkgw', 'sk_e9747dfe55b94e64805221563eb37874');
	
	
	$chargeData = array(
	    'method' => 'card',
	    'source_id' => "",
	    'amount' => ""
	    ,'description' => ""
	    ,'device_session_id' => "");
	
	$chargeData = array_merge($chargeData,$app -> request() -> params());
	$app->log->info(print_r($chargeData,true));
	$charge = $openpay->charges->create($chargeData);
	
	$authorization 	= $charge ->__get("authorization");
	$creation_date 	= $charge ->__get("creation_date");
	$currency		= $charge ->__get("currency");
	$customer_id	= $charge ->__get("customer_id");
	$operation_type	= $charge ->__get("operation_type");
	$status			= $charge ->__get("status");
	$transaction_type=$charge ->__get("transaction_type");
	
	$response = array(
					'autorizacion' 		=> $authorization
					,'creation_date'	=> $creation_date
					,'currency'			=> $currency
					,'customer_id'		=> $customer_id
					,'operation_type'	=> $operation_type
					,'status'			=> $status
					,'transaction_type'	=> $transaction_type
					);
	
	$app->log->info("Proceso Compelto autorizacion $authorization, Fecha de creacion $creation_date"); 
	} catch (Exception $e){
		$msg = sprintf("%s, codigo de error %s  Consulte a su adminsitrador", $e -> getDescription(), $e -> getErrorCode());
		$app->log->info($msg);		
		$response['message'] = $msg;
		//print_r($e);
	}
	
	$jsonStr=json_encode($response);
	$app->log->info("Servicio pago con tarjeta - Response \n->$jsonStr<-");
	$app->response->headers->set('Content-Type', 'application/json');
	$app->response->body($jsonStr);
	
	$app->stop();
}


$app->run();
?>