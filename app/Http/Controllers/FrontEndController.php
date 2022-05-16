<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FrontEndController extends Controller
{
    public function viewHome()
    {
        return view('welcome');
    }

    public function drink()
    {
        return view('drinks');
    }

    public function dish()
    {
        return view('dishes');
    }
}
