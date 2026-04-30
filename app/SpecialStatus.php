<?php

namespace App;

enum SpecialStatus: string
{
    case SelectedForDelivery = 'selected_for_delivery';
    case ContinueForNext = 'continue_for_next';
    case StuckOrder = 'stuck_order';

    public function getLabel(): string
    {
        return match ($this) {
            self::SelectedForDelivery => 'Selected for delivery',
            self::ContinueForNext => 'Continue for next',
            self::StuckOrder => 'Stuck order',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::SelectedForDelivery => 'bg-green-100 text-green-800',
            self::ContinueForNext => 'bg-blue-100 text-blue-800',
            self::StuckOrder => 'bg-red-100 text-red-800',
        };
    }
}
