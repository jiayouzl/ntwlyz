<?php

namespace Tests\Feature;

use App\UserData;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use UrlSigner;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        $response = $this -> get('/');

        $response -> assertStatus(200);
    }

    public function testLei()
    {
        //将数组已JSON编码,不然数组不支持直接加密
        $val = json_encode([
            'code' => 3000,
            'msg'  => '参数错误'
        ]);
        $ret = authcode_leilei($val, 'ENCODE', 'china', 1000, 1);
        dump('加密后:' . $ret);
        $ret = authcode_leilei($ret, 'DECODE', 'china', '', 1);
        dump('解密后:' . $ret);
        $this -> assertTrue(true);
    }

    public function testguanlian()
    {
        $loginlog = UserData ::find(1) -> loginlog;
        dump($loginlog);
        $this -> assertTrue(true);
    }

    public function testDate()
    {
        dump(Carbon ::parse('2019-07-14 02:08:37') -> addDays(365) -> toDateTimeString());
        $this -> assertTrue(true);
    }

    public function testbuchang()
    {
        //"UPDATE `user_data` SET `enddate` = date_add(str_to_date(`enddate`,'%Y-%m-%d %H:%i:%s'), interval '{$tianshu}' day) WHERE `enddate` > Now()"
        $ret = DB ::update("UPDATE `user_data` SET `enddate` = date_add(str_to_date(`enddate`,'%Y-%m-%d %H:%i:%s'), interval '1' day) WHERE `enddate` > Now()");
        dump($ret);
        $this -> assertTrue(true);
    }

    public function testarr()
    {
        $testarr = [
            1,
            2,
            3,
            4
        ];
        $arr     = [];
        foreach ($testarr as $value) {
            $arr[] = $value;
        }
        dump($arr);
        $this -> assertTrue(true);
    }

    public function testurlsign()
    {
        dump(UrlSigner ::sign(env('APP_URL') . '/?key=12345&', Carbon ::now() -> addMinutes(5)));
        $this -> assertTrue(true);
    }

    public function testurlvalidate()
    {
        dump(UrlSigner ::validate('http://localhost:8000/login?key=12345&expires=1564513047&signature=478acea1e8927191bb5ffa5ddf99777e'));
        $this -> assertTrue(true);
    }

    public function testqujianketianshu()
    {
        $date = carbon ::parse('2019-07-13 00:46:28');// 格式化一个时间日期字符串为 carbon 对象
        dump(Carbon ::now() -> diffInDays($date,true));
        $this -> assertTrue(true);
    }
}
