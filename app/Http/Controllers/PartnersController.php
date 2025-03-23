<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PartnersController extends Controller
{
    public function index()
    {
        // Logic to retrieve and display partners
        return view('partners.index'); 
        
    }
} 