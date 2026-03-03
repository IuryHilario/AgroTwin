<?php

namespace App\View\Components\Form;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Select extends Component
{
    public string $name;
    public string $label;
    public array $options;
    public string|null $value;
    public bool $required;
    public string|null $error;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $name,
        string $label = '',
        array $options = [],
        string $value = null,
        bool $required = false,
        string $error = null
    ) {
        $this->name = $name;
        $this->label = $label;
        $this->options = $options;
        $this->value = $value;
        $this->required = $required;
        $this->error = $error;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.form.select');
    }
}
