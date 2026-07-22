<?php

namespace App\Data\Chart;

use Spatie\LaravelData\Data;

/**
 * @property string $title
 * @property string $value
 * @property string $interval
 * @property string $trend
 * @property array<int> $data
 */
class ProductData extends Data
{
    /**
     * @param  array<int>  $data
     */
    public function __construct(
        public string $title,
        public string $value,
        public string $interval,
        public string $trend,
        public array $data
    ) {}
}
