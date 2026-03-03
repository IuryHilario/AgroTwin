<?php

namespace App\View\Components\Form;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Button extends Component
{
    public string $type;
    public string $text;
    public string $icon;
    public string $class;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $text,
        string $type = 'submit',
        string $icon = '',
        string $class = 'btn btn-primary'
    ) {
        $this->type = $type;
        $this->text = $text;
        $this->icon = $icon;
        $this->class = $class;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.form.button');
    }
}
