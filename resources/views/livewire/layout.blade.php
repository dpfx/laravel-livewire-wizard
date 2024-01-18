<div>
    @include('livewire-wizards::livewire.steps-header')
    <div class="container p-4 mx-auto min-h-56">
        @foreach ($instances as $step => $data)
            @livewire($step, ['wizard' => get_class($this), 'uuid' => $key, 'position' => $data['position'], 'store' => $this->store], key($key . $step))
        @endforeach
    </div>
    @include('livewire-wizards::livewire.steps-footer')
</div>
