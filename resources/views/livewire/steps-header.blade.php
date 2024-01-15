<div class="w-full py-2">
    <div class="flex justify-center">
        <h2>{{ $title }}</h2>
    </div>
    <div class="flex my-4">
        @foreach($this->instances as $key => $data)
            @include('livewire-wizards::livewire.step-header')
        @endforeach
    </div>
</div>
