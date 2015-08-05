<?php
require_once(dirname(dirname(dirname(__FILE__)))."/dependencies/vendor/autoload.php");

class ClienteTest extends PHPUnit_Framework_TestCase {
	
	private $idCliente  = "ak3hcep2n4faqqrey8t7/";
	private $server		= "http://localhost/";
	private $servicioBase="openpay/v1/monetizador/";
	private $recurso	= "clientes/";
	
 	/**
	  * Test Listar, lista los Clientees relacionados con el comercio 
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
	  * @tes
	  * 
	  */
 	public function listar(){
 		
		$client = new GuzzleHttp\Client();
		$servicio = sprintf("%s%s%s",$this -> server, $this -> servicioBase, $this -> recurso);
		$request = $client -> get($servicio);
		error_log(print_r("Resultado de listar >>>>>>>>>>>>>\n",true));
		error_log(print_r((string) $request -> getBody(),true));
		
		$response = $request  -> json(array('object' => true));
 		$this -> assertTrue(is_array($request -> json()));
		$this -> assertEquals($response -> status , 'exito');
		$this -> assertGreaterThan(0, count($response -> body));
 	}
	
	/**
	  * listarEspecifico, lista un suscrupcion especifico relacionados con el comercio 
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
	  * @tes
	  *  
	  */
	public function listarEspecifico(){
 		
		$client = new GuzzleHttp\Client();
		$idCliente = "ak3hcep2n4faqqrey8t7";
		$servicio = sprintf("%s%s%s%s",$this -> server, $this -> servicioBase, $this -> recurso, $idCliente);
		$request = $client -> get($servicio);
		error_log(print_r("Resultado de testListar >>>>>>>>>>>>>\n",true));
		error_log(print_r((string) $request -> getBody(),true));
		$response = $request  -> json(array('object' => true));
 		$this -> assertTrue(is_array($request -> json()));
		$this -> assertEquals($response -> status , 'exito');
		$this -> assertGreaterThan(0, count($response -> body));
 	}
	
	/**
	  * Crear, Crea un suscrupcion relacionado con el comercio 
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
	  * @test
	  */
	public function crear(){
 		
		$dataRequest = array(
		     'external_id' => '5',
		     'name' => 'customer name 5',
		     'last_name' => 'Last name 5',
		     'email' => 'customer_email5@me.com',
		     'requires_account' => false,
		     'phone_number' => '44209087655',
		     'address' => array(
		         'line1' => 'Calle 10 5',
		         'line2' => 'col. san pablo 5',
		         'line3' => 'entre la calle 1 y la 2 5',
		         'state' => 'Queretaro 5',
		         'city' => 'Queretaro 5',
		         'postal_code' => '76005',
		         'country_code' => 'MX'
		      )
		   );
					
		$client = new GuzzleHttp\Client();
		
		$options = array(
							'body' 	=>  $dataRequest
							,'config'	=> array()
							,'headers'	=> array()	
						 );
			
		$servicio = sprintf("%s%s%s",$this -> server, $this -> servicioBase, $this -> recurso);
		$request = $client -> post($servicio,$options);
		error_log(print_r("Resultado de crear >>>>>>>>>>>>>\n",true));
		error_log(print_r((string) $request -> getBody(),true));					
		$response = $request  -> json(array('object' => true));
 		$this -> assertTrue(is_array($request -> json()));
		$this -> assertEquals($response -> status , 'exito');
		$this -> assertGreaterThan(0, count($response -> body));
		
 	}
	
	/**
	  * testEliminar, Elimina un suscrupcion relacionado con el comercio 
	  *
	  * El test valida que la respuesta de la peticion rest sea una respuesta exitosa
	  * asi como el resultado contenga un objeto especificando el suscrupcion creado
	  * usando el servicio http://ip-expuesta/openpay/v1/monetizador/suscrupciones/:id mediante el 
	  * metodo DELETE
	  * 
	  * @author Christian Hernandez <christian.hernandez@masnegocio.com>
	  * @version 1.0
	  * @copyright MásNegocio 
	  * 
	  * @param assertTrue assertTrue(is_array($request -> json())) Verifica que la respuesta se parse como un array 
	  * @param assertEquals assertEquals($response -> status , 'exito'), Verifica que la respuesta sea la palabra exito.
	  * 
	  * @test
	  */
	public function eliminar(){
					
		$client = new GuzzleHttp\Client();
		$idCliente = "aeyaft9soq3kbzzipt5w";
		$options = array(
							'body' 		=> array()
							,'config'	=> array()
							,'headers'	=> array()	
						 );
			
		$servicio = sprintf("%s%s%s%s",$this -> server, $this -> servicioBase, $this -> recurso,$idCliente);
		$request = $client -> delete($servicio,$options);
		error_log(print_r("Resultado de eliminar >>>>>>>>>>>>>\n",true));
		error_log(print_r((string) $request -> getBody(),true));	
		
		$response = $request  -> json(array('object' => true));
 		$this -> assertTrue(is_array($request -> json()));
		$this -> assertEquals($response -> status , 'exito');
 	}
	
	/**
	  * Actualizar, Actualiza un suscrupcion relacionado con el comercio 
	  *
	  * El test valida que la respuesta de la peticion rest sea una respuesta exitosa
	  * usando el servicio http://ip-expuesta/openpay/v1/monetizador/suscrupciones/id mediante el 
	  * metodo PUT
	  * 
	  * @author Christian Hernandez <christian.hernandez@masnegocio.com>
	  * @version 1.0
	  * @copyright MásNegocio 
	  * 
	  * @param assertTrue assertTrue(is_array($request -> json())) Verifica que la respuesta se parse como un array 
	  * @param assertEquals assertEquals($response -> status , 'exito'), Verifica que la respuesta sea la palabra exito.
	  * 
	  * @tes
	  */
	public function actualizar(){
 		
		$dataRequest = array(
		     'name' => 'customer name Actualizado 5',
		     'last_name' => 'Last name 5',
		     'email' => 'customer_email5@me.com'
		   );
		$idCliente = "amrplbeoiagnrrqwetui";
		$client = new GuzzleHttp\Client();
		
		$options = array(
							'body' 	=>  $dataRequest
							,'config'	=> array()
							,'headers'	=> array()	
						 );
			
		$servicio = sprintf("%s%s%s%s",$this -> server, $this -> servicioBase, $this -> recurso,$idCliente);
		$request = $client -> put($servicio,$options);
		error_log(print_r("Resultado de actualizar >>>>>>>>>>>>>\n",true));
		error_log(print_r((string) $request -> getBody(),true));	
							
		$response = $request  -> json(array('object' => true));
 		$this -> assertTrue(is_array($request -> json()));
		$this -> assertEquals($response -> status , 'exito');
		$this -> assertGreaterThan(0, count($response -> body));
 	}
}
?>