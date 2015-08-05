<?php

use Slim\Slim;

/**
 * Suscripcion
 *
 * Clase Suscripcion contiene las funciones propias para crear, listar 
 * borrar, editar y actualizar un Suscripcion 
 * 
 * PHP Version 5.4
 *
 * @category Open Pay
 * @author   Christian Hernandez <christian.hernandez@masnegocio.com>
 * @license  http://www.masnegocio.com Copyright 2015 MasNegocio
 * @method public crear
 * @method public listar
 * @method public eliminar
 * @method public actualizar
 * 
 * @see	SuscripcionDTO
 */

class Suscripcion  {
	use MNTrait\Comun\MagicMethod;
	use MNTrait\Comun\Response;
	
	private $openpay 		= null;
	private $apikeyPrivate	= 'mgvxcww4nbopuaimkkgw';
	private $apikey			= 'sk_e9747dfe55b94e64805221563eb37874';
	private $status			= false;
	private $response		= null;
	private $app			= null;		
	
	function __construct() {
		$this -> openpay = Openpay::getInstance($this -> apikeyPrivate ,$this -> apikey );
		$this -> app = Slim::getInstance();
		$this -> response = $this ->__response();
	}
	
	/**
	  * Crea un Suscripcion implementando la API de OPENPAY 
	  * 
	  * Cuando se crea un Suscripcion se establecen los datos a nivel de cliente,
	  * el Suscripcion es necesario para realizar suscripciones de pago. 
	  * 
	  *
	  * @author Christian Hernandez <christian.hernandez@masnegocio.com>
	  * @version 1.0
	  * @copyright MásNegocio
	  * 
	  * @param	array 	params contiene los datos requerido para la creacion de un Suscripcion.
	  * @throws	OpenpayApiTransactionError Si Falta un campo requerido para la alta de un Suscripcion.
	  * @throws OpenpayApiAuthError si falla la autenticacion, este error debe de acercarce a cero,
	  * 			no deberia de suceder.
	  * @throws OpenpayApiTransactionError Petecion invalida o mal formada, este error debe de acercarce a cero
	  *			no deberia de pasar 			 
	  * @return  
	  * 
	  */
	public function crear($idCliente = "", array $params = array()){
			
		$suscripcionDto = new SuscripcionDTO();
		try {
			$this -> app -> log -> info(print_r("Inicia proceso de alta de Suscripcion",true));
			
			foreach ($params as $key => $value) {
				$suscripcionDto -> __set($key,$value) ;
			}
			
			$this -> app->log->info(print_r("Accion openpay -> suscripcions -> add ",true));
			$customer = $this -> openpay -> customers -> get($idCliente);
			$suscripcion = $customer->subscriptions->add( (array) $suscripcionDto);
			
			foreach ($suscripcionDto as $key => $value) {
				if ($key === 'card_id') 
					continue;
				$suscripcionDto -> $key = $suscripcion -> __get($key);
			}
					
			$this -> response["message"]= "Suscripcion creado con exito";
			$this -> response["body"] 	= $suscripcionDto;
			$this -> response["status"] = "exito";
		} catch (OpenpayApiTransactionError $e) {
			$this -> app -> log -> info(print_r("OpenpayApiTransactionError",true));
			$this -> response["message"]= $e -> getMessage();
			$this -> response["codigo"]	= $e -> getErrorCode();
		} catch (OpenpayApiRequestError $e) {
			$this -> app -> log -> info(print_r("OpenpayApiRequestError",true));
			$this -> response["message"]= $e -> getMessage();
			$this -> response["codigo"]	= $e -> getErrorCode();
		} catch (OpenpayApiAuthError $e) {
			$this -> app -> log -> info(print_r("OpenpayApiAuthError",true));
			$this -> app -> log -> info(print_r($e -> getMessage(),true));
			$this -> response["message"]= "Error de interno del comercio intente mas tarde";
		}
		
	}
	
	/**
	  * Lista los Suscripciones relacionados con el cliente implementando la API de OPENPAY 
	  *
	  * La funcion listar implementa la liberia de OpenPay
	  * la cual obtiene la lista de Suscripciones que estan registrados.
	  * 
	  *
	  * @author Christian Hernandez <christian.hernandez@masnegocio.com>
	  * @version 1.0
	  * @copyright MásNegocio 
	  * 
	  * @param	array 	params contiene los datos requerido generar un filtro de Suscripcions.
	  * @param	idsuscripcion 	es el idetificador del suscripcion , si es null, vacio o no se envia a la funcion
	  *			regresara la lista completa de los suscripciones limitada a 25. 	 
	  * @throws	OpenpayApiTransactionError Si Falta un campo requerido para la alta de un Suscripcion.
	  * @throws OpenpayApiAuthError si falla la autenticacion, este error debe de acercarce a cero,
	  * 			no deberia de suceder.
	  * @throws OpenpayApiTransactionError Petecion invalida o mal formada, este error debe de acercarce a cero
	  *			no deberia de pasar 			 
	  * @return  
	  * 
	  */
	public function listar($idCliente = "" ,$idsuscripcion = '', array $params = array()){
		$suscripcionList = array();
		$suscripcions = array();
		try {
			$this -> app -> log -> info(print_r("Inicializa proceso listar",true));
			$this -> app -> log -> info(print_r("Inicializa proceso listar - buscar Suscripcion = $idsuscripcion",true));
			
			$this -> app->log->info(print_r("Accion openpay -> customers -> get ",true));
			$customer = $this -> openpay -> customers -> get($idCliente);
			if ($idsuscripcion == '' || empty($idsuscripcion)){
				$findData = array(
				    'offset' => 0,
				    'limit' => 5);
			
				$findData = array_merge($findData, $params);
				$suscripcionList = $customer -> subscriptions -> getList($findData); 
			} else {
				$suscripcionList[] =  $customer -> subscriptions -> get($idsuscripcion);
			}
			
			if (count($suscripcionList > 0)) {
				foreach ($suscripcionList as $key => $genericsuscripcion) {
					$SuscripcionDTO = new SuscripcionDTO();
					$SuscripcionDTO -> id 			= $genericsuscripcion -> __get("id");
					$SuscripcionDTO -> creation_date= $genericsuscripcion -> __get("creation_date");
					
					$tmm = $genericsuscripcion -> __get("serializableData");
					foreach ($genericsuscripcion -> __get("serializableData") as $key => $value) {
						$SuscripcionDTO -> $key = $value;
					}
					array_push($suscripcions,$SuscripcionDTO);
				}
			}
			$this -> response["status"] = "exito";
			$this -> response["message"] = "Suscripcion listado con exito";
			$this -> response["body"] = $suscripcions;
			
			$this -> app -> log -> info(print_r("Finalizando proceso listar",true));
		} catch (OpenpayApiTransactionError $e) {
			$this -> app -> log -> info(print_r("OpenpayApiTransactionError",true));
			$this -> response["message"]= $e -> getMessage();
			$this -> response["codigo"]	= $e -> getErrorCode();
		} catch (OpenpayApiRequestError $e) {
			$this -> app -> log -> info(print_r("OpenpayApiRequestError",true));
			$this -> response["message"]= $e -> getMessage();
			$this -> response["codigo"]	= $e -> getErrorCode();
		} catch (OpenpayApiAuthError $e) {
			$this -> app -> log -> info(print_r("OpenpayApiAuthError",true));
			$this -> app -> log -> info(print_r($e -> getMessage(),true));
			$this -> response["message"]= "Error de interno del comercio intente mas tarde";
		}
	}
	
	/**
	  * Elimina el Suscripcion mediante su id implementando la API de OPENPAY 
	  * 
	  * Elimina el Suscripcion previamente registrado, este Suscripcion queda eliminado definitivamente 
	  * no existe opción de restaurar, lo que secedera es dar de alta un nuevo Suscripcion
	  *   
	  * 
	  * @author Christian Hernandez <christian.hernandez@masnegocio.com>
	  * @version 1.0
	  * @copyright MásNegocio
	  * 
	  * @param  $idSuscripcion Id del Suscripcion registro con el cual se procedera a su eliminación.
	  */
	public function eliminar($idCliente, $idSuscripcion){
		try {
			$this -> app -> log -> info(print_r("Inicializando proceso de eliminacion",true));
			$this -> app->log->info(print_r("Suscripcion  a eliminar $idSuscripcion",true));
			$this -> app->log->info(print_r("Accion openpay -> customers -> get ",true));
			$customer = $this -> openpay -> customers -> get($idCliente);
			$this -> app->log->info(print_r("Accion openpay -> suscripcions -> get ",true));
			$suscripcion = $customer -> subscriptions -> get($idSuscripcion);		
			$suscripcion->delete();
			$this -> response["message"] = "Suscripcion Eliminado con exito";
			$this -> response["body"] = $suscripcion;
			$this -> response["status"] = "exito";
			
			$this -> app -> log -> info(print_r("Finalizando proceso de eliminacion",true));
		} catch (OpenpayApiTransactionError $e) {
			$this -> app -> log -> info(print_r("OpenpayApiTransactionError idSuscripcion = $idSuscripcion",true));
			$this -> response["message"]= $e -> getMessage();
			$this -> response["codigo"]	= $e -> getErrorCode();
		} catch (OpenpayApiRequestError $e) {
			$this -> app -> log -> info(print_r("OpenpayApiRequestError idSuscripcion = $idSuscripcion",true));
			$this -> response["message"]= $e -> getMessage();
			$this -> response["codigo"]	= $e -> getErrorCode();
		} catch (OpenpayApiAuthError $e) {
			$this -> app -> log -> info(print_r("OpenpayApiAuthError idSuscripcion = $idSuscripcion",true));
			$this -> app -> log -> info(print_r($e -> getMessage(),true));
			$this -> response["message"]= "Error de interno del comercio intente mas tarde";
		}
		
	}

	/**
	  * Actualizar Suscripcion mediante su id, implementando la API de OPENPAY 
	  * 
	  * Actualizar el Suscripcion previamente registrado, este Suscripcion se actualizara 
	  * en base a los parametros enviados por el request. Una vez actualizado 
	  * no hay forma de revertir los cambios. 
	  *   
	  * 
	  * @author Christian Hernandez <christian.hernandez@masnegocio.com>
	  * @version 1.0
	  * @copyright MásNegocio
	  * 
	  * @param  $idSuscripcion Id del Suscripcion registro con el cual se procedera a su actualización.
	  * @param  $params Contiene los datos que se actualizaran.
	  */
	public function actualizar($idCliente, $idSuscripcion , array $params = array()){
		try {
				$this -> app -> log -> info(print_r("Inicializando proceso de actualización",true));
				$this -> app->log->info(print_r("Suscripcion  a actualizar $idSuscripcion",true));
				$this -> app->log->info(print_r("Accion openpay -> customers -> get ",true));
				$customer = $this -> openpay -> customers -> get($idCliente);
				$this -> app->log->info(print_r("Accion openpay -> suscripcions -> get ",true));
				$suscripcion = $customer -> subscriptions -> get($idSuscripcion);			

				$this -> app->log->info(print_r("Suscripcion -  actualizar - procesando el request",true));
				foreach ($params as $key => $value) {
					$suscripcion -> $key = $value;	
				}
				$this -> app->log->info(print_r("Accion suscripcions -> save ",true));
				$suscripcion->save();
				$this -> response["message"] = "Suscripcion  actualizado con exito";
				$this -> response["status"] = "exito";
				$this -> app -> log -> info(print_r("Finalizando proceso de actualización",true));
		} catch (OpenpayApiTransactionError $e) {
			$this -> app -> log -> info(print_r("OpenpayApiTransactionError idSuscripcion = $idSuscripcion",true));
			$this -> response["message"]= $e -> getMessage();
			$this -> response["codigo"]	= $e -> getErrorCode();
		} catch (OpenpayApiRequestError $e) {
			$this -> app -> log -> info(print_r("OpenpayApiRequestError idSuscripcion = $idSuscripcion",true));
			$this -> response["message"]= $e -> getMessage();
			$this -> response["codigo"]	= $e -> getErrorCode();
		} catch (OpenpayApiAuthError $e) {
			$this -> app -> log -> info(print_r("OpenpayApiAuthError idSuscripcion = $idSuscripcion",true));
			$this -> app -> log -> info(print_r($e -> getMessage(),true));
			$this -> response["message"]= "Error de interno en la actualización intente mas tarde";
		} catch (Exception $e){
			$this -> app -> log -> info(print_r("Exception idSuscripcion = $idSuscripcion",true));
		}
		
	}



}

/**
  * DTO para la Tarjeta
  *
  * Dicha clase genera un DTO para el intercambio de informacion
  *
  * @author Christian Hernandez <christian.hernandez@masnegocio.com>
  * @version 1.0
  * @copyright MásNegocio
  * 
  */
 
class SuscripcionDTO {
	use MNTrait\Comun\MagicMethod;
	
	public $id					= "";
	public $cancel_at_period_end= "";
	public $charge_date	 		= "";
	public $creation_date	 	= "";
	public $current_period_number="";
	public $period_end_date		= "";
	public $trial_end_date		= "";
	public $plan_id				= "";
	public $customer_id			= "";
	//public $card				= "";
	public $card_id				= "";
}

?>