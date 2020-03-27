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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// App v1 API
Route::group([
    'middleware' => ['api', 'api.version:1'],  
    'prefix'     => 'v1',
  ], function ($router) {
    require base_path('routes/app_api.v1.php');
});
  
Route::get('/loginSaml', function(){
    
    if(\Auth::guest())
    {       
        // login($returnTo = null, array $parameters = array(), $forceAuthn = false, $isPassive = false, $stay = false, $setNameIdPolicy = true)
        // return  \Saml2::login('http://localhost:4200/',array(),false,false,true);  
        /*
        if (\App::environment('local')) {
            return  \Saml2::login('http://localhost:4200/home');            
        }
        return  \Saml2::login('https://unidemdev.uniurb.it/unidem/uniconv/uniconvclient/home');
        */
        if (\App::environment('local')) {
            if (\Request::ip() == "192.168.5.135" || \Request::ip() == "192.168.5.137" || \Request::ip() == "127.0.0.1" ) {
                return  \Saml2::login(config('unidem.client_url').'home');            
            } else {
                return  abort(404);
            }
        }
        // return  \Saml2::login('https://unidemdev.uniurb.it/unidem/uniconv/uniconvclient/home');
        return  \Saml2::login(config('unidem.client_url').'home');                             
        
    }
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth',
    'namespace'=>'Api'
], function ($router) {   
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');         
});


//Route::resource('roles', 'RoleController');
//Route::resource('permissions', 'PermissionController');
