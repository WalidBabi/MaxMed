<?php

namespace App\View\Components;

use App\Services\FeatureAccessService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class FeatureAccess extends Component
{
    public string $feature;
    public bool $canAccess;

    /**
     * Create a new component instance.
     */
    public function __construct(string $feature)
    {
        $this->feature = $feature;
        $this->canAccess = Auth::check() && FeatureAccessService::canAccess(Auth::user(), $feature);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return $this->canAccess ? $this->view('components.feature-access') : '';
    }
}
