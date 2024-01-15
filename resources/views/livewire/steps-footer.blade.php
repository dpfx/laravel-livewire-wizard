<div class="p-2 flex flex-row-reverse justify-between">
    @if($this->hasNextStep())
        <x-button lg primary right-icon="chevron-right" wire:click="goToNextStep" :label="__('navigation.next')"/>
    @else
        <x-button lg primary wire:click="save" type="submit" spinner="submit" :label="__('navigation.submit')"/>
    @endif
    @if($this->hasPrevStep())
        <x-button lg primary :label="__('navigation.back')" icon="chevron-left" wire:click="goToPrevStep"/>
    @endif
</div>
