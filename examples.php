<?php

require ('Arg_classes.php');

//tests:
// try to change settings
// try to change arguments
// read info in TypeValidation.php

/*
//dev settings
TypeValidator::setConfig(array(
	'check_level' => 1,
	'log_level' => 0,
	'stop_level' => 1,	//2//1
	'return_level' => 1,	
));
*/

//prod settings
TypeValidator::setConfig(array(
	'check_level' => 1, //0
	'log_level' => 2,
	'stop_level' => 0,	
	'return_level' => 0,	
));



$test_1 = new TestClass();

echo 'TEST f1() <br>';
$test_1->f1(25.5,33,'22');

echo 'TEST sum() <br>';
echo $test_1->sum('gg','sd');
