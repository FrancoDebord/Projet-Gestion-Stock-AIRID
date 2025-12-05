<?php

namespace App\Http\Controllers;

use App\Models\StockLocation;
use Illuminate\Http\Request;

class InterfaceController extends Controller
{
    //
    function __construct()
    {
        $this->middleware("auth");
    }

    function dashboard()
    {
        $locations = StockLocation::all();
        return view("dashboard_new", compact('locations'));
    }
}
