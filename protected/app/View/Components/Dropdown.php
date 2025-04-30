<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Dropdown extends Component
{
    /**
     * The alignment of the dropdown.
     */
    public $align;

    /**
     * The width of the dropdown.
     */
    public $width;

    /**
     * The content classes of the dropdown.
     */
    public $contentClasses;

    /**
     * Create a new component instance.
     */
    public function __construct($align = 'right', $width = '48', $contentClasses = 'py-1 bg-white dark:bg-gray-700')
    {
        $this->align = $align;
        $this->width = $width;
        $this->contentClasses = $contentClasses;
    }

    /**
     * Get the view / contents that represents the component.
     */
    public function render()
    {
        return view('components.dropdown');
    }
}
