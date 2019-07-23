<?php

namespace Tests\Feature;

use App\UserData;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

	public function testLei(){
    	//将数组已JSON编码,不然数组不支持直接加密
    	$val=json_encode([
		    'code' => 3000,
		    'msg'  => '参数错误'
	    ]);
		$ret = authcode_leilei($val,'ENCODE','china',1000);
		dump('加密后:'.$ret);
		$ret = authcode_leilei($ret,'DECODE','china');
		dump('解密后:'.$ret);
		$this->assertTrue(true);
	}

	public function testguanlian(){
		$loginlog = UserData::find(1)->loginlog;
		dump($loginlog);
		$this->assertTrue(true);
	}
}