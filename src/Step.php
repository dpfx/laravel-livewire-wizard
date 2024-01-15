<?php

namespace Dpfx\LaravelLivewireWizards;

use Livewire\Attributes\On;
use Livewire\Component;
use Illuminate\Support\Str;

abstract class Step extends Component
{
    public string $wizard;

    public string $uuid;

    public int $position;

    public array $state = [];

    public array $oldState = [];

    public string $icon = 'check';

    public string $defaultIcon = '';

    public string $title = '';

    public string $defaultTitle = '';

    public bool $open = false;

    public abstract function render();

    #[On('open')] 
    public function beforeOpen(string $key, int $position)
    {
        if ($key !== $this->uuid || $position !== $this->position) {
            return;
        }

        if (method_exists($this, 'onOpen')) {
            $this->onOpen();
        }

        $this->open = true;

        $this->notifyWizard('step-opened', $position);
    }

    #[On('close')] 
    public function beforeClose(string $key, int $position)
    {
        if ($key !== $this->uuid || $position !== $this->position) {
            return;
        }

        if (method_exists($this, 'onClose')) {
            $this->onClose();
        }

        $this->open = false;

        $this->notifyWizard('step-closed', $position);
    }

    #[On('onStepOut')] 
    public function onStepOut()
    {
        if (method_exists($this, 'out')) {
            $this->out();
        }
        $this->share();
    }

    #[On('updated-store')] 
    public function refreshStore(string $key, int $position, array $store, array $oldStore)
    {
        if ($key !== $this->uuid || $position !== $this->position) {
            return;
        }

        $this->mount($store, $oldStore);

        if (method_exists($this, 'onChangedState')) {
            $this->onChangedState();
        }
    }

    public function updatedState()
    {
        $this->share();
    }

    public function mount(array $store, array $oldStore = [])
    {
        $this->state = array_merge($this->state, $store);
        $this->oldState = $oldStore;

        $this->dispatch('step-mounted', $this->uuid, $this->position);
    }

    public function share(array $data = null): void
    {
        $data ??= $this->state;
        $this->dispatch('commit',
            $this->uuid,
            $data,
        )->to($this->wizard);
    }

    public function state(string $key, mixed $default = null): mixed
    {
        return $this->state[$key] ?? $default;
    }

    public function setIcon(string $icon = ''): void
    {
        $this->icon = $icon ? $icon : $this->defaultIcon;

        $this->dispatch(
            'updated-icon',
            get_class($this),
            $this->icon,
        )->to($this->wizard);
    }

    public function setTitle(string $title = ''): void
    {
        $this->title = $title ? $title : $this->defaultTitle;

        $this->dispatch(
            'updated-title',
            get_class($this),
            $this->title,
        )->to($this->wizard);
    }

    public function setDefaultIcon(string $icon = ''): void
    {
        $this->defaultIcon = $icon;
    }

    public function setDefaultTitle(string $title = ''): void
    {
        $this->defaultTitle = $title;
    }

    public function notifyWizard($event, ...$params): void
    {
        $this->dispatch($event, $this->uuid, ...$params)->to($this->wizard);
    }

    // public static function path()
    // {
    //     $class = static::class;
    //     $path = Str::after($class, 'App\Livewire\\');
    //     $path = Str::kebab($path);
    //     $path = Str::replace('\-', '.', $path);

    //     return $path;
    // }
}