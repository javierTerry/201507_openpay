<?php
require_once(dirname(dirname(dirname(__FILE__)))."/dependencies/vendor/autoload.php");

class TarjetaTest extends PHPUnit_Framework_TestCase {
	
	private $idCliente  = "ak3hcep2n4faqqrey8t7/";
	private $server		= "http://localhost/";
	private $servicioBase="openpay/v1/monetizador/clientes/";
	private $recurso	= "tarjetas/";
	
 	/**
	  * Test Listar, lista los Tarjetaes relacionados con el comercio 
	  *
	  * El test valida que la respuesta de la peticion rest sea una respuesta exitosa
	  * asi como el resultado contenga una lista de Tarjetaes 
	  *
	  * @author Christian Hernandez <christian.hernandez@masnegocio.com>
	  * @version 1.0
	  * @copyright MásNegocio 
	  * 
	  * @param assertTrue assertTrue(is_array($request -> json())) Verifica que la respuesta se parse como un array 
	  * @param assertEquals assertEquals($response -> status , 'exito'), Verifica que la respuesta sea la palabra exito.
	  * @param assertGreaterThan assertGreaterThan(0, count($response -> body)), Verifica qe el body contega almenos un registro.
	  *  
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
	  * testListarEspecifico, lista un Tarjeta especifico relacionados con el comercio 
	  *
	  * El test valida que la respuesta de la peticion rest sea una respuesta exitosa
	  * asi como el resultado contenga una lista de Tarjetaes 
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
		$idTarjeta = "kt3qrvmwz29n2m9lvm4z";
		$servicio = sprintf("%s%s%s%s%s",$this -> server, $this -> servicioBase, $this -> idCliente, $this -> recurso,$idTarjeta);
		$request = $client -> get($servicio);
		error_log(print_r("Resultado de testListarEspecifico >>>>>>>>>>>>>\n",true));
		error_log(print_r((string) $request -> getBody(),true));
		$response = $request  -> json(array('object' => true));
 		$this -> assertTrue(is_array($request -> json()));
		$this -> assertEquals($response -> status , 'exito');
		$this -> assertGreaterThan(0, count($response -> body));
 	}
	
	/**
	  * testCrear, Crea un Tarjeta relacionado con el comercio 
	  *
	  * El test valida que la respuesta de la peticion rest sea una respuesta exitosa
	  * asi como el resultado contenga un objeto especificando el Tarjeta creado
	  * usando el servicio http://ip-expuesta/openpay/v1/monetizador/Tarjetaes/ mediante el 
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
 		//**Tarjeta declinada//
		$dataRequest = array(
						    'holder_name' => 'Teofilo Velazco',
						    'card_number' => '4916394462033681',
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
										
		$dataRequest = array(
						    'holder_name' => 'Teofilo Velazco',
						    'card_number' => '4111111111111111',
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
					
		$client = new GuzzleHttp\Client();
		
		$options = array(
							'body' 	=>  $dataRequest
							,'config'	=> array()
							,'headers'	=> array()	
						 );
			
		$servicio = sprintf("%s%s%s%s",$this -> server, $this -> servicioBase, $this -> idCliente, $this -> recurso);
		$request = $client -> post($servicio,$options);
		error_log(print_r("Resultado de testListarEspecifico >>>>>>>>>>>>>\n",true));
		error_log(print_r((string) $request -> getBody(),true));					
		$response = $request  -> json(array('object' => true));
 		$this -> assertTrue(is_array($request -> json()));
		$this -> assertEquals($response -> status , 'exito');
		$this -> assertGreaterThan(0, count($response -> body));
		
 	}
	
	/**
	  * testEliminar, Elimina un Tarjeta relacionado con el comercio 
	  *
	  * El test valida que la respuesta de la peticion rest sea una respuesta exitosa
	  * asi como el resultado contenga un objeto especificando el Tarjeta creado
	  * usando el servicio http://ip-expuesta/openpay/v1/monetizador/Tarjetaes/:id mediante el 
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
		$idTarjeta = "kfa1rckvtjynxm1lpns8";
		$options = array(
							'body' 		=> array()
							,'config'	=> array()
							,'headers'	=> array()	
						 );
			
		$servicio = sprintf("%s%s%s%s%s",$this -> server, $this -> servicioBase, $this -> idCliente, $this -> recurso,$idTarjeta);
		$request = $client -> delete($servicio,$options);
		error_log(print_r("Resultado de testEliminar >>>>>>>>>>>>>\n",true));
		error_log(print_r((string) $request -> getBody(),true));
							
		$response = $request  -> json(array('object' => true));
 		$this -> assertTrue(is_array($request -> json()));
		$this -> assertEquals($response -> status , 'exito');
 	}
}
?>