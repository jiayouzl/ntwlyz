<p align="center"><img src="https://laravel.com/assets/img/components/logo-laravel.svg"></p>
<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/license.svg" alt="License"></a>
</p>

## 为什么要开发Ntwlyz
最近有个朋友想要写一套这样功能的网络验证系统但他不会Web开发，我近期也有空闲时间，帮他完成了。后续我也会对该项目于进行更新以达到符合更多人的需求。

## Ntwlyz的开发环境
1. PHP 7.1.30
2. Laraver5.8.29（最低需要PHP7.1.3）
3. MySQL 5.5.53
4. Windows 10 Pro 1803

## Ntwlyz后续计划

* ~~安全的`动态加解密`技术~~(已完成)
    * 近期有空就会加入这个，对于网络验证而言数据交互之间的加密还是必须要有滴。
* ~~远程载入功能代码。~~(已完成)
	* 将重要代码以DLL动态链接库形式编译，将其上传至服务器，通过内存加载的方式调用远程DLL中的函数，从而达到代码分离的效果，为防破解添加一道防护。
* 欢迎所有`Laraver`爱好者一起充实它。

## 安装说明
1. 自行导入NTSQL文件夹中的data.sql文件到MySQL。
2. 自行修改根目录中.env.example中的配置后改名.env。(修改APP_URL与MySQL连接配置)
3. 执行composer install安装所需扩展包。
4. Nginx需将运行目录指定至项目public目录。

## 接口说明
注册与验证通用接口（GET）
`http://127.0.0.1:8000/login?key={key}`

充值接口（GET）
`http://127.0.0.1:8000/pay?key={key}&card={card}&password={password}`

管理后台(name:admin,password:admin)
`http://127.0.0.1:8000/admin`

## 更新日志

更新内容(2019年7月21日)     v1.20
1. 验证登录接口动态加解密上线,服务端⇄客户端互通.
2. 易语言DEMO兼容动态加解密的验证.

更新内容(2019年7月19日 晚上) v1.11
1. 易语言DEMO新增在内存中调用远程DLL函数的示范.

更新内容(2019年7月19日 下午) v1.10
1. 系统设置增加版本号管理.
2. 系统设置增加DLL上传功能.(保存路径:public\upload\files)

功能介绍(2019年7月15日) v1.00
1. 用户列表
2. 充值卡列表
3. 试用功能，可自定义试用的周期或关闭试用。
4. 充值卡批量生成。
5. 。。。。

## 有问题反馈
在使用中有任何问题，欢迎反馈给我，可以在Issues中或以下联系方式跟我交流。

## 关于作者
* Email:(jiayouzl#vip.qq.com, 把#换成@)
* Telegram:[https://t.me/hzleilei](https://t.me/hzleilei)