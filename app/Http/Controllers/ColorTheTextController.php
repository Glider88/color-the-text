<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contract\LLM\OpenLikeApiInterface;
use App\Jobs\ProcessSse;
use App\LLM\OpenLikeApi;
use App\Models\Article;
use Illuminate\Container\Attributes\Give;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Redis\RedisManager;
use Illuminate\Support\Facades\Log;
use MercureConfig;

class ColorTheTextController extends Controller
{
    public function __construct(
        #[Give(OpenLikeApi::class)] private readonly OpenLikeApiInterface $api,
        private readonly RedisManager $redis,
        private readonly MercureConfig $mercureConfig,
    ) {}

    public function upload(): View
    {
        $models = $this->api->models();
        $articles = Article::orderBy('updated_at', 'desc')->get();

        return view('color.app', [
            'articles' => $articles,
            'models' => $models,
        ]);
    }

    public function save(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => ['required'],
            'content' => ['required', 'max:10000'],
            'model' => ['required'],
        ]);

        $title = $request->input("title", '');
        $content = $request->input("content", '');
        $model = $request->input("model", '');
        Log::debug('request content: ' . $content);

        $article = new Article();
        $article->title = $title;
        $article->content = $content;
        $article->model = $model;
        $article->save();

        return redirect()->route('read', ['id' => $article->id]);
    }

    public function read(int $id): View | RedirectResponse
    {
        $article = Article::find($id);
        if ($article === null) {
            return redirect()->route('upload');
        }

        $articles = Article::orderBy('updated_at', 'desc')->get();

        if (! $article->is_completed) {
            Log::debug('read dispatch sse: ' . $article->id);
            ProcessSse::dispatch($article->id);
        }

        return view('color.app', [
            'currentArticle' => $article,
            'articles' => $articles,
        ]);
    }

    public function finish(Request $request): Response
    {
        $request->validate([
            'id' => ['required', 'int'],
            'content' => ['required', 'max:10000'],
        ]);

        $article = Article::find($request->get('id'));
        $final = $request->get('content');
        $article->content = $final;
        $article->is_completed = true;
        $article->save();
        $this->redis->del($this->mercureConfig->topicPrefix . $article->id);
        Log::debug('finished: ' . $article->id);
        Log::debug('clear: ' . $this->mercureConfig->topicPrefix . $article->id);

        return response()->noContent();
    }

    public function delete(Request $request): RedirectResponse
    {
        $request->validate([
            'id' => ['required', 'int'],
        ]);

        $article = Article::find($request->get('id'));
        $article->delete();
        Log::debug('deleted : ' . $article->id);

        return redirect()->route('upload');
    }
}
