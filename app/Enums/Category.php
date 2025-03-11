<?php

namespace App\Enums;

enum Category: string
{
    case ELECTRONICS = 'Electronics & Gadgets';
    case FASHION = 'Fashion & Apparel';
    case HOME_KITCHEN = 'Home & Kitchen';
    case BEAUTY = 'Beauty & Personal Care';
    case HEALTH_WELLNESS = 'Health & Wellness';
    case TOYS_GAMES = 'Toys & Games';
    case SPORTS_OUTDOOR = 'Sports & Outdoor';
    case AUTOMOTIVE = 'Automotive & Tools';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
