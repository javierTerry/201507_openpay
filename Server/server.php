<?php
require_once(dirname(dirname(dirname(__FILE__)))."/dependencies/vendor/autoload.php");
require_once dirname(dirname(dirname(__FILE__))).'/dependencies/MyTrait/MagicMethods.php';
require_once dirname(dirname(__FILE__)).'/Core/Openpay.php';

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
	require_once dirname(dirname(__FILE__)).'/Implementacion/CargoComercio.php';
	$app = Slim::getInstance();
	$response = array('message' => "Error inesperado intente mas tarde"
						,'codigo'	=> 0
						,'status'	=> "fallo"
						);
	try{
		
		$cargo = new CargoComercio();
		$app->log->info(print_r($app -> request() -> params(),true));
		
		$cargo -> send($app -> request() -> params());
		$charge = $cargo -> __get("charge");

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
		
		$app->log->info("Autorizacion $authorization, Fecha de creacion $creation_date");
		$app->log->info("Proceso Compelto "); 
	} catch (Exception $e){
		$msg = sprintf("%s, codigo de error %s  Consulte a su adminsitrador", $e -> getDescription(), $e -> getErrorCode());
		$app->log->info($msg);
	}
	
	$jsonStr=json_encode($response);
	$app->log->info("Servicio pago con tarjeta - Response \n->$jsonStr<-");
	$app->response->headers->set('Content-Type', 'application/json');
	$app->response->body($jsonStr);
	
	$app->stop();
}


$app->run();
?>