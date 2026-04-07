@php
    $pollingInterval = $this->getPollingInterval();
@endphp


<x-filament-widgets::widget
    :attributes="
        (new \Illuminate\View\ComponentAttributeBag)
            ->merge([
                'wire:poll.' . $pollingInterval => $pollingInterval ? true : null,
            ], escape: false)
            ->class([
                'fi-wi-stats-overview',
            ])
    "
>
<style>
    .fi-wi-stats-overview-stat .fi-wi-stats-overview-stat-value {
        font-size: 14px;
    }
    .filter-box {
        display: flex;
        justify-content: flex-start;
    }
    </style>
    <div class="fi-wi-stats-overview-stat">
    <div class="mb-4 flex justify-start filter-box">
        <x-filament::dropdown
            placement="bottom-end"
            shift
            width="md"
            class="fi-wi-chart-filter"
        >
            <x-slot name="trigger">
                {{ $this->getFiltersTriggerAction() }}
            </x-slot>

            <div class="fi-wi-chart-filter-content p-4">
                {{ $this->getFiltersSchema() }}
            </div>
        </x-filament::dropdown>
    </div>

    {{ $this->content }}
    </div>
</x-filament-widgets::widget>
