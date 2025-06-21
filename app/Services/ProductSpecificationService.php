<?php

namespace App\Services;

use App\Models\ProductSpecification;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductSpecificationService
{
    /**
     * Save product specifications
     *
     * @param int $productId
     * @param array $specifications
     * @return void
     */
    public function saveProductSpecifications(int $productId, array $specifications): void
    {
        // Delete existing specifications for this product
        ProductSpecification::where('product_id', $productId)->delete();

        // Insert new specifications
        foreach ($specifications as $spec) {
            if (!empty($spec['specification_key']) && !empty($spec['specification_value'])) {
                ProductSpecification::create([
                    'product_id' => $productId,
                    'specification_key' => $spec['specification_key'],
                    'specification_value' => $spec['specification_value'],
                    'unit' => $spec['unit'] ?? null,
                    'category' => $spec['category'] ?? 'General',
                    'display_name' => $spec['display_name'] ?? $spec['specification_key'],
                    'description' => $spec['description'] ?? null,
                    'sort_order' => $spec['sort_order'] ?? 0,
                    'is_filterable' => $spec['is_filterable'] ?? false,
                    'is_searchable' => $spec['is_searchable'] ?? false,
                    'show_on_listing' => $spec['show_on_listing'] ?? false,
                    'show_on_detail' => $spec['show_on_detail'] ?? true,
                ]);
            }
        }
    }

    /**
     * Get existing specifications for a product
     *
     * @param int $productId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getExistingSpecifications(int $productId)
    {
        return ProductSpecification::where('product_id', $productId)
            ->orderBy('category', 'asc')
            ->orderBy('sort_order', 'asc')
            ->get();
    }

    /**
     * Get category specification templates
     *
     * @param int $categoryId
     * @return array
     */
    public function getCategorySpecificationTemplates(int $categoryId): array
    {
        $category = Category::find($categoryId);
        
        if (!$category) {
            \Log::info("Category not found for ID: {$categoryId}");
            return [];
        }

        \Log::info("Looking for templates for category: {$category->name}");

        // Define templates based on category name - format for JavaScript frontend
        $templates = [
            'Lab Equipment' => [
                'Physical' => [
                    [
                        'key' => 'dimensions',
                        'name' => 'Dimensions',
                        'type' => 'text',
                        'unit' => 'cm',
                        'category' => 'Physical',
                        'sort_order' => 1,
                        'is_filterable' => true,
                        'show_on_listing' => true,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                    [
                        'key' => 'weight',
                        'name' => 'Weight',
                        'type' => 'decimal',
                        'unit' => 'kg',
                        'category' => 'Physical',
                        'sort_order' => 2,
                        'is_filterable' => true,
                        'show_on_listing' => true,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                ],
                'Electrical' => [
                    [
                        'key' => 'power_consumption',
                        'name' => 'Power Consumption',
                        'type' => 'decimal',
                        'unit' => 'W',
                        'category' => 'Electrical',
                        'sort_order' => 3,
                        'is_filterable' => false,
                        'show_on_listing' => false,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                    [
                        'key' => 'voltage',
                        'name' => 'Voltage',
                        'type' => 'decimal',
                        'unit' => 'V',
                        'category' => 'Electrical',
                        'sort_order' => 4,
                        'is_filterable' => true,
                        'show_on_listing' => false,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                ],
                'Construction' => [
                    [
                        'key' => 'material',
                        'name' => 'Material',
                        'type' => 'text',
                        'category' => 'Construction',
                        'sort_order' => 5,
                        'is_filterable' => true,
                        'show_on_listing' => false,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                ],
            ],
            'Mixing & Shaking Equipment' => [
                'Performance' => [
                    [
                        'key' => 'max_speed',
                        'name' => 'Max Speed',
                        'type' => 'decimal',
                        'unit' => 'rpm',
                        'category' => 'Performance',
                        'sort_order' => 1,
                        'is_filterable' => true,
                        'show_on_listing' => true,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                    [
                        'key' => 'capacity',
                        'name' => 'Capacity',
                        'type' => 'text',
                        'unit' => '',
                        'category' => 'Performance',
                        'sort_order' => 2,
                        'is_filterable' => true,
                        'show_on_listing' => true,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                ],
                'Construction' => [
                    [
                        'key' => 'material',
                        'name' => 'Material',
                        'type' => 'text',
                        'category' => 'Construction',
                        'sort_order' => 3,
                        'is_filterable' => true,
                        'show_on_listing' => false,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                ],
            ],
            'Centrifuges' => [
                'Performance' => [
                    [
                        'key' => 'max_speed',
                        'name' => 'Max Speed',
                        'type' => 'decimal',
                        'unit' => 'rpm',
                        'category' => 'Performance',
                        'sort_order' => 1,
                        'is_filterable' => true,
                        'show_on_listing' => true,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                    [
                        'key' => 'max_rcf',
                        'name' => 'Max RCF',
                        'type' => 'decimal',
                        'unit' => 'g',
                        'category' => 'Performance',
                        'sort_order' => 2,
                        'is_filterable' => true,
                        'show_on_listing' => true,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                ],
                'Capacity' => [
                    [
                        'key' => 'capacity',
                        'name' => 'Capacity',
                        'type' => 'text',
                        'unit' => '',
                        'category' => 'Capacity',
                        'sort_order' => 3,
                        'is_filterable' => true,
                        'show_on_listing' => true,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                ],
            ],
            'Mixers' => [
                'Performance' => [
                    [
                        'key' => 'max_speed',
                        'name' => 'Max Speed',
                        'type' => 'decimal',
                        'unit' => 'rpm',
                        'category' => 'Performance',
                        'sort_order' => 1,
                        'is_filterable' => true,
                        'show_on_listing' => true,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                ],
                'Construction' => [
                    [
                        'key' => 'material',
                        'name' => 'Material',
                        'type' => 'text',
                        'category' => 'Construction',
                        'sort_order' => 2,
                        'is_filterable' => true,
                        'show_on_listing' => false,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                ],
            ],
            'Shakers' => [
                'Performance' => [
                    [
                        'key' => 'max_speed',
                        'name' => 'Max Speed',
                        'type' => 'decimal',
                        'unit' => 'rpm',
                        'category' => 'Performance',
                        'sort_order' => 1,
                        'is_filterable' => true,
                        'show_on_listing' => true,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                ],
                'Construction' => [
                    [
                        'key' => 'material',
                        'name' => 'Material',
                        'type' => 'text',
                        'category' => 'Construction',
                        'sort_order' => 2,
                        'is_filterable' => true,
                        'show_on_listing' => false,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                ],
            ],
            'Microbiology Equipment' => [
                'Performance' => [
                    [
                        'key' => 'capacity',
                        'name' => 'Capacity',
                        'type' => 'text',
                        'unit' => '',
                        'category' => 'Performance',
                        'sort_order' => 1,
                        'is_filterable' => true,
                        'show_on_listing' => true,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                ],
                'Construction' => [
                    [
                        'key' => 'material',
                        'name' => 'Material',
                        'type' => 'text',
                        'category' => 'Construction',
                        'sort_order' => 2,
                        'is_filterable' => true,
                        'show_on_listing' => false,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                ],
            ],
            'Pathology Equipment' => [
                'Performance' => [
                    [
                        'key' => 'capacity',
                        'name' => 'Capacity',
                        'type' => 'text',
                        'unit' => '',
                        'category' => 'Performance',
                        'sort_order' => 1,
                        'is_filterable' => true,
                        'show_on_listing' => true,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                ],
                'Construction' => [
                    [
                        'key' => 'material',
                        'name' => 'Material',
                        'type' => 'text',
                        'category' => 'Construction',
                        'sort_order' => 2,
                        'is_filterable' => true,
                        'show_on_listing' => false,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                ],
            ],
            'Thermal & Process Equipment' => [
                'Performance' => [
                    [
                        'key' => 'temperature_range',
                        'name' => 'Temperature Range',
                        'type' => 'text',
                        'unit' => '°C',
                        'category' => 'Performance',
                        'sort_order' => 1,
                        'is_filterable' => true,
                        'show_on_listing' => true,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                ],
                'Construction' => [
                    [
                        'key' => 'material',
                        'name' => 'Material',
                        'type' => 'text',
                        'category' => 'Construction',
                        'sort_order' => 2,
                        'is_filterable' => true,
                        'show_on_listing' => false,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                ],
            ],
            'Distillation Systems' => [
                'Performance' => [
                    [
                        'key' => 'capacity',
                        'name' => 'Capacity',
                        'type' => 'text',
                        'unit' => 'L',
                        'category' => 'Performance',
                        'sort_order' => 1,
                        'is_filterable' => true,
                        'show_on_listing' => true,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                ],
            ],
            'Incubators & Ovens' => [
                'Performance' => [
                    [
                        'key' => 'temperature_range',
                        'name' => 'Temperature Range',
                        'type' => 'text',
                        'unit' => '°C',
                        'category' => 'Performance',
                        'sort_order' => 1,
                        'is_filterable' => true,
                        'show_on_listing' => true,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                ],
            ],
            'Disinfection And Sterilization Equipment' => [
                'Performance' => [
                    [
                        'key' => 'sterilization_method',
                        'name' => 'Sterilization Method',
                        'type' => 'text',
                        'category' => 'Performance',
                        'sort_order' => 1,
                        'is_filterable' => true,
                        'show_on_listing' => true,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                ],
            ],
            'Air Protection Equipment' => [
                'Performance' => [
                    [
                        'key' => 'airflow',
                        'name' => 'Airflow',
                        'type' => 'text',
                        'unit' => 'm3/h',
                        'category' => 'Performance',
                        'sort_order' => 1,
                        'is_filterable' => true,
                        'show_on_listing' => true,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                ],
            ],
            'Cold Chain Products' => [
                'Performance' => [
                    [
                        'key' => 'temperature_range',
                        'name' => 'Temperature Range',
                        'type' => 'text',
                        'unit' => '°C',
                        'category' => 'Performance',
                        'sort_order' => 1,
                        'is_filterable' => true,
                        'show_on_listing' => true,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                ],
            ],
            'Lab Consumables' => [
                'Physical' => [
                    [
                        'key' => 'size',
                        'name' => 'Size',
                        'type' => 'text',
                        'category' => 'Physical',
                        'sort_order' => 1,
                        'is_filterable' => true,
                        'show_on_listing' => true,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                ],
                'Construction' => [
                    [
                        'key' => 'material',
                        'name' => 'Material',
                        'type' => 'text',
                        'category' => 'Construction',
                        'sort_order' => 2,
                        'is_filterable' => true,
                        'show_on_listing' => false,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                ],
                'Quality' => [
                    [
                        'key' => 'sterility',
                        'name' => 'Sterility',
                        'type' => 'select',
                        'options' => ['Sterile', 'Non-sterile', 'Single-use', 'Reusable'],
                        'category' => 'Quality',
                        'sort_order' => 3,
                        'is_filterable' => true,
                        'show_on_listing' => false,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                ],
                'General' => [
                    [
                        'key' => 'packaging',
                        'name' => 'Packaging',
                        'type' => 'text',
                        'category' => 'General',
                        'sort_order' => 4,
                        'is_filterable' => false,
                        'show_on_listing' => false,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                ],
            ],
            'Lab Essentials (Tubes, Pipettes, Glassware)' => [
                'Physical' => [
                    [
                        'key' => 'size',
                        'name' => 'Size',
                        'type' => 'text',
                        'category' => 'Physical',
                        'sort_order' => 1,
                        'is_filterable' => true,
                        'show_on_listing' => true,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                ],
                'Material' => [
                    [
                        'key' => 'material',
                        'name' => 'Material',
                        'type' => 'text',
                        'category' => 'Material',
                        'sort_order' => 2,
                        'is_filterable' => true,
                        'show_on_listing' => false,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                ],
                'Quality' => [
                    [
                        'key' => 'sterility',
                        'name' => 'Sterility',
                        'type' => 'select',
                        'options' => ['Sterile', 'Non-sterile', 'Single-use', 'Reusable'],
                        'category' => 'Quality',
                        'sort_order' => 3,
                        'is_filterable' => true,
                        'show_on_listing' => false,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                ],
            ],
            'Chemical & Reagents' => [
                'Chemical Properties' => [
                    [
                        'key' => 'purity',
                        'name' => 'Purity',
                        'type' => 'decimal',
                        'unit' => '%',
                        'category' => 'Chemical Properties',
                        'sort_order' => 1,
                        'is_filterable' => true,
                        'show_on_listing' => true,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                    [
                        'key' => 'grade',
                        'name' => 'Grade',
                        'type' => 'text',
                        'category' => 'Chemical Properties',
                        'sort_order' => 2,
                        'is_filterable' => true,
                        'show_on_listing' => true,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                ],
                'Packaging' => [
                    [
                        'key' => 'packaging',
                        'name' => 'Packaging',
                        'type' => 'text',
                        'category' => 'Packaging',
                        'sort_order' => 3,
                        'is_filterable' => false,
                        'show_on_listing' => false,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                ],
            ],
            'Medical Consumables' => [
                'Physical' => [
                    [
                        'key' => 'size',
                        'name' => 'Size',
                        'type' => 'text',
                        'category' => 'Physical',
                        'sort_order' => 1,
                        'is_filterable' => true,
                        'show_on_listing' => true,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                ],
                'Construction' => [
                    [
                        'key' => 'material',
                        'name' => 'Material',
                        'type' => 'text',
                        'category' => 'Construction',
                        'sort_order' => 2,
                        'is_filterable' => true,
                        'show_on_listing' => false,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                ],
                'Quality' => [
                    [
                        'key' => 'sterility',
                        'name' => 'Sterility',
                        'type' => 'select',
                        'options' => ['Sterile', 'Non-sterile', 'Single-use', 'Reusable'],
                        'category' => 'Quality',
                        'sort_order' => 3,
                        'is_filterable' => true,
                        'show_on_listing' => false,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                ],
                'General' => [
                    [
                        'key' => 'packaging',
                        'name' => 'Packaging',
                        'type' => 'text',
                        'category' => 'General',
                        'sort_order' => 4,
                        'is_filterable' => false,
                        'show_on_listing' => false,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                ],
            ],
            'PPE & Safety Gear' => [
                'Physical' => [
                    [
                        'key' => 'size',
                        'name' => 'Size',
                        'type' => 'text',
                        'category' => 'Physical',
                        'sort_order' => 1,
                        'is_filterable' => true,
                        'show_on_listing' => true,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                ],
                'Material' => [
                    [
                        'key' => 'material',
                        'name' => 'Material',
                        'type' => 'text',
                        'category' => 'Material',
                        'sort_order' => 2,
                        'is_filterable' => true,
                        'show_on_listing' => false,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                ],
                'Quality' => [
                    [
                        'key' => 'sterility',
                        'name' => 'Sterility',
                        'type' => 'select',
                        'options' => ['Sterile', 'Non-sterile', 'Single-use', 'Reusable'],
                        'category' => 'Quality',
                        'sort_order' => 3,
                        'is_filterable' => true,
                        'show_on_listing' => false,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                ],
            ],
            'Dental Consumables' => [
                'Physical' => [
                    [
                        'key' => 'size',
                        'name' => 'Size',
                        'type' => 'text',
                        'category' => 'Physical',
                        'sort_order' => 1,
                        'is_filterable' => true,
                        'show_on_listing' => true,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                ],
                'Material' => [
                    [
                        'key' => 'material',
                        'name' => 'Material',
                        'type' => 'text',
                        'category' => 'Material',
                        'sort_order' => 2,
                        'is_filterable' => true,
                        'show_on_listing' => false,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                ],
                'Quality' => [
                    [
                        'key' => 'sterility',
                        'name' => 'Sterility',
                        'type' => 'select',
                        'options' => ['Sterile', 'Non-sterile', 'Single-use', 'Reusable'],
                        'category' => 'Quality',
                        'sort_order' => 3,
                        'is_filterable' => true,
                        'show_on_listing' => false,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                ],
            ],
            'Analytical Instruments' => [
                'Performance' => [
                    [
                        'key' => 'accuracy',
                        'name' => 'Accuracy',
                        'type' => 'decimal',
                        'unit' => '%',
                        'category' => 'Performance',
                        'sort_order' => 1,
                        'is_filterable' => true,
                        'show_on_listing' => true,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                    [
                        'key' => 'precision',
                        'name' => 'Precision',
                        'type' => 'decimal',
                        'unit' => '%',
                        'category' => 'Performance',
                        'sort_order' => 2,
                        'is_filterable' => true,
                        'show_on_listing' => true,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                    [
                        'key' => 'detection_limit',
                        'name' => 'Detection Limit',
                        'type' => 'decimal',
                        'unit' => 'ppm',
                        'category' => 'Performance',
                        'sort_order' => 3,
                        'is_filterable' => false,
                        'show_on_listing' => false,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                    [
                        'key' => 'calibration_range',
                        'name' => 'Calibration Range',
                        'type' => 'text',
                        'category' => 'Performance',
                        'sort_order' => 4,
                        'is_filterable' => false,
                        'show_on_listing' => false,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                ],
            ],
            'Chromatography Consumables' => [
                'Physical' => [
                    [
                        'key' => 'size',
                        'name' => 'Size',
                        'type' => 'text',
                        'category' => 'Physical',
                        'sort_order' => 1,
                        'is_filterable' => true,
                        'show_on_listing' => true,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                ],
                'Material' => [
                    [
                        'key' => 'material',
                        'name' => 'Material',
                        'type' => 'text',
                        'category' => 'Material',
                        'sort_order' => 2,
                        'is_filterable' => true,
                        'show_on_listing' => false,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                ],
            ],
            'UV-Vis Spectrophotometers' => [
                'Performance' => [
                    [
                        'key' => 'wavelength_range',
                        'name' => 'Wavelength Range',
                        'type' => 'text',
                        'unit' => 'nm',
                        'category' => 'Performance',
                        'sort_order' => 1,
                        'is_filterable' => true,
                        'show_on_listing' => true,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                    [
                        'key' => 'bandwidth',
                        'name' => 'Bandwidth',
                        'type' => 'decimal',
                        'unit' => 'nm',
                        'category' => 'Performance',
                        'sort_order' => 2,
                        'is_filterable' => true,
                        'show_on_listing' => true,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                ],
            ],
            'Electrochemistry Equipment' => [
                'Performance' => [
                    [
                        'key' => 'measurement_range',
                        'name' => 'Measurement Range',
                        'type' => 'text',
                        'unit' => '',
                        'category' => 'Performance',
                        'sort_order' => 1,
                        'is_filterable' => true,
                        'show_on_listing' => true,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                ],
            ],
            'Ion Chromatography(IC)' => [
                'Performance' => [
                    [
                        'key' => 'detection_limit',
                        'name' => 'Detection Limit',
                        'type' => 'decimal',
                        'unit' => 'ppm',
                        'category' => 'Performance',
                        'sort_order' => 1,
                        'is_filterable' => true,
                        'show_on_listing' => true,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                ],
            ],
            'Liquid Chromatograph (HPLC)' => [
                'Performance' => [
                    [
                        'key' => 'flow_rate',
                        'name' => 'Flow Rate',
                        'type' => 'decimal',
                        'unit' => 'mL/min',
                        'category' => 'Performance',
                        'sort_order' => 1,
                        'is_filterable' => true,
                        'show_on_listing' => true,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                ],
            ],
            'Life Science & Research' => [
                'Product Specifications' => [
                    [
                        'key' => 'concentration',
                        'name' => 'Concentration',
                        'type' => 'decimal',
                        'unit' => 'mg/mL',
                        'category' => 'Product Specifications',
                        'sort_order' => 1,
                        'is_filterable' => true,
                        'show_on_listing' => true,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                    [
                        'key' => 'purity',
                        'name' => 'Purity',
                        'type' => 'decimal',
                        'unit' => '%',
                        'category' => 'Product Specifications',
                        'sort_order' => 2,
                        'is_filterable' => true,
                        'show_on_listing' => true,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                    [
                        'key' => 'storage_conditions',
                        'name' => 'Storage Conditions',
                        'type' => 'text',
                        'category' => 'Product Specifications',
                        'sort_order' => 3,
                        'is_filterable' => false,
                        'show_on_listing' => false,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                    [
                        'key' => 'expiry_date',
                        'name' => 'Expiry Date',
                        'type' => 'text',
                        'category' => 'Product Specifications',
                        'sort_order' => 4,
                        'is_filterable' => false,
                        'show_on_listing' => false,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                ],
            ],
            'Antibodies' => [
                'Product Specifications' => [
                    [
                        'key' => 'concentration',
                        'name' => 'Concentration',
                        'type' => 'decimal',
                        'unit' => 'mg/mL',
                        'category' => 'Product Specifications',
                        'sort_order' => 1,
                        'is_filterable' => true,
                        'show_on_listing' => true,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                    [
                        'key' => 'purity',
                        'name' => 'Purity',
                        'type' => 'decimal',
                        'unit' => '%',
                        'category' => 'Product Specifications',
                        'sort_order' => 2,
                        'is_filterable' => true,
                        'show_on_listing' => true,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                ],
            ],
            'Recombinant Proteins' => [
                'Product Specifications' => [
                    [
                        'key' => 'concentration',
                        'name' => 'Concentration',
                        'type' => 'decimal',
                        'unit' => 'mg/mL',
                        'category' => 'Product Specifications',
                        'sort_order' => 1,
                        'is_filterable' => true,
                        'show_on_listing' => true,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                ],
            ],
            'Recombinant Monoclonal Antibodies (recmAbTM)' => [
                'Product Specifications' => [
                    [
                        'key' => 'concentration',
                        'name' => 'Concentration',
                        'type' => 'decimal',
                        'unit' => 'mg/mL',
                        'category' => 'Product Specifications',
                        'sort_order' => 1,
                        'is_filterable' => true,
                        'show_on_listing' => true,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                ],
            ],
            'Cell Lines' => [
                'Product Specifications' => [
                    [
                        'key' => 'species',
                        'name' => 'Species',
                        'type' => 'text',
                        'category' => 'Product Specifications',
                        'sort_order' => 1,
                        'is_filterable' => true,
                        'show_on_listing' => true,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                ],
            ],
            'Ligands and Inhibitors' => [
                'Product Specifications' => [
                    [
                        'key' => 'concentration',
                        'name' => 'Concentration',
                        'type' => 'decimal',
                        'unit' => 'mg/mL',
                        'category' => 'Product Specifications',
                        'sort_order' => 1,
                        'is_filterable' => true,
                        'show_on_listing' => true,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                ],
            ],
            'Immune-Check Point' => [
                'Product Specifications' => [
                    [
                        'key' => 'concentration',
                        'name' => 'Concentration',
                        'type' => 'decimal',
                        'unit' => 'mg/mL',
                        'category' => 'Product Specifications',
                        'sort_order' => 1,
                        'is_filterable' => true,
                        'show_on_listing' => true,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                ],
            ],
            'Research Kits and Reagents' => [
                'Product Specifications' => [
                    [
                        'key' => 'kit_type',
                        'name' => 'Kit Type',
                        'type' => 'text',
                        'category' => 'Product Specifications',
                        'sort_order' => 1,
                        'is_filterable' => true,
                        'show_on_listing' => true,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                ],
            ],
            'sdMAB ™ (Single Domain Monoclonal Antibody)' => [
                'Product Specifications' => [
                    [
                        'key' => 'concentration',
                        'name' => 'Concentration',
                        'type' => 'decimal',
                        'unit' => 'mg/mL',
                        'category' => 'Product Specifications',
                        'sort_order' => 1,
                        'is_filterable' => true,
                        'show_on_listing' => true,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                ],
            ],
            'Veterinary' => [
                'General' => [
                    [
                        'key' => 'application',
                        'name' => 'Application',
                        'type' => 'text',
                        'category' => 'General',
                        'sort_order' => 1,
                        'is_filterable' => true,
                        'show_on_listing' => true,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                ],
            ],
            'Veterinary Diagnostics' => [
                'General' => [
                    [
                        'key' => 'test_type',
                        'name' => 'Test Type',
                        'type' => 'text',
                        'category' => 'General',
                        'sort_order' => 1,
                        'is_filterable' => true,
                        'show_on_listing' => true,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                ],
            ],
            'Technology & AI Solutions' => [
                'General' => [
                    [
                        'key' => 'solution_type',
                        'name' => 'Solution Type',
                        'type' => 'text',
                        'category' => 'General',
                        'sort_order' => 1,
                        'is_filterable' => true,
                        'show_on_listing' => true,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                ],
            ],
            'Women\'s Health Rapid Tests' => [
                'Performance' => [
                    [
                        'key' => 'tests_per_kit',
                        'name' => 'Tests per Kit',
                        'type' => 'number',
                        'unit' => 'tests',
                        'category' => 'Performance',
                        'sort_order' => 1,
                        'is_filterable' => true,
                        'show_on_listing' => true,
                        'show_on_detail' => true,
                        'required' => true,
                    ],
                    [
                        'key' => 'detection_time',
                        'name' => 'Detection Time',
                        'type' => 'number',
                        'unit' => 'minutes',
                        'category' => 'Performance',
                        'sort_order' => 2,
                        'is_filterable' => true,
                        'show_on_listing' => true,
                        'show_on_detail' => true,
                        'required' => true,
                    ],
                    [
                        'key' => 'sensitivity',
                        'name' => 'Sensitivity',
                        'type' => 'decimal',
                        'unit' => '%',
                        'category' => 'Performance',
                        'sort_order' => 3,
                        'is_filterable' => false,
                        'show_on_listing' => false,
                        'show_on_detail' => true,
                        'required' => true,
                    ],
                    [
                        'key' => 'specificity',
                        'name' => 'Specificity',
                        'type' => 'decimal',
                        'unit' => '%',
                        'category' => 'Performance',
                        'sort_order' => 4,
                        'is_filterable' => false,
                        'show_on_listing' => false,
                        'show_on_detail' => true,
                        'required' => true,
                    ],
                ],
                'Physical' => [
                    [
                        'key' => 'sample_type',
                        'name' => 'Sample Type',
                        'type' => 'select',
                        'options' => ['Nasal Swab', 'Throat Swab', 'Saliva', 'Blood', 'Urine'],
                        'category' => 'Physical',
                        'sort_order' => 1,
                        'is_filterable' => true,
                        'show_on_listing' => true,
                        'show_on_detail' => true,
                        'required' => true,
                    ],
                    [
                        'key' => 'storage_temperature',
                        'name' => 'Storage Temperature',
                        'type' => 'text',
                        'unit' => '°C',
                        'category' => 'Physical',
                        'sort_order' => 2,
                        'is_filterable' => false,
                        'show_on_listing' => false,
                        'show_on_detail' => true,
                        'required' => true,
                    ],
                    [
                        'key' => 'shelf_life',
                        'name' => 'Shelf Life',
                        'type' => 'number',
                        'unit' => 'months',
                        'category' => 'Physical',
                        'sort_order' => 3,
                        'is_filterable' => true,
                        'show_on_listing' => false,
                        'show_on_detail' => true,
                        'required' => true,
                    ],
                ],
                'Regulatory' => [
                    [
                        'key' => 'ce_marking',
                        'name' => 'CE Marking',
                        'type' => 'text',
                        'category' => 'Regulatory',
                        'sort_order' => 1,
                        'is_filterable' => true,
                        'show_on_listing' => true,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                    [
                        'key' => 'fda_approval',
                        'name' => 'FDA Status',
                        'type' => 'text',
                        'category' => 'Regulatory',
                        'sort_order' => 2,
                        'is_filterable' => true,
                        'show_on_listing' => false,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                    [
                        'key' => 'iso_certification',
                        'name' => 'ISO Certification',
                        'type' => 'text',
                        'category' => 'Regulatory',
                        'sort_order' => 3,
                        'is_filterable' => false,
                        'show_on_listing' => false,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                ],
                'Technical' => [
                    [
                        'key' => 'test_method',
                        'name' => 'Test Method',
                        'type' => 'text',
                        'category' => 'Technical',
                        'sort_order' => 1,
                        'is_filterable' => true,
                        'show_on_listing' => true,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                    [
                        'key' => 'reading_method',
                        'name' => 'Reading Method',
                        'type' => 'text',
                        'category' => 'Technical',
                        'sort_order' => 2,
                        'is_filterable' => true,
                        'show_on_listing' => false,
                        'show_on_detail' => true,
                        'required' => false,
                    ],
                ],
            ],
        ];

        // Return templates for the specific category, or empty array if not found
        $result = $templates[$category->name] ?? [];
        \Log::info("Found " . count($result) . " template groups for category: {$category->name}");
        
        return $result;
    }

    /**
     * Get specifications grouped by category for a product
     *
     * @param int $productId
     * @return \Illuminate\Support\Collection
     */
    public function getSpecificationsByCategory(int $productId)
    {
        return ProductSpecification::where('product_id', $productId)
            ->orderBy('category', 'asc')
            ->orderBy('sort_order', 'asc')
            ->get()
            ->groupBy('category');
    }

    /**
     * Delete all specifications for a product
     *
     * @param int $productId
     * @return bool
     */
    public function deleteProductSpecifications(int $productId): bool
    {
        return ProductSpecification::where('product_id', $productId)->delete();
    }

    /**
     * Update a single specification
     *
     * @param int $specificationId
     * @param array $data
     * @return bool
     */
    public function updateSpecification(int $specificationId, array $data): bool
    {
        $specification = ProductSpecification::find($specificationId);
        
        if (!$specification) {
            return false;
        }

        return $specification->update($data);
    }

    /**
     * Create a single specification
     *
     * @param array $data
     * @return ProductSpecification
     */
    public function createSpecification(array $data): ProductSpecification
    {
        return ProductSpecification::create($data);
    }
} 