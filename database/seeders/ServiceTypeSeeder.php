<?php

namespace Database\Seeders;

use App\Models\ServiceType;
use App\Models\ServiceTypeProducts;
use App\Models\TypeCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        TypeCategory::factory(['label' => 'Bedroom supplies'])
            ->has(
                ServiceType::factory([
                    'label' => 'Bed Set — Double / Queen / King',
                    'price' => 17.5
                ])->quantifiable()
                    ->available()
                    ->has(ServiceTypeProducts::factory(['label' => 'Sheets', 'quantity' => 3]))
                    ->has(ServiceTypeProducts::factory(['label' => 'Pillowcases', 'quantity' => 4]))
                    ->has(ServiceTypeProducts::factory(['label' => 'Bath Towels', 'quantity' => 2]))
            )
            ->has(
                ServiceType::factory([
                    'label' => 'Bed Set — Single',
                    'price' => 10
                ])->quantifiable()
                    ->available()
                    ->has(ServiceTypeProducts::factory(['label' => 'Sheets', 'quantity' => 3]))
                    ->has(ServiceTypeProducts::factory(['label' => 'Pillowcases', 'quantity' => 2]))
                    ->has(ServiceTypeProducts::factory(['label' => 'Bath Towels', 'quantity' => 1]))
            )
            ->create();

        TypeCategory::factory(['label' => 'Bathroom supplies'])
            ->has(
                ServiceType::factory([
                    'label' => 'Shower Set',
                    'price' => 8.5
                ])->quantifiable()
                    ->available()
                    ->has(ServiceTypeProducts::factory(['label' => 'Cleansing Shampoo', 'quantity' => 1]))
                    ->has(ServiceTypeProducts::factory(['label' => 'Body Wash', 'quantity' => 1]))
                    ->has(ServiceTypeProducts::factory(['label' => 'Body Lotion', 'quantity' => 1]))
                    ->has(ServiceTypeProducts::factory(['label' => 'Cleansing Soap', 'quantity' => 1]))
            )
            ->has(
                ServiceType::factory([
                    'label' => 'Bathroom Set',
                    'price' => 12.5
                ])->quantifiable()
                    ->available()
                    ->has(ServiceTypeProducts::factory(['label' => 'Bath Mat', 'quantity' => 1]))
                    ->has(ServiceTypeProducts::factory(['label' => 'Hand Towel', 'quantity' => 1]))
                    ->has(ServiceTypeProducts::factory(['label' => 'Face Washers', 'quantity' => 2]))
                    ->has(ServiceTypeProducts::factory(['label' => 'Bath Towels', 'quantity' => 2]))
                    ->has(ServiceTypeProducts::factory(['label' => 'Toilet Paper', 'quantity' => 3]))
            )
            ->create();

        TypeCategory::factory(['label' => 'Consumables and Amenities'])
            ->has(
                ServiceType::factory([
                    'label' => 'Kitchen Pack',
                    'price' => 5
                ])->quantifiable()
                    ->available()
                    ->has(ServiceTypeProducts::factory(['label' => 'Sponge + Scourer', 'quantity' => 1]))
                    ->has(ServiceTypeProducts::factory(['label' => 'Domestic Wipe', 'quantity' => 1]))
                    ->has(ServiceTypeProducts::factory(['label' => 'Laundry Powder', 'quantity' => 1]))
                    ->has(ServiceTypeProducts::factory(['label' => 'Dishwashing Liquid', 'quantity' => 1]))
                    ->has(ServiceTypeProducts::factory(['label' => 'Dishwashing Tablet', 'quantity' => 1]))
                    ->has(ServiceTypeProducts::factory(['label' => 'Extra Bin Liners', 'quantity' => 3]))
            )
            ->has(
                ServiceType::factory([
                    'label' => 'Beverages Pack',
                    'price' => 2.5
                ])->quantifiable()
                    ->available()
                    ->has(ServiceTypeProducts::factory(['label' => 'Milk', 'quantity' => 1]))
                    ->has(ServiceTypeProducts::factory(['label' => 'Tea', 'quantity' => 2]))
                    ->has(ServiceTypeProducts::factory(['label' => 'Coffee', 'quantity' => 2]))
                    ->has(ServiceTypeProducts::factory(['label' => 'Sugar', 'quantity' => 2]))
            )
            ->create();

        TypeCategory::factory(['label' => 'Cleaning supplies'])
            ->has(ServiceType::factory([
                'label' => 'Deep Clean / Extended Clean',
                'price' => '50'
            ])->quantifiable(false)
                ->available()
                ->has(ServiceTypeProducts::factory(['label' => 'Charged in blocks of 1 hour'])))
            ->create();
    }
}
