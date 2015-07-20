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
	
	private $app			= null;		
	
	function __construct() {
		$this -> openpay = Openpay::getInstance($this -> apikeyPrivate ,$this -> apikey );
		$this -> app = Slim::getInstance();
	}
	
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
}

/**
 * 
 */
 
class TarjetaDTO  {
	
	public $brand 	= "dsd";
	public $type 	= "";
	public $allows_payouts;
	public $creation_date;
	public $bank_name;
	public $bank_code;
	public $customer_id;
}

?>