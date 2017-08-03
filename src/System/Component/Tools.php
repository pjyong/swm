<?php
namespace Cheyoo\System\Component;

class Tools
{
	/**
	 * UTF-8 字符串截取
	 * @param string $str 需要做截取的字符串
	 * @param int $length 截取的长度
	 * @param bool $append 是否附加省略号
	 * @return string
	 */
	public static function utf8_substr($str, $length=0, $append=true)
	{
		$str = trim($str);
		$strlength = strlen($str);

		if($length == 0 || $length >= $strlength) {
			return $str;
		}elseif($length < 0) {
			$length = $strlength + $length;
			if($length < 0) {
				$length = $strlength;
			}
		}

		if(function_exists('mb_substr')) {
			$newstr = mb_substr($str, 0, $length, 'UTF-8');
		}elseif(function_exists('iconv_substr')) {
			$newstr = iconv_substr($str, 0, $length, 'UTF-8');
		}else{
			$newstr = substr($str, 0, $length);
		}

		if($append && $str != $newstr) {
			$newstr .= '...';
		}
		return $newstr;
	}

	/**
	 * 计算字符串的长度,汉字算两个字符
	 * @param string $str
	 * @return int
	 */
	public static function stringLen($str) {
		$length = strlen(preg_replace('/[\x00-\x7F]/','',$str));
		if($length) {
			return strlen($str) - $length + intval($length/3) * 2;
		}else{
			return strlen($str);
		}
	}

	/**
	 * 用一个字符串连接两个字符串
	 *
	 * @param string $str1
	 * @param string $str2
	 * @param string $linkStr
	 * @return string
	 */
	public static function joinStr($str1, $str2, $linkStr){
		if($str1 != '' && $str2 != ''){
			return $str1 . $linkStr . $str2;
		}else{
			return $str1 . $str2;
		}
	}

	/**
	 * 将二维数组的值转换成串。
	 *
	 * @param array $array
	 * @param string $glue
	 * @return string
	 */
	public static function arrayToString($array, $glue)
	{
		if(empty($array))
			return false;
		$str = '';
		foreach ($array as $val)
			$str .= $val . ',';
		return substr($str, 0, -1);
	}

	/**
	 * 字符串加密解密
	 *
	 * @param string $string	需要操作的字符串
	 * @param boolean $string	是否解密 DECODE 解密 ENCODE 加密
	 * @return string
	 */
	public static function encryption($string, $operation = 'ENCODE'){
		$key = '^&%$*Hy#t^HUY(*D';
		$key_length = strlen($key);
		if($key_length == 0) {
			return false;
		}
		$string = $operation == 'DECODE' ? base64_decode(str_replace(array('_', '-'), array('+', '/'), $string)) :
						substr(md5($string . $key), 0, 8).$string;
		$string_length = strlen($string);

		$rndkey = $box = array();
		$result = '';

		for($i = 0; $i <= 255; $i++) {
			$rndkey[$i] = ord($key[$i % $key_length]);
			$box[$i] = $i;
		}

		for($j = $i = 0; $i < 256; $i++) {
			$j = ($j + $box[$i] + $rndkey[$i]) % 256;
			$tmp = $box[$i];
			$box[$i] = $box[$j];
			$box[$j] = $tmp;
		}

		for($a = $j = $i = 0; $i < $string_length; $i++) {
			$a = ($a + 1) % 256;
			$j = ($j + $box[$a]) % 256;
			$tmp = $box[$a];
			$box[$a] = $box[$j];
			$box[$j] = $tmp;
			$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
		}

		//以上的过程就是加、解密的过程。只要key不变，($box[($box[$a] + $box[$j]) % 256])都是唯一的值，
		//加密时当1^0时变成了1，解密时1^0自然变成了1,或者这样说，加密时0^1变成1,解密时1^1就变成了0.

		if($operation == 'DECODE') {
			if(substr($result, 0, 8) == substr(md5(substr($result, 8) . $key), 0, 8)) {
				return substr($result, 8);
			} else {
				return '';
			}
		} else {
			return str_replace(array('=', '+', '/'), array('', '_','-'), base64_encode($result));
		}
	}

	/**
	 * 获取汉字首字母
	 *
	 * @param string $s0
	 * @return string
	 */
	public static function getfirstchar($s0){
		$fchar = ord($s0{0});
		if($fchar >= ord("A") and $fchar <= ord("z") )return strtoupper($s0{0});
		$s1 = iconv("UTF-8","gb2312", $s0);
		$s2 = iconv("gb2312","UTF-8", $s1);
		if($s2 == $s0){$s = $s1;}else{$s = $s0;}
		$asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
		if($asc >= -20319 and $asc <= -20284) return "A";
		if($asc >= -20283 and $asc <= -19776) return "B";
		if($asc >= -19775 and $asc <= -19219) return "C";
		if($asc >= -19218 and $asc <= -18711) return "D";
		if($asc >= -18710 and $asc <= -18527) return "E";
		if($asc >= -18526 and $asc <= -18240) return "F";
		if($asc >= -18239 and $asc <= -17923) return "G";
		if($asc >=- 17922 and $asc <=- 17418) return "H";
		if($asc >= -17922 and $asc <= -17418) return "I";
		if($asc >= -17417 and $asc <= -16475) return "J";
		if($asc >= -16474 and $asc <= -16213) return "K";
		if($asc >= -16212 and $asc <= -15641) return "L";
		if($asc >= -15640 and $asc <= -15166) return "M";
		if($asc >= -15165 and $asc <= -14923) return "N";
		if($asc >= -14922 and $asc <= -14915) return "O";
		if($asc >= -14914 and $asc <= -14631) return "P";
		if($asc >= -14630 and $asc <= -14150) return "Q";
		if($asc >= -14149 and $asc <= -14091) return "R";
		if($asc >= -14090 and $asc <= -13319) return "S";
		if($asc >= -13318 and $asc <= -12839) return "T";
		if($asc >= -12838 and $asc <= -12557) return "W";
		if($asc >= -12556 and $asc <= -11848) return "X";
		if($asc >= -11847 and $asc <= -11056) return "Y";
		if($asc >= -11055 and $asc <= -10247) return "Z";
		return null;
	}

	/**
	 * 在字符串上加上引号
	 *
	 * @param string $str
	 * @param string $type
	 * @return string
	 */
	public static function wrapStr($str, $type = 1){
		if($type == 0){
			$quotation = "'";
		}else{
			$quotation = '"';
		}
		return $quotation . $str . $quotation;
	}

	/**
	 * 调试参数中的变量并中断程序的执行，参数可以为任意多个,类型任意，
	 * 如果参数中含有'debug'参数，刚显示所有的调用过程。
	 *
	 * <code>
	 * debug($var1, $obj1, $array1[,]...);
	 * debug($var1, 'debug');
	 * </code>
	 */
	public static function debug(){
		$args = func_get_args();
		header('Content-type: text/html; charset=utf-8');
		echo "<pre>\n---------------------------------调试信息---------------------------------\n";
		foreach ($args as $value){
			if(is_null($value)){
				echo '[is_null]';
			}elseif (is_bool($value) || empty($value)){
				var_dump($value);
			}else{
				print_r($value);
			}
			echo "\n";
		}
		$trace = debug_backtrace();
		$next = array_merge(
			array(
				'line'	=> '??',
				'file'	=> '[internal]',
				'class' => null,
				'function' => '[main]'
			),
			$trace[0]
		);

		/*if(strpos($next['file'], ZEQII_PATH) === 0){
			$next['file'] = str_replace(ZEQII_PATH, DS . 'library' . DS, $next['file']);
		}elseif (strpos($next['file'], ROOT_PATH) === 0){
			$next['file'] = str_replace(ROOT_PATH, DS . 'public' . DS, $next['file']);
		}*/
		echo "\n---------------------------------输出位置---------------------------------\n\n";
		echo $next['file'] . "\t第" . $next['line'] . "行.\n";
		if(in_array('debug', $args)){
			echo "\n<pre>";
			echo "\n---------------------------------跟踪信息---------------------------------\n";
			print_r($trace);
		}
		echo "\n---------------------------------调试结束---------------------------------\n";
		exit();
	}

	public static function showError($message, $obj)
	{
		throw new CException(Yii::t('yii','{class} Error: ' . $message, array('{class}'=>get_class($obj))));
		return false;
	}

	/**
	 * Redirects the browser to the specified URL or route (controller/action).
	 * @param mixed $url the URL to be redirected to. If the parameter is an array,
	 * the first element must be a route to a controller action and the rest
	 * are GET parameters in name-value pairs.
	 * @param boolean $terminate whether to terminate the current application after calling this method. Defaults to true.
	 * @param integer $statusCode the HTTP status code. Defaults to 302. See {@link http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html}
	 * for details about HTTP status code.
	 */
	public static function redirectSub($url, $terminate=true, $statusCode=302)
	{
		if(is_array($url))
		{
			$route=isset($url[0]) ? $url[0] : '';
			$url=self::createUrl($route, array_splice($url, 1));
		}
		Yii::app()->getRequest()->redirect($url, $terminate, $statusCode);
	}

	/**
	 * Refreshes the current page.
	 * The effect of this method call is the same as user pressing the
	 * refresh button on the browser (without post data).
	 * @param boolean $terminate whether to terminate the current application after calling this method
	 * @param string $anchor the anchor that should be appended to the redirection URL.
	 * Defaults to empty. Make sure the anchor starts with '#' if you want to specify it.
	 */
	public static function refresher($terminate=true, $anchor='')
	{
		self::redirect(Yii::app()->getRequest()->getUrl() . $anchor, $terminate);
	}

	/**
	 * Redirect from current page to anther one.
	 *
	 * @param string $type The type of the tip.
	 * @param string $message The message of the tip
	 * @param boolean $returnCurrent 是否刷新当前页 false 返回上一页
	 * @param string $redirectUrl 跳转地址
	 * @param boolean $ajax
	 */
	public function redirecter($type = 'cuccess', $message = '', $returnCurrent = false, $redirectUrl = '', $ajax = false, $data = array())
	{
		if($ajax) {
			echo json_encode(array('status'=>$type, 'message'=>$message, 'data'=>$data));exit();
		}else{
			Yii::app()->user->setFlash($type, $message);
			if($returnCurrent)
				self::refresher();
			else
				$url = empty($redirectUrl) ? Yii::app()->request->urlReferrer : $redirectUrl;
			self::redirectSub($url);
		}
	}

	/**
	 * Zeqii 门票函数
	 *
	 * @param string $module 模块名称
	 * @param integer $userID 用户ID
	 * @param string $username 用户名
	 * @return string 门票
	 */
	public static function zeqiiTicket($module, $userID = 0, $username = '')
	{
		$systemTicket = Yii::app()->params[strtolower($module)];
		$string = md5($userID . $username .  $systemTicket);
		$ticket = md5(substr($string, -10, 10) . $systemTicket . substr($string, 0, 22));
		return $ticket;
	}

	/**
	 * 数组层级格式化
	 *
	 * @param array $array
	 * @param integer $parentID
	 * @param string $space
	 * @return array
	 */
	public static $newArray = array();
	public static function arrayFormat($array, $parentID = 0, $space = '&nbsp;&nbsp;', $unsetArray = false, $column = 'ParentID')
	{
		if(empty($array)) return false;
		if ($unsetArray) self::$newArray = null;
		foreach ($array as $key=>$val)
			if($parentID == $val[$column])
			{
				$val['Space'] = $space;
				self::$newArray[] = $val;
				unset($array[$key]);
				self::arrayFormat($array, $val['ID'], $space . "━", $unsetArray, $column);
			}
		return self::$newArray;
	}

	/**
	 * This function formats array to tree array.
	 * The parent-child relationship in the original array must be used ID and ParentID to indicate.
	 * @param array $array	original array
	 * @param integer $parentID
	 * @return array
	 */
	public static function arrayToTree($array, $parentID = 0)
	{
		$tree = array();
		foreach ($array as $key=>$val)
			if($parentID == $val['ParentID'])
			{
				$tmp = self::arrayToTree($array, $val['ID']);
				if (!empty($tmp))
					$val['Children'] = $tmp;
				$tree[] = $val;
			}
		return $tree;
	}

	/*排序数组*/
	public static function arraySort($array,$keys,$type='asc'){
		if(!isset($array) || !is_array($array) || empty($array)){
			return '';
		}
		if(!isset($keys) || trim($keys)==''){
			return '';
		}
		if(!isset($type) || $type=='' || !in_array(strtolower($type),array('asc','desc'))){
			return '';
		}
		$keysvalue=array();
		foreach($array as $key=>$val){
			$val[$keys] = str_replace('-','',$val[$keys]);
			$val[$keys] = str_replace(' ','',$val[$keys]);
			$val[$keys] = str_replace(':','',$val[$keys]);
			$keysvalue[] =$val[$keys];
		}
		asort($keysvalue); //key值排序
		reset($keysvalue); //指针重新指向数组第一个
		foreach($keysvalue as $key=>$vals) {
			$keysort[] = $key;
		}
		$keysvalue = array();
		$count=count($keysort);
		if(strtolower($type) != 'asc'){
			for($i=$count-1; $i>=0; $i--) {
				$keysvalue[] = $array[$keysort[$i]];
			}
		}else{
			for($i=0; $i<$count; $i++){
				$keysvalue[] = $array[$keysort[$i]];
			}
		}
		return $keysvalue;
	}

	/**
	 * 获取客服端真实地址
	 *
	 * @return string IP
	 */
	public static function getIP()
	{
		static $realIP = null;
		if($realIP !== null){
			return $realIP;
		}

		if(isset($_SERVER)){
			if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
				$arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
				foreach ($arr as $ip){
					$ip = trim($ip);
					if($ip != 'unkonow'){
						$realIP = $ip;
						break;
					}
				}
			}elseif (isset($_SERVER['HTTP_CLIENT_IP'])){
				$realIP = $_SERVER['HTTP_CLIENT_IP'];
			}else{
				if(isset($_SERVER['REMOTE_ADDR'])){
					$realIP = $_SERVER['REMOTE_ADDR'];
				}else{
					$realIP = '0.0.0.0';
				}
			}
		}else{
			if(getenv('HTTP_X_FORWARDED_FOR')){
				$realIP = getenv('HTTP_X_FORWARDED_FOR');
			}elseif (getenv('HTTP_CLIENT_IP')){
				$realIP = getenv('HTTP_CLIENT_IP');
			}else{
				$realIP = getenv('REMOTE_ADDR');
			}
		}
		preg_match("/[\d\.]{7,15}/", $realIP, $onlineIP);
		$realIP = !empty($onlineIP[0]) ? $onlineIP[0] : '0.0.0.0';
		return $realIP;
	}

	/**
	 * 将一个串中的字母转换为数字
	 *
	 * @param string $str
	 * @return string
	 */
	public static function StrToNum($str){
		$wordLen = strlen($str);
		$tempNumStr = '';
		for ($i = 0; $i < $wordLen; $i++){
			$string = $str[$i];
			if(!is_numeric($string)){
				$string = ord($string);
			}
			$tempNumStr .= $string;
		}
		return $tempNumStr;
	}

	/**
	 * 创建像这样的查询: "IN('a','b')";
	 * @access   public
	 * @param    mix      $item_list      列表数组或字符串
	 * @param    string   $field_name     字段名称
	 * @return   void
	 */
	public static function dbCreateIn($item_list, $field_name = '', $arrayFieldName = ''){
	    if (empty($item_list)){
	        return $field_name . " IN ('') ";
	    }else{
	        if (!is_array($item_list)){
	            $item_list = explode(',', $item_list);
	        }

//	        $item_list = array_unique($item_list);	//移除重复值
	        $item_list_tmp = '';
	        foreach ($item_list AS $item){
	            if ($item !== ''){
	            	if(is_array($item)){
	            		$item = $item[$arrayFieldName];
	            	}
	                $item_list_tmp .= $item_list_tmp ? ",'$item'" : "'$item'";
	            }
	        }

	        if (empty($item_list_tmp)){
	            return $field_name . " IN ('') ";
	        }else{
	            return $field_name . ' IN (' . $item_list_tmp . ') ';
	        }
	    }
	}

	/**
	 * 根据二维数组的某个字段的值，形成SQL的IN条件
	 *
	 * @param array $array			原始二维数组
	 * @param string $field			需要取值的数组字段
	 * @param string $cloumn		SQL条件的表字段
	 * @param string $wrapStr		是否为值加上引号
	 * @return boolean|string		"IN('a','b')";
	 */
	public static function arrayToInWhere($array, $field, $cloumn = '', $wrapStr = true)
	{
		if (!is_array($array))
			return false;

		if (empty($array))
			return $cloumn . " IN ('')";
		else {
			$tmpArray = array();
			foreach ($array as $val) {
				$tmpArray[] = $wrapStr ? self::wrapStr($val[$field], 0) : $val[$field];
			}

			$tmpStr = implode(',', $tmpArray);
			return !empty($cloumn) ? $cloumn . " IN (".$tmpStr.")" : " IN (".$tmpStr.")";
		}
	}


	/**
	 * 对二维数组中的某个值进行排序
	 *
	 * @param array $array
	 * @param string $keys
	 * @param string $type
	 * @return array
	 */
	public static function array_sort($array, $keys, $type='asc')
	{
		if(empty($array) || empty($keys))
			return false;

		$keysvalue = $new_array = array();
		foreach ($array as $key=>$val)
			$keysvalue[$key] = $val[$keys];

		if($type == 'asc')
			asort($keysvalue);
		else
			arsort($keysvalue);

		reset($keysvalue);
		foreach ($keysvalue as $key=>$val)
			$new_array[$key] = $array[$key];
		return $new_array;
	}

	/**
	 * 生成随机字符串
	 *
	 * @return string
	 */
	public static function genRandomString()
	{
	    // 验证码的长度
	    $length = 6;

	    // 验证码包含的字符
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
	    $string = '';

	    // 获取 $characters 的长度，并随机截取其中一个字符，直到验证码的长度达到 6 个字符
	    for ($p = 0; $p < $length; $p++)
	        $string .= $characters[mt_rand(0, strlen($characters) - 1)];

	    // 返回生成的验证码
	    return $string;
	}

	public static function getValue($area, $column, $postfix = '')
	{
		if (empty($area))	return false;
		if (!empty($postfix)) {
			echo !empty($area[$postfix][$column]) ? $area[$postfix][$column] : 0;
		} else {
			echo !empty($area[$column]) ? $area[$column] : 0;
		}
	}

	public static function createSidebar($menuData, $pRoot = '', $current = '')
	{
		if (empty($menuData))	return false;
		$menuHtml = '';
		foreach ($menuData as $val) {
			$ctr = strtolower($val['Controller']);
			$act = strtolower($val['Action']);
			$url = ($val['Type'] == 1) ? '/'.$ctr.'/'.$act.'.html' : 'javascript:;';

			$childNav = !empty($val['Children']) ? true : false;
			$menuHtml .= '<li class="hover"><a href="'.$url.'" ';
			$menuTitle = '<span class="menu-text">' . $val['NavName'] . '</span>';
			if($val['Icon']){
				$menuIcon = '<i class="menu-icon fa ' . $val['Icon'] .'"></i>';
			}else{
				$menuIcon = '<i class="menu-icon fa fa-caret-right"></i>';
			}
			if ($childNav) {
				$menuHtml .= 'class="dropdown-toggle">' . $menuIcon . $menuTitle;
				$menuHtml .= '<b class="arrow fa fa-angle-down"></b>';
			} else {
				$menuHtml .= '>'. $menuIcon . $menuTitle;
			}
			$menuHtml .= '</a><b class="arrow"></b>';

			if ($childNav) {
				$menuHtml .= '<ul class="submenu">';
				$menuHtml .= self::createSidebar($val['Children']);
				$menuHtml .= '</ul>';
			}

			$menuHtml .= '</li>';
		}

		return $menuHtml;
	}

	/**
	 * 格式化时间输出
	 *
	 * @param integer $date
	 * @param string $time
	 * @param string $type
	 * @return mixed
	 */
	public static function formatZeqiiTime($date, $time, $type = 'long')
	{
		if(empty($date))
			return false;

		$type = strtolower($type);
		if($type == 'long')
			return $date . $time;
		elseif ($type == 'number')
			return strtotime($date . ' ' .$time);
	}

	/**
	 * 生成加密密码和密码盐
	 * @param string $password
	 * @param string $salt
	 */
	public static function generatePassword($password)
	{
		$salt = md5(time() . time());
		$system = new System();
		$password = $system->hashPassword(md5($password), $salt);
		$data['Password'] = $password;
		$data['Salt'] = $salt;
		return $data;
	}

	/**
	 * 创建时间流水号,不重复
	 */
	public static function GUID()
	{
		$time = time();
		$microtime = microtime();
		return $time.substr($microtime, 3, 6);
	}

	/**
	 * 汉字转拼音
	 * @param unknown $str
	 * @param unknown $charset
	 * @return mixed
	 */
	public static function getPinYin($str, $charset="UTF8")
	{
		$py = new PinYin("UTF8");
		return $py->Pinyin($str);
	}

	public static function formatWechatTime($time)
	{
		return date('Y-m-d H:i:s', strtotime($time));
	}

	/**
	 * 将一个MD5串中字母转换为数字
	 * @param string $md5Str
	 * @return string
	 */
	public static function md5ToStr($md5Str)
	{
		if (empty($md5Str))
			return false;

		$len = strlen($md5Str);
		$numStr = '';
		for ($i = 0; $i < $len; ++$i)
			$numStr .= is_numeric($md5Str[$i]) ? $md5Str[$i] : ord($md5Str[$i]);

		return $numStr;
	}

	/**
	 * 将一个值（数组）加入到一个数组的头部
	 * @param unknown $array	二维数组
	 * @param unknown $value	只有一组key和value的二维数组
	 */
	public static function addValueToArrayHeader($value, $array)
	{
		if (empty($array))
			return false;

		$k = array_keys($value);
		$newArray = array($k[0]=>$value[$k[0]]);
		foreach ($array as $key=>$val)
			$newArray[$key] = $val;
		return $newArray;
	}

	public static function removeExtraChars( $text )
	{
		return trim(preg_replace('/\s\s+/', '', $text));
	}

	/**
	 * 获取整数的最后一位数
	 */
	public static function getLastNumber( $num )
	{
		return substr( $num, -1 );
	}

	/**
	 * 获取指定月份的所有日期
	 */
	public static function getMonthDays($month = "this month", $format = "Y-m-d", $dateTimeZone = false) {
		if(!$dateTimeZone) $dateTimeZone = new DateTimeZone("Asia/Shanghai");
		$start = new DateTime("first day of $month", $dateTimeZone);
		$end = new DateTime("last day of $month", $dateTimeZone);
		$days = array();
		for($time = $start; $time <= $end; $time = $time->modify("+1 day")) {
			$days[] = $time->format($format);
		}
		return $days;
	}

	// 生成token给API服务器验证
	public static function generateToken($params, $secretKey)
	{
		if (!is_array($params)) {
			return false;
		}
		ksort($params);
		$paramsStr = '';
		foreach ($params as $key=>$val) {
			$paramsStr .= $key . '=' . $val;
		}
		return md5(urlencode($paramsStr . $secretKey));
	}

	/*
     * 获取月份
     * 上月
     * 下月
     */
	public static function getMonth($sign=1)
	{
		//得到系统的年月
		$tmp_date=date("Ym");
		//切割出年份
		$tmp_year=substr($tmp_date,0,4);
		//切割出月份
		$tmp_mon =(int)substr($tmp_date,4,2);
		$tmp_nextmonth=mktime(0,0,0,$tmp_mon+1,1,$tmp_year);
		$tmp_forwardmonth=mktime(0,0,0,$tmp_mon-1,1,$tmp_year);
		if($sign==0){
			//得到当前月的下一个月
			return $fm_next_month=date("Y-m",$tmp_nextmonth);
		}else{
			//得到当前月的上一个月
			return $fm_forward_month=date("Y-m",$tmp_forwardmonth);
		}
	}

	/**
	 *  保留两位小数
	 */
	public static function formatMoney( $m )
	{
		$m = $m/100;
		return number_format($m, 2, '.', '');
	}
	/**
	 * 设置cookie参数
	 * @param string $key 键
	 * @param mixed $val 值
	 * @param integer $time 超时时间(单位:秒)
	 */
	public static function setCookie($key, $val, $time=3600)
	{
		Tools::unsetCookie($key);
		$cookie = new CHttpCookie($key, $val);
		$cookie->expire = time() + $time;
		//$cookie->httpOnly = true;
		// $cookie->secure = true;

		Yii::app()->request->cookies[$key]=$cookie;
	}
	/**
	 * 获取cookie参数
	 * @param unknown $key 键
	 */
	public static function getCookie($key)
	{
		$cookie = Yii::app()->request->getCookies();
		if(isset($cookie[$key]))
			return $cookie[$key]->value;
			return "";
	}

	/**
	 * 销毁cookie参数
	 */
	public static function unsetCookie($key)
	{
		$cookie = Yii::app()->request->getCookies();
		unset($cookie[$key]);
	}

	/**
	 * 过滤输入
	 */
	public static function filterIn($str)
	{
		$searchSTR = array(
			'%20',
			'|',
			'&',
			';',
			'$',
			'%',
			"'",
			'"',
			'<',
			'>',
			'(',
			')',
			'+',
			'CR',
			'LF',
			'\\',
			'BS',
			'SELECT',
			'select',
			'DELETE',
			'FROM',
			'CREATE',
			'ALTER',
			'DROP',
			'TRUNCATE',
			'TABLE',
			'DATABASE',
			'UNION',
			'INSERT',
			'UPDATE',
			'DECLARE',
			'COUNT',
			'AND',
			'OR'
		);
		return addslashes(str_replace($searchSTR,'',$str));
	}

	public static function validateDate($date, $format = 'Y-m-d H:i:s')
	{
		$d = DateTime::createFromFormat($format, $date);
		return $d && $d->format($format) == $date;
	}

	// 将一个数组转换成指定key的数组
	public static function changeKeyForArr($keyName, $arr)
	{
		$newArr = array();
		foreach($arr as $a){
			$newArr[$a[$keyName]] = $a;
		}
		return $newArr;
	}

	/**
	 * 发起Http POST 请求
	 *
	 * @param string $url
	 * @param array $postFields
	 */
	public static function curl($url, $postFields)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FAILONERROR, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
		//$headers = array('content-type: application/x-www-form-urlencoded;charset=utf-8');
		//curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		$response = curl_exec($ch);

		if (curl_errno($ch)) {
			throw new Exception(curl_error($ch), 0);
		} else {
			$httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			if (200 !== $httpStatusCode)
				throw new Exception($response, $httpStatusCode);
		}

		curl_close($ch);
		return $response;
	}

}
