<?php
namespace Cheyoo\System\Ext\SSDB;

class SimpleSSDB extends SSDB
{
	function __construct($host, $port, $timeout_ms=2000){
		parent::__construct($host, $port, $timeout_ms);
		$this->easy();
	}
}
