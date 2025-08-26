<?php

namespace App\Livewire\Onboarding;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class BudgetWizard extends Component
{
    public int $currentStep = 1;

    public function nextStep(): void
    {
        $this->currentStep++;
    }

    public function finish(): void
    {
        $user = Auth::user();
        $user->has_completed_budget_onboarding = true;
        $user->save();

        // Hide the component by dispatching an event
        $this->dispatch('onboardingComplete');
    }

    public function render()
    {
        return view('livewire.onboarding.budget-wizard');
    }
}