<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserData extends Model
{
    protected $connection = 'mysql';
	protected $table = 'user_data';
	//可写入字段
	protected $fillable = [
		'key', 'ip', 'regdate', 'enddate'
	];
	/**
	 * 表明模型是否应该被打上时间戳
	 *
	 * @var bool
	 */
	public $timestamps = false;
}