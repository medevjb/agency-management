<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DashboardChart extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $title,
        public string $type = 'line',
        public array $data = [],
        public ?string $height = '300px'
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.dashboard-chart');
    }
}