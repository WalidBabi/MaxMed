<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - MaxMed</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50">
    <x-navigation />

    <div class="max-w-7xl mx-auto px-4 py-8 md:py-12 mt-[70px]">
        <div class="flex flex-col md:flex-row gap-8">
            <!-- Sidebar -->
            <div class="w-full md:w-64 bg-white shadow-lg rounded-lg p-6">
                <h2 class="text-xl font-bold mb-4">Product Categories</h2>
                <ul class="space-y-2">
                    <li><a href="#" class="block text-gray-700 hover:text-blue-600 hover:bg-gray-50 px-4 py-2 rounded">Maxtest rapid tests</a></li>
                    <li><a href="#" class="block text-gray-700 hover:text-blue-600 hover:bg-gray-50 px-4 py-2 rounded">Infectious Disease</a></li>
                    <li><a href="#" class="block text-gray-700 hover:text-blue-600 hover:bg-gray-50 px-4 py-2 rounded">Drug of Abuse</a></li>
                    <li><a href="#" class="block text-gray-700 hover:text-blue-600 hover:bg-gray-50 px-4 py-2 rounded">Fertility Health</a></li>
                    <li><a href="#" class="block text-gray-700 hover:text-blue-600 hover:bg-gray-50 px-4 py-2 rounded">Cardiology</a></li>
                    <li><a href="#" class="block text-gray-700 hover:text-blue-600 hover:bg-gray-50 px-4 py-2 rounded">Oncology</a></li>
                    <li><a href="#" class="block text-gray-700 hover:text-blue-600 hover:bg-gray-50 px-4 py-2 rounded">Inflammation</a></li>
                    <li><a href="#" class="block text-gray-700 hover:text-blue-600 hover:bg-gray-50 px-4 py-2 rounded">Biochemistry</a></li>
                    <li><a href="#" class="block text-gray-700 hover:text-blue-600 hover:bg-gray-50 px-4 py-2 rounded">Instrument</a></li>
                    <li><a href="#" class="block text-gray-700 hover:text-blue-600 hover:bg-gray-50 px-4 py-2 rounded">Fluorescence Immunoassay</a></li>
                    <li><a href="#" class="block text-gray-700 hover:text-blue-600 hover:bg-gray-50 px-4 py-2 rounded">Veterinary Test</a></li>
                    <li><a href="#" class="block text-gray-700 hover:text-blue-600 hover:bg-gray-50 px-4 py-2 rounded">Others</a></li>
                </ul>
            </div>

            <!-- Main content area -->
            <div class="flex-1">
                <h1 class="text-3xl font-bold mb-8">Our Products</h1>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Product Card 1 -->
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                        <img src="https://images.unsplash.com/photo-1579154204601-01588f351e67?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60" alt="COVID-19 Rapid Test" class="w-full h-48 object-cover">
                        <div class="p-6">
                            <h3 class="text-xl font-bold mb-2">COVID-19 Antigen Rapid Test</h3>
                            <p class="text-gray-600 mb-4">Quick and reliable COVID-19 antigen detection with results in 15 minutes</p>
                            <div class="flex justify-between items-center">
                                <span class="text-blue-600 font-semibold">25 tests/box</span>
                                <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors">Learn More</button>
                            </div>
                        </div>
                    </div>

                    <!-- Product Card 2 -->
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                        <img src="https://images.unsplash.com/photo-1583324113626-70df0f4deaab?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60" alt="Influenza Test" class="w-full h-48 object-cover">
                        <div class="p-6">
                            <h3 class="text-xl font-bold mb-2">Influenza A/B Rapid Test</h3>
                            <p class="text-gray-600 mb-4">Dual detection of Influenza A and B viruses with high accuracy</p>
                            <div class="flex justify-between items-center">
                                <span class="text-blue-600 font-semibold">20 tests/box</span>
                                <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors">Learn More</button>
                            </div>
                        </div>
                    </div>

                    <!-- Product Card 3 -->
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                        <img src="https://images.unsplash.com/photo-1576086213369-97a306d36557?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60" alt="Strep Test" class="w-full h-48 object-cover">
                        <div class="p-6">
                            <h3 class="text-xl font-bold mb-2">Strep A Rapid Test</h3>
                            <p class="text-gray-600 mb-4">Fast detection of Group A Streptococcal infection from throat swabs</p>
                            <div class="flex justify-between items-center">
                                <span class="text-blue-600 font-semibold">30 tests/box</span>
                                <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors">Learn More</button>
                            </div>
                        </div>
                    </div>

                    <!-- Product Card 4 -->
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                        <img src="https://images.unsplash.com/photo-1584362917165-526a968579e8?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60" alt="Dengue Test" class="w-full h-48 object-cover">
                        <div class="p-6">
                            <h3 class="text-xl font-bold mb-2">Dengue NS1 Rapid Test</h3>
                            <p class="text-gray-600 mb-4">Early detection of dengue infection with NS1 antigen testing</p>
                            <div class="flex justify-between items-center">
                                <span class="text-blue-600 font-semibold">20 tests/box</span>
                                <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors">Learn More</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include your footer here -->
</body>

</html>