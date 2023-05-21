<?php

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Api\V1\Admin'], function () {
    Route::resource('permissions', 'PermissionsApiController');

    Route::resource('roles', 'RolesApiController');

    Route::resource('users', 'UsersApiController');

    Route::resource('products', 'ProductsApiController');
});

Route::group(['prefix' => 'public', 'as' => 'public.', 'namespace' => 'Api\V1\Guest'], function () {
    Route::get('check_auth', 'UserApiController@checkAuth');
    Route::get('logout', 'UserApiController@logout');
    Route::get('getData', 'UserApiController@getData');
    Route::post('update_referral', 'UserApiController@update_referral');
    Route::post('update_userProfile', 'UserApiController@update_userProfile');
    Route::get('total_referral', 'UserApiController@totalRefferal');
    Route::get('battles/{id}', 'BattleApiController@index');
    Route::get('live_battles/{id}', 'BattleApiController@liveBtl');
    Route::post('kyc_uploads', 'KycUploadApiController@store');
    Route::post('create_order', 'DepositHistoryApiController@createOrder');
    Route::post('confirm_order', 'DepositHistoryApiController@confirmOrder');
    Route::post('create_room', 'CreateRoomControl@createRoom');
    Route::post('game_result', 'CreateRoomControl@gameResult');
    Route::get('get_documents', 'KycUploadApiController@getDocuments');
    Route::get('get_states', 'KycUploadApiController@getStates');

    Route::post('withdraw_funds', 'WithdrawApiController@withdraw');
    Route::post('manual_withdraw_funds', 'MannualWithdrawApiController@withdraw');

    // Route::post('withdraw_funds', 'WithdrawApiController@withdraw');
    Route::post('add_room_code', 'CreateRoomControl@addRoomCode');
    Route::post('add_game_battle', 'CreateRoomControl@addGameBattle');
    Route::delete('delete_game_battle', 'CreateRoomControl@deleteGameBattle');
    Route::post('reject_game_battle', 'CreateRoomControl@rejectGameBattle');
    Route::post('joined_user_detail', 'CreateRoomControl@joinedUserDetails');
    Route::get('get_room_code/{game_id}', 'CreateRoomControl@getRoomCode');

    Route::get('game_history', 'GameHistoryApiController@gameHistory');
    Route::get('wallet_transaction_history', 'TransactionApiController@walletTransactionHistory');
    Route::get('battle_transaction_history', 'TransactionApiController@battleTransactionHistory');
    Route::get('refer_transaction_history', 'TransactionApiController@referTransactionHistory');
    Route::post('withdraw_referral_amount', 'TransactionApiController@withdraw_referral_amount');
});

Route::group(['prefix' => 'public', 'as' => 'public.', 'namespace' => 'Api\V1\Guest'], function () {
    Route::resource('game_listings', 'GameListingApiController');
    Route::post('send_otp', 'UserApiController@sendOtp');
    Route::post('verify_otp', 'UserApiController@verifyOtp');
    // Route::get('update_record', 'UserApiController@update_record');
    Route::get('getAllBattle', 'BattleApiController@getAllBattle');

    Route::get('/migrate', function () {
        Artisan::call('migrate:fresh --seed');
        dd("migrated");
    });
    Route::get('/seeding', function () {
        Artisan::call('db:seed --class=StateSeeder');
        Artisan::call('db:seed --class=DocumentSeeder');
        Artisan::call('db:seed --class=CommissionLimitSeeder');
        Artisan::call('db:seed --class=AdminCommissionSeeder');
        Artisan::call('db:seed --class=BattleManagementSeeder');

        dd("seeding");
    });
    Route::get('/storage', function () {
        Artisan::call('storage:link');
        dd("storage link");
    });
    Route::get('/signle_migrate', function () {
        Artisan::call('migrate:refresh --path=/database/migrations/2022_10_13_114535_create_transaction_histories_table.php');
        dd("single file migrated");
    });
});

// Route::group(['prefix' => 'public', 'as' => 'public.', 'namespace' => 'Api\V1\Guest'], function () {

// });
