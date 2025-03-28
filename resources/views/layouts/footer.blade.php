<!-- Footer -->
<footer class="bg-gradient-to-b from-[#171e60] to-[#0d1338] text-white">


    <div class="container mx-auto px-6 py-12">
        <!-- Main Footer Content -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">
            <!-- Company Column -->
            <div class="space-y-6">
                <div class="flex items-center">
                    <img src="{{ asset('/Images/logofooter.jpg') }}" alt="MaxMed Logo" class="h-16 mr-3">
                    <div class="footer-logo-light"></div>
                </div>
                <p class="text-gray-300 mt-4 leading-relaxed">
                    Empowering medical laboratories with cutting-edge equipment and comprehensive solutions.
                </p>
                <div class="flex space-x-4 pt-2">
                    <a href="https://www.linkedin.com/company/maxmed-me/about/?viewAsMember=true" 
                       class="social-link linkedin" 
                       aria-label="LinkedIn">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                        </svg>
                    </a>
                    <a href="#" class="social-link twitter" aria-label="Twitter">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                        </svg>
                    </a>
                    <a href="#" class="social-link facebook" aria-label="Facebook">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/>
                        </svg>
                    </a>
                    <a href="#" class="social-link instagram" aria-label="Instagram">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                        </svg>
                    </a>
                </div>
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
                <h3 class="footer-heading">Stay Updated</h3>
                <p class="text-gray-300 mb-4">Subscribe to our newsletter for the latest products and industry insights.</p>
                
                <form class="newsletter-form">
                    <div class="relative">
                        <input type="email" placeholder="Your email address" required
                            class="newsletter-input">
                        <div class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <button type="submit" class="newsletter-button">
                        Subscribe
                    </button>
                </form>
                
                <div class="mt-6">
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
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
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
        display: inline-block;
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
    
    .social-link.twitter:hover {
        background-color: #1da1f2;
    }
    
    .social-link.facebook:hover {
        background-color: #1877f2;
    }
    
    .social-link.instagram:hover {
        background: radial-gradient(circle at 30% 107%, #fdf497 0%, #fdf497 5%, #fd5949 45%, #d6249f 60%, #285AEB 90%);
    }
    
    .newsletter-input {
        width: 100%;
        padding: 0.75rem 1rem;
        padding-right: 2.5rem;
        background-color: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 8px;
        color: white;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }
    
    .newsletter-input:focus {
        outline: none;
        background-color: rgba(255, 255, 255, 0.15);
        border-color: rgba(255, 255, 255, 0.3);
        box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.3);
    }
    
    .newsletter-input::placeholder {
        color: rgba(255, 255, 255, 0.6);
    }
    
    .newsletter-button {
        display: block;
        width: 100%;
        margin-top: 0.75rem;
        padding: 0.75rem 1rem;
        background: linear-gradient(to right, #0a5694, #171e60);
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .newsletter-button:hover {
        background: linear-gradient(to right, #0c62a6, #1d2575);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(10, 86, 148, 0.3);
    }
    
    .newsletter-button::after {
        content: '';
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: -100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: all 0.6s ease;
    }
    
    .newsletter-button:hover::after {
        left: 100%;
    }
    
    .newsletter-form {
        position: relative;
    }
    
    @media (max-width: 768px) {
        .footer-heading {
            margin-top: 1.5rem;
        }
    }
</style>
