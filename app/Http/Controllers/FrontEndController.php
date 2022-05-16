<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FrontEndController extends Controller
{
    public function viewHome()
    {
        return view('welcome');
    }

    public function drank()
    {
        return view('drank');
    }

    public function gerechten()
    {
        return view('gerechten');
    }
}
