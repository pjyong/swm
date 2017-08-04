<?php
namespace Cheyoo\System\Ext\SSDB;

class CSSDBCacheServerConfiguration extends CComponent
{
	/**
	 * @var string SSDB server hostname or IP address
	 */
	public $host;
	/**
	 * @var integer SSDB server port
	 */
	public $port = 8888;
	/**
	 * @var integer value in seconds which will be used for connecting to the server
	 */
	public $timeout = 15;

	/**
	 * Constructor.
	 * @param array $config list of SSDB server configurations.
	 * @throws CException if the configuration is not an array
	 */
	public function __construct($config)
	{
		foreach($config as $key=>$value)
				$this->$key=$value;
		if($this->host===null)
			throw new CException(Yii::t('yii','CSSDBCache server configuration must have "host" value.'));
	}
}
