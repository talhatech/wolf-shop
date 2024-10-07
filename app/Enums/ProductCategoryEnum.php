<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

final class ProductCategoryEnum extends Enum
{
    const NORMAL = 'normal'; // Regular item
    const LEGENDARY = 'legendary'; // Legendary items like "Samsung Galaxy S23". *Hope you like the name :-)*
    const INCREASING_WITH_AGE = 'increasing_with_age'; // Items that increase in quality over time, e.g., "Apple AirPods"
    const TIME_SENSITIVE = 'time_sensitive'; // Items with quality that increases as the sell-in date approaches but drops after a specific time, e.g., "Apple iPad Air"
    const CONJURED = 'conjured'; // Items like "Xiaomi Redmi Note 13" that degrade twice as fast

}
