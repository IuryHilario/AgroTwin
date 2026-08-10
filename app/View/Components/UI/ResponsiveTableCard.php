<?php
namespace App\View\Components\UI;

use Illuminate\View\Component;

class ResponsiveTableCard extends Component
{
    public $arTableHead;
    public $arValores;
    public $showActions;

    public function __construct($arTableHead, $arValores, $showActions = false)
    {
        $this->arTableHead = $arTableHead;
        $this->arValores = $arValores;
        $this->showActions = $showActions;
    }

    public function render()
    {
        return view('components.ui.responsive-table-card');
    }
}
