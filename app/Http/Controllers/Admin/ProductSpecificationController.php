<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductSpecification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductSpecificationController extends Controller
{
    public function index()
    {
        $products = Product::with(['specifications', 'category', 'brand'])
            ->paginate(20);
            
        return view('admin.product-specifications.index', compact('products'));
    }

    public function show(Product $product)
    {
        $product->load(['specifications' => function($query) {
            $query->orderBy('category', 'asc')->orderBy('sort_order', 'asc');
        }]);
        
        $specsByCategory = $product->specifications->groupBy('category');
        
        return view('admin.product-specifications.show', compact('product', 'specsByCategory'));
    }

    public function create(Product $product)
    {
        $categories = ProductSpecification::distinct('category')
            ->whereNotNull('category')
            ->pluck('category')
            ->toArray();
            
        $commonSpecs = $this->getCommonSpecifications();
        
        return view('admin.product-specifications.create', compact('product', 'categories', 'commonSpecs'));
    }

    public function store(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'specification_key' => 'required|string|max:255',
            'specification_value' => 'required|string',
            'unit' => 'nullable|string|max:50',
            'category' => 'nullable|string|max:100',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_filterable' => 'boolean',
            'is_searchable' => 'boolean',
            'show_on_listing' => 'boolean',
            'show_on_detail' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check for duplicate specification_key for this product
        $exists = ProductSpecification::where('product_id', $product->id)
            ->where('specification_key', $request->specification_key)
            ->exists();

        if ($exists) {
            return back()
                ->withErrors(['specification_key' => 'This specification already exists for this product.'])
                ->withInput();
        }

        ProductSpecification::create([
            'product_id' => $product->id,
            'specification_key' => $request->specification_key,
            'specification_value' => $request->specification_value,
            'unit' => $request->unit,
            'category' => $request->category ?: 'General',
            'display_name' => $request->display_name,
            'description' => $request->description,
            'sort_order' => $request->sort_order ?: 0,
            'is_filterable' => $request->boolean('is_filterable'),
            'is_searchable' => $request->boolean('is_searchable'),
            'show_on_listing' => $request->boolean('show_on_listing'),
            'show_on_detail' => $request->boolean('show_on_detail', true),
        ]);

        return redirect()
            ->route('admin.product-specifications.show', $product)
            ->with('success', 'Product specification added successfully.');
    }

    public function edit(Product $product, ProductSpecification $specification)
    {
        if ($specification->product_id !== $product->id) {
            abort(404);
        }
        
        $categories = ProductSpecification::distinct('category')
            ->whereNotNull('category')
            ->pluck('category')
            ->toArray();
            
        return view('admin.product-specifications.edit', compact('product', 'specification', 'categories'));
    }

    public function update(Request $request, Product $product, ProductSpecification $specification)
    {
        if ($specification->product_id !== $product->id) {
            abort(404);
        }

        $validator = Validator::make($request->all(), [
            'specification_key' => 'required|string|max:255',
            'specification_value' => 'required|string',
            'unit' => 'nullable|string|max:50',
            'category' => 'nullable|string|max:100',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_filterable' => 'boolean',
            'is_searchable' => 'boolean',
            'show_on_listing' => 'boolean',
            'show_on_detail' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check for duplicate specification_key for this product (excluding current)
        $exists = ProductSpecification::where('product_id', $product->id)
            ->where('specification_key', $request->specification_key)
            ->where('id', '!=', $specification->id)
            ->exists();

        if ($exists) {
            return back()
                ->withErrors(['specification_key' => 'This specification already exists for this product.'])
                ->withInput();
        }

        $specification->update([
            'specification_key' => $request->specification_key,
            'specification_value' => $request->specification_value,
            'unit' => $request->unit,
            'category' => $request->category ?: 'General',
            'display_name' => $request->display_name,
            'description' => $request->description,
            'sort_order' => $request->sort_order ?: 0,
            'is_filterable' => $request->boolean('is_filterable'),
            'is_searchable' => $request->boolean('is_searchable'),
            'show_on_listing' => $request->boolean('show_on_listing'),
            'show_on_detail' => $request->boolean('show_on_detail', true),
        ]);

        return redirect()
            ->route('admin.product-specifications.show', $product)
            ->with('success', 'Product specification updated successfully.');
    }

    public function destroy(Product $product, ProductSpecification $specification)
    {
        if ($specification->product_id !== $product->id) {
            abort(404);
        }

        $specification->delete();

        return redirect()
            ->route('admin.product-specifications.show', $product)
            ->with('success', 'Product specification deleted successfully.');
    }

    public function bulkCreate(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'template' => 'required|string|in:rapid_test,lab_equipment,medical_supply',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $specifications = $this->getTemplateSpecifications($request->template);
        
        foreach ($specifications as $spec) {
            // Skip if already exists
            $exists = ProductSpecification::where('product_id', $product->id)
                ->where('specification_key', $spec['specification_key'])
                ->exists();
                
            if (!$exists) {
                ProductSpecification::create(array_merge($spec, [
                    'product_id' => $product->id,
                ]));
            }
        }

        return redirect()
            ->route('admin.product-specifications.show', $product)
            ->with('success', 'Template specifications added successfully.');
    }

    private function getCommonSpecifications()
    {
        return [
            'Rapid Test Kit' => [
                'test_count' => 'Tests per Kit',
                'detection_time' => 'Detection Time',
                'sensitivity' => 'Sensitivity',
                'specificity' => 'Specificity',
                'sample_type' => 'Sample Type',
                'storage_temperature' => 'Storage Temperature',
                'shelf_life' => 'Shelf Life',
                'ce_marking' => 'CE Marking',
                'fda_approval' => 'FDA Status',
                'test_method' => 'Test Method',
            ],
            'Lab Equipment' => [
                'power_requirements' => 'Power Requirements',
                'dimensions' => 'Dimensions',
                'weight' => 'Weight',
                'operating_temperature' => 'Operating Temperature',
                'accuracy' => 'Accuracy',
                'measurement_range' => 'Measurement Range',
                'display_type' => 'Display Type',
                'connectivity' => 'Connectivity',
                'warranty' => 'Warranty Period',
            ],
            'Medical Supply' => [
                'material' => 'Material',
                'sterility' => 'Sterility',
                'single_use' => 'Single Use',
                'packaging' => 'Packaging',
                'expiry_period' => 'Expiry Period',
                'compatibility' => 'Compatibility',
                'size_options' => 'Available Sizes',
            ],
        ];
    }

    private function getTemplateSpecifications($template)
    {
        $templates = [
            'rapid_test' => [
                [
                    'specification_key' => 'test_count',
                    'specification_value' => '50',
                    'unit' => 'tests',
                    'category' => 'Performance',
                    'display_name' => 'Tests per Kit',
                    'description' => 'Number of individual tests included in the kit',
                    'sort_order' => 1,
                    'is_filterable' => true,
                    'is_searchable' => true,
                    'show_on_listing' => true,
                    'show_on_detail' => true,
                ],
                [
                    'specification_key' => 'detection_time',
                    'specification_value' => '15',
                    'unit' => 'minutes',
                    'category' => 'Performance',
                    'display_name' => 'Detection Time',
                    'description' => 'Time required to get test results',
                    'sort_order' => 2,
                    'is_filterable' => true,
                    'is_searchable' => true,
                    'show_on_listing' => true,
                    'show_on_detail' => true,
                ],
                [
                    'specification_key' => 'sample_type',
                    'specification_value' => 'Nasal Swab',
                    'unit' => null,
                    'category' => 'Physical',
                    'display_name' => 'Sample Type',
                    'description' => 'Type of biological sample required for testing',
                    'sort_order' => 1,
                    'is_filterable' => true,
                    'is_searchable' => true,
                    'show_on_listing' => true,
                    'show_on_detail' => true,
                ],
                [
                    'specification_key' => 'ce_marking',
                    'specification_value' => 'Yes',
                    'unit' => null,
                    'category' => 'Regulatory',
                    'display_name' => 'CE Marking',
                    'description' => 'European Conformity certification status',
                    'sort_order' => 1,
                    'is_filterable' => true,
                    'is_searchable' => true,
                    'show_on_listing' => true,
                    'show_on_detail' => true,
                ],
            ],
            // Add more templates as needed
        ];

        return $templates[$template] ?? [];
    }
} 