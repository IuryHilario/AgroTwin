<?php
namespace App\View\Components\UI;

use Illuminate\View\Component;

class SectionHeader extends Component
{
    public $title;
    public $icon;

    public function __construct($title, $icon = null)
    {
        $this->title = $title;
        $this->icon = $icon;

    }

    public function render()
    {
        return view('components.ui.section-header');
    }
}
