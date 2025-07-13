<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Carbon\Carbon;

class GenerateEnhancedKeywordSitemaps extends Command
{
    protected $signature = 'sitemap:enhanced-keywords 
                            {--focus=imaging : Focus on specific category (imaging, lab, medical, all)}
                            {--max-keywords=50000 : Maximum keywords per sitemap}
                            {--generate-routes : Generate route definitions}';
    
    protected $description = 'Generate comprehensive enhanced keyword sitemaps with focus on imaging systems and other categories';

    private $baseUrl = 'https://maxmedme.com';
    private $keywords = [];
    private $focus = 'imaging';

    public function handle()
    {
        $this->focus = $this->option('focus');
        $maxKeywords = $this->option('max-keywords');
        
        $this->info('🔍 Generating ENHANCED keyword sitemaps for ' . strtoupper($this->focus) . ' systems...');
        
        // Generate comprehensive keywords
        $this->generateImagingSystemKeywords();
        $this->generateLaboratoryEquipmentKeywords();
        $this->generateMedicalEquipmentKeywords();
        $this->generateRegionalKeywords();
        $this->generateBrandKeywords();
        $this->generateTechnicalKeywords();
        $this->generateApplicationKeywords();
        $this->generateIndustryKeywords();
        
        // Generate sitemaps
        $this->generateEnhancedKeywordSitemaps($maxKeywords);
        
        if ($this->option('generate-routes')) {
            $this->generateRouteDefinitions();
        }
        
        $this->displayEnhancedStats();
        
        return 0;
    }

    private function generateImagingSystemKeywords()
    {
        $this->info('📸 Generating imaging system keywords...');
        
        $imagingKeywords = [
            // High-speed cameras
            'high-speed-camera', 'ultra-high-speed-camera', 'phantom-camera', 'neo-camera',
            'gloria-camera', 'fastcam-camera', 'photron-camera', 'vision-research-camera',
            'high-speed-video-camera', 'ultra-fast-camera', 'motion-analysis-camera',
            
            // Frame rates
            '1000-fps-camera', '2000-fps-camera', '5000-fps-camera', '10000-fps-camera',
            '20000-fps-camera', '50000-fps-camera', '100000-fps-camera', 'million-fps-camera',
            'ultra-high-frame-rate', 'high-speed-imaging', 'slow-motion-camera',
            
            // Imaging applications
            'ballistic-imaging', 'impact-analysis', 'crash-test-imaging', 'sports-analysis',
            'biomechanics-imaging', 'fluid-dynamics-imaging', 'explosion-imaging',
            'material-testing-imaging', 'vibration-analysis', 'stress-analysis-imaging',
            
            // Camera specifications
            '4k-high-speed-camera', '8k-high-speed-camera', 'full-hd-high-speed',
            'ultra-hd-high-speed', 'mega-pixel-high-speed', 'ultra-sensitive-camera',
            'low-light-high-speed', 'infrared-high-speed', 'thermal-imaging-camera',
            
            // Advanced Motion specific
            'advanced-motion-camera', 'advanced-motion-imaging', 'advanced-motion-systems',
            'advanced-motion-scientific', 'advanced-motion-analysis', 'advanced-motion-research',
            'advanced-motion-laboratory', 'advanced-motion-equipment', 'advanced-motion-technology',
            
            // Scientific imaging
            'scientific-imaging-systems', 'research-imaging-camera', 'laboratory-imaging',
            'microscopy-imaging', 'spectroscopy-imaging', 'fluorescence-imaging',
            'confocal-imaging', 'multiphoton-imaging', 'super-resolution-imaging',
            
            // Industrial applications
            'industrial-high-speed-imaging', 'manufacturing-imaging', 'quality-control-imaging',
            'defect-detection-imaging', 'process-monitoring-imaging', 'automation-imaging',
            'robotics-imaging', 'assembly-line-imaging', 'production-line-imaging',
            
            // Research applications
            'research-high-speed-camera', 'academic-imaging', 'university-imaging',
            'institute-imaging', 'laboratory-research-imaging', 'scientific-research-camera',
            'experimental-imaging', 'prototype-imaging', 'development-imaging',
            
            // Medical imaging
            'medical-high-speed-imaging', 'clinical-imaging', 'diagnostic-imaging',
            'surgical-imaging', 'dental-imaging', 'ophthalmology-imaging',
            'cardiology-imaging', 'neurology-imaging', 'orthopedic-imaging',
            
            // Environmental applications
            'environmental-imaging', 'weather-imaging', 'atmospheric-imaging',
            'ocean-imaging', 'geological-imaging', 'seismic-imaging',
            'volcanic-imaging', 'storm-imaging', 'climate-imaging',
            
            // Sports and entertainment
            'sports-high-speed-imaging', 'athletic-imaging', 'performance-analysis',
            'golf-swing-imaging', 'tennis-imaging', 'baseball-imaging',
            'football-imaging', 'soccer-imaging', 'basketball-imaging',
            
            // Automotive and aerospace
            'automotive-high-speed-imaging', 'crash-test-imaging', 'airbag-imaging',
            'aerospace-imaging', 'aviation-imaging', 'space-imaging',
            'rocket-imaging', 'satellite-imaging', 'drone-imaging',
            
            // Energy and power
            'energy-imaging', 'power-generation-imaging', 'turbine-imaging',
            'wind-turbine-imaging', 'solar-imaging', 'nuclear-imaging',
            'combustion-imaging', 'explosion-imaging', 'fire-imaging',
            
            // Materials and testing
            'material-testing-imaging', 'stress-testing-imaging', 'tensile-testing',
            'compression-testing', 'impact-testing', 'fatigue-testing',
            'fracture-imaging', 'crack-propagation', 'deformation-imaging',
            
            // Electronics and semiconductors
            'electronics-imaging', 'semiconductor-imaging', 'chip-imaging',
            'circuit-imaging', 'pcb-imaging', 'microelectronics-imaging',
            'nanotechnology-imaging', 'quantum-imaging', 'photonics-imaging',
            
            // Food and agriculture
            'food-processing-imaging', 'agricultural-imaging', 'crop-imaging',
            'harvesting-imaging', 'packaging-imaging', 'quality-control-food',
            'safety-testing-food', 'spray-imaging', 'irrigation-imaging',
            
            // Chemical and pharmaceutical
            'chemical-reaction-imaging', 'pharmaceutical-imaging', 'drug-delivery-imaging',
            'mixing-imaging', 'crystallization-imaging', 'polymerization-imaging',
            'combustion-imaging', 'explosion-imaging', 'safety-testing-chemical',
            
            // Regional variations
            'imaging-systems-uae', 'high-speed-camera-dubai', 'imaging-equipment-abu-dhabi',
            'scientific-imaging-sharjah', 'research-camera-uae', 'laboratory-imaging-gcc',
            'advanced-motion-dubai', 'phantom-camera-uae', 'gloria-camera-dubai',
            
            // Brand specific
            'phantom-camera-uae', 'gloria-camera-dubai', 'neo-camera-uae',
            'fastcam-dubai', 'photron-camera-uae', 'vision-research-dubai',
            'advanced-motion-systems-uae', 'advanced-motion-dubai', 'advanced-motion-abu-dhabi',
        ];
        
        foreach ($imagingKeywords as $keyword) {
            $this->addKeyword($keyword, '0.9', 'monthly', 'imaging');
        }
        
        $this->line("   ✓ Generated " . count($imagingKeywords) . " imaging system keywords");
    }

    private function generateLaboratoryEquipmentKeywords()
    {
        $this->info('🧪 Generating laboratory equipment keywords...');
        
        $labKeywords = [
            // Analytical instruments
            'analytical-instruments', 'spectrophotometer', 'chromatograph', 'mass-spectrometer',
            'hplc-system', 'gc-system', 'icp-ms', 'atomic-absorption', 'uv-vis-spectrophotometer',
            'ftir-spectrometer', 'raman-spectrometer', 'xrf-analyzer', 'elemental-analyzer',
            
            // Laboratory balances
            'analytical-balance', 'precision-balance', 'micro-balance', 'ultra-micro-balance',
            'semi-micro-balance', 'moisture-balance', 'density-balance', 'counting-balance',
            'top-loading-balance', 'laboratory-scale', 'scientific-balance', 'research-balance',
            
            // Microscopy
            'optical-microscope', 'digital-microscope', 'stereo-microscope', 'compound-microscope',
            'fluorescence-microscope', 'confocal-microscope', 'electron-microscope', 'sem-microscope',
            'tem-microscope', 'atomic-force-microscope', 'scanning-probe-microscope',
            
            // Centrifuges
            'laboratory-centrifuge', 'refrigerated-centrifuge', 'micro-centrifuge', 'ultra-centrifuge',
            'high-speed-centrifuge', 'low-speed-centrifuge', 'bench-top-centrifuge', 'floor-standing-centrifuge',
            'clinical-centrifuge', 'research-centrifuge', 'analytical-centrifuge',
            
            // Incubators and ovens
            'laboratory-incubator', 'co2-incubator', 'bacterial-incubator', 'cell-culture-incubator',
            'laboratory-oven', 'drying-oven', 'vacuum-oven', 'convection-oven', 'gravity-oven',
            'muffle-furnace', 'tube-furnace', 'box-furnace', 'split-tube-furnace',
            
            // Water analysis
            'water-quality-analyzer', 'ph-meter', 'conductivity-meter', 'dissolved-oxygen-meter',
            'turbidity-meter', 'toc-analyzer', 'cod-analyzer', 'bod-analyzer', 'chlorine-analyzer',
            'nitrate-analyzer', 'phosphate-analyzer', 'heavy-metal-analyzer',
            
            // Sample preparation
            'sample-preparation', 'homogenizer', 'sonicator', 'vortex-mixer', 'magnetic-stirrer',
            'hot-plate-stirrer', 'rotary-evaporator', 'freeze-dryer', 'lyophilizer',
            'grinding-mill', 'cryogenic-mill', 'ball-mill', 'mortar-pestle',
            
            // Safety equipment
            'fume-hood', 'biosafety-cabinet', 'laminar-flow-hood', 'clean-bench',
            'glove-box', 'safety-cabinet', 'ventilation-system', 'air-purification',
            'personal-protective-equipment', 'safety-glasses', 'lab-coat', 'gloves',
            
            // Consumables
            'laboratory-consumables', 'pipettes', 'pipette-tips', 'microplates', 'test-tubes',
            'beakers', 'flasks', 'bottles', 'filters', 'syringes', 'needles',
            'cuvettes', 'slides', 'coverslips', 'petri-dishes', 'culture-tubes',
            
            // Chemicals and reagents
            'laboratory-chemicals', 'analytical-reagents', 'research-chemicals', 'solvents',
            'acids', 'bases', 'buffers', 'standards', 'calibration-standards',
            'reference-materials', 'certified-reference-materials', 'quality-control-materials',
            
            // Software and data
            'laboratory-software', 'data-acquisition', 'chromatography-software', 'spectroscopy-software',
            'lims-system', 'laboratory-information-management', 'data-analysis', 'statistical-analysis',
            'calibration-software', 'quality-control-software', 'reporting-software',
            
            // Regional variations
            'laboratory-equipment-uae', 'lab-equipment-dubai', 'analytical-instruments-abu-dhabi',
            'research-equipment-sharjah', 'scientific-equipment-uae', 'laboratory-supplies-gcc',
            'lab-consumables-dubai', 'research-chemicals-uae', 'safety-equipment-dubai',
        ];
        
        foreach ($labKeywords as $keyword) {
            $this->addKeyword($keyword, '0.8', 'monthly', 'laboratory');
        }
        
        $this->line("   ✓ Generated " . count($labKeywords) . " laboratory equipment keywords");
    }

    private function generateMedicalEquipmentKeywords()
    {
        $this->info('🏥 Generating medical equipment keywords...');
        
        $medicalKeywords = [
            // Diagnostic equipment
            'diagnostic-equipment', 'medical-diagnostic', 'clinical-diagnostic', 'patient-monitoring',
            'vital-signs-monitor', 'ecg-machine', 'eeg-machine', 'ultrasound-machine',
            'x-ray-machine', 'ct-scanner', 'mri-machine', 'pet-scanner', 'spect-scanner',
            
            // Surgical instruments
            'surgical-instruments', 'surgical-equipment', 'operating-room-equipment', 'surgical-tools',
            'scalpels', 'forceps', 'clamps', 'retractors', 'surgical-scissors', 'sutures',
            'surgical-drapes', 'surgical-masks', 'surgical-gloves', 'sterilization-equipment',
            
            // Dental equipment
            'dental-equipment', 'dental-instruments', 'dental-chair', 'dental-drill',
            'dental-scaler', 'dental-camera', 'intraoral-camera', 'dental-x-ray',
            'dental-laser', 'dental-ultrasound', 'dental-sterilization', 'dental-consumables',
            
            // Ophthalmology
            'ophthalmology-equipment', 'eye-examination', 'retinoscope', 'ophthalmoscope',
            'slit-lamp', 'tonometer', 'autorefractor', 'keratometer', 'fundus-camera',
            'optical-coherence-tomography', 'visual-field-analyzer', 'corneal-topography',
            
            // Cardiology
            'cardiology-equipment', 'cardiac-monitoring', 'defibrillator', 'pacemaker',
            'cardiac-catheterization', 'echocardiogram', 'stress-test-equipment',
            'holter-monitor', 'event-recorder', 'cardiac-ultrasound', 'ecg-stress-test',
            
            // Neurology
            'neurology-equipment', 'neurological-monitoring', 'brain-monitoring', 'nerve-conduction',
            'emg-machine', 'ncs-machine', 'evoked-potentials', 'sleep-study-equipment',
            'neurological-examination', 'cognitive-testing', 'neurostimulation',
            
            // Orthopedics
            'orthopedic-equipment', 'orthopedic-instruments', 'bone-saw', 'drill-system',
            'fixation-devices', 'implants', 'prosthetics', 'orthotics', 'casting-equipment',
            'traction-equipment', 'rehabilitation-equipment', 'physical-therapy-equipment',
            
            // Emergency medicine
            'emergency-equipment', 'emergency-medical', 'ambulance-equipment', 'trauma-equipment',
            'resuscitation-equipment', 'ventilator', 'defibrillator', 'monitoring-equipment',
            'emergency-lighting', 'emergency-power', 'emergency-communication',
            
            // Patient care
            'patient-care-equipment', 'hospital-bed', 'wheelchair', 'patient-lift',
            'mobility-aids', 'walking-aids', 'bathroom-aids', 'dressing-aids',
            'feeding-equipment', 'respiratory-equipment', 'oxygen-equipment',
            
            // Medical imaging
            'medical-imaging', 'diagnostic-imaging', 'radiology-equipment', 'nuclear-medicine',
            'interventional-radiology', 'fluoroscopy', 'angiography', 'mammography',
            'bone-densitometry', 'ultrasound-imaging', 'doppler-ultrasound',
            
            // Laboratory medicine
            'clinical-laboratory', 'medical-laboratory', 'pathology-laboratory', 'hematology-equipment',
            'clinical-chemistry', 'immunology-equipment', 'microbiology-equipment', 'virology-equipment',
            'molecular-diagnostics', 'genetic-testing', 'cytology-equipment',
            
            // Regional variations
            'medical-equipment-uae', 'hospital-equipment-dubai', 'clinical-equipment-abu-dhabi',
            'diagnostic-equipment-sharjah', 'surgical-equipment-uae', 'dental-equipment-dubai',
            'ophthalmology-equipment-uae', 'cardiology-equipment-dubai', 'orthopedic-equipment-uae',
        ];
        
        foreach ($medicalKeywords as $keyword) {
            $this->addKeyword($keyword, '0.8', 'monthly', 'medical');
        }
        
        $this->line("   ✓ Generated " . count($medicalKeywords) . " medical equipment keywords");
    }

    private function generateRegionalKeywords()
    {
        $this->info('🌍 Generating regional keywords...');
        
        $regions = [
            // GCC & UAE
            'uae', 'dubai', 'abu-dhabi', 'sharjah', 'ajman', 'fujairah', 'ras-al-khaimah', 'umm-al-quwain', 'ksa', 'saudi-arabia', 'gcc',
            // Middle East
            'middle-east', 'egypt', 'jordan', 'kuwait', 'oman', 'bahrain', 'lebanon', 'qatar', 'iraq', 'syria', 'yemen', 'palestine',
            // Africa (major countries & regions)
            'africa', 'north-africa', 'east-africa', 'west-africa', 'sub-saharan-africa', 'south-africa', 'nigeria', 'kenya', 'morocco', 'algeria', 'ghana', 'tanzania', 'ethiopia', 'uganda', 'sudan', 'angola', 'cameroon', 'zambia', 'zimbabwe', 'tunisia', 'libya', 'senegal', 'ivory-coast', 'botswana', 'namibia', 'rwanda', 'malawi', 'mozambique', 'burkina-faso', 'mali', 'madagascar', 'benin', 'chad', 'congo', 'gabon', 'mauritius', 'seychelles', 'djibouti', 'somalia', 'sierra-leone', 'gambia', 'niger', 'guinea', 'togo', 'lesotho', 'swaziland', 'central-african-republic', 'eritrea', 'guinea-bissau', 'comoros', 'cape-verde', 'liberia', 'sao-tome-and-principe'
        ];
        $categories = ['imaging-systems', 'laboratory-equipment', 'medical-equipment', 'scientific-instruments', 'research-equipment'];
        
        $regionalKeywords = [];
        
        foreach ($regions as $region) {
            foreach ($categories as $category) {
                $regionalKeywords[] = "{$category}-{$region}";
                $regionalKeywords[] = "{$region}-{$category}";
                $regionalKeywords[] = "{$category}-supplier-{$region}";
                $regionalKeywords[] = "{$region}-{$category}-supplier";
            }
            // Add specific regional terms
            $regionalKeywords[] = "medical-supplies-{$region}";
            $regionalKeywords[] = "laboratory-supplies-{$region}";
            $regionalKeywords[] = "hospital-supplies-{$region}";
            $regionalKeywords[] = "research-equipment-{$region}";
            $regionalKeywords[] = "scientific-instruments-{$region}";
            $regionalKeywords[] = "analytical-instruments-{$region}";
            $regionalKeywords[] = "diagnostic-equipment-{$region}";
            $regionalKeywords[] = "high-speed-camera-{$region}";
            $regionalKeywords[] = "advanced-motion-{$region}";
            $regionalKeywords[] = "phantom-camera-{$region}";
            $regionalKeywords[] = "gloria-camera-{$region}";
        }

        // Add product and category regional keywords for all regions
        $productSlugs = Product::pluck('slug');
        $categorySlugs = Category::pluck('slug');
        foreach ($productSlugs as $slug) {
            foreach ($regions as $region) {
                $regionalKeywords[] = "{$slug}-{$region}";
                $regionalKeywords[] = "{$region}-{$slug}";
            }
        }
        foreach ($categorySlugs as $slug) {
            foreach ($regions as $region) {
                $regionalKeywords[] = "{$slug}-{$region}";
                $regionalKeywords[] = "{$region}-{$slug}";
            }
        }
        
        foreach ($regionalKeywords as $keyword) {
            $this->addKeyword($keyword, '0.9', 'monthly', 'regional');
        }
        
        $this->line("   ✓ Generated " . count($regionalKeywords) . " regional keywords");
    }

    private function generateBrandKeywords()
    {
        $this->info('🏷️ Generating brand keywords...');
        
        $brands = [
            'phantom', 'gloria', 'neo', 'fastcam', 'photron', 'vision-research',
            'advanced-motion', 'maxmed', 'abeomics', 'biobase', 'dlab', 'lansionbiotech',
            'maxtest', 'maxware', 'restek', 'revealerhighspeed', 'ringbio', 'shodex',
            'waters-corporation', 'wuhan-ecalbio', 'yooning', 'agilent-technologies'
        ];
        
        $brandKeywords = [];
        
        foreach ($brands as $brand) {
            $brandKeywords[] = "{$brand}-camera";
            $brandKeywords[] = "{$brand}-equipment";
            $brandKeywords[] = "{$brand}-systems";
            $brandKeywords[] = "{$brand}-instruments";
            $brandKeywords[] = "{$brand}-supplies";
            $brandKeywords[] = "{$brand}-products";
            $brandKeywords[] = "{$brand}-uae";
            $brandKeywords[] = "{$brand}-dubai";
            $brandKeywords[] = "{$brand}-supplier";
            $brandKeywords[] = "{$brand}-distributor";
        }
        
        foreach ($brandKeywords as $keyword) {
            $this->addKeyword($keyword, '0.7', 'monthly', 'brand');
        }
        
        $this->line("   ✓ Generated " . count($brandKeywords) . " brand keywords");
    }

    private function generateTechnicalKeywords()
    {
        $this->info('⚙️ Generating technical keywords...');
        
        $technicalKeywords = [
            // Specifications
            'high-resolution', 'ultra-high-resolution', '4k-resolution', '8k-resolution',
            'mega-pixel', 'ultra-sensitive', 'low-light', 'high-dynamic-range',
            'wide-dynamic-range', 'high-bit-depth', 'raw-format', 'compressed-format',
            
            // Performance
            'high-performance', 'ultra-fast', 'real-time', 'low-latency',
            'high-throughput', 'high-efficiency', 'energy-efficient', 'power-efficient',
            'temperature-controlled', 'humidity-controlled', 'vibration-isolated',
            
            // Connectivity
            'ethernet-interface', 'usb-interface', 'gigabit-ethernet', 'wireless-interface',
            'bluetooth-interface', 'wifi-interface', 'network-interface', 'serial-interface',
            'parallel-interface', 'hdmi-interface', 'sdi-interface', 'coax-interface',
            
            // Software
            'analysis-software', 'acquisition-software', 'processing-software',
            'calibration-software', 'control-software', 'monitoring-software',
            'data-logging', 'remote-control', 'web-interface', 'mobile-app',
            
            // Standards
            'iso-certified', 'ce-marked', 'fda-approved', 'rohs-compliant',
            'reach-compliant', 'warranty', 'calibration-certificate', 'traceability',
            'quality-assurance', 'quality-control', 'good-manufacturing-practice',
        ];
        
        foreach ($technicalKeywords as $keyword) {
            $this->addKeyword($keyword, '0.6', 'monthly', 'technical');
        }
        
        $this->line("   ✓ Generated " . count($technicalKeywords) . " technical keywords");
    }

    private function generateApplicationKeywords()
    {
        $this->info('🎯 Generating application keywords...');
        
        $applicationKeywords = [
            // Research applications
            'research-applications', 'academic-research', 'university-research',
            'institute-research', 'laboratory-research', 'scientific-research',
            'experimental-research', 'fundamental-research', 'applied-research',
            
            // Industrial applications
            'industrial-applications', 'manufacturing-applications', 'quality-control-applications',
            'process-monitoring', 'defect-detection', 'automation-applications',
            'production-line', 'assembly-line', 'robotics-applications',
            
            // Medical applications
            'medical-applications', 'clinical-applications', 'diagnostic-applications',
            'therapeutic-applications', 'surgical-applications', 'dental-applications',
            'ophthalmology-applications', 'cardiology-applications', 'neurology-applications',
            
            // Environmental applications
            'environmental-applications', 'weather-monitoring', 'climate-research',
            'atmospheric-studies', 'oceanographic-research', 'geological-studies',
            'seismic-monitoring', 'volcanic-monitoring', 'storm-tracking',
            
            // Sports applications
            'sports-applications', 'athletic-analysis', 'performance-analysis',
            'biomechanics-analysis', 'motion-analysis', 'gait-analysis',
            'sports-science', 'training-analysis', 'rehabilitation-analysis',
            
            // Automotive applications
            'automotive-applications', 'crash-testing', 'safety-testing',
            'aerodynamic-testing', 'engine-analysis', 'transmission-analysis',
            'brake-testing', 'tire-testing', 'vibration-analysis',
            
            // Aerospace applications
            'aerospace-applications', 'aviation-research', 'space-research',
            'satellite-monitoring', 'rocket-testing', 'drone-applications',
            'aircraft-testing', 'wind-tunnel-testing', 'flight-analysis',
            
            // Energy applications
            'energy-applications', 'power-generation', 'turbine-analysis',
            'wind-energy', 'solar-energy', 'nuclear-research',
            'combustion-analysis', 'fuel-analysis', 'emissions-monitoring',
        ];
        
        foreach ($applicationKeywords as $keyword) {
            $this->addKeyword($keyword, '0.7', 'monthly', 'application');
        }
        
        $this->line("   ✓ Generated " . count($applicationKeywords) . " application keywords");
    }

    private function generateIndustryKeywords()
    {
        $this->info('🏭 Generating industry keywords...');
        
        $industryKeywords = [
            // Healthcare industry
            'healthcare-industry', 'medical-industry', 'pharmaceutical-industry',
            'biotechnology-industry', 'clinical-research', 'medical-device-industry',
            'hospital-industry', 'dental-industry', 'veterinary-industry',
            
            // Research and academia
            'research-industry', 'academic-industry', 'university-industry',
            'institute-industry', 'laboratory-industry', 'scientific-industry',
            'government-research', 'private-research', 'contract-research',
            
            // Manufacturing industry
            'manufacturing-industry', 'automotive-industry', 'aerospace-industry',
            'electronics-industry', 'semiconductor-industry', 'chemical-industry',
            'pharmaceutical-manufacturing', 'food-manufacturing', 'textile-industry',
            
            // Energy industry
            'energy-industry', 'power-industry', 'oil-gas-industry', 'renewable-energy',
            'nuclear-industry', 'solar-industry', 'wind-industry', 'hydroelectric-industry',
            'geothermal-industry', 'biomass-industry',
            
            // Environmental industry
            'environmental-industry', 'water-treatment', 'waste-management',
            'air-quality-monitoring', 'soil-analysis', 'environmental-testing',
            'climate-research', 'conservation-research', 'sustainability-research',
            
            // Sports and entertainment
            'sports-industry', 'entertainment-industry', 'media-industry',
            'broadcasting-industry', 'film-industry', 'television-industry',
            'gaming-industry', 'esports-industry', 'fitness-industry',
            
            // Defense and security
            'defense-industry', 'military-industry', 'security-industry',
            'law-enforcement', 'forensic-science', 'intelligence-gathering',
            'surveillance-industry', 'border-security', 'cybersecurity',
            
            // Transportation industry
            'transportation-industry', 'automotive-industry', 'aviation-industry',
            'marine-industry', 'railway-industry', 'logistics-industry',
            'supply-chain', 'warehousing', 'distribution-industry',
        ];
        
        foreach ($industryKeywords as $keyword) {
            $this->addKeyword($keyword, '0.6', 'monthly', 'industry');
        }
        
        $this->line("   ✓ Generated " . count($industryKeywords) . " industry keywords");
    }

    private function addKeyword($keyword, $priority, $changefreq, $category)
    {
        $slug = Str::slug($keyword);
        if (!isset($this->keywords[$slug])) {
            $this->keywords[$slug] = [
                'keyword' => $keyword,
                'priority' => $priority,
                'changefreq' => $changefreq,
                'category' => $category
            ];
        }
    }

    private function generateEnhancedKeywordSitemaps($maxKeywords)
    {
        $this->info('🗺️ Generating enhanced keyword sitemaps...');
        
        // Split keywords into chunks
        $chunks = array_chunk($this->keywords, $maxKeywords, true);
        
        foreach ($chunks as $index => $chunk) {
            $this->generateEnhancedKeywordSitemap($chunk, $index);
        }
        
        // Generate enhanced keyword index
        $this->generateEnhancedKeywordIndex(count($chunks));
    }

    private function generateEnhancedKeywordSitemap($keywords, $index)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        foreach ($keywords as $slug => $data) {
            $xml .= "    <url>\n";
            $xml .= "        <loc>{$this->baseUrl}/keywords/{$slug}</loc>\n";
            $xml .= "        <lastmod>" . now()->toAtomString() . "</lastmod>\n";
            $xml .= "        <changefreq>{$data['changefreq']}</changefreq>\n";
            $xml .= "        <priority>{$data['priority']}</priority>\n";
            $xml .= "    </url>\n";
        }
        
        $xml .= "</urlset>\n";
        
        $filename = "sitemap-enhanced-keywords-{$index}.xml";
        File::put(public_path($filename), $xml);
        $this->line("   ✓ Generated {$filename} with " . count($keywords) . " keywords");
    }

    private function generateEnhancedKeywordIndex($count)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        for ($i = 0; $i < $count; $i++) {
            $xml .= "    <sitemap>\n";
            $xml .= "        <loc>{$this->baseUrl}/sitemap-enhanced-keywords-{$i}.xml</loc>\n";
            $xml .= "        <lastmod>" . now()->toAtomString() . "</lastmod>\n";
            $xml .= "    </sitemap>\n";
        }
        
        $xml .= "</sitemapindex>\n";
        
        File::put(public_path('sitemap-enhanced-keywords.xml'), $xml);
        $this->line("   ✓ Generated sitemap-enhanced-keywords.xml index");
    }

    private function generateRouteDefinitions()
    {
        $this->info('🛣️ Generating route definitions...');
        
        $routeFile = base_path('routes/web.php');
        $routeContent = "\n// Enhanced Keyword Routes\n";
        
        foreach ($this->keywords as $slug => $data) {
            $routeContent .= "Route::get('/keywords/{$slug}', function () {\n";
            $routeContent .= "    return view('keywords.show', ['keyword' => '{$data['keyword']}']);\n";
            $routeContent .= "})->name('keywords.{$slug}');\n\n";
        }
        
        // Append to routes file
        file_put_contents($routeFile, $routeContent, FILE_APPEND);
        $this->line("   ✓ Generated route definitions");
    }

    private function displayEnhancedStats()
    {
        $this->info('📊 Enhanced Keyword Sitemap Statistics:');
        $this->line('');
        
        $categories = [];
        foreach ($this->keywords as $data) {
            $category = $data['category'];
            if (!isset($categories[$category])) {
                $categories[$category] = 0;
            }
            $categories[$category]++;
        }
        
        foreach ($categories as $category => $count) {
            $this->line("   📁 {$category}: {$count} keywords");
        }
        
        $this->line('');
        $this->info("🎯 Total Enhanced Keywords: " . count($this->keywords));
        $this->info("📁 Categories: " . count($categories));
        $this->info("🗺️ Sitemap Files: " . ceil(count($this->keywords) / 50000));
        
        $this->line('');
        $this->info('🚀 Enhanced keyword sitemaps generated successfully!');
        $this->info('📝 You can now submit these to search engines for maximum SEO coverage.');
    }
} 