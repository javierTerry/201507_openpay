<?php
namespace Implementacion\MyTrait;

trait MagicMethod {
	
	public function __set($key, $value) {
		if ($value === '' || !$value){
			error_log(" The property '".$key."' will be set to en empty string which will be intepreted ad a NULL in request");
		} 
		
		$this -> $key = $value;
	}
	
	public function __get($key) {
		if (property_exists($this, $key)) {
			return $this->$key;
		} else {
			$resourceName = get_class($this);
			error_log(" Undefined property of $resourceName instance: $key"); // TODO error_log?
			return null;
		}
	}
}

?>