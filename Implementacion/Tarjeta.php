<?php
use Slim\Slim;

/**
 * Tarjeta
 *
 * PHP Version 5.4
 *
 * @category Open Pay
 * @author   Christian Hernandez <christian.hernandez@masnegocio.com>
 * @license  http://www.masnegocio.com Copyright 2015 MasNegocio
 */

class Tarjeta  {
	use MNTrait\Comun\MagicMethod;
	use MNTrait\Comun\Response;
	
	private $openpay 		= null;
	private $apikeyPrivate	= 'mgvxcww4nbopuaimkkgw';
	private $apikey			= 'sk_e9747dfe55b94e64805221563eb37874';
	private $estatus		= false;
	private $charge			= null;
	private $cardList		= null;
	private $card			= null;
	private $app			= null;		
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
	  * @copyright MásNegocio 
	  * 
	  */
	public function listar($idCliente, $idTarjeta, array $params = array()){
		$cardList = array();
		$tarjetas = array();
		$findDataRequest = array(
		    'offset' => 0,
		    'limit' => 25);
		try{
			$this -> app -> log -> info(print_r("Inicia proceso de lista de cargos",true));
			$this -> app -> log -> info(print_r("Verifica cliente $idCliente",true));
			$this -> app -> log -> info(print_r("Verifica tarjeta $idTarjeta",true));
			$findDataRequest = array_merge($findDataRequest, $params);
		
			if ($idCliente == NULL || $idCliente == ""){
				$this -> app -> log -> info("openpay -> cards -> getList");
				$tarjetas = $this -> openpay->cards->getList($findDataRequest);	
			} else {
				$this -> app -> log -> info("openpay -> customers -> get");
				$customer = $this -> openpay -> customers -> get($idCliente);
				
				if ($idTarjeta == null || $idTarjeta == ""){
					$this -> app -> log -> info("customers -> cards -> getlist");
					$tarjetas = $customer -> cards -> getList($findDataRequest);
				} else {
					$this -> app -> log -> info("customers -> cards -> get");
					$this -> app -> log -> info(print_r("Verifica tarjeta 1 $idTarjeta",true));
					$tarjetas[] = $customer -> cards -> get($idTarjeta);	
				}
				
					
			}
			
			foreach ($tarjetas as $key => $tarjeta) {
				$tarjetaDTO = new TarjetaDTO();
				$tarjetaDTO -> brand 	= $tarjeta -> __get("brand");
				$tarjetaDTO -> type 	= $tarjeta -> __get("type");
				$tarjetaDTO -> id 		= $tarjeta -> __get("id");
				$tarjetaDTO -> bank_code= $tarjeta -> __get("bank_code");
				$tarjetaDTO -> bank_name= $tarjeta -> __get("bank_name");
				array_push($cardList,$tarjetaDTO);
			}
	
			$this -> response["message"]= "Listado creado con exito";
			$this -> response["body"] 	= $cardList;
			$this -> response["status"] = "exito";
		} catch (OpenpayApiTransactionError $e) {
			$this -> app -> log -> info(print_r("OpenpayApiTransactionError",true));
			$this -> response["message"]= $e -> getMessage();
			$this -> response["codigo"]	= $e -> getErrorCode();
		} catch (OpenpayApiRequestError $e) {
			$this -> app -> log -> info(print_r("OpenpayApiRequestError",true));
			//$this -> app -> log -> info(print_r($e,true));
			$this -> response["message"]= $e -> getMessage();
			$this -> response["codigo"]	= $e -> getErrorCode();
		} catch (OpenpayApiAuthError $e) {
			$this -> app -> log -> info(print_r("OpenpayApiAuthError",true));
			$this -> app -> log -> info(print_r($e -> getMessage(),true));
			$this -> response["message"]= "Error de interno del comercio intente mas tarde";
		}
	}
	
	/**
	  * Crea una tarjeta implementando la API de OPENPAY 
	  * 
	  * Cuando se crea una tarjeta debe especificarse cliente, 
	  * si no se especifica el cliente la tarjeta quedará registrada para la cuenta del comercio.
	  *
	  * @author Christian Hernandez <christian.hernandez@masnegocio.com>
	  * @version 1.0
	  * @copyright MásNegocio
	  * 
	  */
	public function crear($idCliente = null, array $params = array()){
		try{
			$this -> card = new TarjetaDTO();
			$this -> app -> log -> info(print_r("Inicia proceso de crear tarjeta",true));
			$this -> app->log->info(print_r("Existe el idCliente $idCliente",true));
			if ($idCliente === NULL){
				//$card = $this -> openpay->cards->getList($cardDataRequest);	
			} else {
				$customer = $this -> openpay->customers->get($idCliente);
				$card = $customer->cards->add($params);
				$this -> card -> id			= $card -> __get("id");
				$this -> card -> brand		= $card -> __get("brand");
				$this -> card -> type		= $card -> __get("type");
				$this -> card -> bank_code	= $card -> __get("bank_code");
				$this -> card -> bank_name	= $card -> __get("bank_name");
				
		
			}
			
			$this -> response["message"]= "Listado creado con exito";
			$this -> response["body"] 	= $this -> card;
			$this -> response["status"] = "exito";
		} catch (OpenpayApiTransactionError $e) {
			$this -> app -> log -> info(print_r("OpenpayApiTransactionError",true));
			$this -> response["message"]= $e -> getMessage();
			$this -> response["codigo"]	= $e -> getErrorCode();
		} catch (OpenpayApiRequestError $e) {
			$this -> app -> log -> info(print_r("OpenpayApiRequestError",true));
			//$this -> app -> log -> info(print_r($e,true));
			$this -> response["message"]= $e -> getMessage();
			$this -> response["codigo"]	= $e -> getErrorCode();
		} catch (OpenpayApiAuthError $e) {
			$this -> app -> log -> info(print_r("OpenpayApiAuthError",true));
			$this -> app -> log -> info(print_r($e -> getMessage(),true));
			$this -> response["message"]= "Error de interno del comercio intente mas tarde";
		}
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
	public function eliminar($idCliente = null, $idTarjeta = "", array $params = array()){
		try{
			$this -> app -> log -> info(print_r("Inicia proceso de eliminar tarjeta",true));
			$this -> app->log->info(print_r("idCliente $idCliente",true));
			$this -> app->log->info(print_r("idTarjeta $idTarjeta",true));
			if ($idCliente == NULL || $idCliente == ""){
				$card = $this -> openpay->cards->get($idTarjeta);
				$card->delete();
			} else {
				$this -> app->log->info(print_r("openpay -> customers -> get",true));
				$customer = $this -> openpay->customers->get($idCliente);
				$this -> app->log->info(print_r("customers cards -> get",true));
				$card = $customer->cards->get($idTarjeta);
				$card->delete();
			}
			$this -> response["message"]= "Tarjeta eliminada con exito";
			$this -> response["status"] = "exito";
		} catch (OpenpayApiTransactionError $e) {
			$this -> app -> log -> info(print_r("OpenpayApiTransactionError",true));
			$this -> response["message"]= $e -> getMessage();
			$this -> response["codigo"]	= $e -> getErrorCode();
		} catch (OpenpayApiRequestError $e) {
			$this -> app -> log -> info(print_r("OpenpayApiRequestError",true));
			//$this -> app -> log -> info(print_r($e,true));
			$this -> response["message"]= $e -> getMessage();
			$this -> response["codigo"]	= $e -> getErrorCode();
		} catch (OpenpayApiAuthError $e) {
			$this -> app -> log -> info(print_r("OpenpayApiAuthError",true));
			$this -> app -> log -> info(print_r($e -> getMessage(),true));
			$this -> response["message"]= "Error de interno del comercio intente mas tarde";
		}	
		$this -> app->log->info(print_r("Existe el idCliente $idCliente",true));
		$this -> app->log->info(print_r("Tarjeta a borrar $idTarjeta",true));
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
 
class TarjetaDTO {
	use MNTrait\Comun\MagicMethod;
	
	public $id		= "";
	public $brand 	= "";
	public $type 	= "";
	public $allows_payouts;
	public $creation_date;
	public $bank_name;
	public $bank_code;
	//public $customer_id;
}

?>