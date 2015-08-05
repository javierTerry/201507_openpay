<?php
require_once(dirname(dirname(dirname(__FILE__)))."/dependencies/vendor/autoload.php");

class CargoTest extends PHPUnit_Framework_TestCase {
	
	private $idCliente  = "ak3hcep2n4faqqrey8t7/";
	private $server		= "http://localhost/";
	private $servicioBase="openpay/v1/monetizador/clientes/";
	private $recurso	= "suscripciones/";
	
 	/**
	  * Test Listar, lista los Suscrupciones relacionados con el comercio 
	  *
	  * El test valida que la respuesta de la peticion rest sea una respuesta exitosa
	  * asi como el resultado contenga una lista de suscrupciones 
	  *
	  * @author Christian Hernandez <christian.hernandez@masnegocio.com>
	  * @version 1.0
	  * @copyright MásNegocio 
	  * 
	  * @param assertTrue assertTrue(is_array($request -> json())) Verifica que la respuesta se parse como un array 
	  * @param assertEquals assertEquals($response -> status , 'exito'), Verifica que la respuesta sea la palabra exito.
	  * @param assertGreaterThan assertGreaterThan(0, count($response -> body)), Verifica qe el body contega almenos un registro.
	  * 
	  * @codeCoverageIgnore 
	  * 
	  */
 	public function testListar(){
 		
		$client = new GuzzleHttp\Client();
		$servicio = sprintf("%s%s%s%s",$this -> server, $this -> servicioBase, $this -> idCliente, $this -> recurso);
		$request = $client -> get($servicio);
		error_log(print_r("Resultado de testListar >>>>>>>>>>>>>\n",true));
		error_log(print_r((string) $request -> getBody(),true));
		$response = $request  -> json(array('object' => true));
 		$this -> assertTrue(is_array($request -> json()));
		$this -> assertEquals($response -> status , 'exito');
		$this -> assertGreaterThan(0, count($response -> body));
 	}
	
	/**
	  * testListarEspecifico, lista un suscrupcion especifico relacionados con el comercio 
	  *
	  * El test valida que la respuesta de la peticion rest sea una respuesta exitosa
	  * asi como el resultado contenga una lista de suscrupciones 
	  *
	  * @author Christian Hernandez <christian.hernandez@masnegocio.com>
	  * @version 1.0
	  * @copyright MásNegocio 
	  * 
	  * @param assertTrue assertTrue(is_array($request -> json())) Verifica que la respuesta se parse como un array 
	  * @param assertEquals assertEquals($response -> status , 'exito'), Verifica que la respuesta sea la palabra exito.
	  * @param assertGreaterThan assertGreaterThan(0, count($response -> body)), Verifica qe el body contega almenos un registro 
	  * 
	  * @codeCoverageIgnore 
	  *  
	  */
	public function testListarEspecifico(){
 		
		$client = new GuzzleHttp\Client();
		$idSuscrupcion = "s8wedcovbzcjobyf0gyc";
		$servicio = sprintf("%s%s%s%s%s",$this -> server, $this -> servicioBase, $this -> idCliente, $this -> recurso,$idSuscrupcion);
		$request = $client -> get($servicio);
		error_log(print_r("Resultado de testListarEspecifico >>>>>>>>>>>>>\n",true));
		error_log(print_r((string) $request -> getBody(),true));
		$response = $request  -> json(array('object' => true));
 		$this -> assertTrue(is_array($request -> json()));
		$this -> assertEquals($response -> status , 'exito');
		$this -> assertGreaterThan(0, count($response -> body));
 	}
	
	/**
	  * testCrear, Crea un suscrupcion relacionado con el comercio 
	  *
	  * El test valida que la respuesta de la peticion rest sea una respuesta exitosa
	  * asi como el resultado contenga un objeto especificando el suscrupcion creado
	  * usando el servicio http://ip-expuesta/openpay/v1/monetizador/suscrupciones/ mediante el 
	  * metodo POST 
	  * 
	  * @author Christian Hernandez <christian.hernandez@masnegocio.com>
	  * @version 1.0
	  * @copyright MásNegocio 
	  * 
	  * @param assertTrue assertTrue(is_array($request -> json())) Verifica que la respuesta se parse como un array 
	  * @param assertEquals assertEquals($response -> status , 'exito'), Verifica que la respuesta sea la palabra exito.
	  * @param assertGreaterThan assertGreaterThan(0, count($response -> body)), Verifica qe el body contega almenos un registro 
	  * 
	  */
	public function testCrear(){
 		
		$dataRequest = array(
		    "trial_end_date" => "2014-01-01", 
		    'plan_id' => 'poyujhznxu2vfptoptq2',
		    'card_id' => 'k71csg5shp9miedczap6');
					
		$client = new GuzzleHttp\Client();
		
		$options = array(
							'body' 	=>  $dataRequest
							,'config'	=> array()
							,'headers'	=> array()	
						 );
			
		$request = $client -> post('http://localhost/openpay/v1/monetizador/clientes/ak3hcep2n4faqqrey8t7/suscripciones/', $options);
		error_log(print_r("Resultado de testCrear >>>>>>>>>>>>>\n",true));
		error_log(print_r((string) $request -> getBody(),true));					
		$response = $request  -> json(array('object' => true));
 		$this -> assertTrue(is_array($request -> json()));
		$this -> assertEquals($response -> status , 'exito');
		$this -> assertGreaterThan(0, count($response -> body));
		
 	}
}
?>