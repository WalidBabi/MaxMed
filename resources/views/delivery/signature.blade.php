<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign for Delivery - MaxMed</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .brand-gradient {
            background: linear-gradient(135deg, #171e60 0%, #0a5694 100%);
        }
        #signature-canvas {
            border: 2px dashed #d1d5db;
            border-radius: 8px;
            cursor: crosshair;
        }
        #signature-canvas.signing {
            border-color: #3b82f6;
            background-color: #fefefe;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <div class="brand-gradient shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center">
                        <img src="{{ asset('Images/logo.png') }}" alt="MaxMed" class="h-10 w-auto">
                        <span class="ml-3 text-white text-xl font-bold">MaxMed</span>
                    </div>
                    <div class="text-white text-sm">
                        Delivery Signature
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <!-- Delivery Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-xl font-semibold text-gray-900">Delivery Information</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tracking Number</label>
                            <p class="text-gray-900 bg-gray-50 px-3 py-2 rounded-md font-mono">{{ $delivery->tracking_number }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Order Number</label>
                            <p class="text-gray-900 bg-gray-50 px-3 py-2 rounded-md">{{ $delivery->order->order_number }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Customer</label>
                            <p class="text-gray-900 bg-gray-50 px-3 py-2 rounded-md">{{ $delivery->order->getCustomerName() }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Total Amount</label>
                            <p class="text-gray-900 bg-gray-50 px-3 py-2 rounded-md">{{ number_format($delivery->order->total_amount, 2) }} AED</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Order Items</h3>
                </div>
                <div class="overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($delivery->order->items as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $item->product ? $item->product->name : 'Product #' . $item->product_id }}
                                        </div>
                                        @if($item->variation)
                                            <div class="text-sm text-gray-500">{{ $item->variation }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->quantity }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($item->price, 2) }} AED</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Signature Form -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 w-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                        </svg>
                        Sign for Delivery
                    </h3>
                </div>
                <div class="p-6">
                    <form id="signature-form">
                        @csrf
                        <div class="space-y-6">
                            <div>
                                <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-2">Customer Name *</label>
                                <input type="text" 
                                       id="customer_name" 
                                       name="customer_name" 
                                       required 
                                       value="{{ $delivery->order->getCustomerName() }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Digital Signature *</label>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <canvas id="signature-canvas" width="600" height="300" class="w-full max-w-full h-64 bg-white"></canvas>
                                    <div class="flex justify-between items-center mt-3">
                                        <p class="text-sm text-gray-600">Please sign above to confirm receipt of your delivery</p>
                                        <button type="button" 
                                                id="clear-signature" 
                                                class="px-3 py-1 text-sm text-gray-600 border border-gray-300 rounded hover:bg-gray-50">
                                            Clear
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label for="delivery_conditions" class="block text-sm font-medium text-gray-700 mb-2">Delivery Conditions (Optional)</label>
                                <textarea id="delivery_conditions" 
                                          name="delivery_conditions" 
                                          rows="3"
                                          placeholder="Any notes about the condition of the delivery (e.g., 'Delivered in good condition', 'Minor packaging damage')"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                            </div>

                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div class="flex">
                                    <svg class="h-5 w-5 text-blue-400 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                    </svg>
                                    <div class="text-sm text-blue-800">
                                        <p class="font-medium">Important:</p>
                                        <ul class="mt-1 list-disc list-inside space-y-1">
                                            <li>By signing, you confirm that you have received the delivery</li>
                                            <li>Please inspect the items before signing</li>
                                            <li>Your signature will be recorded along with the timestamp and IP address</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="flex space-x-4">
                                <a href="{{ route('delivery.track', ['tracking' => $delivery->tracking_number]) }}" 
                                   class="flex-1 inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Cancel
                                </a>
                                <button type="submit" 
                                        id="submit-signature"
                                        disabled
                                        class="flex-1 inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:bg-gray-400 disabled:cursor-not-allowed">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Confirm Delivery
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Modal -->
    <div id="loading-modal" style="display: none;" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white rounded-lg shadow-lg p-6 max-w-sm w-full">
            <div class="text-center">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mb-4"></div>
                <p class="text-gray-600">Processing signature...</p>
            </div>
        </div>
    </div>

    <script>
        // Signature pad functionality
        const canvas = document.getElementById('signature-canvas');
        const ctx = canvas.getContext('2d');
        const submitBtn = document.getElementById('submit-signature');
        const clearBtn = document.getElementById('clear-signature');
        const form = document.getElementById('signature-form');
        
        let drawing = false;
        let signatureExists = false;

        // Set canvas size for high DPI displays
        const rect = canvas.getBoundingClientRect();
        const scaleX = canvas.width / rect.width;
        const scaleY = canvas.height / rect.height;

        // Mouse events
        canvas.addEventListener('mousedown', startDrawing);
        canvas.addEventListener('mousemove', draw);
        canvas.addEventListener('mouseup', stopDrawing);
        canvas.addEventListener('mouseout', stopDrawing);

        // Touch events for mobile
        canvas.addEventListener('touchstart', handleTouch);
        canvas.addEventListener('touchmove', handleTouch);
        canvas.addEventListener('touchend', stopDrawing);

        function startDrawing(e) {
            drawing = true;
            canvas.classList.add('signing');
            const coords = getCoordinates(e);
            ctx.beginPath();
            ctx.moveTo(coords.x, coords.y);
        }

        function draw(e) {
            if (!drawing) return;
            
            const coords = getCoordinates(e);
            ctx.lineWidth = 2;
            ctx.lineCap = 'round';
            ctx.strokeStyle = '#000';
            ctx.lineTo(coords.x, coords.y);
            ctx.stroke();
            
            signatureExists = true;
            updateSubmitButton();
        }

        function stopDrawing() {
            drawing = false;
            canvas.classList.remove('signing');
        }

        function getCoordinates(e) {
            const rect = canvas.getBoundingClientRect();
            return {
                x: (e.clientX - rect.left) * scaleX,
                y: (e.clientY - rect.top) * scaleY
            };
        }

        function handleTouch(e) {
            e.preventDefault();
            const touch = e.touches[0];
            const mouseEvent = new MouseEvent(e.type === 'touchstart' ? 'mousedown' : 
                                            e.type === 'touchmove' ? 'mousemove' : 'mouseup', {
                clientX: touch.clientX,
                clientY: touch.clientY
            });
            canvas.dispatchEvent(mouseEvent);
        }

        function clearSignature() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            signatureExists = false;
            updateSubmitButton();
        }

        function updateSubmitButton() {
            const customerName = document.getElementById('customer_name').value.trim();
            submitBtn.disabled = !(signatureExists && customerName);
        }

        // Event listeners
        clearBtn.addEventListener('click', clearSignature);
        document.getElementById('customer_name').addEventListener('input', updateSubmitButton);

        // Form submission
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            if (!signatureExists) {
                alert('Please provide your signature before submitting.');
                return;
            }

            const customerName = document.getElementById('customer_name').value.trim();
            if (!customerName) {
                alert('Please enter your name.');
                return;
            }

            // Show loading
            document.getElementById('loading-modal').style.display = 'block';

            try {
                // Get signature data
                const signatureData = canvas.toDataURL();
                
                const formData = new FormData();
                formData.append('_token', document.querySelector('input[name="_token"]').value);
                formData.append('signature', signatureData);
                formData.append('customer_name', customerName);
                formData.append('delivery_conditions', document.getElementById('delivery_conditions').value);

                const response = await fetch('{{ route("delivery.process-signature", $delivery->tracking_number) }}', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    alert('Delivery signed successfully!');
                    window.location.href = result.redirect;
                } else {
                    alert(result.error || 'Failed to process signature. Please try again.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            } finally {
                document.getElementById('loading-modal').style.display = 'none';
            }
        });

        // Initial button state
        updateSubmitButton();
    </script>
</body>
</html> 