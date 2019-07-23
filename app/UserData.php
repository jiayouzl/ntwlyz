<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserData extends Model
{
	protected $connection = 'mysql';
	protected $table      = 'user_data';
	//可写入字段
	protected $fillable = [
		'key',
		'ip',
		'regdate',
		'enddate'
	];

	/**
	 * 表明模型是否应该被打上时间戳
	 *
	 * @var bool
	 */
	public $timestamps = false;

	//关联一对多登录日志.
	public function loginlog()
	{
		return $this -> hasMany('App\LoginLog', 'user_id', 'id');
	}

	//关联删除,删除某用户的时候如果这用户有登录日志也连并删除.
	protected static function boot()
	{
		parent ::boot();
		static ::deleting(function ($UserData) {
			$UserData->loginlog()->delete();
		});
	}
}