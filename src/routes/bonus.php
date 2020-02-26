<?php

Route::get('/bonuses/user/{id}', function(\Illuminate\Http\Request $request, string $id) {
    return redirect(str_replace('{id}', $id, config('bonus.show_user_url')));
});

Route::get('/bonuses/order/{id}', function(\Illuminate\Http\Request $request, string $id) {
    return redirect(str_replace('{id}', $id, config('bonus.show_order_url')));
});
