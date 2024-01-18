<div class="p-2 flex flex-row-reverse justify-between">
    @if($this->hasNextStep())
        <x-secondary-button wire:click="goToNextStep">{{ __('navigation.next') }}</x-secondary-button>
    @else
        <x-primary-button type="submit" wire:click="save">{{ __('navigation.submit') }}</x-primary-button>
    @endif
    @if($this->hasPrevStep())
        <x-secondary-button wire:click="goToPrevStep">{{ __('navigation.back') }}</x-secondary-button>
    @endif
</div>
