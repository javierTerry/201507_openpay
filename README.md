# 201507_openpay
Investigación para realizar pagos desde la web o dispositivo movil

Qué es nuevo?
=============

Version 0.5 (July 29st, 2015)
----------------------------------
 
Version 0.5 Implementacion Cliente - Eliminar
Se agrega los siguientes recursos

* ``/v1/monetizador/clientes/:id`` 			(DELETE)
* ``/v1/monetizador/clientes/:id`` 			(PUT)

$_REQUEST = array(
     'name' => 'customer name',
     'last_name' => '',
     'email' => 'customer_email@me.com',
     'phone_number' => '44209087654',
     'address' => {"line1": "Calle 10", "line2": "col. san pablo","line3": "entre la calle 1 y la 2","state": "Queretaro","city": "Queretaro","postal_code": "76000","country_code": "MX"} 
   );

Qué es nuevo?
=============

Version 0.4 (July 18st, 2015)
----------------------------------
 
Version 0.4 Implementacion Cliente - Listar
Se agrega los siguientes recursos

* ``/v1/monetizador/clientes`` 				(GET)
* ``/v1/monetizador/clientes/:id`` 			(GET)

Ejemplo de request, solo para ``/v1/monetizador/clientes``

$_REQUEST = array(
    'creation[gte]' => '2013-01-01',
    'creation[lte]' => '2013-12-31',
    'offset' => 0,
    'limit' => 5);


Qué es nuevo?
=============

Version 0.3 (July 12st, 2015)
----------------------------------
 
Version 0.3 Implementacion Cliente - Crear
Se agrega los siguientes recursos

* ``/v1/monetizador/clientes`` (POST)

Ejemplo de request

$_REQUEST = array(
     'external_id' => '',
     'name' => 'customer name',
     'last_name' => '',
     'email' => 'customer_email@me.com',
     'phone_number' => '44209087654',
     'address' => {"line1": "Calle 10", "line2": "col. san pablo","line3": "entre la calle 1 y la 2","state": "Queretaro","city": "Queretaro","postal_code": "76000","country_code": "MX"} 
   );


What's new?
===========

Version 0.2 (July 12st, 2015)
----------------------------------

Recursos

* ``/v1/monetizador/test`` 					(GET)
* ``/v1/monetizador/cargos`` 				(GET)
* ``/v1/monetizador/cargos`` 				(POST)
* ``/v1/monetizador/cargos/clientes:/id`` 	(POST)
* ``/v1/monetizador/tarjetas`` 				(GET)
* ``/v1/monetizador/tarjetas`` 				(POST)
* ``/v1/monetizador/tarjetas/:id`` 			(DELETE)
* ``/v1/monetizador/clientes`` 				(POST)
* ``/v1/monetizador/tarjetas/clientes/:id`` (POST)
* ``/v1/monetizador/tarjetas/clientes/:id`` (GET)
* ``/v1/monetizador/clientes/:idcliente/tarjetas/:id`` (GET)
* ``/applications/:client_id/tokens/:access_token`` (GET)
