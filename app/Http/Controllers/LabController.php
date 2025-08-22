<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class LabController extends Controller
{
    public function count(): View
    {
        A::$a += 1;

        return view('count', ['a' => A::$a]);
    }
}

class A
{
    public static int $a = 0;
}
