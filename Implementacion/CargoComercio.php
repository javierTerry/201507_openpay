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


class CargoComercio  {
	use MyTrait\MagicMethod;	
	
	private $openpay 		= null;
	private $apikeyPrivate	= 'mgvxcww4nbopuaimkkgw';
	private $apikey			= 'sk_e9747dfe55b94e64805221563eb37874';
	private $params			= array();
	private $estatus		= false;
	private $charge			= null;
	
	function __construct() {
		$this -> openpay = Openpay::getInstance($this -> apikeyPrivate ,$this -> apikey );
	}
	
	public function send(array $params = array()){
		$chargeData = array(
		    'method' => 'card',
		    'source_id' => "",
		    'amount' => ""
		    ,'description' => ""
		    ,'device_session_id' => "");
		
		
		error_log(print_r($params,true));
		error_log("----------------------------------------------");
		$chargeData = array_merge($chargeData, $params);
		error_log(print_r($chargeData,true));
		$this -> charge = $this -> openpay -> charges -> create($chargeData);
		$this -> estatus = true;
	}
	
	
}

/*
$cc = new CargoComercio();
//error_log(print_r($cc,true));
$test = $cc -> __get("openpay");
error_log(print_r($test,true));

 * 
 */?>