<?php
require_once(dirname(dirname(dirname(__FILE__)))."/dependencies/vendor/autoload.php");

class PlanTest extends PHPUnit_Framework_TestCase {
	
	public function testMagicMethod(){
 		$this -> assertTrue(true);
 	}
 	/**
	  * Test Listar, lista los Planes relacionados con el comercio 
	  *
	  * El test valida que la respuesta de la peticion rest sea una respuesta exitosa
	  * asi como el resultado contenga una lista de planes 
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
		$request = $client -> get('http://localhost/openpay/v1/monetizador/planes');
		$response = $request  -> json(array('object' => true));
 		$this -> assertTrue(is_array($request -> json()));
		$this -> assertEquals($response -> status , 'exito');
		$this -> assertGreaterThan(0, count($response -> body));
 	}
	
	/**
	  * testListarEspecifico, lista un plan especifico relacionados con el comercio 
	  *
	  * El test valida que la respuesta de la peticion rest sea una respuesta exitosa
	  * asi como el resultado contenga una lista de planes 
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
		$idPlan = "pmpmqlv6h9vki2hfeoe0";
		$request = $client -> get('http://localhost/openpay/v1/monetizador/planes/'.$idPlan);
		$response = $request  -> json(array('object' => true));
 		$this -> assertTrue(is_array($request -> json()));
		$this -> assertEquals($response -> status , 'exito');
		$this -> assertGreaterThan(0, count($response -> body));
 	}
	
	/**
	  * testCrear, Crea un plan relacionado con el comercio 
	  *
	  * El test valida que la respuesta de la peticion rest sea una respuesta exitosa
	  * asi como el resultado contenga un objeto especificando el plan creado
	  * usando el servicio http://ip-expuesta/openpay/v1/monetizador/planes/ mediante el 
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
 		
		$planDataRequest = array(
				    'amount' => 150.00,
				    'status_after_retry' => 'cancelled',
				    'retry_times' => 2,
				    'name' => 'Plan Curso Verano 15',
				    'repeat_unit' => 'month',
				    'trial_days' => '30',
				    'repeat_every' => '3',
				    'currency' => 'MXN'
					);
					
		$client = new GuzzleHttp\Client();
		
		$options = array(
							'body' 	=>  $planDataRequest
							,'config'	=> array()
							,'headers'	=> array()	
						 );
			
		$request = $client -> post('http://localhost/openpay/v1/monetizador/planes/', $options);
							
		$response = $request  -> json(array('object' => true));
 		$this -> assertTrue(is_array($request -> json()));
		$this -> assertEquals($response -> status , 'exito');
		$this -> assertGreaterThan(0, count($response -> body));
 	}
	
	/**
	  * testEliminar, Elimina un plan relacionado con el comercio 
	  *
	  * El test valida que la respuesta de la peticion rest sea una respuesta exitosa
	  * asi como el resultado contenga un objeto especificando el plan creado
	  * usando el servicio http://ip-expuesta/openpay/v1/monetizador/planes/:id mediante el 
	  * metodo DELETE
	  * 
	  * @author Christian Hernandez <christian.hernandez@masnegocio.com>
	  * @version 1.0
	  * @copyright MásNegocio 
	  * 
	  * @param assertTrue assertTrue(is_array($request -> json())) Verifica que la respuesta se parse como un array 
	  * @param assertEquals assertEquals($response -> status , 'exito'), Verifica que la respuesta sea la palabra exito.
	  * 
	  */
	public function testEliminar(){
					
		$client = new GuzzleHttp\Client();
		$idPlan = "ps3rtpfumfhpcca17zsk";
		$options = array(
							'body' 		=> array()
							,'config'	=> array()
							,'headers'	=> array()	
						 );
			
		$request = $client -> delete('http://localhost/openpay/v1/monetizador/planes/'.$idPlan, $options);
							
		$response = $request  -> json(array('object' => true));
 		$this -> assertTrue(is_array($request -> json()));
		$this -> assertEquals($response -> status , 'exito');
 	}
	
	/**
	  * testActualizar, Actualiza un plan relacionado con el comercio 
	  *
	  * El test valida que la respuesta de la peticion rest sea una respuesta exitosa
	  * usando el servicio http://ip-expuesta/openpay/v1/monetizador/planes/id mediante el 
	  * metodo PUT
	  * 
	  * @author Christian Hernandez <christian.hernandez@masnegocio.com>
	  * @version 1.0
	  * @copyright MásNegocio 
	  * 
	  * @param assertTrue assertTrue(is_array($request -> json())) Verifica que la respuesta se parse como un array 
	  * @param assertEquals assertEquals($response -> status , 'exito'), Verifica que la respuesta sea la palabra exito.
	  * 
	  */
	public function testActualizar(){
 		
		$planDataRequest = array(
				    'amount' => 150.00,
				    'status_after_retry' => 'cancelled',
				    'retry_times' => 2,
				    'name' => 'Plan Curso Verano 15 / 2',
				    'repeat_unit' => 'month',
				    'trial_days' => '30',
				    'repeat_every' => '3',
				    'currency' => 'MXN'
					);
		$idPlan = "pr5fkr931kneu4x5kv56";
		$client = new GuzzleHttp\Client();
		
		$options = array(
							'body' 	=>  $planDataRequest
							,'config'	=> array()
							,'headers'	=> array()	
						 );
			
		$request = $client -> put('http://localhost/openpay/v1/monetizador/planes/'.$idPlan, $options);
							
		$response = $request  -> json(array('object' => true));
 		$this -> assertTrue(is_array($request -> json()));
		$this -> assertEquals($response -> status , 'exito');
		$this -> assertGreaterThan(0, count($response -> body));
 	}
}
?>