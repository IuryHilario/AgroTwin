<?php

namespace App\View\Components\Ui;

use Illuminate\View\Component;

class Stepper extends Component
{
    public $steps;
    public $currentStep;
    public $formId;

    /**
     * Create a new component instance.
     *
     * @param array $steps
     * @param int $currentStep
     * @param string $formId
     * @return void
     */
    public function __construct($steps = [], $currentStep = 1, $formId = 'stepperForm')
    {
        $this->steps = $steps;
        $this->currentStep = $currentStep;
        $this->formId = $formId;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.ui.stepper');
    }
}
