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
	use MyTrait\MagicMethod;
	use MyTrait\Response;
	
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
			if ($idCustomer == '' || empty($idCustomer)){
				$findDataRequest = array(
			    'offset' => 0,
			    'limit' => 25);
			
				$findDataRequest = array_merge($findDataRequest, $params);
				$customerList = $this -> openpay -> customers -> getList($findDataRequest); 
			} else {
				$customerList = $this -> openpay -> customers -> get($idCustomer);
				$this -> app -> log -> info(print_r($customerList,true));
			}
			
			
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
			
		$this -> customer = new ClienteDTO();
		try {
			$this -> app -> log -> info(print_r("Inicia proceso de alta de cliente",true));
			
			foreach ($params as $key => $value) {
				$this -> customer -> __set($key,$value) ;
			}
			//Proceso Manual chs  buscar solucion
			$this -> customer -> address = json_decode($this -> customer -> address,true);
			
			$this -> app -> log -> info(print_r($this -> customer,true));
			$this -> app->log->info(print_r("Accion openpay -> customers -> add ",true));
			
			$customer = $this -> openpay -> customers -> add( (array) $this -> customer);
			$this -> customer -> id = $customer -> __get("id");
			$this -> app -> log -> info(print_r($customer,true));
			
			$this -> response["message"] = "Cliente creado con exito";
			$this -> response["body"] = $this -> customer;
			$this -> status = true;
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
	  * Borrar el cliente mediante su id implementando la API de OPENPAY 
	  * 
	  * Borra el cliente previamente registrado, esta tarjeta queda eliminada definitivamente 
	  * no existe opción de restaurar, lo que secedera es dar de alta una nueva o la misma tarjeta
	  *   
	  * 
	  * 
	  * @author Christian Hernandez <christian.hernandez@masnegocio.com>
	  * @version 1.0
	  * @copyright MásNegocio
	  * 
	  */
	public function borrar($idTarjeta = "", $idCliente = null, array $params = array()){
		$this -> card = new TarjetaDTO();
		
		$this -> app->log->info(print_r("Existe el idCliente $idCliente",true));
		$this -> app->log->info(print_r("Tarjeta a borrar $idTarjeta",true));
		if ($idCliente === NULL){
			$card = $this -> openpay->cards->get($idTarjeta);
			$card->delete();
		} else {
			$customer = $this -> openpay->customers->get($idCliente);
			$card = $customer->cards->get($idTarjeta);
			$card->delete();
		}
		$this -> estatus = true;
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
	use MyTrait\MagicMethod;
	
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