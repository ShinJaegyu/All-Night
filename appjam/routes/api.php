<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/v1/user', function (Request $request) {
    return response()->json($request->user(),200,[],JSON_UNESCAPED_UNICODE);
});

Route::group(['prefix' => '/v1', 'namespace' => 'api\v1'], function() {
    // 유저 관련 컨트롤러
    Route::resource('user','UserController')->except(['index', 'create', 'edit']);

    // 로그인
    Route::post('login','UserController@login');
});