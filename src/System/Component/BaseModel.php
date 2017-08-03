<?php
namespace Cheyoo\System\Component;

/**
 * Model基类
 */
class BaseModel
{
	protected $connectionDBSYS = null;		//the system db connection object
	protected $connectionDBM = null;		//the master db connection object
	protected $connectionDBS = null;		//the slave db connection object
	protected $connectionMSSQL = null;		//the mssql db connection object
	protected $connectionDBPAY = null;
	protected $connectionDBSUP = null;
	protected $command;
	protected $connectionDB2016 = null;		//the 2016 db connection object
    protected $connectionDB7766 = null;
    protected $connectionDB7767 = null;
    protected $connectionDB7711 = null;

    /**
	 * 初始化数据库连接
	 */
	public function __construct()
	{
//		if(null === $this->connectionDBSYS)
//			$this->connectionDBSYS = Yii::app()->db;
//		if(null === $this->connectionDBM)
//			$this->connectionDBM = Yii::app()->db1;
//		if(null === $this->connectionDBS)
//			$this->connectionDBS = Yii::app()->db2;
//		if(null === $this->connectionDBPAY)
//			$this->connectionDBPAY = Yii::app()->db3;
        //if(null === $this->connectionDBSUP)
            //$this->connectionDBSUP = Yii::app()->db4;
	}

	//初始化数据库连接 被动  $this->initDB($db)
	public function initDB($db)
	{
		if($db == 'system' && null === $this->connectionDBSYS)
			$this->connectionDBSYS = Yii::app()->db;
		if($db == 'master' && null === $this->connectionDBM)
			$this->connectionDBM = Yii::app()->db1;
		if($db == 'slave' && null === $this->connectionDBS)
			$this->connectionDBS = Yii::app()->db2;
		if($db == 'dbpay' && null === $this->connectionDBPAY)
			$this->connectionDBPAY = Yii::app()->db3;
		if($db == 'support' && null === $this->connectionDBSUP)
            $this->connectionDBSUP = Yii::app()->db4;
		if($db == '2016' && null ===$this->connectionDB2016)
			$this->connectionDB2016 = Yii::app()->db5;
        if($db == 'slave_7766' && null ===$this->connectionDB7766)
            $this->connectionDB7766 = Yii::app()->db6;
        if($db == 'slave_7767' && null ===$this->connectionDB7767)
            $this->connectionDB7767 = Yii::app()->db7;
        if($db == 'slave_7711' && null ===$this->connectionDB7711)
            $this->connectionDB7711 = Yii::app()->db8;
	}

    //初始化command
    public function initCommand($db,$sql)
    {
        if($db == 'master')
            $this->command = $this->connectionDBM->createCommand($sql);
        elseif ($db == 'system')
            $this->command = $this->connectionDBSYS->createCommand($sql);
        elseif ($db == 'dbpay')
            $this->command = $this->connectionDBPAY->createCommand($sql);
        elseif ($db == 'support')
            $this->command = $this->connectionDBSUP->createCommand($sql);
        elseif ($db == '2016')
            $this->command = $this->connectionDB2016->createCommand($sql);
        elseif ($db == 'slave_7766')
            $this->command = $this->connectionDB7766->createCommand($sql);
        elseif ($db == 'slave_7767')
            $this->command = $this->connectionDB7767->createCommand($sql);
        elseif ($db == 'slave_7711')
            $this->command = $this->connectionDB7711->createCommand($sql);
        else
            $this->command = $this->connectionDBS->createCommand($sql);
    }

    //初始化dbConnection
    public function initDBConnection($db)
    {
        $dbConnection = '';
        if($db == 'master')
            $dbConnection = $this->connectionDBM;
        elseif ($db == 'system')
            $dbConnection = $this->connectionDBSYS;
        elseif ($db == 'slave')
            $dbConnection = $this->connectionDBS;
        elseif ($db == 'mssql')
            $dbConnection = $this->connectionMSSQL;
        elseif ($db == '2016')
            $dbConnection = $this->connectionDB2016;
        elseif ($db == 'slave_7766')
            $dbConnection = $this->connectionDB7766;
        elseif ($db == 'slave_7767')
            $dbConnection = $this->connectionDB7767;
        elseif ($db == 'slave_7711')
            $dbConnection = $this->connectionDB7711;
        return $dbConnection;
    }

	public function attributeNames(){}

	// 防止缓存失效击穿数据库
	public function startFileLock($key)
	{
		// 将这些SQL语句存入日志文件
		// Yii::log($key, 'error', 'error');
		$fp = fopen(dirname(__FILE__).'/../runtime/keys/'.md5($key), 'w+');
		if (!flock($fp, LOCK_EX)) {
		}
		return $fp;
	}

	public function endFileLock($fp)
	{
		flock($fp, LOCK_UN);
		fclose($fp);
	}

	/**
	 * 数据获取
	 *
	 * @param string $sql
	 * @param string $db
	 * @param string $method
	 * @param integer $time
	 * @param string $tag
	 * @return array
	 */
	private function getData($sql, $db, $method, $time, $cacheKey, $tag)
	{
		global $cacheStatistic;
		// 如果时间为0，那么直接查询
		if($time == 0){
			$this->initDB($db);
			$this->initCommand($db,$sql);
			$rs = $this->command->$method();
			return $rs;
		}
		$key = !empty($cacheKey) ? $cacheKey : $sql;
		$cacheStatistic['query'] = $cacheStatistic['query'] + 1;
		$rs = $this->cacheGet($key);
		if($rs === false) {
			// 查数据库加锁
			$fp = $this->startFileLock($key);
			$cacheStatistic['hitdb'] = $cacheStatistic['hitdb'] + 1;
			$this->initDB($db);
            $this->initCommand($db,$sql);
			$rs = $this->command->$method();
			if(!empty($time))
				$this->cacheSet($key, $rs, $time, $tag);

			$this->endFileLock($fp);
		}else{
			// 往tag里面添加
			if($tag){
				//
				$this->setCacheTag($tag, $key);
			}
		}

		return $rs;
	}

	/**
	 * 执行SQL语句
	 *
	 * @param string or array $sql	需要执行的SQL语句
	 * @param string $db 对应的数据库
	 * @param string or array $method SQL语句执行方法
	 * @param integer $time	缓存时间 单位：秒
	 * @param string or array $colum 列名，在通常在分页时用
	 * @param string or array $cacheKey	自定义缓存Key，如果为空则使用当前执行的SQL语句md5加密作为缓存Key
	 * @param string $tag	缓存Key的标签
	 * @return array
	 */
	public function executer($sql, $db, $method, $time = 0, $cacheKey = '', $tag = '')
	{
		if(is_array($sql)) {	//Sql参数为数组
			if(!is_array($method))	//当Sql参数为数组时，method必须为数组
				return false;

			if (empty($cacheKey))
				$cacheKey = json_encode($sql);
            // 如果 cacheTime 为 0 ，则不从 cache 获取数据
            $rs = false;
            if($time != 0){
                $rs = $this->cacheGet($cacheKey);
            }
			// Yii::log($cacheKey, 'error', 'june');

			if($rs !== false){
				if($tag){
					$this->setCacheTag($tag, $cacheKey);
				}
				return $rs;
			}

			foreach ($sql as $key=>$val)
				// 禁用次级查询的缓存
				$rs[$key] = $this->getData($val, $db, $method[$key], 0, $cacheKey, $tag);

			if(!empty($time))
				$this->cacheSet($cacheKey, $rs, $time, $tag);
		}else
			$rs = $this->getData($sql, $db, $method, $time, $cacheKey, $tag);
		return $rs;
	}

	/**
	 * 设置缓存
	 *
	 * @param string $key
	 * @param mixed $data
	 * @param integer $time
	 */
	public function cacheSet($key, $data, $time = 600, $tag = '')
	{
		$obj = $this->getCacheObject();
		if (empty($obj))	return  false;

		if (!empty($tag))
			$this->setCacheTag($tag, $key);

		$obj->set(md5($key), $data, $time);
	}

	/**
	 * 获得缓存值
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function cacheGet($key)
	{
// 		Yii::log($key, 'error', 'error');
		$obj = $this->getCacheObject();
		if (empty($obj))	return  false;
		return $obj->get(md5($key));
	}

	/**
	 * 删除缓存值
	 */
	public function cacheDelete($key)
	{
		$obj = $this->getCacheObject();
		if (empty($obj))	return  false;
		return $obj->delete(md5($key));
	}

	/**
	 * 刷新缓存值
	 */
	public function cacheFlush()
	{
		$obj = $this->getCacheObject();
		if (empty($obj))	return  false;
		$obj->flush();
	}

	/**
	 * 刷新指定缓存，如果缓存数据为分页列表，则指定$cycles参数。$cycles为每页记录数
	 * 如果$cycles不为空，则$cacheKey需要去掉于分页相关的部分。一般为：'_' . $offset . '_' . $pageSize
	 *
	 * @param string $cacheKey
	 * @param integer $cycles
	 * @return boolean
	 */
	public function cacheFlushPlus($cacheKey, $cycles = 0)
	{
		if (empty($cacheKey))
			return false;

		$cacheObj = $this->getCacheObject();
		if (empty($cacheObj))	return false;

		if (empty($cycles))
			$cacheObj->delete(md5($cacheKey));
		else {
			$i = 0;
			$key = md5($cacheKey . '_' . ($i * $cycles) . '_' . $cycles);
			while ($cacheObj->offsetExists($key)) {
				$cacheObj->delete($key);
				$i++;
			}
		}
	}

	/**
	 * 设置缓存tag和key
	 * @param string or array $tag	md5字符串
	 * @param string $key				明文字符串
	 * @return boolean
	 */
	private function setCacheTag($tag, $key)
	{
		if (empty($tag) || empty($key))
			return false;

		if (is_array($tag)) {
			foreach ($tag as $val) {
				$this->cacheTag($val, $key);
			}
		} else {
			$this->cacheTag($tag, $key);
		}
	}

	/**
	 * 缓存标签与key之间的关系
	 * @param string $tag
	 * @param string $key
	 * @return boolean
	 */
	private function cacheTag($tag, $key)
	{
		if (empty($tag) || empty($key))
			return false;

		if (!in_array($tag, Yii::app()->params['cacheTags']))
			return false;

		$cacheObj = $this->getCacheObject();
		$expire = 157680000;
		$tag = md5($tag);
		$fp = $this->startFileLock($tag);
		if ($cacheObj->offsetExists($tag)) {	// 如果tag存在
			$keyList = json_decode($cacheObj->get($tag), true);
			if(!is_array($keyList)){
				return false;
			}
			if (!in_array($key, $keyList)) {	// key 不存在则将key加入到keylist中
				array_push($keyList, $key);
				$cacheObj->set($tag, json_encode($keyList), $expire);
			}
		} else {
			$cacheObj->set($tag, json_encode(array($key)), $expire);
		}
		$this->endFileLock($fp);
		return true;
	}

	/**
	 * 根据tag删除对应key的缓存
	 * @param string $tag
	 * @return boolean
	 */
	public function flushCacheByTag($tag)
	{
		if (empty($tag))
			return false;

		if (!in_array($tag, Yii::app()->params['cacheTags']))
			return false;
		$oldTag = $tag;
		$tag = md5($tag);
		$cacheObj = $this->getCacheObject();
		if ($cacheObj->offsetExists($tag)) {
			$keyList = json_decode($cacheObj->get($tag), true);
			Yii::log('allKeys from '.$oldTag, 'error', 'error');
			Yii::log('allKeys total num: '.count($keyList), 'error', 'error');
			Yii::log(var_export($keyList, true), 'error', 'error');
			try{
			if (!empty($keyList)) {
				$deleteIndex = 0;
				foreach ($keyList as $val) {
					if(!$cacheObj->delete(md5($val))){
						Tools::debug('delete cache failed:'.md5($val));
					}else{
						$deleteIndex = $deleteIndex + 1;
					}
				}
				Yii::log('allKeys delete num: '.$deleteIndex, 'error', 'error');
			}
			$cacheObj->delete($tag);
			}catch(Exception $e){
				echo $e->getMessage();
				exit;
			}
		}
		return true;
	}

	/**
	 * 根据一个或者多个缓存标签刷新缓存
	 * @param string or array $tags
	 * @return boolean
	 */
	public function flushCacheByTags($tags)
	{
		if (empty($tags))
			return false;

		if (is_array($tags)) {
			foreach ($tags as $val) {
				$this->flushCacheByTag($val);
			}
		} else {
			$this->flushCacheByTag($tags);
		}
	}

	/**
	 * 获取缓存对象
	 * @return Ambigous <NULL, CCache>
	 */
	private function getCacheObject()
	{
		$obj = null;
		if(!empty(Yii::app()->ssdb) && Yii::app()->params->ssdbcache)
			$obj = Yii::app()->ssdb;
		elseif (!empty(Yii::app()->cache))
			$obj = Yii::app()->cache;
		return $obj;
	}

	/**
	 * Similar to PEAR DB's autoExecute(), except that
	 * If $mode == 'UPDATE', then $where is compulsory as a safety measure.
	 *
	 * @param string $db  数据库
	 * @param string $table 目标数据表
	 * @param array $fields_values 操作的数据，数组
	 * @param string $mode INSERT OR UPDATE
	 * @param string $where 如果是UPDATE，$where则为UPDATE的条件
	 * @param string or array $cacheTag 需要刷新的缓存标签
	 * @return integer
	 */
	public function AutoExecute($db, $table, $fields_values, $mode = "INSERT", $where = '', $returnLastInsertID = false, $cacheTag = '')
	{
		if ($mode == 'UPDATE' && $where == '') {
			throw new CException(Yii::t('yii','{class}AutoExecute: Illegal mode=UPDATE with empty WHERE clause.',
					array('{class}'=>get_class($this))));
			return false;
		}

		if(empty($fields_values)) {
			throw new CException(Yii::t('yii','{class}fields_values is empty.', array('{class}'=>get_class($this))));
			return false;
		}
		$this->initDB($db);
        $dbConnection = $this->initDBConnection($db);
        if(empty($dbConnection)){
            throw new CException(Yii::t('yii','{class}AutoExecute: $db must be master.', array('{class}'=>get_class($this))));
            return false;
        }

		//检查表是否存在
		$tableNames = $this->cacheGet($db . '_tableNames');
		if ($tableNames == false)
			$tableNames = $dbConnection->getSchema()->getTableNames();
		if (!in_array($table, $tableNames))
			return false;	//table does not exist

		//获取数据库表结构，并缓存
		//$this->cacheDelete($db . $table);
		$dbSchemaTable = $this->cacheGet($db . $table);
		if($dbSchemaTable === false) {
			$dbSchemaTable = $dbConnection->getSchema()->tables[$table];
			$this->cacheSet($db . $table, $dbSchemaTable, 3600*30, 'System');		//将表结构缓存30天
		}

		$fields_values_keys = array_keys($fields_values);
		$columCount = count($fields_values_keys);
		switch ((string)$mode){
			case 'UPDATE':
				$sql = "UPDATE ".$table." SET ";
				foreach ($fields_values_keys as $key => $val)
					foreach ($dbSchemaTable->columns as $v)
						if($v->name == $val)		//比较提交与数据库表字段，表结构中没有的字段忽略掉
						{
							$sql .= '`' . $val . '`' . '=:' . $val;
							if($key != $columCount-1)
								$sql .= ',';
						}
				$sql .= ' WHERE ' . $where;
				$this->command = $dbConnection->createCommand($sql);
				foreach ($fields_values_keys as $val)
					if(!empty($dbSchemaTable->columns[$val]))
						$this->command->bindValue(':'.$val, $fields_values[$val], $this->getColumnType($dbSchemaTable->columns[$val]));
				break;

			case 'INSERT':
				$sql = "INSERT INTO ".$table." (";
				foreach ($fields_values_keys as $key=>$val)
					foreach ($dbSchemaTable->columns as $v)
						if($v->name == $val)
						{
							$sql .= '`'.$val.'`';
							if($key != $columCount-1)
								$sql .= ',';
						}
				$sql .= ") VALUES (";
				foreach ($fields_values_keys as $key=>$val)
					foreach ($dbSchemaTable->columns as $v)
						if($v->name == $val)
						{
							$sql .= ':'.$val;
							if($key != $columCount-1)
								$sql .= ',';
							else
								$sql .= ')';
						}

				$this->command = $dbConnection->createCommand($sql);
				foreach ($fields_values_keys as $val)
					if(!empty($dbSchemaTable->columns[$val]))
						$this->command->bindValue(':'.$val, $fields_values[$val], $this->getColumnType($dbSchemaTable->columns[$val]));
				break;

			default:
				throw new CException(Yii::t('yii','{class}AutoExecute: Unknown mode=$mode.', array('{class}'=>get_class($this))));
				return false;
		}
		$rs = $this->command->execute();

		if (!empty($cacheTag))
			$this->flushCacheByTags($cacheTag);

		if($returnLastInsertID)
			return $dbConnection->lastInsertID;
		else
			return $rs;
	}

	/**
	 * 根据字段类型确定绑定参数的PDO参数类型
	 *
	 * @param object $columnSchema
	 * @return integer
	 */
	protected function getColumnType($columnSchema)
	{
		$phpType = $columnSchema->type;
		if($phpType == 'string' || $phpType == 'double')
			return PDO::PARAM_STR;
		elseif ($phpType == 'boolean')
			return PDO::PARAM_BOOL;
		elseif ($phpType == 'integer')
			return PDO::PARAM_INT;
		else
			return PDO::PARAM_STR;
	}

	/**
	 * 获取数据列表，当前只针对单表操作
	 * @param string $db		数据库类型 master, slave, system
	 * @param string $table		表名称
	 * @param string $where		附加查询条件, where 前不加 AND
	 * @param string $column	需要获取的字段，多个使用逗号分隔
	 * @param number $offset	分页偏移
	 * @param number $pageSize	分页每页数据量
	 * @param integer $cacheTime	缓存时间，单位：秒
	 * @param string $cacheKey	自定义CacheKey
	 * @param string $tag	缓存标签
	 */
	public function getList($db, $table, $where = '', $offset = 0, $pageSize = 15, $column = '*', $orderBy = '', $cacheTime = 0, $cacheKey = '', $tag = '')
	{
		$where = !empty($where) ? "AND " . $where : '';
		$limiter = !empty($pageSize) ? " LIMIT ".$offset.",".$pageSize."" : '';
		$orderBy = !empty($orderBy) ? " ORDER BY " . $orderBy : ' ORDER BY Sort ASC, ID DESC';
		$sql = "SELECT ".$column." FROM ".$table." WHERE 1=1 ".$where;
		$sqlArr = array(
			'Count'=>"SELECT COUNT(*) AS Count FROM (".$sql.") x ",
			'List'=>$sql . $orderBy . $limiter
		);
		return $this->executer($sqlArr, $db, array('Count'=>'queryScalar', 'List'=>'queryAll'), $cacheTime, $cacheKey, $tag);
	}

	/**
	 * 获取一条数据，只支持单表
	 * @param string $db
	 * @param string $table
	 * @param string $where
	 * @param string $column
	 * @param number $cacheTime
	 * @param string $cacheKey
	 * @param string $tag
	 * @return Ambigous <multitype:, boolean, mixed, string>
	 */
	public function getInfo($db, $table, $where = '', $column = '*', $except = array(), $cacheTime = 0, $cacheKey = '', $tag = '')
	{
		$where = !empty($where) ? "AND " . $where : '';
		$sql = "SELECT ".$column." FROM ".$table." WHERE 1=1 " . $where;
		$rs = $this->executer($sql, $db, 'queryRow', $cacheTime, $cacheKey, $tag);
		if (!empty($except)) {
			foreach ($except as $val)
				unset($rs[$val]);
		}
		return $rs;
	}

	/**
	 * 获取记录总数
	 *
	 * @param string $db
	 * @param string $table
	 * @param string $where
	 * @param number $cacheTime
	 * @param string $cacheKey
	 */
	public function getCount($db, $table, $signCloumn = '', $where = '', $cacheTime = 0, $cacheKey = '', $tag = '')
	{
		$where = !empty($where) ? "AND " . $where : '';
		$cloumn = !empty($signCloumn) ? $signCloumn . ',' : '';
		$groupBy = !empty($signCloumn) ? ' GROUP BY ' . $signCloumn : '';
		$sql = "SELECT ".$cloumn."COUNT(*) AS Count FROM ".$table." WHERE 1=1 " . $where . $groupBy;
		return $this->executer($sql, $db, 'queryAll', $cacheTime, $cacheKey, $tag);
	}

	public function addInfo($db, $table, $data)
	{
	}

	public function updateInfo($db, $table, $data, $where)
	{
	}

	/**
	 * 删除一条数据，只支持单表
	 * @param string $db
	 * @param string $table
	 * @param string $where
	 * @return boolean
	 */
	public function deleteInfo($db, $table, $where)
	{
		if (empty($where))
			return false;
		$sql = "DELETE FROM ".$table." WHERE " . $where;
		$rs = $this->executer($sql, $db, 'execute');
		return !empty($rs) ? true : false;
	}

	/**
	 * 根据给的值，返回值对应的数据
	 * @param string $db
	 * @param string $table
	 * @param string $column
	 * @param string $value
	 */
	public function getArrayValues($db, $table, $sign, $column, $columnWithValue, $value)
	{
		if (empty($value) || empty($column) || empty($columnWithValue))
			return false;

		if (is_array($value))
			$value = implode(',', $value);

		$sql = " SELECT ".$column." FROM ".$table."
				WHERE ".$columnWithValue." IN (".$value.")
				AND Sign='".$sign."'";
		$rs = $this->executer($sql, $db, 'queryAll', TIME_MONTH);
		$data = array();
		if (!empty($rs)) {
			foreach ($rs as $val)
				$data[] = $val[$column];
		}
		return implode(',', $data);
	}

	//批量插入
	public function AutoBachExecute($db, $table, $sqlValue, $returnLastInsertID = false, $cacheTag = '')
	{
		$this->initDB($db);
        $dbConnection = $this->initDBConnection($db);
        if(empty($dbConnection)){
            throw new CException(Yii::t('yii','{class}AutoExecute: $db must be master.', array('{class}'=>get_class($this))));
            return false;
        }

        //检查表是否存在
        $tableNames = $this->cacheGet($db . '_tableNames');
        if ($tableNames == false)
            $tableNames = $dbConnection->getSchema()->getTableNames();
            if (!in_array($table, $tableNames))
                return false;	//table does not exist

                //获取数据库表结构，并缓存
                //$this->cacheDelete($db . $table);
                $dbSchemaTable = $this->cacheGet($db . $table);
                if($dbSchemaTable === false) {
                    $dbSchemaTable = $dbConnection->getSchema()->tables[$table];
                    $this->cacheSet($db . $table, $dbSchemaTable, 3600*30);		//将表结构缓存30天
                }

                $this->command = $dbConnection->createCommand($sqlValue);

                $rs = $this->command->execute();

                if (!empty($cacheTag))
                    $this->flushCacheByTags($cacheTag);

                    if($returnLastInsertID)
                        return $dbConnection->lastInsertID;
                        else
                            return $rs;
	}
}
