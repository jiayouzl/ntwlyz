<?php
//IP所在区域解析
function getIpPlace($ip)
{
	$iplocation = new \IpLocation(app_path('Models/ip/QQWry.Dat'));
	$ipresult   = $iplocation -> getlocation($ip);
	return $ipresult['country'] . $ipresult['area'];
}

/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @return mixed
 */
function get_client_ip($type = 0)
{
	$type = $type ? 1 : 0;
	static $ip = NULL;
	if ($ip !== NULL) return $ip[$type];
	if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
		$pos = array_search('unknown', $arr);
		if (false !== $pos) unset($arr[$pos]);
		$ip = trim($arr[0]);
	} else if (isset($_SERVER['HTTP_CLIENT_IP'])) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} else if (isset($_SERVER['REMOTE_ADDR'])) {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	// IP地址合法验证
	$long = sprintf("%u", ip2long($ip));
	$ip   = $long ? array(
		$ip,
		$long
	) : array(
		'0.0.0.0',
		0
	);
	return $ip[$type];
}

//自用的动态解解密
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0)
{
	$ckey_length = 4;
	$key         = md5($key != '' ? $key : C('AUTH_KEY'));
	$keya        = md5(substr($key, 0, 16));
	$keyb        = md5(substr($key, 16, 16));
	$keyc        = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';

	$cryptkey   = $keya . md5($keya . $keyc);
	$key_length = strlen($cryptkey);

	$string        = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
	$string_length = strlen($string);

	$result = '';
	$box    = range(0, 255);

	$rndkey = array();
	for ($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}

	for ($j = $i = 0; $i < 256; $i++) {
		$j       = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp     = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}

	for ($a = $j = $i = 0; $i < $string_length; $i++) {
		$a       = ($a + 1) % 256;
		$j       = ($j + $box[$a]) % 256;
		$tmp     = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result  .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}

	if ($operation == 'DECODE') {
		if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
			return substr($result, 26);
		} else {
			return '';
		}
	} else {
		return $keyc . str_replace('=', '', base64_encode($result));
	}
}

/**************************************************************
 *
 * 将数组转换为JSON字符串（兼容中文）
 * @param array $array 要转换的数组
 * @return string      转换得到的json字符串
 * @access public
 *
 *************************************************************/
function JSON($array)
{
	arrayRecursive($array, 'urlencode', true);
	$json = json_encode($array);
	return urldecode($json);
}

function arrayRecursive(&$array, $function, $apply_to_keys_also = false)
{
	static $recursive_counter = 0;
	if (++$recursive_counter > 1000) {
		die('possible deep recursion attack');
	}
	foreach ($array as $key => $value) {
		if (is_array($value)) {
			arrayRecursive($array[$key], $function, $apply_to_keys_also);
		} else {
			$array[$key] = $function($value);
		}
		if ($apply_to_keys_also && is_string($key)) {
			$new_key = $function($key);
			if ($new_key != $key) {
				$array[$new_key] = $array[$key];
				unset($array[$key]);
			}
		}
	}
	$recursive_counter--;
}

/*
Utf-8、gb2312都支持的汉字截取函数
cut_str(字符串, 截取长度, 开始长度, 编码);
编码默认为 utf-8
开始长度默认为 0
*/
function cut_str($string, $sublen, $start = 0, $code = 'UTF-8')
{
	if ($code == 'UTF-8') {
		$pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
		preg_match_all($pa, $string, $t_string);

		if (count($t_string[0]) - $start > $sublen) return join('', array_slice($t_string[0], $start, $sublen)) . "...";
		return join('', array_slice($t_string[0], $start, $sublen));
	} else {
		$start  = $start * 2;
		$sublen = $sublen * 2;
		$strlen = strlen($string);
		$tmpstr = '';

		for ($i = 0; $i < $strlen; $i++) {
			if ($i >= $start && $i < ($start + $sublen)) {
				if (ord(substr($string, $i, 1)) > 129) {
					$tmpstr .= substr($string, $i, 2);
				} else {
					$tmpstr .= substr($string, $i, 1);
				}
			}
			if (ord(substr($string, $i, 1)) > 129) $i++;
		}
		if (strlen($tmpstr) < $strlen) $tmpstr .= "....";
		return $tmpstr;
	}
}

//充值卡时效天数生成
function cardType($type)
{
	//1=天卡,2=周卡,3=月卡,4=季卡,5=半年卡,6=年卡
	if ($type == 1) {
		return 1;
	} elseif ($type == 2) {
		return 7;
	} elseif ($type == 3) {
		return 30;
	} elseif ($type == 4) {
		return 90;
	} elseif ($type == 5) {
		return 180;
	} elseif ($type == 6) {
		return 365;
	}
}