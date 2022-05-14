<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/authorization', function (Request $request) {  //Get authorization
    $request->session()->put('state', $state = Str::random(40));
 
    $query = http_build_query([
        'client_id' => '6',
        'redirect_uri' => 'https://auth4500.herokuapp.com/callback',
        'response_type' => 'code',
        'scope' => '',
        'state' => $state,
    ]);

    return redirect('https://auth4500.herokuapp.com/oauth/authorize?'.$query);
})->name('authorization');

use Illuminate\Http\Request;

Route::get('/callback', function (Request $request) {   //Get Token after authorization
    $state = $request->session()->pull('state');
    
    if(strlen($state) > 0 && $state === $request->state) {
 
        $response = Http::asForm()->post('https://auth4500.herokuapp.com/oauth/token', [
            'grant_type' => 'authorization_code',
            'client_id' => '6',
            'client_secret' => '4lhN8fhqeCxYBcrZM2RzuDoG0qPK4FscjsUuZTTU',
            'redirect_uri' => 'https://auth4500.herokuapp.com/callback',
            'code' => $request->code,
        ]);
        
        $accessToken = $response->json()['access_token'];
    
        //Use the token to request data
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$accessToken,
        ])->get('https://auth4500.herokuapp.com/api/users');
        
        return $response->json();
        
    } else {
        return redirect()->route('authorization');
    }
});


Route::get('/db-migrate', function () {
    Artisan::call('migrate');
    echo Artisan::output();
});

Route::get('/db-migrate-refresh', function () {
    Artisan::call('migrate:refresh');
    echo Artisan::output();
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';
