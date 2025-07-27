<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class LabController extends Controller
{
    public function index(): View
    {
        A::$a += 1;

        return view('lab', ['a' => A::$a]);
    }
}

class A
{
    public static int $a = 0;
}
