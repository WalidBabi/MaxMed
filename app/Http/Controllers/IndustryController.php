<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;

class IndustryController extends Controller
{
    /**
     * Healthcare & Medical Facilities
     */
    public function clinics()
    {
        // Get categories related to clinics & medical centers
        $categories = Category::where('name', 'like', '%clinic%')
            ->orWhere('name', 'like', '%medical center%')
            ->take(6)
            ->get();
            
        // Get featured products for clinics
        $products = Product::whereHas('category', function($query) {
                $query->where('name', 'like', '%clinic%')
                    ->orWhere('name', 'like', '%medical center%');
            })
            ->take(8)
            ->get();
            
        return view('industry.healthcare.clinics', compact('categories', 'products'));
    }
    
    public function hospitals()
    {
        // Get categories related to hospitals
        $categories = Category::where('name', 'like', '%hospital%')
            ->take(6)
            ->get();
            
        // Get featured products for hospitals
        $products = Product::whereHas('category', function($query) {
                $query->where('name', 'like', '%hospital%');
            })
            ->take(8)
            ->get();
            
        return view('industry.healthcare.hospitals', compact('categories', 'products'));
    }
    
    public function veterinary()
    {
        // Get categories related to veterinary clinics
        $categories = Category::where('name', 'like', '%veterinary%')
            ->orWhere('name', 'like', '%animal%')
            ->take(6)
            ->get();
            
        // Get featured products for veterinary clinics
        $products = Product::whereHas('category', function($query) {
                $query->where('name', 'like', '%veterinary%')
                    ->orWhere('name', 'like', '%animal%');
            })
            ->take(8)
            ->get();
            
        return view('industry.healthcare.veterinary', compact('categories', 'products'));
    }
    
    public function medicalLaboratories()
    {
        // Get categories related to medical laboratories
        $categories = Category::where('name', 'like', '%medical lab%')
            ->take(6)
            ->get();
            
        // Get featured products for medical laboratories
        $products = Product::whereHas('category', function($query) {
                $query->where('name', 'like', '%medical lab%');
            })
            ->take(8)
            ->get();
            
        return view('industry.healthcare.medical-laboratories', compact('categories', 'products'));
    }
    
    /**
     * Scientific & Research Institutions
     */
    public function researchLabs()
    {
        // Get categories related to research laboratories
        $categories = Category::where('name', 'like', '%research lab%')
            ->take(6)
            ->get();
            
        // Get featured products for research laboratories
        $products = Product::whereHas('category', function($query) {
                $query->where('name', 'like', '%research lab%');
            })
            ->take(8)
            ->get();
            
        return view('industry.research.research-labs', compact('categories', 'products'));
    }
    
    public function academia()
    {
        // Get categories related to universities & academia
        $categories = Category::where('name', 'like', '%universit%')
            ->orWhere('name', 'like', '%academia%')
            ->orWhere('name', 'like', '%education%')
            ->take(6)
            ->get();
            
        // Get featured products for universities & academia
        $products = Product::whereHas('category', function($query) {
                $query->where('name', 'like', '%universit%')
                    ->orWhere('name', 'like', '%academia%')
                    ->orWhere('name', 'like', '%education%');
            })
            ->take(8)
            ->get();
            
        return view('industry.research.academia', compact('categories', 'products'));
    }
    
    public function biotech()
    {
        // Get categories related to biotech & pharmaceutical industries
        $categories = Category::where('name', 'like', '%biotech%')
            ->orWhere('name', 'like', '%pharma%')
            ->take(6)
            ->get();
            
        // Get featured products for biotech & pharmaceutical industries
        $products = Product::whereHas('category', function($query) {
                $query->where('name', 'like', '%biotech%')
                    ->orWhere('name', 'like', '%pharma%');
            })
            ->take(8)
            ->get();
            
        return view('industry.research.biotech', compact('categories', 'products'));
    }
    
    public function forensic()
    {
        // Get categories related to forensic laboratories
        $categories = Category::where('name', 'like', '%forensic%')
            ->take(6)
            ->get();
            
        // Get featured products for forensic laboratories
        $products = Product::whereHas('category', function($query) {
                $query->where('name', 'like', '%forensic%');
            })
            ->take(8)
            ->get();
            
        return view('industry.research.forensic', compact('categories', 'products'));
    }
    
    /**
     * Specialized Testing & Diagnostics
     */
    public function environmentLabs()
    {
        // Get categories related to environment laboratories
        $categories = Category::where('name', 'like', '%environment%')
            ->take(6)
            ->get();
            
        // Get featured products for environment laboratories
        $products = Product::whereHas('category', function($query) {
                $query->where('name', 'like', '%environment%');
            })
            ->take(8)
            ->get();
            
        return view('industry.testing.environment', compact('categories', 'products'));
    }
    
    public function foodLabs()
    {
        // Get categories related to food laboratories
        $categories = Category::where('name', 'like', '%food%')
            ->take(6)
            ->get();
            
        // Get featured products for food laboratories
        $products = Product::whereHas('category', function($query) {
                $query->where('name', 'like', '%food%');
            })
            ->take(8)
            ->get();
            
        return view('industry.testing.food', compact('categories', 'products'));
    }
    
    public function materialLabs()
    {
        // Get categories related to material testing laboratories
        $categories = Category::where('name', 'like', '%material%')
            ->take(6)
            ->get();
            
        // Get featured products for material testing laboratories
        $products = Product::whereHas('category', function($query) {
                $query->where('name', 'like', '%material%');
            })
            ->take(8)
            ->get();
            
        return view('industry.testing.material', compact('categories', 'products'));
    }
    
    public function cosmeticLabs()
    {
        // Get categories related to cosmetic & dermatology labs
        $categories = Category::where('name', 'like', '%cosmetic%')
            ->orWhere('name', 'like', '%dermatology%')
            ->take(6)
            ->get();
            
        // Get featured products for cosmetic & dermatology labs
        $products = Product::whereHas('category', function($query) {
                $query->where('name', 'like', '%cosmetic%')
                    ->orWhere('name', 'like', '%dermatology%');
            })
            ->take(8)
            ->get();
            
        return view('industry.testing.cosmetic', compact('categories', 'products'));
    }
    
    /**
     * Government & Regulatory Bodies
     */
    public function publicHealth()
    {
        // Get categories related to public health institutions
        $categories = Category::where('name', 'like', '%public health%')
            ->take(6)
            ->get();
            
        // Get featured products for public health institutions
        $products = Product::whereHas('category', function($query) {
                $query->where('name', 'like', '%public health%');
            })
            ->take(8)
            ->get();
            
        return view('industry.government.public-health', compact('categories', 'products'));
    }
    
    public function military()
    {
        // Get categories related to military & defense research centers
        $categories = Category::where('name', 'like', '%military%')
            ->orWhere('name', 'like', '%defense%')
            ->take(6)
            ->get();
            
        // Get featured products for military & defense research centers
        $products = Product::whereHas('category', function($query) {
                $query->where('name', 'like', '%military%')
                    ->orWhere('name', 'like', '%defense%');
            })
            ->take(8)
            ->get();
            
        return view('industry.government.military', compact('categories', 'products'));
    }
    
    public function regulatory()
    {
        // Get categories related to health ministries & regulatory agencies
        $categories = Category::where('name', 'like', '%regulatory%')
            ->orWhere('name', 'like', '%health ministr%')
            ->take(6)
            ->get();
            
        // Get featured products for health ministries & regulatory agencies
        $products = Product::whereHas('category', function($query) {
                $query->where('name', 'like', '%regulatory%')
                    ->orWhere('name', 'like', '%health ministr%');
            })
            ->take(8)
            ->get();
            
        return view('industry.government.regulatory', compact('categories', 'products'));
    }
    
    /**
     * Emerging & AI-driven Healthcare
     */
    public function telemedicine()
    {
        // Get categories related to telemedicine & remote diagnostics
        $categories = Category::where('name', 'like', '%telemedicine%')
            ->orWhere('name', 'like', '%remote diagnos%')
            ->take(6)
            ->get();
            
        // Get featured products for telemedicine & remote diagnostics
        $products = Product::whereHas('category', function($query) {
                $query->where('name', 'like', '%telemedicine%')
                    ->orWhere('name', 'like', '%remote diagnos%');
            })
            ->take(8)
            ->get();
            
        return view('industry.technology.telemedicine', compact('categories', 'products'));
    }
    
    public function aiMedical()
    {
        // Get categories related to AI-powered medical technology firms
        $categories = Category::where('name', 'like', '%AI%')
            ->orWhere('name', 'like', '%artificial intelligence%')
            ->take(6)
            ->get();
            
        // Get featured products for AI-powered medical technology firms
        $products = Product::whereHas('category', function($query) {
                $query->where('name', 'like', '%AI%')
                    ->orWhere('name', 'like', '%artificial intelligence%');
            })
            ->take(8)
            ->get();
            
        return view('industry.technology.ai-medical', compact('categories', 'products'));
    }
} 