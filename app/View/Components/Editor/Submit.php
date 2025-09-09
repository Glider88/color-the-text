<?php declare(strict_types=1);

namespace App\View\Components\Editor;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Submit extends Component
{
    public function render(): View
    {
        return view('components.editor.submit');
    }
}
