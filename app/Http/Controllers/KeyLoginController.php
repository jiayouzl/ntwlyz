<?php

namespace App\Http\Controllers;

use App\LoginLog;
use App\UserData;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use UrlSigner;

class KeyLoginController extends Controller
{
    private $setting;

    public function __construct()
    {
        //Cache::clear();//清除缓存
        //通过魔术构造方法取系统设置(先读缓存如不存在就读取数据库)
        $this -> setting = Cache ::remember('setting', 600, function () {
            return DB ::table('setting') -> first();
        });
    }

    //显示首页
    public function index()
    {
        return view('welcome');
    }

    //加密方法
    private function jiami($arr = array(), $key)
    {
        //将数组已JSON编码,不然数组不支持直接加密
        $val = json_encode($arr);
        //test参数设置为true为全局不加密,方便调试.
        $ret = authcode_leilei($val, 'ENCODE', $key, 88888, $this -> setting -> quanjujiami);
        return $ret;
    }

    //URL签名
    public function urlsign(Request $request)
    {
        //env('APP_URL')有坑,要用config('app.url')
        return UrlSigner ::sign(config('app.url') . '/rules/login?key=' . $request -> key, Carbon ::now() -> addMinutes(120));
    }

    //注册,登录验证通用接口
    public function login(Request $request)
    {
        if (empty($request -> key)) {
            return [
                'code' => 3000,
                'msg'  => '参数错误'
            ];
        }
        if ($this -> setting -> urlsign === 1) {
            $urlvali = UrlSigner ::validate($request -> getUri());
            if ($urlvali === false) {
                return [
                    'code' => 3002,
                    'msg'  => 'URL签名认证失败'
                ];
            }
        }
        if (is_numandlitter($request -> key, 32) == false) {
            return [
                'code' => 3004,
                'msg'  => '机器码格式错误'
            ];
        }
        $ret = DB ::table('user_data') -> where('key', $request -> key) -> first();
        if (empty($ret)) {
            $db            = new UserData();
            $db -> key     = $request -> key;
            $db -> ip      = getIpPlace(get_client_ip());
            $db -> regdate = Carbon ::now() -> toDateString();
            if ($this -> setting -> shifoushiyong === 1) {
                $enddate = Carbon ::now() -> addSeconds($this -> setting -> time);//到期时间后面为当前时间+秒
            } else {
                $enddate = Carbon ::now();//没有试用就写入当前时间.
            }
            $db -> enddate = $enddate;
            $result        = $db -> save();
            if ($this -> setting -> shifoushiyong === 1) {
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
                    'banben' => $this -> setting -> banben,
                    'dll'    => $this -> setting -> dlldown
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

    //充值接口
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
                if ($retcard -> beifeng == 1) {
                    return [
                        'code' => 2006,
                        'msg'  => '该充值卡被封请于售卡商取得联系.'
                    ];
                }
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

    //授权转移
    public function replace(Request $request)
    {
        if (!$request -> key1 || !$request -> key2) {
            return [
                'code' => 3003,
                'msg'  => '授权转绑参数错误'
            ];
        }
        if ($this -> setting -> replace === 0) {
            return [
                'code' => 2012,
                'msg'  => '授权转移功能未开启'
            ];
        }
        $key1 = $request -> key1;
        $key2 = $request -> key2;
        if ($key1 === $key2) {
            return [
                'code' => 2011,
                'msg'  => '被转与待转机器码不可重复'
            ];
        }
        $ret_key1 = DB ::table('user_data') -> where('key', $key1) -> first();
        $ret_key2 = DB ::table('user_data') -> where('key', $key2) -> first();
        if (empty($ret_key1)) {
            return [
                'code' => 2009,
                'msg'  => '被转绑的机器码不存在'
            ];
        }
        if (empty($ret_key2)) {
            return [
                'code' => 2010,
                'msg'  => '待转绑的机器码不存在'
            ];
        }
        $ret_key1_edddate_day = Carbon ::now() -> diffInDays(carbon ::parse($ret_key1 -> enddate), true);
        if ($ret_key1_edddate_day < 3) {
            return [
                'code' => 2007,
                'msg'  => '被转绑的机器码有效期小于3天无法转绑'
            ];
        }
        $ret_key1_replace_day = DB ::table('replace_logs') -> where('key1', $ret_key1 -> key) -> orderBy('id', 'desc') -> first('replacetime');
        if (empty($ret_key1_replace_day) || Carbon ::now() -> diffInDays(Carbon ::parse($ret_key1_replace_day -> replacetime), true) >= 30) {
            $up_key1 = DB ::table('user_data') -> where('key', $key1) -> update(['enddate' => Carbon ::now()]);

            $nowdate = Carbon ::now() -> toDateTimeString();
            if ($nowdate > $ret_key2 -> enddate) {
                $newdate = Carbon ::parse($nowdate) -> addDays($ret_key1_edddate_day);
            } else {
                $newdate = Carbon ::parse($ret_key2 -> enddate) -> addDays($ret_key1_edddate_day);
            }

            $up_key2 = DB ::table('user_data') -> where('key', $key2) -> update([
                'enddate' => $newdate
            ]);

            $set_log = DB ::table('replace_logs') -> insert([
                'key1'        => $ret_key1 -> key,
                'key2'        => $ret_key2 -> key,
                'day'         => $ret_key1_edddate_day,
                'replacetime' => Carbon ::now()
            ]);

            if (!empty($up_key1) && !empty($up_key2) && !empty($set_log)) {
                return [
                    'code' => 1003,
                    'msg'  => '授权转绑成功'
                ];
            }

        } else {
            return [
                'code' => 2008,
                'msg'  => '被转绑的机器码冷却时间小于30天无法转绑'
            ];
        }
    }
}
