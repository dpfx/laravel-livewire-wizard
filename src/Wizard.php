<?php

namespace Dpfx\LaravelLivewireWizards;

use Livewire\Attributes\On;
use Livewire\Component;
use Illuminate\Support\Str;

abstract class Wizard extends Component
{
    /**
     * The key that enables multiple wizards to coexist on the same page. Every
     * step will dispatch its events to the wizard using this key.
     */
    public string $key;

    /**
     * The title of the wizard. Can hold both either plain text or a localized
     * key.
     */
    public string $title;

    /**
     * The specific data each step will be built with.
     */
    public array $instances = [];

    /**
     * Holds the current state of each step.
     */
    public array $store = [];

    /**
     * Holds the amount of steps currently mounted.
     */
    public int $stepsMounted = 0;

    /**
     * The currently opened position (including "0").
     */
    public int $step = 0;

    /**
     * Will be invoked as soon as the user submits the form built by the wizard.
     */
    public abstract function save(): void;

    /**
     * Each step reports back after it mounted itself. As soon as all steps
     * reported in we can open the configured first step.
     */
    #[On('step-mounted')] 
    public function onStepMounted(string $key, int $position)
    {
        if ($key !== $this->key) {
            return;
        }

        $this->stepsMounted++;

        if ($this->stepsMounted >= count($this->steps)) {
            $this->openStep($this->step);
        }
    }

    /**
     * Each step reports in as soon as it has been opened.
     */
    #[On('step-opened')] 
    public function onStepOpened(string $key, int $position)
    {
        if ($key !== $this->key) {
            return;
        }

        //
    }

    /**
     * Each step reports in as soon as it has been closed. All we have to do is
     * open the new current step.
     */
    #[On('step-closed')] 
    public function onStepClosed(string $key, int $position)
    {
        if ($key !== $this->key) {
            return;
        }

        $this->openStep($this->step);
    }

    /**
     * Each step commits its data on various occasions. That data has to be
     * stored and transmitted to all steps so that they can update their local
     * state based on the new data.
     */
    #[On('commit')] 
    public function commit(string $key, array $state)
    {
        if ($key !== $this->key) {
            return;
        }

        $old = $this->store;

        $this->store = array_merge($this->store, $state);

        $this->notifySteps('updated-store', $this->store, $old);
    }

    /**
     * The icon of each step is located within a view managed by the wizard.
     * Therefore each step has to report an icon change so that the wizard can
     * re-render it.
     */
    #[On('updated-icon')] 
    public function setIconOfStep(string $step, string $icon = ''): void
    {
        $this->instances[$step]['icon'] = $icon;
    }

    /**
     * The title of each step is located within a view managed by the wizard.
     * Therefore each step has to report a title change so that the wizard can
     * re-render it.
     */
    #[On('updated-title')] 
    public function setTitleOfStep(string $step, string $title = ''): void
    {
        $this->instances[$step]['title'] = $title;
    }

    public function mount()
    {
        $this->key = Str::uuid();
        $this->title = __($this->title);
        $this->prepareSteps();
    }

    public function render()
    {
        return view('laravel-livewire-wizards::livewire.layout');
    }

    public function prepareSteps(): void
    {
        for ($i = 0; $i < count($this->steps); $i++) {
            $step = $this->steps[$i];
            $instance = app()->make($step);
            $this->instances[$step] = [
                'position' => $i,
                'title' => __($instance->title),
                'icon' => $instance->icon,
                'step' => $step,
            ];
        }
    }

    public function setStep(int $step)
    {
        $this->closeStep($this->step);

        // Set the position of the new step.
        $this->step = $step;
    }

    public function openStep(int $position)
    {
        $this->notifyStep($position, 'open');
    }

    public function closeStep(int $position)
    {
        $this->notifyStep($position, 'close');
    }

    public function notifyStep(int $position, string $event, ...$params): void
    {
        $this->dispatch($event, $this->key, $position, ...$params);
    }

    public function notifySteps(string $event, ...$params): void
    {
        for ($i = 0; $i < count($this->steps); $i++) {
            $this->notifyStep($i, $event, ...$params);
        }
    }

    public function hasNextStep(): bool
    {
        return count($this->steps) - 1 > $this->step;
    }

    public function hasPrevStep(): bool
    {
        $steps = count($this->steps);
        return $steps && $this->step;
    }

    public function goToNextStep(): void
    {
        if (!$this->hasNextStep()) {
            return;
        }

        $this->setStep($this->step + 1);
    }

    public function goToPrevStep(): void
    {
        if (!$this->hasPrevStep()) {
            return;
        }

        $this->setStep($this->step - 1);
    }
}
