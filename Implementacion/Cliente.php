<?php
use Slim\Slim;

/**
 * Cliente
 *
 * Clase cliente contiene las funciones propias para crear, listar 
 * borrar, editar y actualizar un Cliente 
 * 
 * PHP Version 5.4
 *
 * @category Open Pay
 * @author   Christian Hernandez <christian.hernandez@masnegocio.com>
 * @license  http://www.masnegocio.com Copyright 2015 MasNegocio
 * @method public listar
 */

class Cliente  {
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
	  * Lista los clientes relacionados con el comercio 
	  *
	  * La funcion listar implementa la liberia de OpenPay
	  * la cual obtiene la lista de clientes que estan registrados.
	  * 
	  *
	  * @author Christian Hernandez <christian.hernandez@masnegocio.com>
	  * @version 1.0
	  * @copyright MásNegocio 
	  * 
	  * @param	array 	params contiene los datos requerido generar un filtro de clientes.
	  * @throws	OpenpayApiTransactionError Si Falta un campo requerido para la alta de un cliente.
	  * @throws OpenpayApiAuthError si falla la autenticacion, este error debe de acercarce a cero,
	  * 			no deberia de suceder.
	  * @throws OpenpayApiTransactionError Petecion invalida o mal formada, este error debe de acercarce a cero
	  *			no deberia de pasar 			 
	  * @return  
	  * 
	  */
	public function listar($idCustomer,array $params = array()){
		$customerList = array();
		$customers = array();
		try {
			$this -> app -> log -> info(print_r("Inicializa proceso listar",true));
			$this -> app -> log -> info(print_r("Inicializa proceso listar - buscar cliente = $idCustomer",true));
			if ($idCustomer == '' || empty($idCustomer)){
				$findDataRequest = array(
			    'offset' => 0,
			    'limit' => 25);
			
				$findDataRequest = array_merge($findDataRequest, $params);
				$customerList = $this -> openpay -> customers -> getList($findDataRequest); 
			} else {
				$customerList[] = $this -> openpay -> customers -> get($idCustomer);
			}
			
			if (count($customerList > 0)) {
				foreach ($customerList as $key => $genericCustomer) {
					$clienteDTO = new ClienteDTO();
					$clienteDTO -> id 			= $genericCustomer -> __get("id");
					$clienteDTO -> creation_date= $genericCustomer -> __get("creation_date");
					$clienteDTO -> balance		= $genericCustomer -> __get("balance");
					
					$tmm = $genericCustomer -> __get("serializableData");
					foreach ($genericCustomer -> __get("serializableData") as $key => $value) {
						$clienteDTO -> $key = $value;
					}
					array_push($customers,$clienteDTO);
				}
			}
			$this -> response["status"] = "exito";
			$this -> response["message"] = "Cliente listado con exito";
			$this -> response["body"] = $customers;
			
			$this -> status = true;	
			$this -> app -> log -> info(print_r("Finalizando proceso listar",true));
		} catch (OpenpayApiTransactionError $e) {
			$this -> app -> log -> info(print_r("OpenpayApiTransactionError",true));
			$this -> response["message"]= $e -> getMessage();
			$this -> response["codigo"]	= $e -> getErrorCode();
		} catch (OpenpayApiRequestError $e) {
			$this -> app -> log -> info(print_r($e,true));
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
	  * Crea un cliente implementando la API de OPENPAY 
	  * 
	  * Cuando se crea un cliente se establecen los datos a nivel de comercio,
	  * el cliente es necesario para realizar planes de pago, ademas de tener ligado 
	  * al cliente una tarjeta. 
	  * 
	  *
	  * @author Christian Hernandez <christian.hernandez@masnegocio.com>
	  * @version 1.0
	  * @copyright MásNegocio
	  * 
	  * @param	array 	params contiene los datos requerido para la creacion de un cliente.
	  * @throws	OpenpayApiTransactionError Si Falta un campo requerido para la alta de un cliente.
	  * @throws OpenpayApiAuthError si falla la autenticacion, este error debe de acercarce a cero,
	  * 			no deberia de suceder.
	  * @throws OpenpayApiTransactionError Petecion invalida o mal formada, este error debe de acercarce a cero
	  *			no deberia de pasar 			 
	  * @return  
	  * 
	  */
	public function crear(array $params = array()){
			
		$clienteDTO = new ClienteDTO();
		try {
			$this -> app -> log -> info(print_r("Inicia proceso de alta de cliente",true));
			
			foreach ($params as $key => $value) {
				$clienteDTO -> __set($key,$value) ;
			}
			
			$this -> app -> log -> debug(print_r($clienteDTO,true));
			$this -> app->log->info(print_r("Accion openpay -> customers -> add ",true));
			
			$customer = $this -> openpay -> customers -> add( (array) $clienteDTO);
			$clienteDTO -> id = $customer -> __get("id");
			
			$this -> response["message"] = "Cliente eliminado con exito";
			$this -> response["body"] = $clienteDTO;
			$this -> response["status"] = "exito";
		} catch (OpenpayApiTransactionError $e) {
			$this -> app -> log -> info(print_r("OpenpayApiTransactionError",true));
			$this -> response["message"]= $e -> getMessage();
			$this -> response["codigo"]	= $e -> getErrorCode();
		} catch (OpenpayApiRequestError $e) {
			$this -> app -> log -> info(print_r($e,true));
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
	  * Elimina el cliente mediante su id implementando la API de OPENPAY 
	  * 
	  * Elimina el cliente previamente registrado, este cliente queda eliminado definitivamente 
	  * no existe opción de restaurar, lo que secedera es dar de alta un nuevo cliente
	  *   
	  * 
	  * @author Christian Hernandez <christian.hernandez@masnegocio.com>
	  * @version 1.0
	  * @copyright MásNegocio
	  * 
	  * @param  $idCliente Id del cliente registro con el cual se procedera a su eliminación.
	  */
	public function eliminar($idCliente = ""){
		try {
				$this -> app -> log -> info(print_r("Inicializando proceso de eliminacion",true));
				$this -> app->log->info(print_r("Cliente  a eliminar $idCliente",true));
				$customer = $this -> openpay -> customers -> get($idCliente);
				$customer->delete();
				$this -> response["message"] = "Cliente creado con exito";
				$this -> response["body"] = null;
				$this -> response["status"] = "exito";
				$this -> app -> log -> info(print_r("Finalizando proceso de eliminacion",true));
		} catch (OpenpayApiTransactionError $e) {
			$this -> app -> log -> info(print_r("OpenpayApiTransactionError idCliente = $idCliente",true));
			$this -> response["message"]= $e -> getMessage();
			$this -> response["codigo"]	= $e -> getErrorCode();
		} catch (OpenpayApiRequestError $e) {
			$this -> app -> log -> info(print_r("OpenpayApiRequestError idCliente = $idCliente",true));
			$this -> response["message"]= $e -> getMessage();
			$this -> response["codigo"]	= $e -> getErrorCode();
		} catch (OpenpayApiAuthError $e) {
			$this -> app -> log -> info(print_r("OpenpayApiAuthError idCliente = $idCliente",true));
			$this -> app -> log -> info(print_r($e -> getMessage(),true));
			$this -> response["message"]= "Error de interno del comercio intente mas tarde";
		}
		
	}

	/**
	  * Actualizar cliente mediante su id, implementando la API de OPENPAY 
	  * 
	  * Actualizar el cliente previamente registrado, este cliente se actualizara 
	  * en base a los parametros enviados por el request. Una vez actualizado 
	  * no hay forma de revertir los cambios. 
	  *   
	  * 
	  * @author Christian Hernandez <christian.hernandez@masnegocio.com>
	  * @version 1.0
	  * @copyright MásNegocio
	  * 
	  * @param  $idCliente Id del cliente registro con el cual se procedera a su actualización.
	  * @param  $params Contiene los datos que se actualizaran.
	  */
	public function actualizar($idCliente = "", array $params = array()){
		try {
				$this -> app -> log -> info(print_r("Inicializando proceso de actualización",true));
				$this -> app->log->info(print_r("Cliente  a actualizar $idCliente",true));
				$this -> app->log->info(print_r("Accion openpay -> customers -> get ",true));
				$customer = $this -> openpay -> customers -> get($idCliente);
				$this -> app->log->info(print_r("Cliente -  actualizar - procesando el request",true));
				foreach ($params as $key => $value) {
					if ( json_decode($value) == "" ) {
						$customer -> $key = $value;	
					} else {
						$tmpObj = json_decode($value);
						foreach ($tmpObj as $key_B => $value_B) {
							$customer -> $key -> $key_B = $value_B;
						}
					}
				}
				$this -> app->log->info(print_r("Accion customers -> save ",true));
				$customer->save();
				$this -> response["message"] = "Cliente - actualizar - exito";
				$this -> response["status"] = "exito";
				$this -> app -> log -> info(print_r("Finalizando proceso de actualización",true));
		} catch (OpenpayApiTransactionError $e) {
			$this -> app -> log -> info(print_r("OpenpayApiTransactionError idCliente = $idCliente",true));
			$this -> response["message"]= $e -> getMessage();
			$this -> response["codigo"]	= $e -> getErrorCode();
		} catch (OpenpayApiRequestError $e) {
			$this -> app -> log -> info(print_r("OpenpayApiRequestError idCliente = $idCliente",true));
			$this -> response["message"]= $e -> getMessage();
			$this -> response["codigo"]	= $e -> getErrorCode();
		} catch (OpenpayApiAuthError $e) {
			$this -> app -> log -> info(print_r("OpenpayApiAuthError idCliente = $idCliente",true));
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
 
class ClienteDTO {
	use MNTrait\Comun\MagicMethod;
	
	public $id				= "";
	public $external_id	 	= "";
	public $name		 	= "";
	public $last_name		= "";
	public $creation_date	= "";
	public $email			= "";
	public $phone_number	= "";
	public $requires_account= false;
	public $address			= array (
						         'line1' => ''
						         ,'line2' => ''
						         ,'line3' => ''
						         ,'state' => ''
						         ,'city' => ''
						         ,'postal_code' => ''
						         ,'country_code' => '');
	public $balance 		= "";
}

?>