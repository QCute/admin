<?php

namespace App\Http\Controllers;

use App\Http\Models\NavigationModel;

class HomeController extends Controller
{
    public function index()
    {
        $menu = NavigationModel::getNavigationList();

        return view('index', ['menu' => $menu]);
    }
}
