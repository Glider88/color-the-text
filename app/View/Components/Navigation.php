<?php declare(strict_types=1);

namespace App\View\Components;

use App\Models\Article;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\Component;

class Navigation extends Component
{
    public function __construct(
        public ?int $currentArticleId,
    ) {}

    public function isSelected(int $articleId): bool
    {
        if ($this->currentArticleId === null) {
            return false;
        }

        return $this->currentArticleId === $articleId;
    }

    public function fixedTitle(string $title): string
    {
        return Str::of($title)->limit(12)->value();
    }

    public function render(): View | RedirectResponse
    {
        $articles = Article::orderBy('updated_at', 'desc')->get();
        $currentArticle = null;
        if ($this->currentArticleId !== null) {
            $currentArticle = Article::find($this->currentArticleId);
        }

        return view('components.navigation.navigation', [
            'articles' => $articles,
            'currentArticle' => $currentArticle,
        ]);
    }
}
