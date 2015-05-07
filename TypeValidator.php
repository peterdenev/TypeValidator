<?php

/*
Ideas:
- casting or not the values
*/

class TypeValidator{
	
	/*
	check:
	0 - no check (production)
	1 - check primitives (production)/(development)
	2 - check primitives and objects  (instead use native php type hint for objects )

	log:
	0 - no log (development)
	1 - log in file (production)
	
	log_deep_level: 
	0 - no backtrace
	1 - show crashed method
	2 - show crashed method  and caller method
	
	stop:
	0 - no stop (production)
	1 - E_USER_NOTICE - show NOTICE on screen but continue 
	2 - E_USER_ERROR - show ERROR on screen and stop all (development)
	
	return:
	0 - true/false (production)
	1 - true/error_array (development)
	
	*/
	
	private static $config = array(
		'check_level' => 2,
		'log_level' => 1,
		'log_deep_level' => 2,
		'stop_level' => 1,
		'return_level' => 1,
		'nonobject_types' =>  array(
			'boolean',
			'integer',
			'float',
			'string',
			'resource',
			'array',
			'double', 
		),
		'log_file'=> 'arg_validator.log',
	);
	
	public static function setConfig(array $new_config){
		self::$config = array_merge(self::$config, $new_config);
	}
	public static function getConfig(array $new_config){
		return self::$config;
	}
	
	public static function check($args, $types, $options=array()){
		$types = (array) $types; // single param to single element array		
		$config = array_merge(self::$config, $options);
		$isOK = true;
		$err_msgs = array();
		
		if($config['check_level']>0){			
			foreach($types as $type_key=>$type_val){				
				if($type_val && count($args)>=$type_key && isset($args[$type_key])){
					$arg_type = getType($args[$type_key]); // toLower ?	
					
					// both primitives
					if(in_array($arg_type,$config['nonobject_types']) && in_array($type_val,$config['nonobject_types'])){ // ignore case ?
						if($arg_type!= ($type_val=='float' ? 'double' : $type_val) ){
							$err_msgs[$type_key] = 'Invalid argument! '.
								'Arg '.($type_key+1).
								' must be '.$type_val.' but it is '.$arg_type;							
						}
						
					// both objects
					}else if(!in_array($arg_type,$config['nonobject_types']) && !in_array($type_val,$config['nonobject_types'])){ // ignore case ?
						if($config['check_level']>1){
							if(!($args[$type_key] instanceof $type_val)){
								$err_msgs[$type_key] = 'Invalid argument! '.
									'Arg '.($type_key+1).
									' must be instance of '.$type_val.' but it is '.get_class($args[$type_key]);
							}
						}
						
					// mixed object and primitive
					}else{
						$err_msgs[$type_key] = 'Invalid argument! '.
							'Arg '.($type_key+1).
							' must be '.$type_val.' but it is '.
							( $arg_type=='object' ? get_class($args[$type_key]) : $arg_type );
			
					}
					
					if(isset($err_msgs[$type_key])){
						if($config['log_level']>0){
							$trace_info = '';
							if($config['log_level']>1){									
								$bt = debug_backtrace();
								for($d=0; $d<$config['log_deep_level']; $d++){
									$caller = array_shift($bt);	
									$trace_info .="\n\t".'['.$caller['file'].':'.$caller['line'].'] '.$caller['class'].$caller['type'].$caller['function'].'()';
								}								
							}
							file_put_contents(
								$config['log_file'] ,
								date("Y-m-d H:i:s").' | '.$err_msgs[$type_key].$trace_info."\n",
								FILE_APPEND
							);
						}
						if($config['stop_level']>0){																					
							trigger_error(
								$err_msgs[$type_key],
								$config['stop_level']==1 ? E_USER_NOTICE : E_USER_ERROR
							);
						}
						$isOK = false;								
					}
				}		
			}
		}
		return $isOK ? TRUE : 
			$config['return_level']==0 ? FALSE : $err_msgs;
	}		
	
}
