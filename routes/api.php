use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

<?php


Route::get('/example-test', function () {
    return response()->json(['message' => 'Hello, API!']);
});