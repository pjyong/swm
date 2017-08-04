<?php
namespace Cheyoo\System\Ext\SSDB;

use Cheyoo\System\Ext\Yii\CCache;

class CSSDBCache extends CCache
{
	/**
	 * @var ssdb instance.
	 */
	public $_cache = null;

	public $_cacheKeys = 'leveldb_cachekey';

	/**
	 * @var array list of ssdb server configurations.
	 */
	public $_servers = array();

	public function init()
	{
		parent::init();
		$servers = $this->getServers();
		$this->_cache =  new SSDB($servers->host, $servers->port, $servers->timeout);
	}

	public function getServers()
	{
		return $this->_servers;
	}

	public function setServers($config)
	{
		$this->_servers = new CSSDBCacheServerConfiguration($config);
	}

	public function getKeys()
	{
		return $this->_cache->hkeys($this->_cacheKeys, '', '', $this->_cache->hsize($this->_cacheKeys)->data)->data;
	}

	protected function getValue($key)
	{
		return $this->_cache->get($key)->data;
	}

	protected function setValue($key, $value, $expire)
	{
		$this->_cache->hset($this->_cacheKeys, $key, 1);
		if ($expire > 0)
			return $this->_cache->setx($key, $value, (int)$expire);
		else
			return $this->_cache->set($key, $value);
	}

	protected function addValue($key,$value,$expire)
	{
		return $this->setValue($key, $value, $expire);
	}

	protected function deleteValue($key)
	{
		$this->_cache->hdel($this->_cacheKeys, $key);
		return $this->_cache->del($key);
	}

	protected function flushValues()
	{
		$this->_cache->multi_del($this->getKeys());
		return $this->_cache->hclear($this->_cacheKeys);
	}
}
