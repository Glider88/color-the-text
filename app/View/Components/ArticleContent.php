<?php declare(strict_types=1);

namespace App\View\Components;

use App\Models\Article;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ArticleContent extends Component
{
    public function __construct(
        public int $currentArticleId,
    ) {}

    public function render(): View
    {
        $currentArticle = Article::findOrFail($this->currentArticleId);

        return view('components.article-content', [
            'content' => $currentArticle->content,
        ]);
    }
}
