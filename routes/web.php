<?php

Route::redirect('/', '/login');

Route::redirect('/home', '/admin');

Auth::routes(['register' => false]);

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['auth']], function () {
    Route::get('/', 'HomeController@index')->name('home');

    Route::delete('permissions/destroy', 'PermissionsController@massDestroy')->name('permissions.massDestroy');

    Route::resource('permissions', 'PermissionsController');

    Route::delete('roles/destroy', 'RolesController@massDestroy')->name('roles.massDestroy');

    Route::resource('roles', 'RolesController');

    Route::resource('users', 'UsersController')->middleware('referral');
    Route::delete('users/destroy', 'UsersController@massDestroy')->name('users.massDestroy');
    Route::post('user/{user}/toggle', 'UsersController@toggle')->name('users.toggle');

    Route::delete('products/destroy', 'ProductsController@massDestroy')->name('products.massDestroy');

    Route::resource('products', 'ProductsController');

    // routes for kycs
    Route::resource('kyc_uploads','KycUploadController');
    // Route::get('kyc_uploads', 'KycUploadController@index')->name('kyc_uploads.index');
    // Route::get('kyc_uploads/{user}', 'KycUploadController@show')->name('kyc_uploads.show');
    // Route::put('kyc_uploads/{user}', 'KycUploadController@update')->name('kyc_uploads.update');

    Route::get('mannual_withdrawls', 'MannualWithdrawlController@index')->name('mannual_withdrawls.index');
    Route::get('mannual_withdrawls/{user}', 'MannualWithdrawlController@show')->name('mannual_withdrawls.show');
    Route::put('mannual_withdrawls/{user}', 'MannualWithdrawlController@update')->name('mannual_withdrawls.update');

    Route::get('activate_mannual_settings', 'ActivateMannualSettingController@index')->name('activate_mannual_settings.index');
    Route::post('activate_mannual_settings/{activate_mannual_setting}/toggle', 'ActivateMannualSettingController@toggle')->name('activate_mannual_settings.toggle');

    Route::get('ajax/datatable/{type}', 'DataTableController@index')->name('ajax.datatable');



    // routes for room history / game detail
    // Route::resource('room_historys', 'RoomHistoryController');
    Route::get('gameDetail/{roomId}', 'RoomHistoryController@getGameByRoomId')->name('gameDetail.getGameByRoomId');
    Route::put('gameDetail/{roomHistoryId}', 'RoomHistoryController@update')->name('gameDetail.update');
    Route::post('gameDetail/{roomHistoryId}', 'RoomHistoryController@cancel')->name('gameDetail.cancel');
    Route::post('gameDetail/penalty/{roomHistoryId}', 'RoomHistoryController@penalty')->name('gameDetail.penalty');

    //routes for game listings
    Route::resource('game_listings', 'GameListingController');
    // routes for rooms
    Route::resource('rooms', 'RoomController');

    // routes for game history
    Route::get('game_history', 'GameHistory@index')->name('game_history.index');

    // routes for transactions
    Route::get('transactions', 'TransactionHistoryController@index')->name('transactions.index');
    Route::get('transactions/{user_id}', 'TransactionHistoryController@show')->name('transactions.show');

    Route::get('battle_transactions', 'TransactionHistoryController@battle_transactions')->name('transactions.battle_transactions');
    Route::get('battle_transactions/{user_id}', 'TransactionHistoryController@user_battle_transactions')->name('transactions.user_battle_transactions');

    Route::get('wallet_transactions', 'TransactionHistoryController@wallet_transactions')->name('transactions.wallet_transactions');
    Route::get('wallet_transactions/{user_id}', 'TransactionHistoryController@user_wallet_transactions')->name('transactions.user_wallet_transactions');

    Route::get('referral_transactions', 'TransactionHistoryController@referral_transactions')->name('transactions.referral_transactions');
    Route::get('referral_transactions/{user_id}', 'TransactionHistoryController@user_referral_transactions')->name('transactions.user_referral_transactions');

    Route::get('penalty_histories', 'TransactionHistoryController@penalty_histories')->name('transactions.penalty_histories');
    Route::get('penalty_histories/{user_id}', 'TransactionHistoryController@user_penalty_histories')->name('transactions.user_penalty_histories');
    // route for admin commission history
    Route::get('admin_commission_histories', 'AdminCommissionHistoryController@index')->name('admin_commission_histories.index');

    // routes for battles
    // Route::resource('battles', 'BattleController');
    // Route::get('battles/{gameId}', 'BattleController@getBattleByGameId')->name('battles.getBattleByGame');
    // Route::delete('battles/{battle}', 'BattleController@destroy')->name('battles.destroy');
    // Route::get('battles/{gameId}/create', 'BattleController@createBattle')->name('battles.create.game');
    // Route::post('battles/{gameId}/store', 'BattleController@store')->name('battles.create.store');
    // Route::get('battles/{battle}/edit/{gameId}', 'BattleController@edit')->name('battles.edit');
    // Route::put('battles/{battle}', 'BattleController@update')->name('battles.update');
    // Route::get('battles/{battle}/create','BattleController@createBattle');

    // routes for refer-commission
    Route::resource('refer_commissions', 'ReferCommissionController');

    // routes for user-profile
    // Route::get('user_profiles', 'UserProfileController@index')->name('user_profiles.index');
    // Route::get('user_profiles/{user}', 'UserProfileController@edit')->name('user_profiles.edit');
    // Route::post('user_profiles', 'UserProfileController@store')->name('user_profiles.store');

    // routes for commission
    Route::resource('commission_limit_managements', 'CommissionLimitController');
    Route::resource('admin_commissions', 'AdminCommissionController');
    Route::resource('battle_managements', 'BattleManagementController');

});
