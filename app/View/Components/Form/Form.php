<?php

namespace App\View\Components\Form;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Form extends Component
{
    public string $action;
    public string $method;
    public string $title;
    public string $class;
    public $fields;
    public $buttons;

    public function __construct(
        string $action,
        string $method = 'POST',
        string $title = '',
        string $class = '',
        $fields = null,
        $buttons = null
    ) {
        $this->action = $action;
        $this->method = $method;
        $this->title = $title;
        $this->class = $class;
        $this->fields = $fields;
        $this->buttons = $buttons;
    }

    public function render(): View|Closure|string
    {
        return view('components.form.form');
    }
}
