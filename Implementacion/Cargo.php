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
	use MyTrait\MagicMethod;
	use MyTrait\Response;	
	
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
		
		$this -> estatus = true;
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
	public function crear(array $params = array(), $customerId = null){
		$charge = null;
		$cargoVO = new CargoVO();
		try{
			$chargeData = array(
		    'method' => 'card',
		    'source_id' => "",
		    'amount' => ""
		    ,'description' => ""
		    ,'device_session_id' => "");
		
			$chargeData = array_merge($chargeData, $params);
			
			$this -> app -> log -> info(print_r("Exite el cliente $customerId",true));
			if ($customerId === null) {
				$charge = $this -> openpay -> charges -> create($chargeData);
			} else {
				$customer = $this -> openpay -> customers->get($customerId);
				$charge = $customer->charges->create($chargeData);
			}
		}catch (OpenpayApiRequestError $e){
				$app->log->info(print_r("OpenpayApiRequestError",true));
				$this -> response = array('message' => $e -> getDescription()
										,'codigo'	=> $e -> getErrorCode()
										,'status'	=> "fallo"
									);
			throw new Exception($this -> response, 1);
			
		}
		$cargoVO -> authorization 	= $charge ->__get("authorization");
		$cargoVO -> creation_date 	= $charge ->__get("creation_date");
		$cargoVO -> currency		= $charge ->__get("currency");
		$cargoVO -> customer_id		= $charge ->__get("customer_id");
		$cargoVO -> operation_type	= $charge ->__get("operation_type");
		$cargoVO -> status			= $charge ->__get("status");
		$cargoVO -> card_type		= $charge ->__get("card") -> __get("type");
		$cargoVO -> bank_code		= $charge ->__get("card") -> __get("bank_code");
		$cargoVO -> bank_name		= $charge ->__get("card") -> __get("bank_name");
		$cargoVO -> brand			= $charge ->__get("card") -> __get("brand");
		
		$this -> response["message"]= "Cargo Realizado Exitosamente";
		$this -> response["status"]	= "exito";
		$this -> response["codigo"]	= "1001";
		$this -> response["body"] = $cargoVO;
	}


	/**
	  * Borrar la tarjeta mediante su id implementando la API de OPENPAY 
	  * 
	  * Borra la terjeta previamente registrada, esta tarjeta queda eliminada definitivamente 
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
 
class CargoVO {
	
	public $authorization	= "";
	public $creation_date 	= "";
	public $currency 		= "";
	public $customer_id		= "";
	public $operation_type 	= "";
	public $status			= "";
	public $bank_code		= "";
	public $card_type		= "";	
	public $bank_name		= "";
	public $brand			= "";
	
}
?>