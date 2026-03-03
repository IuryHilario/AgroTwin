<?php

namespace App\View\Components\Form;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Input extends Component
{
    public string $name;
    public string $label;
    public string $type;
    public string|null $value;
    public bool $required;
    public string|null $error;
    public string|null $placeholder;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $name,
        string $label = '',
        string $type = 'text',
        string $value = null,
        bool $required = false,
        string $error = null,
        string $placeholder = null
    ) {
        $this->name = $name;
        $this->label = $label;
        $this->type = $type;
        $this->value = $value;
        $this->required = $required;
        $this->error = $error;
        $this->placeholder = $placeholder;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.form.input');
    }
}
