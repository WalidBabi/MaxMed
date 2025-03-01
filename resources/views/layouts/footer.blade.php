<!-- Footer -->
<footer class="bg-[#171e60] text-white py-12">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- Company Overview -->
            <div>
                <img src="{{ asset('/Images/logofooter.jpg') }}" alt="MaxMed Logo">
                <p class="text-gray-400 mb-4 mt-2">Empowering medical laboratories with cutting-edge equipment since 2010.</p>
                <div class="flex space-x-4">
                    <a href="https://www.linkedin.com/company/maxmed-me/about/?viewAsMember=true" class="text-gray-400 hover:text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" />
                        </svg>
                    </a>

                </div>
            </div>
            <!-- Solutions -->
            <div class="ml-5">
                <h5 class="text-xl font-bold mb-4">Products</h5>
                <ul class="space-y-2">
                    <li><a href="{{ route('products.index') }}" class="text-gray-400 hover:text-white transition-colors duration-300">All Products</a></li>
                    <li><a href="{{ route('products.index', ['category' => 'rapid-tests']) }}" class="text-gray-400 hover:text-white transition-colors duration-300">MaxTest© Rapid Tests IVD</a></li>
                    <li><a href="{{ route('products.index', ['category' => 'plasticware']) }}" class="text-gray-400 hover:text-white transition-colors duration-300">MaxWare© Plasticware</a></li>
                    <li><a href="{{ route('products.index', ['category' => 'analytical-chemistry']) }}" class="text-gray-400 hover:text-white transition-colors duration-300">Analytical Chemistry</a></li>
                    <li><a href="{{ route('products.index', ['category' => 'microbiology']) }}" class="text-gray-400 hover:text-white transition-colors duration-300">Microbiology</a></li>
                    <li><a href="{{ route('products.index', ['category' => 'molecular-biology']) }}" class="text-gray-400 hover:text-white transition-colors duration-300">Molecular Biology</a></li>
                    <li><a href="{{ route('products.index', ['category' => 'consumables']) }}" class="text-gray-400 hover:text-white transition-colors duration-300">Medical Consumables</a></li>
                </ul>
            </div>


            <!-- Connect -->
            <div class="ml-5">
                <h5 class="text-xl font-bold mb-4 flex items-center">
                    <span class="mr-2">Stay Connected</span>
                    <svg class="w-5 h-5 text-blue-400 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </h5>
                <div class="mb-6 transform hover:scale-105 transition-transform duration-300">
                    <p class="text-gray-400 mb-4">Subscribe to our newsletter for industry insights and product updates.</p>
                    <form class="space-y-2">
                        <div class="relative">
                            <input type="email" placeholder="Enter your email"
                                class="w-full px-4 py-2 rounded bg-gray-700 text-white focus:ring-2 focus:ring-blue-400 focus:outline-none transition-all duration-300">
                            <div class="absolute right-3 top-2.5 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        </div>
                        <button class="w-full bg-[#0a5694] hover:bg-[#171e60] text-white px-4 py-2 rounded transform hover:translate-y-[-2px] transition-all duration-300 hover:shadow-lg">
                            Subscribe Now
                        </button>
                    </form>
                </div>

            </div>
            <div class="transform hover:scale-105 transition-transform duration-300 p-4 rounded-lg">
                <h6 class="font-semibold mb-2 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                    24/7 Support
                </h6>
                <p class="text-gray-400 hover:text-blue-400 transition-colors duration-300">Email: sales@maxmedme.com</p>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="border-t border-gray-700 mt-8 pt-8">
            <div class="flex flex-col md:flex-row justify-between items-center text-gray-400 text-sm">
                <p>Copyright © 2025 MaxMed Scientific & Laboratory Equipment Trading Co L.L.C - All Rights Reserved.</p>

                <p>Powered by Walid</p>

            </div>
        </div>
    </div>
</footer>