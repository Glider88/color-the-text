<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class LabController extends Controller
{
    public function count(): View
    {
        A::$a += 1;

        return view('count', ['a' => A::$a]);
    }

    public function lab(): View
    {
        return view('lab');
    }

    public function ping()
    {
        return response()->json([]);
    }
}

class A
{
    public static int $a = 0;
}
