<?php

namespace App\View\Components\Ui;

use Illuminate\View\Component;

class StepContent extends Component
{
    public $step;
    public $title;
    public $icon;
    public $active;

    /**
     * Create a new component instance.
     *
     * @param int $step
     * @param string $title
     * @param string $icon
     * @param bool $active
     * @return void
     */
    public function __construct($step = 1, $title = '', $icon = 'fas fa-info-circle', $active = false)
    {
        $this->step = $step;
        $this->title = $title;
        $this->icon = $icon;
        $this->active = $active;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.ui.step-content');
    }
}
