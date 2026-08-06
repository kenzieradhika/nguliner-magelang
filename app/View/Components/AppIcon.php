<?php

namespace App\View\Components;

use Illuminate\View\Component;

class AppIcon extends Component
{
    public function __construct(
        public string $name = '',
        public string $class = 'h-4 w-4',
    ) {}

    public function render()
    {
        return view('components.icon');
    }
}