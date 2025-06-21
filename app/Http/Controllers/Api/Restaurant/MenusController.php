<?php

namespace App\Http\Controllers\Api\Restaurant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\User;
// use Auth;   
// use Illuminate\Support\Facades\Auth;


class MenusController extends Controller
{
   

    public function index()
    {
        return Menu::all(); 
        // return Auth::;
    }
}
