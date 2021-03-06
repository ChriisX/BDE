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

use App\Http\Middleware\IpFilter;
use Illuminate\Http\Request;


Route::get('/', function () {
    return view('home');
});

// Mail
Route::post('mail', function (Request $request) {
    $request->validate([
        'nom' => 'required',
        'email' => 'required|email',
        'message' => 'required'
    ]);

    $data = array('title' => 'Message de la part de ' . request('nom'), 'subtitle' => 'Message', "description" => request('message'), "url" => "mailto:" . request('email'), 'linkText' => "Contacter");

    Mail::send('layout.mail', $data, function($message) {
        $message->to(env('ADMIN_MAIL', 'cbenard2013@gmail.com'), 'Administrator')->subject('Message depuis l\'interface');
        $message->from(env('MAIL_USERNAME', 'bde@bde.fr'), 'BDE');
    });

    return back();
});

Auth::routes();

// API
Route::group(['middleware' => IpFilter::class], function () {
    Route::post('/api/register', 'ApiController@register');
    Route::put('/api/profile', 'ApiController@updateSelf');
    Route::put('/api/users/{id}', 'ApiController@updateUser');
});

// Administration
Route::get('/administration', 'AdministrationController@index')->middleware('auth');
Route::get('/users/{user}/edit', 'AdministrationController@edit')->middleware('auth');
Route::put('/users/{user}', 'AdministrationController@update')->middleware('auth');

// Notifications
Route::get('/notifications', 'NotificationsController@index')->middleware('auth');
Route::delete('/notifications/{notification}', 'NotificationsController@delete')->middleware('auth');

//Evenement
Route::resource('event', 'EventController');
Route::post('/event/register/{eventSelec}', 'EventController@registerEvent')->middleware('auth');
Route::post('/event/unregister/{eventSelec}', 'EventController@unRegisterEvent')->middleware('auth');
Route::post('/event/signal/{eventSelec}', 'EventController@signalEvent')->middleware('auth');
Route::post('/event/downloadParticipant/{idEvent}', 'DownloadController@downloadParticipants')->middleware('auth');
Route::get('/searchEvent', 'EventController@searchEvent');

Route::resource('photoEvent', 'PhotoEventController')->only(['store', 'destroy'])->middleware('auth');
Route::resource('photoEvent', 'PhotoEventController')->only(['show']);
Route::post('/photoEvent/signaler/{photo}', 'PhotoEventController@signal')->middleware('auth');
Route::post('/photoEvent/like/{photo}', 'PhotoEventController@like')->middleware('auth');
Route::post('/photoEvent/unlike/{photo}', 'PhotoEventController@unLike')->middleware('auth');
Route::post('/photoEvent/comment/{photo}', 'PhotoEventController@comment')->middleware('auth');
Route::post('/photoEvent/comment/signaler/{comment}', 'PhotoEventController@signalerComment')->middleware('auth');
Route::delete('/photoEvent/comment/destroy/{comment}', 'PhotoEventController@destroyComment')->middleware('auth');

//Profil
Route::get('/profil', 'UserController@index')->name('profil')->middleware('auth');
Route::get('/profil/modifier/{user}', 'UserController@getModifier')->middleware('auth');
Route::post('/profil/modifier/{user}', 'UserController@postModifier')->middleware('auth');
Route::post('/profil/modifier/{user}/photo', 'UserController@postModifierAvatar')->middleware('auth');
Route::get('/download', 'DownloadController@downloadImages')->middleware('auth');

// Ideas Box
Route::get('ideas', 'IdeasController@index');
Route::get('ideas/search', 'IdeasController@searchIdea');
Route::post('ideas', 'IdeasController@createIdea')->middleware('auth');
Route::get('ideas/create' , 'IdeasController@create')->middleware('auth');
Route::get('ideas/{id}/edit', 'IdeasController@edit')->middleware('auth');
Route::put('ideas/{id}', 'IdeasController@editIdea')->middleware('auth');
Route::delete('ideas/{id}', 'IdeasController@deleteIdea')->middleware('auth');


// Vote des idées
Route::post('votes/{id}', 'IdeasController@addVote')->middleware('auth');
Route::delete('votes/{id}', 'IdeasController@deleteVote')->middleware('auth');

// Legal Notice
Route::get('legal-notice', 'LegalNoticeController@index');


// Articles
Route::get('/articles', 'ArticlesController@index');
Route::get('/articles/create', 'ArticlesController@create');
Route::post('/articles', 'ArticlesController@store');
Route::get('/articles/{article}/edit', 'ArticlesController@edit');
Route::put('/articles/{article}', 'ArticlesController@update');
Route::delete('/articles/{article}', 'ArticlesController@destroy');
Route::get('/articles/search', 'ArticlesController@search');

// Purchase
Route::get('/purchase', 'PurchaseController@index')->middleware('auth');
Route::post('/purchase', 'PurchaseController@store')->middleware('auth');
Route::delete('/purchase/{purchase}', 'PurchaseController@destroy')->middleware('auth');
Route::put('/purchase/{purchase}', 'PurchaseController@update')->middleware('auth');

// Payment
Route::get('/payment', 'PurchaseController@payment')->middleware('auth');
Route::get('/payment/cash', 'PurchaseController@paymentCash')->middleware('auth');
