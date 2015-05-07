<?php

require('TypeValidator.php');

interface TestInterface{
}

class TestClass implements TestInterface{
	
	//basic
	function sum($a, $b){
		TypeValidator::check(func_get_args(),array('integer','integer'));
		return $a+$b;
	}

	//advanced
	function f1($param1, $param2, $param3){
		$custom_config = array(
			//'stop_level'=>2 // uncomment to overwrite and force set fatal error if check return false
		);
		$validation_result = TypeValidator::check(func_get_args(),array('float',null,'integer'), $custom_config);
		
		if(is_array($validation_result)){
			//debug errors if dev
			echo "----------DEBUG ERRORS-----------<br>";
			print_r($validation_result);	
			echo "<br>--------------------------<br>";			
		}else{
			echo 'f1 ok';
		}
	}			

}

class TestClass2 extends TestClass{	
}