<?php
/**
 * CargoComercio.
 *
 * PHP Version 5.4
 *
 * @category Open Pay
 * @author   Christian Hernandez <christian.hernandez@masnegocio.com>
 * @license  http://www.masnegocio.com Copyright 2015 MasNegocio
 */

use Slim\Slim;

class Tarjeta  {
	use MyTrait\MagicMethod;
	
	
	private $openpay 		= null;
	private $apikeyPrivate	= 'mgvxcww4nbopuaimkkgw';
	private $apikey			= 'sk_e9747dfe55b94e64805221563eb37874';
	private $estatus		= false;
	private $charge			= null;
	private $cardList		= null;
	private $card			= null;
	private $app			= null;		
	
	function __construct() {
		$this -> openpay = Openpay::getInstance($this -> apikeyPrivate ,$this -> apikey );
		$this -> app = Slim::getInstance();
	}
	
	/**
	  * Lista las tarjetas relacionadas con el comercio y comercio/cliente
	  *
	  * La funcion cargos implementa la CargoComercio, la cual
	  * contiene la logica de openpay para poder realizar la via
	  * API-OpenOPay.
	  *
	  * @author Christian Hernandez <christian.hernandez@masnegocio.com>
	  * @version 1.0
	  * @copyright MásNegocio 
	  * 
	  */
	public function listar($idCliente = null, array $params = array()){
		$cardList = array();
		$tarjetas = array();
		$findDataRequest = array(
		    'offset' => 0,
		    'limit' => 5);
		
		$findDataRequest = array_merge($findDataRequest, $params);
		
		if ($idCliente === NULL){
			$tarjetas = $this -> openpay->cards->getList($findDataRequest);	
		} else {
			$customer = $this -> openpay -> customers -> get($idCliente);
			$tarjetas = $customer -> cards -> getList($findDataRequest);	
		}
		
		$this -> app->log->info(print_r(count($tarjetas),true));
			
		foreach ($tarjetas as $key => $tarjeta) {
			$tarjetaDTO = new TarjetaDTO();
			$tarjetaDTO -> brand =  $tarjeta ->__get("brand");
			$tarjetaDTO -> type =  $tarjeta ->__get("type");
			array_push($cardList,$tarjetaDTO);
		}

		$this -> app->log->info(print_r($cardList,true));
		$this -> cardList = $cardList;
		
		$this -> estatus = true;
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
		$this -> card = new TarjetaDTO();
		$cardDataRequest = array(
		    'holder_name' => '',
		    'card_number' => '5555555555554444',
		    'cvv2' => '123',
		    'expiration_month' => '12',
		    'expiration_year' => '15',
		    'address' => array(
		            'line1' => 'Privada Rio No. 12',
		            'line2' => 'Co. El Tintero',
		            'line3' => '',
		            'postal_code' => '76920',
		            'state' => 'Querétaro',
		            'city' => 'Querétaro.',
		            'country_code' => 'MX'));
		
		$cardDataRequest = array_merge($cardDataRequest, $params);
		$this -> app->log->info(print_r("Existe el idCliente $idCliente",true));
		if ($idCliente === NULL){
			$card = $this -> openpay->cards->getList($cardDataRequest);	
		} else {
			$customer = $this -> openpay->customers->get($idCliente);
			$card = $customer->cards->add($cardDataRequest);
			$this -> card -> id			= $card -> __get("id");
			$this -> card -> brand		= $card -> __get("brand");
			$this -> card -> type		= $card -> __get("type");
			$this -> card -> bank_code	= $card -> __get("bank_code");
			$this -> card -> bank_name	= $card -> __get("bank_name");
			
	
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
 
class TarjetaDTO {
	
	public $id		= "";
	public $brand 	= "";
	public $type 	= "";
	public $allows_payouts;
	public $creation_date;
	public $bank_name;
	public $bank_code;
	public $customer_id;
}

?>