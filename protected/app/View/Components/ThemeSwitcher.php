<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ThemeSwitcher extends Component
{
    /**
     * Get the view / contents that represents the component.
     */
    public function render()
    {
        return view('components.theme-switcher');
    }
}
