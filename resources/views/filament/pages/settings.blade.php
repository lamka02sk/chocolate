<x-filament::page>
    <form wire:submit.prevent="save">
        {{ $this->form }}
        <x-filament::button type="submit" wire:loading.attr="disabled" class="mt-4">
            <div wire:loading.flex class="gap-2">
                <x-filament::loading-indicator class="h-5 w-5" />
                Saving settings...
            </div>
            <div wire:loading.remove>Save settings</div>
        </x-filament::button>
    </form>
</x-filament::page>
