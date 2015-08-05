<?php
use Slim\Slim;

/**
 * La clase Cargo implementa la liberia de OPENPAY.
 *
 * Los cargos se pueden realizar cargos a tarjetas, tiendas y bancos. 
 * A cada cargo se le asigna un identificador único en el sistema.
 * En cargos a tarjeta puedes hacerlo a una tarjeta guardada usando 
 * el id de la tarjeta, usando un token o puedes enviar la 
 * información de la tarjeta al momento de la invocación.
 * 
 *  
 * PHP Version 5.4
 *
 * @author   Christian Hernandez <christian.hernandez@masnegocio.com>
 * @copyright 2015 MasNegocio
 * 
 * @method public listar
 */

class Cargo {
	use MNTrait\Comun\MagicMethod;
	use MNTrait\Comun\Response;
	
	private $openpay 		= null;
	private $apikeyPrivate	= 'mgvxcww4nbopuaimkkgw';
	private $apikey			= 'sk_e9747dfe55b94e64805221563eb37874';
	
	protected $app			= null;
	private $charge			= null;
	private $response		= null;
	
	function __construct() {
		$this -> openpay = Openpay::getInstance($this -> apikeyPrivate ,$this -> apikey );
		$this -> app = Slim::getInstance();
		$this -> response = $this ->__response();
	}
	
	/**
	  * Lista las tarjetas relacionadas con el comercio y comercio/cliente
	  *
	  * La funcion cargos implementa la CargoComercio, la cual
	  * contiene la logica de openpay para poder realizar la via
	  * API-OpenOPay.
	  * 
	  *
	  * @author Christian Hernandez <christian.hernandez@masnegocio.com>
	  * @version 1.0
	  * 
	  * @param boolean $idCliente 
	  * 
	  * @throws InvalidArgumentException
	  * 
	  * @return   
	  * 
	  */
	public function listar($idCliente = null, array $params = array()){
		
		$cargoDTO = new CargoDTO();
		try {
			$this -> app -> log -> info(print_r("Inicia proceso de lista de cargos",true));
			$this -> app -> log -> info(print_r("Verifica si existe cliente",true));
			
			$findData = array(
		    		'offset' => 0,
		    		'limit' => 25
				);
			$findData = array_merge($findData, $params);
			if ($idCliente === null || $idCliente == "") {
				$this -> app -> log -> info(print_r(" openpay -> charges -> getList ",true));
				$charge = $this -> openpay -> charges -> getList($findData);
			} else {
				$this -> app -> log -> info(print_r(" openpay -> customers -> get ",true));
				$customer = $this -> openpay -> customers -> get($idCliente);
				$this -> app -> log -> info(print_r(" customer -> charges -> getList ",true));
				$charge = $customer -> charges -> getList($findData);
			}
								
			$this -> response["message"]= "Listado creado con exito";
			$this -> response["body"] 	= $cargoDTO;
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
	  * Crea una cargo implementando la API de OPENPAY 
	  * 
	  * La accion de crear un cargo tiene dos vertientes 
	  *
	  * @author Christian Hernandez <christian.hernandez@masnegocio.com>
	  * @version 1.0
	  * 
	  * @param string $customerId
	  * @param array params
	  * 
	  * @throws InvalidArgumentException
	  * 
	  * @return
	  * 
	  */
	public function crear( $customerId = "", array $params = array()){
		$charge = null;
		$cargoDTO = new CargoDTO();
		try{
			$chargeData = array(
		    'method' => 'card',
		    'source_id' => "",
		    'amount' => ""
		    ,'description' => ""
		    ,'device_session_id' => "");
		
			$chargeData = array_merge($chargeData, $params);
			
			$this -> app -> log -> info(print_r("Exite el cliente $customerId",true));
			if ($customerId === null || $customerId == "") {
				$this -> app -> log -> info(print_r(" openpay -> charges -> create ",true));
				$charge = $this -> openpay -> charges -> create($chargeData);
			} else {
				$this -> app -> log -> info(print_r(" openpay -> customers -> get ",true));
				$customer = $this -> openpay -> customers -> get($customerId);
				$this -> app -> log -> info(print_r(" customer -> charges -> create ",true));
				$charge = $customer -> charges -> create($chargeData);
			}
			
			foreach ($cargoDTO as $key => $value) {
				error_log(print_r( $key ,true));
				$cargoDTO -> $key = $charge ->__get($key);
				if (is_object($cargoDTO -> $key) ) {
					$this -> app -> log -> debug(print_r("Esto es un objeto",true));	
				}
				
			}
	/*		
			$cargoDTO -> card_type		= $charge ->__get("card") -> __get("type");
			$cargoDTO -> bank_code		= $charge ->__get("card") -> __get("bank_code");
			$cargoDTO -> bank_name		= $charge ->__get("card") -> __get("bank_name");
			$cargoDTO -> brand			= $charge ->__get("card") -> __get("brand");	
		
	 * 
	 *
	 */
		} catch (OpenpayApiTransactionError $e) {
			$this -> app -> log -> info(print_r("OpenpayApiTransactionError",true));
			$this -> response["message"]= $e -> getMessage();
			$this -> response["codigo"]	= $e -> getErrorCode();
		} catch (OpenpayApiRequestError $e) {
			$this -> app -> log -> info(print_r("OpenpayApiRequestError",true));
			$this -> app -> log -> debug(print_r($e,true));
			$this -> response["message"]= $e -> getMessage();
			$this -> response["codigo"]	= $e -> getErrorCode();
		} catch (OpenpayApiAuthError $e) {
			$this -> app -> log -> info(print_r("OpenpayApiAuthError",true));
			$this -> app -> log -> info(print_r($e -> getMessage(),true));
			$this -> response["message"]= "Error de interno del comercio intente mas tarde";
		}
		
		$this -> response["message"]= "Cargo Realizado Exitosamente";
		$this -> response["status"]	= "exito";
		$this -> response["codigo"]	= "1001";
		$this -> response["body"] = $cargoDTO;
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
 
class CargoDTO {
	use MNTrait\Comun\MagicMethod;
	
	public $authorization	= "";
	public $creation_date 	= "";
	public $currency 		= "";
	public $customer_id		= "";
	public $operation_type 	= "";
	public $status			= "";
	public $card			= "";
	
}
?>