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

require_once dirname(__FILE__).'/Implementacion/Suscripcion.php';

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

$app -> group("/v1/monetizador/", function () use ($app){
	
	$app -> get('test', "ping");	
	
	//Cargos
	$app -> post('cargos', "cargos");
	$app -> get('cargos', "cargos");
	
	
	//Planes
	$app->group('planes', function () use ($app) {
	 	
	 	$app -> post("/", "plan");
        $app -> get('/', "plan");
        $app -> get('/:id', "plan");
        $app -> put('/:id', "plan");
        $app -> delete('/:id', "plan");
	 });
	 
	 //Cliente
	 $app->group('clientes', function () use ($app) {
	 	
		$app -> get("/", "cliente");
		$app -> get("/:id", "cliente");
		$app -> post("/", "cliente");
		$app -> put("/:id", "cliente");
		$app -> delete("/:id", "cliente");
	 	
		//Cargos
		$app -> post('/:id/cargos', "cargos");
		$app -> get('/:id/cargos', "cargos");
		
		//Suscripciones
	 	$app -> post("/:idCliente/suscripciones/", "suscripcion");
        $app -> get('/:idCliente/suscripciones/', "suscripcion");
        $app -> get('/:idCliente/suscripciones/:idSuscripcion', "suscripcion");
        $app -> put('/:idCliente/suscripciones/:idSuscripcion', "suscripcion");
        $app -> delete('/:idCliente/suscripciones/:idSuscripcion', "suscripcion");
		
		//Tarjetas
		$app -> get('/:idCliente/tarjetas/', "tarjeta");
		$app -> get('/:idCliente/tarjetas/:idTarjeta', "tarjeta");
		$app -> post('/:id/tarjetas/', "tarjeta");
		$app -> delete("/:idcliente/tarjetas/:id/", "tarjeta");

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
   * Clientes
   *
   * Los clientes son recursos en Openpay que se manejan dentro de su
   * cuenta de comercio y puede representar usuarios, clientes o socios segun el tipo de negocio.
   *
   * @author Christian Hernandez <christian.hernandez@masnegocio.com>
   * @version 1.0
   * @copyright MásNegocio
   *  
   * @see Class Cliente
   * @param	array()  app -> request() -> params() Estructura que contiene, datos para uar la clase Cargo 	
   * @param string $isCliente Id del cliente a quie se realizara e cargo
   * @throws Exception Se produce una excepcion general en caso de un error
   *		no contralado  
   *  
 */
 
 function cliente($idCliente = "") {
	$app = Slim::getInstance();
	$cliente = new Cliente();
	$response = array();
	
	try{
		$app->log->info("Servicio cliente - Inicializando");
		
		$app->log->info(print_r($app -> request() -> params(),true));
		$dataRequest = $app -> request() -> params();
		
		switch ($app->request->getMethod()) {
		    case "POST":
				$cliente -> crear($dataRequest);		     
		        break;
		    case "GET":
				$cliente -> listar($idCliente, $dataRequest);
		        break;
		    case "PUT":
		        $cliente -> actualizar($idCliente, $dataRequest);
		        break;
			case "DELETE":
		        $cliente -> eliminar($idCliente);
		        break;
		    
		    default:
		        $app -> response -> setStatus(405);
		};
		$response = $cliente -> __get("response");
		$app->log->info("Servicio cliente - Proceso Completo "); 
	} catch (Exception $e){
		$app->log->info("Servicio cliente - Proceso Incompleto ");
		$app->log->info("Servicio cliente - ". $e -> getMessage());
		$response = $cliente -> __get("response");
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

/**********************************************************************************************************/
/**
   * Tarjeta Bancaria
   *
   * La funcion tarjeta usa la clase Tarjeta que contiene la logica para realizar operaciones basicas .
   * 
   * Dentro de la plataforma Openpay podrás agregar tarjetas a la cuenta del cliente, eliminarlas,
   * recuperar alguna en específico y listarlas​.
   * 
   *
   * @author Christian Hernandez <christian.hernandez@masnegocio.com>
   * @version 1.0 20150730 
   * @copyright MásNegocio
   * 
   * @see Class tarjeta 
   * @param	array()  app -> request() -> params() Estructura que contiene, datos para uar la clase Cargo 	
   * @param string $isCliente Id del cliente a quie se realizara e cargo
   * @throws Exception Se produce una excepcion general en caso de un error
   *		no contralado  
   *  
 */
 

 
 function tarjeta( $isCliente, $idTarjeta = "") {
 	$app = Slim::getInstance();
	$tarjeta = new Tarjeta();
	$response = array();
	try{
		$dataRequest = $app -> request() -> params();
		$app -> log -> info("Servicio Tarjeta - Inicializando - ".$app->request->getMethod() );
		$app -> log -> info(print_r($dataRequest ,true));
		switch ($app->request->getMethod()) {
		    case "POST":
				$tarjeta -> crear($isCliente,  $dataRequest);		     
		        break;
		    case "GET":
				$tarjeta -> listar($isCliente, $idTarjeta, $dataRequest);
		        break;
			case "DELETE":
				$tarjeta -> eliminar($isCliente, $idTarjeta, $dataRequest);
		        break;
		    default:
		        $app -> response -> setStatus(405);
		};
		
		$response = $tarjeta -> __get("response");
		$app -> log -> info("Servicio Cargo - Proceso Completo ");
		 
	} catch (Exception $e){
		$app->log->info("Servicio Tarjeta - Proceso Incompleto ");
		$app->log->info("Servicio Tarjeta - ". $e -> getMessage());
		$response = $tarjeta -> __get("response");
		if ($e -> getCode() == 3000){
			$response['message'] = $e -> getMessage();
		}
	}
	
	$jsonStr=json_encode($response);
	$app->log->info("Servicio Tarjeta - Response \n->$jsonStr<-");
	$app->response->headers->set('Content-Type', 'application/json');
	$app->response->headers->set('Access-Control-Allow-Origin', 'http://10.0.70.21');
	
	$app->response->body($jsonStr);
	
	$app->stop();
}

/**
   * Funcion de CArgo a nivel comercio
   *
   * La funcion cargo usa la clase cargo que contiene la logica
   * para crear un cargo.
   * 
   * Los cargos se pueden realizar cargos a tarjetas, tiendas y bancos.
   * A cada cargo se le asigna un identificador único en el sistema.
   * En cargos a tarjeta puedes hacerlo a una tarjeta guardada usando el id de la tarjeta, 
   * usando un token o puedes enviar la información de la tarjeta al momento de la invocación.
   * 
   *
   * @author Christian Hernandez <christian.hernandez@masnegocio.com>
   * @version 1.0 20150730 
   * @copyright MásNegocio
   * 
   * @see Cargo() 
   * @param	array()  app -> request() -> params() Estructura que contiene, datos para uar la clase Cargo 	
   * @param string $isCliente Id del cliente a quie se realizara e cargo
   * @throws Exception Se produce una excepcion general en caso de un error
   *		no contralado  
   *  
 */
 

 
 function cargos( $isCliente = "") {
 	$app = Slim::getInstance();
	$cargo = new Cargo();
	$response = array();
	try{
		$dataRequest = $app -> request() -> params();
		$app -> log -> info("Servicio Cargo - Inicializando - ".$app->request->getMethod() );
		$app -> log -> info(print_r($dataRequest ,true));
		switch ($app->request->getMethod()) {
		    case "POST":
				$cargo -> crear($isCliente, $dataRequest);		     
		        break;
		    case "GET":
				$cargo -> listar($isCliente, $dataRequest);
		        break;
		    default:
		        $app -> response -> setStatus(405);
		};
		
		$response = $cargo -> __get("response");
		$app -> log -> info("Servicio Cargo - Proceso Completo ");
		 
	} catch (Exception $e){
		$app->log->info("Servicio Cargo - Proceso Incompleto ");
		$app->log->info("Servicio Cargo - ". $e -> getMessage());
		$response = $cargo -> get("response");
		if ($e -> getCode() == 3000){
			$response['message'] = $e -> getMessage();
		}
	}
	
	$jsonStr=json_encode($response);
	$app->log->info("Servicio Plan - Response \n->$jsonStr<-");
	$app->response->headers->set('Content-Type', 'application/json');
	$app->response->headers->set('Access-Control-Allow-Origin', 'http://10.0.70.21');
	
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

/**
   * Funcion de suscripcion a nivel cliente
   *
   * La funcion suscripcion usa la clase Suscripcion que contiene la logica
   * para realizar los metodos basicos de una suscripcion.
   * 
   * Las suscripciones permiten asociar un cliente y una tarjeta para que se pueden realizar cargos recurrentes.
   * Para poder suscribir algún cliente es necesario primero crear un plan.
   * 
   *
   * @author Christian Hernandez <christian.hernandez@masnegocio.com>
   * @version 1.0 20150730 
   * @copyright MásNegocio
   * 
   * @see Suscripcion 
   * @param	array()  app -> request() -> params() Estructura que contiene los datos minimos para una suscripcion.
   * @param	String $idSuscripcion Id unico para cada suscripcion.			
   * 
   * @throws Exception Se produce una excepcion general en caso de un error
   *		no contralado  
   *  
 */
 

 
 function suscripcion( $idCliente, $idSuscripcion = "" ) {
 	$app = Slim::getInstance();
	$suscripcion = new Suscripcion();
	$response = array();
	try{
		$dataRequest = $app -> request() -> params();
		$app -> log -> info("Servicio Suscripcion - Inicializando - ".$app->request->getMethod() );
		$app -> log -> info(print_r($dataRequest,true));
		switch ($app->request->getMethod()) {
		    case "POST":
				$suscripcion -> crear($idCliente, $dataRequest);		     
		        $app -> response -> setStatus(201);
		        break;
		    case "GET":
				$suscripcion -> listar($idCliente, $idSuscripcion,$dataRequest);
		        break;
		    case "PUT":
		        $suscripcion -> actualizar($idCliente, $idSuscripcion, $dataRequest);
				$app -> response -> setStatus(201);
		        break;
			case "DELETE":
		        $suscripcion -> eliminar($idCliente, $idSuscripcion);
		        break;
		    
		    default:
		        $app -> response -> setStatus(405);
		};
		$response = $suscripcion -> __get("response");
		
		
		$app -> log -> info("Servicio suscripcion - Proceso Completo "); 
	} catch (\Exception $e){
		$app->log->info("Servicio suscripcion - Proceso Incompleto ");
		$app->log->info("Servicio suscripcion - ". $e -> getMessage());
		$app->log->debug("Servicio suscripcion - ". $e);
		$response = $suscripcion -> __get("response");
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