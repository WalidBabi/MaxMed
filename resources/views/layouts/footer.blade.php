<!-- Footer -->
<footer class="bg-gradient-to-b from-[#171e60] to-[#0d1338] text-white">


    <div class="container mx-auto px-6 py-8 text-sm">
        <!-- Main Footer Content -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">
            <!-- Company Column -->
            <div class="space-y-6">
                <div class="flex items-center">
                    <img src="{{ asset('/Images/logofooter.jpg') }}" alt="MaxMed Logo" class="h-30 mr-3">
                    <div class="footer-logo-light"></div>
                </div>
                <p class="text-gray-300 mt-4 leading-relaxed">
                    Empowering medical laboratories with cutting-edge equipment and comprehensive solutions.
                </p>
                
            </div>

            <!-- Quick Links Column -->
            <div class="mt-6 md:mt-0">
                <h3 class="footer-heading">Quick Links</h3>
                <ul class="footer-links">
                    <li><a href="{{ route('welcome') }}">Home</a></li>
                    <li><a href="{{ route('about') }}">About Us</a></li>
                    <li><a href="{{ route('products.index') }}">Products</a></li>
                    <li><a href="{{ route('contact') }}">Contact</a></li>
                    <li><a href="#">Service & Support</a></li>
                    <li><a href="#">Blog</a></li>
                </ul>
            </div>

            <!-- Products Column -->
            <div class="mt-6 lg:mt-0">
                <h3 class="footer-heading">Product Categories</h3>
                <ul class="footer-links">
                    @foreach(\App\Models\Category::take(5)->get() as $category)
                    <li><a href="{{ route('categories.show', $category) }}">{{ $category->name }}</a></li>
                    @endforeach
                    <li><a href="{{ route('products.index') }}" class="text-blue-300 hover:text-blue-200">View All Categories →</a></li>
                </ul>
            </div>

            <!-- Newsletter Column -->
            <div class="mt-6 lg:mt-0">



                <h3 class="text-lg font-semibold mb-2 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                    Contact Us
                </h3>
                <p class="text-gray-300 hover:text-blue-300 transition-colors duration-300">
                    <a href="mailto:sales@maxmedme.com">sales@maxmedme.com</a>
                </p>
                <p class="text-gray-300 hover:text-blue-300 transition-colors duration-300">
                    <a href="tel:+97155460250">+971 55 460 2500</a>
                </p>
                <div class="flex space-x-4 pt-4">
                    <a href="https://www.linkedin.com/company/maxmed-me/about/?viewAsMember=true"
                        class="social-link linkedin"
                        aria-label="LinkedIn">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" />
                        </svg>
                    </a>
                    <a href="https://wa.me/971554602500" class="social-link whatsapp" aria-label="WhatsApp">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Footer Bottom -->
        <div class="border-t border-gray-700 mt-10 pt-6">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-400 text-sm">
                    &copy; {{ date('Y') }} MaxMed Scientific & Laboratory Equipment Trading Co L.L.C - All Rights Reserved.
                </p>
                <div class="mt-4 md:mt-0 flex space-x-4 text-sm text-gray-400">
                    <a href="#" class="hover:text-white transition-colors duration-300">Privacy Policy</a>
                    <a href="#" class="hover:text-white transition-colors duration-300">Terms of Service</a>
                    <span>Designed by Max AI</span>
                </div>
            </div>
        </div>
    </div>
</footer>
<style>
    /* Footer Styling */
    .wave-top {
        position: relative;
        display: block;
        margin-top: -50px;
        height: 50px;
    }

    .wave-top svg {
        position: absolute;
        width: 100%;
        height: 50px;
    }

    .wave-top .shape-fill {
        fill: #f8f9fa;
    }

    .footer-logo-light {
        position: absolute;
        width: 120px;
        height: 120px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .footer-heading {
        position: relative;
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        color: white;
        display: inline-block;
    }

    .footer-heading::after {
        content: '';
        position: absolute;
        width: 50%;
        height: 3px;
        background: linear-gradient(to right, #0a5694, transparent);
        bottom: -8px;
        left: 0;
        border-radius: 2px;
    }

    .footer-links {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .footer-links li {
        margin-bottom: 0.75rem;
    }

    .footer-links a {
        color: #cbd5e0;
        text-decoration: none;
        transition: all 0.3s ease;
        position: relative;
 
    }

    .footer-links a::after {
        content: '';
        position: absolute;
        width: 0;
        height: 1px;
        bottom: -2px;
        left: 0;
        background-color: #4299e1;
        transition: width 0.3s ease;
    }

    .footer-links a:hover {
        color: white;
        transform: translateX(5px);
    }

    .footer-links a:hover::after {
        width: 100%;
    }

    .social-link {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        color: white;
        background-color: rgba(255, 255, 255, 0.1);
        transition: all 0.3s ease;
    }

    .social-link:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    }

    .social-link.linkedin:hover {
        background-color: #0077b5;
    }



    @media (max-width: 768px) {
        .footer-heading {
            margin-top: 1.5rem;
        }
    }
</style>