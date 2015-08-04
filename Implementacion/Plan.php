<?php

use Slim\Slim;

/**
 * Plan
 *
 * Clase Plan contiene las funciones propias para crear, listar 
 * borrar, editar y actualizar un Plan 
 * 
 * PHP Version 5.4
 *
 * @category Open Pay
 * @author   Christian Hernandez <christian.hernandez@masnegocio.com>
 * @license  http://www.masnegocio.com Copyright 2015 MasNegocio
 * @method public crear
 * @method public listar
 * 
 * @see	PlanDTO
 */

class Plan  {
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
	  * Crea un Plan implementando la API de OPENPAY 
	  * 
	  * Cuando se crea un Plan se establecen los datos a nivel de comercio,
	  * el Plan es necesario para realizar planes de pago. 
	  * 
	  *
	  * @author Christian Hernandez <christian.hernandez@masnegocio.com>
	  * @version 1.0
	  * @copyright MásNegocio
	  * 
	  * @param	array 	params contiene los datos requerido para la creacion de un Plan.
	  * @throws	OpenpayApiTransactionError Si Falta un campo requerido para la alta de un Plan.
	  * @throws OpenpayApiAuthError si falla la autenticacion, este error debe de acercarce a cero,
	  * 			no deberia de suceder.
	  * @throws OpenpayApiTransactionError Petecion invalida o mal formada, este error debe de acercarce a cero
	  *			no deberia de pasar 			 
	  * @return  
	  * 
	  */
	public function crear(array $params = array()){
			
		$planDto = new PlanDTO();
		try {
			$this -> app -> log -> info(print_r("Inicia proceso de alta de Plan",true));
			
			foreach ($params as $key => $value) {
				$planDto -> __set($key,$value) ;
			}
			
			$this -> app->log->info(print_r("Accion openpay -> plans -> add ",true));
			
			$plan = $this -> openpay -> plans -> add( (array) $planDto );
			$planDto -> id	 		= $plan -> __get("id");
			$planDto -> creation_date=$plan -> __get("creation_date");
			
			$this -> response["message"]= "Plan creado con exito";
			$this -> response["body"] 	= $planDto;
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
	  * Lista los Planes relacionados con el comercio 
	  *
	  * La funcion listar implementa la liberia de OpenPay
	  * la cual obtiene la lista de Planes que estan registrados.
	  * 
	  *
	  * @author Christian Hernandez <christian.hernandez@masnegocio.com>
	  * @version 1.0
	  * @copyright MásNegocio 
	  * 
	  * @param	array 	params contiene los datos requerido generar un filtro de Plans.
	  * @param	idplan 	es el idetificador del plan , si es null, vacio o no se envia a la funcion
	  *			regresara la lista completa de los planes limitada a 25. 	 
	  * @throws	OpenpayApiTransactionError Si Falta un campo requerido para la alta de un Plan.
	  * @throws OpenpayApiAuthError si falla la autenticacion, este error debe de acercarce a cero,
	  * 			no deberia de suceder.
	  * @throws OpenpayApiTransactionError Petecion invalida o mal formada, este error debe de acercarce a cero
	  *			no deberia de pasar 			 
	  * @return  
	  * 
	  */
	public function listar($idplan = '', array $params = array()){
		$planList = array();
		$plans = array();
		try {
			$this -> app -> log -> info(print_r("Inicializa proceso listar",true));
			
			$this -> app -> log -> info(print_r("Inicializa proceso listar - buscar Plan = $idplan",true));
			if ($idplan == '' || empty($idplan)){
				$findDataRequest = array(
			    'offset' => 0,
			    'limit' => 25);
			
				$findDataRequest = array_merge($findDataRequest, $params);
				$planList = $this -> openpay -> plans -> getList($findDataRequest); 
			} else {
				$planList[] = $this -> openpay -> plans -> get($idplan);
			}
			
			if (count($planList > 0)) {
				foreach ($planList as $key => $genericplan) {
					$PlanDTO = new PlanDTO();
					$PlanDTO -> id 			= $genericplan -> __get("id");
					$PlanDTO -> creation_date= $genericplan -> __get("creation_date");
					
					$tmm = $genericplan -> __get("serializableData");
					foreach ($genericplan -> __get("serializableData") as $key => $value) {
						$PlanDTO -> $key = $value;
					}
					array_push($plans,$PlanDTO);
				}
			}
			$this -> response["status"] = "exito";
			$this -> response["message"] = "Plan listado con exito";
			$this -> response["body"] = $plans;
			
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
	  * Elimina el Plan mediante su id implementando la API de OPENPAY 
	  * 
	  * Elimina el Plan previamente registrado, este Plan queda eliminado definitivamente 
	  * no existe opción de restaurar, lo que secedera es dar de alta un nuevo Plan
	  *   
	  * 
	  * @author Christian Hernandez <christian.hernandez@masnegocio.com>
	  * @version 1.0
	  * @copyright MásNegocio
	  * 
	  * @param  $idPlan Id del Plan registro con el cual se procedera a su eliminación.
	  */
	public function eliminar($idPlan = ""){
		try {
			$this -> app -> log -> info(print_r("Inicializando proceso de eliminacion",true));
			$this -> app->log->info(print_r("Plan  a eliminar $idPlan",true));
			$plan = $this -> openpay -> plans -> get($idPlan);
			$plan->delete();
			$this -> response["message"] = "Plan Eliminado con exito";
			$this -> response["body"] = $plan;
			$this -> response["status"] = "exito";
			
			$this -> app -> log -> info(print_r("Finalizando proceso de eliminacion",true));
		} catch (OpenpayApiTransactionError $e) {
			$this -> app -> log -> info(print_r("OpenpayApiTransactionError idPlan = $idPlan",true));
			$this -> response["message"]= $e -> getMessage();
			$this -> response["codigo"]	= $e -> getErrorCode();
		} catch (OpenpayApiRequestError $e) {
			$this -> app -> log -> info(print_r("OpenpayApiRequestError idPlan = $idPlan",true));
			$this -> response["message"]= $e -> getMessage();
			$this -> response["codigo"]	= $e -> getErrorCode();
		} catch (OpenpayApiAuthError $e) {
			$this -> app -> log -> info(print_r("OpenpayApiAuthError idPlan = $idPlan",true));
			$this -> app -> log -> info(print_r($e -> getMessage(),true));
			$this -> response["message"]= "Error de interno del comercio intente mas tarde";
		}
		
	}

	/**
	  * Actualizar Plan mediante su id, implementando la API de OPENPAY 
	  * 
	  * Actualizar el Plan previamente registrado, este Plan se actualizara 
	  * en base a los parametros enviados por el request. Una vez actualizado 
	  * no hay forma de revertir los cambios. 
	  *   
	  * 
	  * @author Christian Hernandez <christian.hernandez@masnegocio.com>
	  * @version 1.0
	  * @copyright MásNegocio
	  * 
	  * @param  $idPlan Id del Plan registro con el cual se procedera a su actualización.
	  * @param  $params Contiene los datos que se actualizaran.
	  */
	public function actualizar($idPlan = "", array $params = array()){
		try {
				$this -> app -> log -> info(print_r("Inicializando proceso de actualización",true));
				$this -> app->log->info(print_r("Plan  a actualizar $idPlan",true));
				$this -> app->log->info(print_r("Accion openpay -> plans -> get ",true));
				$plan = $this -> openpay -> plans -> get($idPlan);
				$this -> app->log->info(print_r("Plan -  actualizar - procesando el request",true));
				foreach ($params as $key => $value) {
					$plan -> $key = $value;	
				}
				$this -> app->log->info(print_r("Accion plans -> save ",true));
				$plan->save();
				$this -> response["message"] = "Plan  actualizado con exito";
				$this -> response["status"] = "exito";
				$this -> app -> log -> info(print_r("Finalizando proceso de actualización",true));
		} catch (OpenpayApiTransactionError $e) {
			$this -> app -> log -> info(print_r("OpenpayApiTransactionError idPlan = $idPlan",true));
			$this -> response["message"]= $e -> getMessage();
			$this -> response["codigo"]	= $e -> getErrorCode();
		} catch (OpenpayApiRequestError $e) {
			$this -> app -> log -> info(print_r("OpenpayApiRequestError idPlan = $idPlan",true));
			$this -> response["message"]= $e -> getMessage();
			$this -> response["codigo"]	= $e -> getErrorCode();
		} catch (OpenpayApiAuthError $e) {
			$this -> app -> log -> info(print_r("OpenpayApiAuthError idPlan = $idPlan",true));
			$this -> app -> log -> info(print_r($e -> getMessage(),true));
			$this -> response["message"]= "Error de interno en la actualización intente mas tarde";
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
 
class PlanDTO {
	use MNTrait\Comun\MagicMethod;
	
	public $id				= "";
	public $name		 	= "";
	public $status		 	= "";
	public $amount		 	= 0.0;
	public $currency	 	= "MXN";
	public $creation_date	= "";
	public $repeat_every	= "";
	public $repeat_unit		= "";
	public $retry_times		= "";
	public $trial_days		= "";
	public $status_after_retry	= "";
}

?>