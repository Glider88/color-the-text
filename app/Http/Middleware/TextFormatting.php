<?php declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TextFormatting
{
    /** @param  Closure(Request): (Response)  $next */
    public function handle(Request $request, Closure $next): Response
    {
        $content = $request->request->get("content", '');
        if ($content !== '') {
            $content = str_replace('&nbsp;', ' ', $content);
            $request->request->set('content', $content);
        }

        return $next($request);
    }
}
