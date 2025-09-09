<?php declare(strict_types=1);

namespace App\View\Components;

use App\Contract\LLM\OpenLikeApiInterface;
use App\LLM\OpenLikeApi;
use Illuminate\Container\Attributes\Give;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ModelPick extends Component
{
    public function __construct(
        #[Give(OpenLikeApi::class)] private readonly OpenLikeApiInterface $api,
    ) {}

    public function render(): View
    {
        $models = $this->api->models();

        return view('components.model-pick', [
            'models' => $models,
        ]);
    }
}
