<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class DocsController extends Controller
{
    public function index(): View
    {
        return view('docs.index');
    }
}
