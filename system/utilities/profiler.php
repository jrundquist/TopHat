<?php

/**
* Container class for Profiler
*
* This class contains a Benchmark_Profiler. The class is instantiated
* 	with their an enabled or disabled flag. If disabled all methods and
* 	calls will return TURE and NULL respectively. This should not harm the
*	normal operation of the software. This class should be an observer
*	only. Never should any of it's returns be used in logic. If the
*	profiler is enabled, it will record the functioning of the scripts
*	being called. After all the scripts and executed and the stop()
*	method is called the results may either be analyzed or displayed.
*
*
* @category SystemClasses
* @package TopHat
* @author James Rundquist james.k.rundquist@gmail.com
* @version Release: 1.0.0
* @since Class available since Release 1.0.0
*/

require_once 'Benchmark/Profiler.php';

class Profiler{

	public $enabled = TRUE;
	private $profiler = NULL;

	public function __construct($b, $enabled){
		$this->enabled = $enabled;
		$this->profiler = new Benchmark_Profiler($b);
	}

	public function start(){
		if(FALSE == $this->enabled)
			return TRUE;
		return $this->profiler->start();
	}
	public function stop(){
		if(FALSE == $this->enabled)
			return TRUE;
		return $this->profiler->stop();
	}
	public function close(){
		if(FALSE == $this->enabled)
			return TRUE;
		return $this->profiler->close();
	}
	public function leaveSection($section){
		if(FALSE == $this->enabled)
			return TRUE;
		return $this->profiler->leaveSection($section);
	}
	public function enterSection($section){
		if(FALSE == $this->enabled)
			return TRUE;
		return $this->profiler->enterSection($section);
	}
	public function getAllSectionsInformations(){
		if(FALSE == $this->enabled)
			return TRUE;
		return $this->profiler->getAllSectionsInformations();
	}
	public function getSectionInformations($section = 'global'){
		if(FALSE == $this->enabled)
			return TRUE;
		return $this->profiler->getSectionInformations($section);
	}

	public function display( $format = 'auto'){
		if(FALSE == $this->enabled)
			return TRUE;
		return $this->profiler->display($format);
	}

	public function __get($name){
		if(FALSE == $this->enabled)
			return NULL;
		return $this->profiler->$name;
	}

	public function __set($n, $v){
		if(FALSE == $this->enabled)
			return NULL;
		$this->profiler->$n = $v;
	}


	public function save(){


	}
}