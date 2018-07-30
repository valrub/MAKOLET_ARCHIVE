<?php

// API Routes

Route::group(['prefix' => 'api', 'middleware' => 'api'], function() {

	// Auth
    Route::post('auth/logout', 'API\AuthController@logout');
	Route::post('auth/login', 'API\AuthController@login');
	Route::post('auth/register', 'API\AuthController@register');

    // Device
    Route::post('device', 'API\DeviceController@store');
    Route::delete('device', 'API\DeviceController@destroy');

	// Customer
	Route::get('customers/me', 'API\CustomerController@me');
    Route::put('customers/{id}/card', 'API\CustomerController@updateCard');
	Route::resource('customers', 'API\CustomerController');

	// Shop
	Route::get('shops/{id}/orders', 'API\OrderController@byShopId');
	Route::resource('shops', 'API\ShopController');

	// Orders
	Route::get('customers/{id}/orders', 'API\OrderController@byCustomerId');
    Route::get('orders/summary', 'API\OrderController@summary');
    Route::get('orders/last', 'API\OrderController@last');
    Route::post('orders/close', 'API\OrderController@close');
	Route::resource('orders', 'API\OrderController');

    // Proposals
    Route::post('proposals/propose', 'API\ProposalController@propose');
    Route::post('proposals/accept', 'API\ProposalController@accept');

    // Feedbacks
    Route::resource('feedbacks', 'API\FeedbackController');

});

// Web Routes

Route::group(['middleware' => 'web'], function () {
    
    Route::auth();

    Route::group(['prefix' => 'admin', 'middleware' => 'admin'], function() {

        Route::get('/', 'Admin\AdminController@index');
        Route::get('push', 'Admin\AdminController@push');
        Route::get('excel', 'Admin\AdminController@excel');
        Route::get('extract', 'Admin\AdminController@extract');
        Route::get('customers/extract', 'Admin\CustomerController@extract');
        Route::resource('customers', 'Admin\CustomerController');
        Route::get('shops/extract', 'Admin\ShopController@extract');
        Route::resource('shops', 'Admin\ShopController');
        Route::get('shops/{id}/feedback', [
            'as' => 'admin.shops.feedback',
            'uses' => 'Admin\ShopController@feedback'
        ]);
        Route::delete('feedback/{id}', [
            'as' => 'admin.feedback.destroy',
            'uses' => 'Admin\ShopController@destroyFeedback'
        ]);
        Route::get('orders/extract', 'Admin\OrderController@extract');
        Route::resource('orders', 'Admin\OrderController');
        Route::resource('comments', 'Admin\CommentController');

    });

    Route::get('/', 'HomeController@index');
    Route::get('home', 'HomeController@index');
    Route::get('map', 'HomeController@map');
    Route::get('join', 'HomeController@join');
    Route::post('join', 'HomeController@joinPost');
    Route::get('contact-us', 'HomeController@contactUs');
    Route::post('contact-us', 'HomeController@contactUsPost');
    Route::get('terms-of-use', 'HomeController@termsOfUse');
    
    // Change language
    Route::get('lang/{lang}', 'HomeController@language');

    // Orders
    Route::get('orders-www', 'OrderController@indexWww');
    Route::get('orders/create-www', 'OrderController@createWww');
    Route::post('orders/{id}/close', 'OrderController@close');
    Route::get('orders/summary', 'OrderController@summary');
    Route::resource('orders', 'OrderController');

    // Proposals
    Route::post('proposals/propose', 'ProposalController@propose');
    Route::post('proposals/accept', 'ProposalController@accept');

    // Feedback
    Route::resource('feedbacks', 'FeedbackController');

    // Notifications
    Route::get('notifications', 'NotificationController@get');

    // Payment
    Route::get('payment', 'HomeController@payment');
    Route::post('payment/register', 'HomeController@paymentRegister');
    
    // Shops
    Route::resource('shops', 'ShopController');

    // Customers
    Route::get('profile', 'CustomerController@index');
    Route::post('profile', 'CustomerController@update');

    // Performance
    Route::get('api/performance', 'HomeController@performance');

});