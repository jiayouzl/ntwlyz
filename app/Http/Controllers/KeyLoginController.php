<?php

namespace App\Http\Controllers;

use App\LoginLog;
use App\UserData;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class KeyLoginController extends Controller
{
	//加密方法
	private function jiami($arr = array(), $key)
	{
		//将数组已JSON编码,不然数组不支持直接加密
		$val = json_encode($arr);
		//test参数设置为false为全局不加密,方便调试.
		$ret = authcode_leilei($val, 'ENCODE', $key, 88888, false);
		return $ret;
	}

	/*
	 * 错误码说明
	 * 1000 用户未过期验证通过
	 * 1001 新用户注册成功
	 * 1002 充值成功
	 *
	 * 2000 用户已过期
	 * 2001 新用户注册失败
	 * 2002 充值卡号或密码错误
	 * 2003 充值卡已被使用
	 * 2004 充值失败
	 * 2005 需充值的Key不存在
	 *
	 * 3000 登录参数错误
	 * 3001 充值参数错误
	 *
	 * 4000 维护中
	 * */
	public function login(Request $request)
	{
		if (empty($request -> key)) {
			return [
				'code' => 3000,
				'msg'  => '参数错误'
			];
		}
		//取系统设置(先读缓存)
		$setting = Cache ::remember('setting', 5, function () {
			return DB ::table('setting') -> where('id', 1) -> first();
		});

		$ret = DB ::table('user_data') -> where('key', $request -> key) -> first();
		if (empty($ret)) {
			$db            = new UserData();
			$db -> key     = $request -> key;
			$db -> ip      = getIpPlace(get_client_ip());
			$db -> regdate = Carbon ::now() -> toDateString();
			if ($setting -> shifoushiyong == '1') {
				$enddate = Carbon ::now() -> addSeconds($setting -> time);//到期时间后面为当前时间+秒
			} else {
				$enddate = Carbon ::now();//没有试用就写入当前时间.
			}
			$db -> enddate = $enddate;
			$result        = $db -> save();
			if ($setting -> shifoushiyong == '1') {
				return $result ? $this -> jiami([
					'code' => 1001,
					'msg'  => '注册成功，到期时间：' . $enddate
				], $request -> key) : $this -> jiami([
					'code' => 2001,
					'msg'  => '注册失败，请与管理员取得联系。'
				], $request -> key);
			} else {
				return $result ? $this -> jiami([
					'code' => 1001,
					'msg'  => '注册成功，请充值后继续使用。'
				], $request -> key) : $this -> jiami([
					'code' => 2001,
					'msg'  => '注册失败，请与管理员取得联系。'
				], $request -> key);
			}
		} else {
			$enddate = $ret -> enddate;
			if ($enddate > Carbon ::now()) {
				//写登录日志开始
				$setlog              = new LoginLog();
				$setlog -> user_id   = $ret -> id;
				$setlog -> ip        = getIpPlace(get_client_ip());
				$setlog -> logindate = Carbon ::now();
				$setlog -> save();
				//写登录日志结束
				$arr = $this -> jiami([
					'code'   => 1000,
					'msg'    => '验证成功，到期时间：' . $enddate,
					'banben' => $setting -> banben,
					'dll'    => $setting -> dlldown
				], $request -> key);
				return $arr;
			} else {
				$arr = $this -> jiami([
					'code' => 2000,
					'msg'  => '已到期请充值后继续使用。'
				], $request -> key);
				return $arr;
			}
		}
	}

	public function pay(Request $request)
	{
		if (!$request -> key || !$request -> card || !$request -> password) {
			return [
				'code' => 3001,
				'msg'  => '充值参数错误'
			];
		}
		$retkey = DB ::table('user_data') -> where('key', $request -> key) -> first();
		if (empty($retkey)) {
			return [
				'code' => 2005,
				'msg'  => '需充值的Key不存在'
			];
		} else {
			$retcard = DB ::table('cards') -> where([
				[
					'card',
					$request -> card
				],
				[
					'password',
					$request -> password
				]
			]) -> first();
			if (empty($retcard)) {
				return [
					'code' => 2002,
					'msg'  => '充值卡号或密码错误'
				];
			} else {
				if ($retcard -> consume == 1) {
					return [
						'code' => 2003,
						'msg'  => '充值卡已被使用'
					];
				} else {
					//Key存在,充值卡也存在并且未充值开始写代码.
					$retupcard = DB ::table('cards') -> where('id', $retcard -> id) -> update([
						'consume'     => 1,
						'key'         => $request -> key,
						'consumetime' => Carbon ::now()
					]);
					if (!empty($retupcard)) {//将充值卡设为已充值成功
						$setday  = cardType($retcard -> type);
						$nowdate = Carbon ::now() -> toDateTimeString();
						if ($nowdate > $retkey -> enddate) {
							$newdate = Carbon ::parse($nowdate) -> addDays($setday);
						} else {
							$newdate = Carbon ::parse($retkey -> enddate) -> addDays($setday);
						}
						$retupuser = DB ::table('user_data') -> where('key', $request -> key) -> update([
							'enddate' => $newdate
						]);
						if (!empty($retupuser)) {
							return [
								'code' => 1002,
								'msg'  => '充值成功增加：' . $setday . '天使用时间。'
							];
						} else {
							return [
								'code' => 2004,
								'msg'  => '充值失败'
							];
						}
					}
				}
			}
		}
	}
}