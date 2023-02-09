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

Route::get('', function () { 
    $menu = DB::table('navigation')->where('parent_id', 0)->orderBy('order')->get()->toArray();
    foreach($menu as &$item) {
        $item->sub = DB::table('navigation')->where('parent_id', $item->id)->orderBy('order')->get()->toArray();
    }
    return view('home', [ 'menu' => $menu ]); 
});
