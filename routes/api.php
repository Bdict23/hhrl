<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Restaurant\MenusController;
use Illuminate\Support\Facades\Auth;

Route::get('/recipes', [MenusController::class, 'index']);
