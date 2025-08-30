<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Jobs\ProcessLlm;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ColorTheTextController extends Controller
{
    public function upload(): View
    {
        return view('color.upload');
    }

    public function read(Request $request): View
    {
        $request->validate([
            'content' => ['required', 'max:10000'],
        ]);

        $text = $request->input("content", '');
        Log::debug('request content: ' . $text);

        ProcessLlm::dispatch($text);

        $content = view('color.content', ['text' => $text])->render();
        Log::debug('request htmled: ' . $content);

        return view('color.read', ['content' => $content]);
    }
}
