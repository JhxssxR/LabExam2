<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // return the new index view created at resources/views/index.blade.php
    return view('index');
});
