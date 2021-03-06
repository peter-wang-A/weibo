<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Routing\Router;

Route::get('/', 'StaticPagesController@home')->name('home');
Route::get('/help', 'StaticPagesController@help')->name('help');
Route::get('/about', 'StaticPagesController@about')->name('about');
Route::get('/signup', 'UsersController@signup')->name('signup');
//用户注册相关
route::resource('users','UsersController');

// Route::get('/users', 'UsersController@index')->name('users.index');显示所有用户列表的页面
// Route::get('/users/create', 'UsersController@create')->name('users.create');创建用户的页面
// Route::get('/users/{user}', 'UsersController@show')->name('users.show');显示用户个人信息的页面
// Route::post('/users', 'UsersController@store')->name('users.store');创建用户的页面
// Route::get('/users/{user}/edit', 'UsersController@edit')->name('users.edit');编辑用户个人资料的页面
// Route::patch('/users/{user}', 'UsersController@update')->name('users.update');更新用户
// Route::delete('/users/{user}', 'UsersController@destroy')->name('users.destroy');删除用户
//会话控制
Route::get('login', 'SessionsController@create')->name('login');//显示登录页面
Route::post('login', 'SessionsController@store')->name('login');//创建新会话（登录）
Route::delete('logout', 'SessionsController@destroy')->name('logout');//销毁会话（退出登录）

//激活路由
Route::get('signup/confirm/{token}', 'UsersController@confirmEmail')->name('confirm_email');

//微博列表增删改查
Route::resource('statuses', 'StatusesController', ['only' => ['store', 'destroy']]);


//粉丝及关注人列表

Route::get('/users/{user}/followings', 'UsersController@followings')->name('users.followings'); //显示用户的关注人列表
Route::get('/users/{user}/followers', 'UsersController@followers')->name('users.followers'); //显示用户的粉丝列表



//关注和取消关注
Route::post('/users/followers/{user}', 'FollowersController@store')->name('followers.store');
Route::delete('/users/followers/{user}', 'FollowersController@destroy')->name('followers.destroy');
