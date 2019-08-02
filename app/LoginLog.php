<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoginLog extends Model
{
	protected $connection = 'mysql';
	protected $table = 'login_logs';
	/**
	 * 表明模型是否应该被打上时间戳
	 *
	 * @var bool
	 */
	public $timestamps = false;

	//关联1对1模型,来实现通过用户ID获取用户表中对应的Key与其他数据
    public function User_Loginlog(){
        return $this->belongsTo('App\UserData','user_id','id');
    }
}
