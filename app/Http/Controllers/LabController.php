<?php declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Response;

class LabController extends Controller
{
    public function count(): View
    {
        global $a;
        $a += 1;

        return view('count', ['a' => $a]);
    }

    public function lab()
    {
        return Response::make(
<<<'HTML'
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Lab</title>
      <script></script>
      <style></style>
  </head>
  <body></body>
</html>
HTML
        );
    }
}
