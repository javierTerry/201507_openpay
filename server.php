<?php
/**
  * Server Api
  *
  * Esta API-REST contiene los recursos las implementaciones de openpay,
  * dentro de las cuales tenemos recursos de comercio y de cliente - comercio.
  *  
  * PHP Version 5.4
  * 
  * @author Christian Hernandez <christian.hernandez@masnegocio.com>
  * @version 1.0
  * @copyright MásNegocio 
  * 
  * 
*/

require_once(dirname(dirname(__FILE__))."/dependencies/vendor/autoload.php");
require_once dirname(__FILE__).'/Core/Openpay.php';
require_once dirname(__FILE__).'/Implementacion/Cargo.php';
require_once dirname(__FILE__).'/Implementacion/Tarjeta.php';
require_once dirname(__FILE__).'/Implementacion/Cliente.php';
require_once dirname(__FILE__).'/Implementacion/Plan.php';

use Slim\Slim;	
use Slim\Exception;

Slim::registerAutoloader();
$app = new Slim(
					array(
			    		'debug' 			=> true,
			    		'log.level' 		=> \Slim\Log::DEBUG,
			    		'log.enabled' 		=> true,
			    		'log.writer' 		=> new \Slim\LogWriter(fopen(dirname(__FILE__).'/log/'.date('Y-M-d').'.log', 'a'))
					)
				);

$app -> get('/v1/monetizador/test', "ping");
//Cargos
$app -> get('/v1/monetizador/cargos', "cargos");
$app -> post('/v1/monetizador/cargos', "cargos");
$app -> post('/v1/monetizador/cargos/clientes:/id', "cargos");

//Tarjetas
$app -> get('/v1/monetizador/tarjetas', "cards");
$app -> post('/v1/monetizador/tarjetas', "cardAdd");
$app -> delete("/v1/monetizador/tarjetas/:id", "cardDelete");
// Cliente
$app -> post("/v1/monetizador/clientes", "cliente");
$app -> get("/v1/monetizador/clientes", "clienteListar");
$app -> get("/v1/monetizador/clientes/:id", "clienteListar");
$app -> delete("/v1/monetizador/clientes/:id", "clienteEliminar");
$app -> put("/v1/monetizador/clientes/:id", "clienteEditar");

// Cliente - Tarjeta
$app -> post('/v1/monetizador/tarjetas/clientes/:id', "cardAdd");
$app -> get('/v1/monetizador/tarjetas/clientes/:id', "cards");
$app -> delete("/v1/monetizador/clientes/:idcliente/tarjetas/:id", "cardDelete");


// Plan
//$app -> post("/v1/monetizador/planes", "plan");
/*
$app -> get("/v1/monetizador/planes", "clienteListar");
$app -> get("/v1/monetizador/planes/:id", "clienteListar");
$app -> delete("/v1/monetizador/planes/:id", "clienteEliminar");
$app -> put("/v1/monetizador/planes/:id", "clienteEditar");
*/
$app -> group("/v1/monetizador/", function () use ($app){
	 $app->group('planes', function () use ($app) {
	 	
	 	$app -> post("/", "plan");
        $app -> get('/', "plan");
        $app -> get('/:id', "plan");
        $app -> put('/:id', "plan");
        $app -> delete('/:id', "plan");
	 });
});

$app->run();
  /**
   * Recurso test 
   *
   * El recurso Test es una funcion que verifica que la conectividad de los servicio
   * este disponible
   *
   * @author Christian Hernandez <christian.hernandez@masnegocio.com>
   * @version 1.0
   * @copyright MásNegocio
   * @source
   */
 function ping() {
    $app = Slim::getInstance();
	$app->log->info("Servicio test Inicializando");
	$response = array('status' => 'exito'
					,'message' => 'Servicio Monetizador activo'
				 );
	$jsonStr=json_encode($response);
	$app->log->info("Servicio test - Response \n->$jsonStr<-");
	$app->response->headers->set('Content-Type', 'application/json');
	$app->response->body($jsonStr);
	$app->log->info("Servicio test Finalizando");
	$app->stop();
}

/**
   * Funcion de clientes a nivel comercio
   *
   * La funcion cargos implementa la CargoComercio, la cual
   * contiene la logica de openpay para poder realizar la via
   * API-OpenOPay.
   *
   * @author Christian Hernandez <christian.hernandez@masnegocio.com>
   * @version 1.0
   * @copyright MásNegocio
   *  
   *  
 */
 
 function cliente() {
	$app = Slim::getInstance();
	try{
		$app->log->info("Servicio cliente - Inicializando");
		$cliente = new Cliente();
		$app->log->info(print_r($app -> request() -> params(),true));
		$cliente -> crear($app -> request() -> params());
		$response = $cliente -> __get("response");
		$app->log->info("Servicio cliente - Proceso Completo "); 
	} catch (Exception $e){
		$app->log->info("Servicio cliente - Proceso Incompleto ");
		$app->log->info("Servicio cliente - ". $e -> getMessage());
		$response = $cliente -> __response();
		if ($e -> getCode() == 3000){
			$response['message'] = $e -> getMessage();
		}
		
		$app->log->info(print_r($response,true));
	}
	
	$jsonStr=json_encode($response);
	$app->log->info("Servicio cliente - Response \n->$jsonStr<-");
	$app->response->headers->set('Content-Type', 'application/json');
	$app->response->body($jsonStr);
	
	$app->stop();
}
 
 /**
   * Funcion de clienteListar a nivel comercio
   *
   * La funcion clienteListar se implementa a nivel Comercio, la cual
   * obtiene una lista de usuarios previamente registrado o un usuario especifico
   * , implementa la libreria de API-OpenOPay.
   *
   * @author Christian Hernandez <christian.hernandez@masnegocio.com>
   * @version 1.0
   * @copyright MásNegocio
   *  
   * @param $idCustomer	si el valor no existe o es blanco se obtendra 
   * 	la lista de usuarios registrados, en caso contrario regresara 
   *	el usuario especificado 
   *  
 */
 
 function clienteListar($idCustomer = "") {
	$app = Slim::getInstance();
	try{
		$app->log->info("Servicio cliente - Inicializando", array ("test" => "caeena test"));
		$cliente = new Cliente();
		$cliente -> listar($idCustomer, $app -> request() -> params());
			$response = $cliente -> __get("response");
		$app->log->info("Servicio cliente - Proceso Completo "); 
		$app->response->setStatus(201);
	} catch (Exception $e){
		$app -> log -> info("Servicio cliente - Proceso Incompleto ");
		$app -> log -> info("Servicio cliente - ". $e -> getMessage());
		$response = $cliente -> __response();
		if ($e -> getCode() == 3000){
			$response['message'] = $e -> getMessage();
		}
		
		$app->log->info(print_r($response,true));
		$app->response->setStatus(400);
	}
	
	$jsonStr=json_encode($response);
	$app->log->info("Servicio cliente - Response \n->$jsonStr<-");
	$app->response->headers->set('Content-Type', 'application/json');
	$app->response->body($jsonStr);
	
	$app->stop();
}

/**
   * Funcion de clienteEliminar a nivel comercio
   *
   * La funcion clienteEliminar se implementa a nivel Comercio, la cual
   * elimina un cliente registrado previamente, utiliza la clase Cliente.
   *
   * @author Christian Hernandez <christian.hernandez@masnegocio.com>
   * @version 1.0
   * @copyright MásNegocio
   *  
   * @param $idCustomer	es el Id del cliente a eliminar
   *  
 */
 
 function clienteEliminar($idCustomer = "") {
	$app = Slim::getInstance();
	try{
		$app->log->info("Servicio cliente - Eliminar - Inicializando");
		$cliente = new Cliente();
		$cliente -> eliminar($idCustomer);
		$response = $cliente -> __get("response");
		$app->log->info("Servicio cliente - Eliminar - Proceso Completo "); 
		$app->response->setStatus(204);
	} catch (Exception $e){
		$app -> log -> info("Servicio cliente - Eliminar - Proceso Incompleto ");
		$app -> log -> info("Servicio cliente - Eliminar - ". $e -> getMessage());
		$response = $cliente -> __response();
		if ($e -> getCode() == 3000){
			$response['message'] = $e -> getMessage();
		}
		
		$app->log->info(print_r($response,true));
		//$app->response->setStatus(400);
	}
	
	
	$jsonStr=json_encode($response);
	$app->log->info("Servicio cliente - Eliminar - Response \n->$jsonStr<-");
	$app->response->headers->set('Content-Type', 'application/json');
	$app->response->body($jsonStr);
	
	$app->stop();
}
 
/**
   * Funcion de clienteEditar a nivel comercio
   *
   * La funcion clienteEditar se implementa a nivel Comercio, la cual
   * actualiza un cliente registrado previamente, utiliza la clase Cliente.
   *
   * @author Christian Hernandez <christian.hernandez@masnegocio.com>
   * @version 1.0
   * @copyright MásNegocio
   *  
   * @param $idCustomer	es el Id del cliente que se actualizara
   *  
 */
 
 function clienteEditar($idCustomer = "") {
	$app = Slim::getInstance();
	try{
		$app->log->info("Servicio cliente - Editar - Inicializando");
		$cliente = new Cliente();
		$cliente -> actualizar($idCustomer, $app -> request() -> params());
		$response = $cliente -> __get("response");
		$app->log->info("Servicio cliente - Editar - Proceso Completo "); 
		$app->response->setStatus(204);
	} catch (Exception $e){
		$app -> log -> info("Servicio cliente - Editar - Proceso Incompleto ");
		$app -> log -> info("Servicio cliente - Editar - ". $e -> getMessage());
		$response = $cliente -> __response();
		if ($e -> getCode() == 3000){
			$response['message'] = $e -> getMessage();
		}
		
		$app->log->info(print_r($response,true));
		//$app->response->setStatus(400);
	}
	
	$jsonStr=json_encode($response);
	$app->log->info("Servicio cliente - Editar - Response \n->$jsonStr<-");
	$app->response->headers->set('Content-Type', 'application/json');
	$app->response->body($jsonStr);
	
	$app->stop();
}
 
 /**
   * Funcion de cargos a nivel comercio
   *
   * La funcion cargos implementa la CargoComercio, la cual
   * contiene la logica de openpay para poder realizar la via
   * API-OpenOPay.
   *
   * @author Christian Hernandez <christian.hernandez@masnegocio.com>
   * @version 1.0
   * @copyright MásNegocio
   *  
   *  
 */
function cargos($customerId = "argmzwukbogwrs9pw3m7") {
	$app = Slim::getInstance();
	try{
		
		$cargo = new Cargo();
		$app->log->info(print_r($app -> request() -> params(),true));
		$cargo -> crear($app -> request() -> params(), $customerId);
		$response = $cargo -> __get("response");
		$app->log->info("Proceso Compelto "); 
	} catch (Exception $e){
		$response = $cargo -> __response();
		$app->log->info(print_r($response,true));
	}
	
	$jsonStr=json_encode($response);
	$app->log->info("Servicio pago con tarjeta - Response \n->$jsonStr<-");
	$app->response->headers->set('Content-Type', 'application/json');
	$app->response->body($jsonStr);
	
	$app->stop();
}

/**
  * Funcion de cards a nivel comercio
  *
  * La funcion cargos implementa la CargoComercio, la cual
  * contiene la logica de openpay para poder realizar la via
  * API-OpenOPay.
  *
  * @author Christian Hernandez <christian.hernandez@masnegocio.com>
  * @version 1.0
  * @copyright MásNegocio
  * @param $idCliente  valor del cliente registro
  * 
  */
function cards($idCliente = null) {
	$app = Slim::getInstance();
	$response = array('message' => "Error inesperado intente mas tarde"
						,'codigo'	=> 0
						,'status'	=> "fallo"
						);
	try{
		
		$tarjeta = new Tarjeta();
		$tarjeta -> listar($idCliente, $app -> request() -> params());
		$response = $tarjeta -> __get("cardList");
		$app->log->info("Proceso Compelto "); 
	}catch (OpenpayApiRequestError $e){
		$app->log->info(print_r("OpenpayApiRequestError",true));
		$response = array('message' => $e -> getDescription()
						,'codigo'	=> $e -> getErrorCode()
						,'status'	=> "fallo"
						);
	} catch (Exception $e){
		$app->log->info(print_r($e,true));
		$msg = sprintf("%s, codigo de error %s  Consulte a su adminsitrador", $e -> getDescription(), $e -> getErrorCode());
		$app->log->info($msg);
	}
	
	$jsonStr=json_encode($response);
	$app->log->info("Servicio pago con tarjeta - Response \n->$jsonStr<-");
	$app->response->headers->set('Content-Type', 'application/json');
	$app->response->body($jsonStr);
	
	$app->stop();
}

/**
  * Funcion de cardAdd agrega una Tarjeta 
  *
  * La funcion cardAdd implementa Tarjeta, la cual contiene la 
  * logica de API-OpenOPay para agregar una tarjeta al comerio o el cliente
  * 
  *
  * @author Christian Hernandez <christian.hernandez@masnegocio.com>
  * @version 1.0
  * @copyright MásNegocio
  * @param $idCliente  valor del cliente registro
  * 
  */
function cardAdd($idCliente = null) {
	$app = Slim::getInstance();
	$app->log->info("Servicio crear tarjeta Inicializando");
	$response = array('message' => "Error inesperado intente mas tarde"
						,'codigo'	=> 0
						,'status'	=> "fallo"
						);
	try{
		$app->log->info(print_r($app -> request() -> params(),true));
		$tarjeta = new Tarjeta();
		$tarjeta -> crear($idCliente, $app -> request() -> params());
		$response = $tarjeta -> __get("card");
		$app->log->info("Proceso Completo ");
	}catch (OpenpayApiTransactionError $e){
		$app->log->info(print_r("OpenpayApiRequestError",true));
		$response = array('message' => $e -> getDescription()
						,'codigo'	=> $e -> getErrorCode()
						,'status'	=> "fallo"
						);
	}catch (OpenpayApiRequestError $e){
		$app->log->info(print_r("OpenpayApiRequestError",true));
		$response = array('message' => $e -> getDescription()
						,'codigo'	=> $e -> getErrorCode()
						,'status'	=> "fallo"
						);
	} catch (Exception $e){
		$app->log->info(print_r($e,true));
		$msg = sprintf("%s, codigo de error  Consulte a su adminsitrador", $e -> getMessage());
		$app->log->info($msg);
	}
	
	$jsonStr=json_encode($response);
	$app->log->info("Servicio crear tarjeta Finalizando- Response \n->$jsonStr<-");
	$app->response->headers->set('Content-Type', 'application/json');
	$app->response->body($jsonStr);
	
	$app->stop();
}

/**
  * Funcion de cardDelete elimina una Tarjeta 
  *
  * La funcion cardDelete elimana una tarjeta previamente registrada 
  * esta accion se puede realizar a nivel comercio o cliente - comercio
  * para deciha accion lo unico que se necesita es 
  *
  * @author Christian Hernandez <christian.hernandez@masnegocio.com>
  * @version 1.0
  * @copyright MásNegocio
  * @param $idCliente  valor del registro del cliente
  * @param $idTarjeta Id de la tarjeta a borrar
  * 
  */
function cardDelete( $idTarjeta = null, $idCliente = null) {
	$app = Slim::getInstance();
	$app->log->info("Servicio crear tarjeta Inicializando");
	$response = array('message' => "Error inesperado intente mas tarde"
						,'codigo'	=> 0
						,'status'	=> "fallo"
						);
	try{
		$app->log->info(print_r($app -> request() -> params(),true));
		$tarjeta = new Tarjeta();
		$tarjeta -> borrar($idTarjeta, $idCliente, $app -> request() -> params());
		$response = array('message' => "Borrado exitoso"
						,'codigo'	=> 0
						,'status'	=> "exito"
						,'body'		=> array()
						);
		$app->log->info("Proceso Completo ");
	}catch (OpenpayApiTransactionError $e){
		$app->log->info(print_r("OpenpayApiTransactionError",true));
		$response = array('message' => $e -> getDescription()
						,'codigo'	=> $e -> getErrorCode()
						,'status'	=> "fallo"
						);
	}catch (OpenpayApiRequestError $e){
		$app->log->info(print_r("OpenpayApiRequestError",true));
		$response = array('message' => $e -> getDescription()
						,'codigo'	=> $e -> getErrorCode()
						,'status'	=> "fallo"
						);
	} catch (Exception $e){
		$app->log->info(print_r($e,true));
		$msg = sprintf("%s, codigo de error  Consulte a su adminsitrador", $e -> getMessage());
		$app->log->info($msg);
	}
	
	$jsonStr=json_encode($response);
	$app->log->info("Servicio crear tarjeta Finalizando- Response \n->$jsonStr<-");
	$app->response->headers->set('Content-Type', 'application/json');
	$app->response->body($jsonStr);
	
	$app->stop();
}


/**
   * Funcion de plan a nivel comercio
   *
   * La funcion plan usa la clase Plan que contiene la logica
   * para crear un plan.
   * 
   * El plan es una platilla con la que se ligara una suscripcion 
   * para realizar pagos recurrentes  
   * 
   *
   * @author Christian Hernandez <christian.hernandez@masnegocio.com>
   * @version 1.0 20150730 
   * @copyright MásNegocio
   * 
   * @see Plan::crear() 
   * @param	array()  app -> request() -> params() Estructura qeu contiene
   * 		necesaria para crea un plan	
   * 
   * @throws Exception Se produce una excepcion general en caso de un error
   *		no contralado  
   *  
 */
 

 
 function plan( $isPlan = "") {
 	$app = Slim::getInstance();
	$plan = new Plan();
	$response = array();
	try{
		$planDataRequest = $app -> request() -> params();
		$app -> log -> info("Servicio Plan - Inicializando - ".$app->request->getMethod() );
		$app -> log -> info(print_r($planDataRequest,true));
		switch ($app->request->getMethod()) {
		    case "POST":
				
				$plan -> crear($planDataRequest);		     
		        break;
		    case "GET":
				$plan -> listar($isPlan, $planDataRequest);
		        break;
		    case "PUT":
		        $plan -> actualizar($isPlan, $planDataRequest);
		        break;
			case "DELETE":
		        $plan -> eliminar($isPlan);
		        break;
		    
		    default:
		        $app -> response -> setStatus(405);
		};
		$response = $plan -> __get("response");
		
		
		$app -> log -> info("Servicio plan - Proceso Completo "); 
	} catch (Exception $e){
		$app->log->info("Servicio Plan - Proceso Incompleto ");
		$app->log->info("Servicio Plan - ". $e -> getMessage());
		$response = $plan -> get("response");
		if ($e -> getCode() == 3000){
			$response['message'] = $e -> getMessage();
		}
	}
	
	$jsonStr=json_encode($response);
	$app->log->info("Servicio Plan - Response \n->$jsonStr<-");
	$app->response->headers->set('Content-Type', 'application/json');
	$app->response->body($jsonStr);
	
	$app->stop();
}

?>