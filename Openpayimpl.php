<?php
/**
 * Openpayimpl.
 *
 * PHP Version 5.4
 *
 * @category Open Pay
 * @package  
 * @author   Christian Hernandez <christian.hernandez@masnegocio.com>
 * @license  http://www.masnegocio.com Copyright 2015 MasNegocio
 */
//require_once dirname(dirname(dirname(__FILE__))).'/dependencies/MyTrait/MagicMethods.php';
require_once dirname(dirname(__FILE__)).'/openpay/Core/Openpay.php';


class Openpayimpl  {
	//use MyTrait\MagicMethod;
	//use Core;
		
	
	private $openpay = null;
	protected $apikeyPrivate = 'mgvxcww4nbopuaimkkgw'; 
	protected $apikey		 = 'sk_e9747dfe55b94e64805221563eb37874';
	
	
	function __construct($argument = null) {
		error_log("contructir");
		$this -> openpay = Openpay::getInstance($this -> apikeyPrivate ,$this -> apikey );
	}
	
	function getOpenpay(){
		return $this -> openpay; 
	}
}

/*
$epi = new Openpayimpl();

 $lalve = $epi -> __get("apikey");
// 
 error_log(print_r($lalve,true));
//error_log(print_r($epi,true));
 * 
 */
?>