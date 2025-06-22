<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductSpecification;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductSpecificationController extends Controller
{
    /**
     * Get category-specific specification templates
     */
    private function getCategorySpecificationTemplates($categoryId)
    {
        $category = Category::find($categoryId);
        $categoryName = $category ? strtolower($category->name) : '';
        
        // Define specification templates based on category
        if (str_contains($categoryName, 'rapid test') || str_contains($categoryName, 'test kit')) {
            return $this->getRapidTestSpecifications();
        } elseif (str_contains($categoryName, 'foot test')) {
            return $this->getRapidFootTestSpecifications();
        } elseif (str_contains($categoryName, 'food test')) {
            return $this->getRapidFoodTestSpecifications();
        } elseif (str_contains($categoryName, 'shaker') || str_contains($categoryName, 'mixer')) {
            return $this->getShakerMixerSpecifications();
        } elseif (str_contains($categoryName, 'centrifuge')) {
            return $this->getCentrifugeSpecifications();
        } elseif (str_contains($categoryName, 'spectrophotometer')) {
            return $this->getSpectrophotometerSpecifications();
        } elseif (str_contains($categoryName, 'ph') || str_contains($categoryName, 'electrochemistry')) {
            return $this->getElectrochemistrySpecifications();
        } elseif (str_contains($categoryName, 'thermal') || str_contains($categoryName, 'pcr')) {
            return $this->getThermalCyclerSpecifications();
        } else {
            return $this->getGeneralSpecifications();
        }
    }

    private function getRapidTestSpecifications()
    {
        return [
            'Performance' => [
                ['key' => 'tests_per_kit', 'name' => 'Tests per Kit', 'unit' => 'tests', 'type' => 'number', 'required' => true],
                ['key' => 'detection_time', 'name' => 'Detection Time', 'unit' => 'minutes', 'type' => 'number', 'required' => true],
                ['key' => 'sensitivity', 'name' => 'Sensitivity', 'unit' => '%', 'type' => 'decimal', 'required' => true],
                ['key' => 'specificity', 'name' => 'Specificity', 'unit' => '%', 'type' => 'decimal', 'required' => true],
                ['key' => 'detection_limit', 'name' => 'Detection Limit', 'unit' => 'ng/mL', 'type' => 'text', 'required' => false],
            ],
            'Sample & Usage' => [
                ['key' => 'sample_type', 'name' => 'Sample Type', 'unit' => '', 'type' => 'select', 'options' => ['Urine', 'Blood', 'Serum', 'Plasma', 'Nasal Swab', 'Throat Swab', 'Saliva'], 'required' => true],
                ['key' => 'sample_volume', 'name' => 'Sample Volume', 'unit' => 'μL', 'type' => 'number', 'required' => false],
                ['key' => 'reading_window', 'name' => 'Reading Window', 'unit' => 'minutes', 'type' => 'text', 'required' => false],
                ['key' => 'storage_temperature', 'name' => 'Storage Temperature', 'unit' => '°C', 'type' => 'text', 'required' => true],
                ['key' => 'shelf_life', 'name' => 'Shelf Life', 'unit' => 'months', 'type' => 'number', 'required' => true],
            ],
            'Regulatory' => [
                ['key' => 'ce_marking', 'name' => 'CE Marking', 'unit' => '', 'type' => 'boolean', 'required' => false],
                ['key' => 'fda_approval', 'name' => 'FDA Approval', 'unit' => '', 'type' => 'select', 'options' => ['Yes', 'No', 'Pending'], 'required' => false],
                ['key' => 'iso_certification', 'name' => 'ISO Certification', 'unit' => '', 'type' => 'text', 'required' => false],
                ['key' => 'clinical_studies', 'name' => 'Clinical Studies', 'unit' => '', 'type' => 'textarea', 'required' => false],
            ]
        ];
    }

    private function getRapidFootTestSpecifications()
    {
        return [
            'Performance' => [
                ['key' => 'tests_per_kit', 'name' => 'Tests per Kit', 'unit' => 'tests', 'type' => 'number', 'required' => true],
                ['key' => 'detection_time', 'name' => 'Detection Time', 'unit' => 'minutes', 'type' => 'number', 'required' => true],
                ['key' => 'sensitivity', 'name' => 'Sensitivity', 'unit' => '%', 'type' => 'decimal', 'required' => true],
                ['key' => 'specificity', 'name' => 'Specificity', 'unit' => '%', 'type' => 'decimal', 'required' => true],
                ['key' => 'target_pathogen', 'name' => 'Target Pathogen', 'unit' => '', 'type' => 'select', 'options' => ['ALP (Alkaline Phosphatase)', 'ATP (Adenosine Triphosphate)', 'Protein', 'Bacteria', 'Fungi', 'Multi-target'], 'required' => true],
            ],
            'Sample & Usage' => [
                ['key' => 'sample_type', 'name' => 'Sample Type', 'unit' => '', 'type' => 'select', 'options' => ['Swab', 'Direct Contact', 'Surface Sample', 'Liquid Sample'], 'required' => true],
                ['key' => 'sample_area', 'name' => 'Sample Area', 'unit' => 'cm²', 'type' => 'text', 'required' => false],
                ['key' => 'application_surface', 'name' => 'Application Surface', 'unit' => '', 'type' => 'select', 'options' => ['Food Contact Surfaces', 'Equipment', 'Footwear', 'Floors', 'Hands/Gloves', 'All Surfaces'], 'required' => false],
                ['key' => 'storage_temperature', 'name' => 'Storage Temperature', 'unit' => '°C', 'type' => 'text', 'required' => true],
                ['key' => 'shelf_life', 'name' => 'Shelf Life', 'unit' => 'months', 'type' => 'number', 'required' => true],
            ],
            'Technical' => [
                ['key' => 'test_method', 'name' => 'Test Method', 'unit' => '', 'type' => 'select', 'options' => ['Enzymatic', 'Immunochromatographic', 'Colorimetric', 'Fluorescent', 'Luminescent'], 'required' => false],
                ['key' => 'reading_method', 'name' => 'Reading Method', 'unit' => '', 'type' => 'select', 'options' => ['Visual', 'Digital Reader', 'Fluorometer', 'Luminometer', 'Colorimeter'], 'required' => false],
                ['key' => 'detection_limit', 'name' => 'Detection Limit', 'unit' => 'CFU/ml', 'type' => 'text', 'required' => false],
                ['key' => 'validation_studies', 'name' => 'Validation Studies', 'unit' => '', 'type' => 'textarea', 'required' => false],
            ],
            'Regulatory' => [
                ['key' => 'ce_marking', 'name' => 'CE Marking', 'unit' => '', 'type' => 'select', 'options' => ['Yes', 'No', 'Pending'], 'required' => false],
                ['key' => 'fda_approval', 'name' => 'FDA Status', 'unit' => '', 'type' => 'select', 'options' => ['Approved', 'EUL', 'Pending', 'Not Required'], 'required' => false],
                ['key' => 'iso_certification', 'name' => 'ISO Certification', 'unit' => '', 'type' => 'text', 'required' => false],
                ['key' => 'haccp_compliance', 'name' => 'HACCP Compliance', 'unit' => '', 'type' => 'select', 'options' => ['Yes', 'No', 'Partial'], 'required' => false],
            ]
        ];
    }

    private function getRapidFoodTestSpecifications()
    {
        return [
            'Performance' => [
                ['key' => 'tests_per_kit', 'name' => 'Tests per Kit', 'unit' => 'tests', 'type' => 'number', 'required' => false],
                ['key' => 'detection_time', 'name' => 'Detection Time', 'unit' => 'minutes', 'type' => 'number', 'required' => false],
                ['key' => 'sensitivity', 'name' => 'Sensitivity', 'unit' => '%', 'type' => 'decimal', 'required' => false],
                ['key' => 'specificity', 'name' => 'Specificity', 'unit' => '%', 'type' => 'decimal', 'required' => false],
                ['key' => 'target_pathogen', 'name' => 'Target Pathogen/Analyte', 'unit' => '', 'type' => 'select', 'options' => ['ALP (Alkaline Phosphatase)', 'ATP (Adenosine Triphosphate)', 'Total Protein', 'E. coli', 'Salmonella', 'Listeria', 'Coliform', 'Multi-target'], 'required' => false],
            ],
            'Sample & Usage' => [
                ['key' => 'sample_type', 'name' => 'Sample Type', 'unit' => '', 'type' => 'select', 'options' => ['Swab', 'Direct Contact', 'Surface Sample', 'Liquid Sample', 'Food Sample'], 'required' => false],
                ['key' => 'sample_area', 'name' => 'Sample Area', 'unit' => 'cm²', 'type' => 'text', 'required' => false],
                ['key' => 'application_surface', 'name' => 'Application Surface', 'unit' => '', 'type' => 'select', 'options' => ['Food Contact Surfaces', 'Processing Equipment', 'Utensils', 'Cutting Boards', 'Hands/Gloves', 'All Food Surfaces'], 'required' => false],
                ['key' => 'storage_temperature', 'name' => 'Storage Temperature', 'unit' => '°C', 'type' => 'text', 'required' => false],
                ['key' => 'shelf_life', 'name' => 'Shelf Life', 'unit' => 'months', 'type' => 'number', 'required' => false],
            ],
            'Technical' => [
                ['key' => 'test_method', 'name' => 'Test Method', 'unit' => '', 'type' => 'select', 'options' => ['Enzymatic', 'Immunochromatographic', 'Colorimetric', 'Fluorescent', 'Luminescent', 'ATP Bioluminescence'], 'required' => false],
                ['key' => 'reading_method', 'name' => 'Reading Method', 'unit' => '', 'type' => 'select', 'options' => ['Visual', 'Digital Reader', 'Fluorometer', 'Luminometer', 'Colorimeter', 'ATP Meter'], 'required' => false],
                ['key' => 'detection_limit', 'name' => 'Detection Limit', 'unit' => 'CFU/ml or RLU', 'type' => 'text', 'required' => false],
                ['key' => 'validation_studies', 'name' => 'Validation Studies', 'unit' => '', 'type' => 'textarea', 'required' => false],
            ],
            'Regulatory' => [
                ['key' => 'ce_marking', 'name' => 'CE Marking', 'unit' => '', 'type' => 'select', 'options' => ['Yes', 'No', 'Pending'], 'required' => false],
                ['key' => 'fda_approval', 'name' => 'FDA Status', 'unit' => '', 'type' => 'select', 'options' => ['Approved', 'GRAS', 'Pending', 'Not Required'], 'required' => false],
                ['key' => 'iso_certification', 'name' => 'ISO Certification', 'unit' => '', 'type' => 'text', 'required' => false],
                ['key' => 'haccp_compliance', 'name' => 'HACCP Compliance', 'unit' => '', 'type' => 'select', 'options' => ['Yes', 'No', 'Partial'], 'required' => false],
                ['key' => 'food_safety_standards', 'name' => 'Food Safety Standards', 'unit' => '', 'type' => 'select', 'options' => ['USDA FSIS', 'FDA', 'BRC', 'SQF', 'IFS', 'FSSC 22000'], 'required' => false],
            ]
        ];
    }

    private function getShakerMixerSpecifications()
    {
        return [
            'Performance' => [
                ['key' => 'speed_range', 'name' => 'Speed Range', 'unit' => 'rpm', 'type' => 'text', 'required' => true],
                ['key' => 'amplitude', 'name' => 'Amplitude', 'unit' => 'mm', 'type' => 'number', 'required' => true],
                ['key' => 'load_capacity', 'name' => 'Load Capacity', 'unit' => 'kg', 'type' => 'decimal', 'required' => true],
                ['key' => 'motion_type', 'name' => 'Motion Type', 'unit' => '', 'type' => 'select', 'options' => ['Orbital', 'Linear', '3D', 'Rocking'], 'required' => true],
                ['key' => 'timer_range', 'name' => 'Timer Range', 'unit' => '', 'type' => 'text', 'required' => false],
            ],
            'Technical' => [
                ['key' => 'display_type', 'name' => 'Display Type', 'unit' => '', 'type' => 'select', 'options' => ['LCD', 'LED', 'Touch Screen'], 'required' => false],
                ['key' => 'motor_type', 'name' => 'Motor Type', 'unit' => '', 'type' => 'text', 'required' => false],
                ['key' => 'power_consumption', 'name' => 'Power Consumption', 'unit' => 'W', 'type' => 'number', 'required' => false],
                ['key' => 'noise_level', 'name' => 'Noise Level', 'unit' => 'dB', 'type' => 'number', 'required' => false],
                ['key' => 'temperature_range', 'name' => 'Operating Temperature', 'unit' => '°C', 'type' => 'text', 'required' => false],
            ],
            'Physical' => [
                ['key' => 'dimensions', 'name' => 'Dimensions (W×D×H)', 'unit' => 'mm', 'type' => 'text', 'required' => true],
                ['key' => 'weight', 'name' => 'Weight', 'unit' => 'kg', 'type' => 'decimal', 'required' => true],
                ['key' => 'platform_size', 'name' => 'Platform Size', 'unit' => 'mm', 'type' => 'text', 'required' => false],
            ]
        ];
    }

    private function getCentrifugeSpecifications()
    {
        return [
            'Performance' => [
                ['key' => 'max_speed', 'name' => 'Maximum Speed', 'unit' => 'rpm', 'type' => 'number', 'required' => true],
                ['key' => 'max_rcf', 'name' => 'Maximum RCF', 'unit' => '×g', 'type' => 'number', 'required' => true],
                ['key' => 'rotor_capacity', 'name' => 'Rotor Capacity', 'unit' => 'tubes', 'type' => 'text', 'required' => true],
                ['key' => 'tube_sizes', 'name' => 'Tube Sizes', 'unit' => 'mL', 'type' => 'text', 'required' => true],
                ['key' => 'acceleration_time', 'name' => 'Acceleration Time', 'unit' => 'seconds', 'type' => 'number', 'required' => false],
                ['key' => 'deceleration_time', 'name' => 'Deceleration Time', 'unit' => 'seconds', 'type' => 'number', 'required' => false],
            ],
            'Technical' => [
                ['key' => 'temperature_control', 'name' => 'Temperature Control', 'unit' => '', 'type' => 'select', 'options' => ['Ambient', 'Refrigerated'], 'required' => true],
                ['key' => 'temperature_range', 'name' => 'Temperature Range', 'unit' => '°C', 'type' => 'text', 'required' => false],
                ['key' => 'noise_level', 'name' => 'Noise Level', 'unit' => 'dB', 'type' => 'number', 'required' => false],
                ['key' => 'rotor_type', 'name' => 'Rotor Type', 'unit' => '', 'type' => 'select', 'options' => ['Fixed Angle', 'Swing-out', 'Both'], 'required' => false],
            ]
        ];
    }

    private function getSpectrophotometerSpecifications()
    {
        return [
            'Optical Performance' => [
                ['key' => 'wavelength_range', 'name' => 'Wavelength Range', 'unit' => 'nm', 'type' => 'text', 'required' => true],
                ['key' => 'spectral_bandwidth', 'name' => 'Spectral Bandwidth', 'unit' => 'nm', 'type' => 'decimal', 'required' => true],
                ['key' => 'photometric_range', 'name' => 'Photometric Range', 'unit' => 'A', 'type' => 'text', 'required' => true],
                ['key' => 'wavelength_accuracy', 'name' => 'Wavelength Accuracy', 'unit' => 'nm', 'type' => 'text', 'required' => false],
                ['key' => 'photometric_accuracy', 'name' => 'Photometric Accuracy', 'unit' => 'A', 'type' => 'text', 'required' => false],
            ],
            'System' => [
                ['key' => 'optical_system', 'name' => 'Optical System', 'unit' => '', 'type' => 'select', 'options' => ['Single Beam', 'Double Beam'], 'required' => true],
                ['key' => 'light_source', 'name' => 'Light Source', 'unit' => '', 'type' => 'text', 'required' => true],
                ['key' => 'detector', 'name' => 'Detector', 'unit' => '', 'type' => 'text', 'required' => false],
                ['key' => 'sample_compartment', 'name' => 'Sample Compartment', 'unit' => '', 'type' => 'text', 'required' => false],
            ]
        ];
    }

    private function getElectrochemistrySpecifications()
    {
        return [
            'Measurement' => [
                ['key' => 'ph_range', 'name' => 'pH Range', 'unit' => 'pH', 'type' => 'text', 'required' => true],
                ['key' => 'ph_accuracy', 'name' => 'pH Accuracy', 'unit' => 'pH', 'type' => 'text', 'required' => true],
                ['key' => 'temperature_range', 'name' => 'Temperature Range', 'unit' => '°C', 'type' => 'text', 'required' => true],
                ['key' => 'calibration_points', 'name' => 'Calibration Points', 'unit' => '', 'type' => 'text', 'required' => false],
            ],
            'Features' => [
                ['key' => 'display_type', 'name' => 'Display Type', 'unit' => '', 'type' => 'text', 'required' => false],
                ['key' => 'data_storage', 'name' => 'Data Storage', 'unit' => 'readings', 'type' => 'number', 'required' => false],
                ['key' => 'ip_rating', 'name' => 'IP Rating', 'unit' => '', 'type' => 'text', 'required' => false],
                ['key' => 'connectivity', 'name' => 'Connectivity', 'unit' => '', 'type' => 'text', 'required' => false],
            ]
        ];
    }

    private function getThermalCyclerSpecifications()
    {
        return [
            'Performance' => [
                ['key' => 'sample_capacity', 'name' => 'Sample Capacity', 'unit' => '', 'type' => 'text', 'required' => true],
                ['key' => 'temperature_range', 'name' => 'Temperature Range', 'unit' => '°C', 'type' => 'text', 'required' => true],
                ['key' => 'heating_rate', 'name' => 'Heating Rate', 'unit' => '°C/sec', 'type' => 'number', 'required' => false],
                ['key' => 'cooling_rate', 'name' => 'Cooling Rate', 'unit' => '°C/sec', 'type' => 'number', 'required' => false],
                ['key' => 'temperature_accuracy', 'name' => 'Temperature Accuracy', 'unit' => '°C', 'type' => 'text', 'required' => false],
                ['key' => 'temperature_uniformity', 'name' => 'Temperature Uniformity', 'unit' => '°C', 'type' => 'text', 'required' => false],
            ],
            'Features' => [
                ['key' => 'gradient_capability', 'name' => 'Gradient Capability', 'unit' => '', 'type' => 'boolean', 'required' => false],
                ['key' => 'gradient_range', 'name' => 'Gradient Range', 'unit' => '°C', 'type' => 'text', 'required' => false],
                ['key' => 'program_capacity', 'name' => 'Program Capacity', 'unit' => 'programs', 'type' => 'number', 'required' => false],
                ['key' => 'connectivity', 'name' => 'Connectivity', 'unit' => '', 'type' => 'text', 'required' => false],
            ]
        ];
    }

    private function getGeneralSpecifications()
    {
        return [
            'Basic' => [
                ['key' => 'model_number', 'name' => 'Model Number', 'unit' => '', 'type' => 'text', 'required' => true],
                ['key' => 'dimensions', 'name' => 'Dimensions', 'unit' => 'mm', 'type' => 'text', 'required' => true],
                ['key' => 'weight', 'name' => 'Weight', 'unit' => 'kg', 'type' => 'decimal', 'required' => true],
                ['key' => 'power_requirements', 'name' => 'Power Requirements', 'unit' => '', 'type' => 'text', 'required' => false],
            ],
            'Performance' => [
                ['key' => 'operating_temperature', 'name' => 'Operating Temperature', 'unit' => '°C', 'type' => 'text', 'required' => false],
                ['key' => 'humidity_range', 'name' => 'Humidity Range', 'unit' => '%RH', 'type' => 'text', 'required' => false],
                ['key' => 'certification', 'name' => 'Certification', 'unit' => '', 'type' => 'text', 'required' => false],
            ]
        ];
    }

    /**
     * Show product specifications management for suppliers
     */
    public function index()
    {
        $user = Auth::user();
        
        if (!$user->isSupplier()) {
            abort(403, 'Access denied. Only suppliers can manage product specifications.');
        }

        // Get supplier's products with their specifications
        $products = $user->supplierProducts()
            ->with(['specifications', 'category', 'brand'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Get assigned categories for filtering
        $assignedCategories = $user->activeAssignedCategories;

        return view('supplier.product-specifications.index', compact('products', 'assignedCategories'));
    }

    /**
     * Show specifications for a specific product
     */
    public function show(Product $product)
    {
        $user = Auth::user();
        
        if (!$user->isSupplier() || $product->supplier_id !== $user->id) {
            abort(403, 'Access denied.');
        }

        $product->load(['specifications' => function($query) {
            $query->orderBy('category', 'asc')->orderBy('sort_order', 'asc');
        }, 'category', 'brand']);
        
        $specsByCategory = $product->specifications->groupBy('category');
        
        // Get category-specific templates
        $templates = $this->getCategorySpecificationTemplates($product->category_id);

        return view('supplier.product-specifications.show', compact('product', 'specsByCategory', 'templates'));
    }

    /**
     * Show form to add/edit specifications for a product
     */
    public function edit(Product $product)
    {
        $user = Auth::user();
        
        if (!$user->isSupplier() || $product->supplier_id !== $user->id) {
            abort(403, 'Access denied.');
        }

        // Check if supplier is assigned to this product's category
        if (!$user->isAssignedToCategory($product->category_id)) {
            abort(403, 'You are not assigned to manage products in this category.');
        }

        $product->load(['specifications', 'category']);
        
        // Get existing specifications grouped by category
        $existingSpecs = $product->specifications->keyBy('specification_key');
        
        // Get category-specific templates
        $templates = $this->getCategorySpecificationTemplates($product->category_id);

        return view('supplier.product-specifications.edit', compact('product', 'existingSpecs', 'templates'));
    }

    /**
     * Update product specifications
     */
    public function update(Request $request, Product $product)
    {
        $user = Auth::user();
        
        if (!$user->isSupplier() || $product->supplier_id !== $user->id) {
            abort(403, 'Access denied.');
        }

        if (!$user->isAssignedToCategory($product->category_id)) {
            abort(403, 'You are not assigned to manage products in this category.');
        }

        $specifications = $request->input('specifications', []);
        
        DB::beginTransaction();
        try {
            // Get category templates for validation
            $templates = $this->getCategorySpecificationTemplates($product->category_id);
            $sortOrder = 1;

            foreach ($templates as $categoryName => $categorySpecs) {
                foreach ($categorySpecs as $spec) {
                    $key = $spec['key'];
                    $value = $specifications[$key] ?? null;
                    
                    // Skip if no value provided and not required
                    if (empty($value) && !$spec['required']) {
                        continue;
                    }

                    // Validate required fields
                    if ($spec['required'] && empty($value)) {
                        throw new \Exception("Field '{$spec['name']}' is required.");
                    }

                    // Update or create specification
                    ProductSpecification::updateOrCreate(
                        [
                            'product_id' => $product->id,
                            'specification_key' => $key
                        ],
                        [
                            'specification_value' => $value,
                            'unit' => $spec['unit'],
                            'category' => $categoryName,
                            'display_name' => $spec['name'],
                            'sort_order' => $sortOrder++,
                            'is_filterable' => in_array($spec['type'], ['select', 'boolean', 'number']),
                            'is_searchable' => in_array($spec['type'], ['text', 'textarea']),
                            'show_on_listing' => in_array($key, ['tests_per_kit', 'detection_time', 'speed_range', 'max_speed', 'sample_type']),
                            'show_on_detail' => true,
                        ]
                    );
                }
            }

            DB::commit();

            return redirect()->route('supplier.product-specifications.show', $product)
                ->with('success', 'Product specifications updated successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Error updating specifications: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Delete a specification
     */
    public function destroySpecification(Product $product, ProductSpecification $specification)
    {
        $user = Auth::user();
        
        if (!$user->isSupplier() || $product->supplier_id !== $user->id) {
            abort(403, 'Access denied.');
        }

        if ($specification->product_id !== $product->id) {
            abort(404, 'Specification not found for this product.');
        }

        $specification->delete();

        return redirect()->back()
            ->with('success', 'Specification deleted successfully.');
    }

    /**
     * Bulk import specifications from CSV
     */
    public function bulkImport(Request $request, Product $product)
    {
        $user = Auth::user();
        
        if (!$user->isSupplier() || $product->supplier_id !== $user->id) {
            abort(403, 'Access denied.');
        }

        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048'
        ]);

        // Process CSV file
        $file = $request->file('csv_file');
        $csvData = array_map('str_getcsv', file($file->getRealPath()));
        $header = array_shift($csvData);

        DB::beginTransaction();
        try {
            $sortOrder = 1;
            foreach ($csvData as $row) {
                $data = array_combine($header, $row);
                
                if (empty($data['specification_key']) || empty($data['specification_value'])) {
                    continue;
                }

                ProductSpecification::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'specification_key' => $data['specification_key']
                    ],
                    [
                        'specification_value' => $data['specification_value'],
                        'unit' => $data['unit'] ?? '',
                        'category' => $data['category'] ?? 'General',
                        'display_name' => $data['display_name'] ?? $data['specification_key'],
                        'sort_order' => $sortOrder++,
                        'show_on_detail' => true,
                    ]
                );
            }

            DB::commit();

            return redirect()->route('supplier.product-specifications.show', $product)
                ->with('success', 'Specifications imported successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Error importing specifications: ' . $e->getMessage());
        }
    }
} 