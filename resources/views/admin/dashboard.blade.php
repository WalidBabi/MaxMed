@extends('admin.layouts.app')

@section('title', 'Sales Dashboard - MaxMed Admin')

@section('content')
@can('dashboard.view')
<div class="-mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Sales Analytics Dashboard</h1>
                <p class="text-gray-600 mt-2">Comprehensive sales trends and analytics with advanced filtering</p>
            </div>
            <div class="flex items-center space-x-3">
                <span class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.75 2a.75.75 0 01.75.75V4h7V2.75a.75.75 0 011.5 0V4h.25A2.25 2.25 0 0117.5 6.25v8.5A2.25 2.25 0 0115.25 17H4.75A2.25 2.25 0 012.5 14.75v-8.5A2.25 2.25 0 014.75 4H5V2.75A.75.75 0 015.75 2zm-1 5.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25v-6.5c0-.69-.56-1.25-1.25-1.25H4.75z" clip-rule="evenodd" />
                    </svg>
                    {{ now()->format('l, F j, Y') }}
                </span>
            </div>
        </div>
    </div>

    <!-- Revenue vs Cash Flow Section -->
    <div class="mb-8">
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Revenue AED -->
            <div class="overflow-hidden rounded-xl bg-white px-4 py-6 shadow-sm ring-1 ring-gray-900/5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-500">
                            <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Revenue (AED)</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($salesData['revenue']['aed'], 2) }} AED</p>
                        <p class="text-xs text-gray-500 mt-1">All sent invoices</p>
                    </div>
                </div>
            </div>

            <!-- Revenue USD -->
            <div class="overflow-hidden rounded-xl bg-white px-4 py-6 shadow-sm ring-1 ring-gray-900/5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-500">
                            <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Revenue (USD)</p>
                        <p class="text-2xl font-semibold text-gray-900">${{ number_format($salesData['revenue']['usd'], 2) }}</p>
                        <p class="text-xs text-gray-500 mt-1">All sent invoices</p>
                    </div>
                </div>
            </div>

            <!-- Cash Flow AED -->
            <div class="overflow-hidden rounded-xl bg-white px-4 py-6 shadow-sm ring-1 ring-gray-900/5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-emerald-500">
                            <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Cash Flow (AED)</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($salesData['cash_flow']['aed'], 2) }} AED</p>
                        <p class="text-xs text-gray-500 mt-1">Paid invoices only</p>
                    </div>
                </div>
            </div>

            <!-- Cash Flow USD -->
            <div class="overflow-hidden rounded-xl bg-white px-4 py-6 shadow-sm ring-1 ring-gray-900/5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-teal-500">
                            <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Cash Flow (USD)</p>
                        <p class="text-2xl font-semibold text-gray-900">${{ number_format($salesData['cash_flow']['usd'], 2) }}</p>
                        <p class="text-xs text-gray-500 mt-1">Paid invoices only</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Combined Revenue vs Cash Flow -->
    <div class="mb-8">
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <!-- Combined Revenue -->
            <div class="overflow-hidden rounded-xl bg-white px-4 py-6 shadow-sm ring-1 ring-gray-900/5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-purple-500">
                            <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($salesData['revenue']['combined'], 2) }} AED</p>
                        <p class="text-xs text-gray-500 mt-1">AED + USD converted</p>
                    </div>
                </div>
            </div>

            <!-- Combined Cash Flow -->
            <div class="overflow-hidden rounded-xl bg-white px-4 py-6 shadow-sm ring-1 ring-gray-900/5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-indigo-500">
                            <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Cash Flow</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($salesData['cash_flow']['combined'], 2) }} AED</p>
                        <p class="text-xs text-gray-500 mt-1">AED + USD converted</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Summary Cards -->
    <div class="mb-8">
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Total AED Sales -->
            <div class="overflow-hidden rounded-xl bg-white px-4 py-6 shadow-sm ring-1 ring-gray-900/5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-500">
                                <svg class="h-8 w-8 text-white" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 375 374.999991" preserveAspectRatio="xMidYMid meet">
                                    <g clip-rule="nonzero">
                                        <path style="stroke:none;fill-rule:nonzero;fill:#ffffff;fill-opacity:1;" d="M 125.828125 122.585938 C 125.898438 122.679688 126.21875 123.082031 126.539062 123.472656 C 128.820312 126.203125 130.542969 130.644531 131.464844 136.242188 C 132.078125 139.917969 132.109375 141.074219 132.109375 155.089844 L 132.109375 168.144531 L 125.859375 168.144531 C 120.144531 168.144531 119.484375 168.117188 118.359375 167.890625 C 116.496094 167.472656 114.890625 166.582031 113.542969 165.21875 C 112.570312 164.183594 112.601562 164.125 112.660156 167.261719 C 112.738281 169.855469 112.765625 170.140625 113.140625 171.558594 C 113.738281 173.777344 114.5625 175.453125 115.804688 176.9375 C 117.492188 178.980469 119.214844 180.121094 121.667969 180.882812 C 122.191406 181.035156 123.300781 181.097656 127.21875 181.125 L 132.113281 181.199219 L 132.113281 194.210938 L 125.214844 194.164062 L 118.285156 194.121094 L 117.089844 193.640625 C 115.667969 193.070312 115.027344 192.648438 113.632812 191.402344 L 112.617188 190.488281 L 112.675781 193.355469 C 112.75 196.011719 112.765625 196.308594 113.140625 197.660156 C 114.441406 202.433594 117.582031 205.839844 121.726562 206.949219 C 122.761719 207.234375 123.164062 207.25 127.492188 207.3125 L 132.113281 207.371094 L 132.113281 220.824219 C 132.113281 228.941406 132.066406 234.925781 131.992188 235.949219 C 131.917969 236.878906 131.679688 238.617188 131.46875 239.832031 C 130.496094 245.433594 128.746094 249.648438 126.234375 252.378906 L 125.722656 252.933594 L 151.027344 252.933594 C 166.152344 252.933594 177.464844 252.875 179.125 252.800781 C 182.042969 252.648438 188.550781 252.003906 190.019531 251.6875 C 190.480469 251.597656 191.347656 251.476562 191.917969 251.375 C 193.132812 251.191406 195.132812 250.773438 198.023438 250.039062 C 201.941406 249.066406 205.746094 247.757812 209.433594 246.113281 C 210.570312 245.605469 213.832031 243.9375 214.699219 243.414062 C 215.164062 243.144531 215.71875 242.8125 215.929688 242.707031 C 216.957031 242.109375 217.949219 241.453125 218.90625 240.742188 C 219.609375 240.234375 220.3125 239.738281 220.460938 239.632812 C 221.089844 239.214844 223.257812 237.398438 224.246094 236.480469 C 227.917969 233.125 231.03125 229.308594 233.582031 225.03125 C 233.925781 224.429688 234.378906 223.683594 234.570312 223.367188 C 235.066406 222.527344 237.097656 218.324219 237.292969 217.695312 C 237.355469 217.46875 237.445312 217.253906 237.5625 217.050781 C 237.953125 216.542969 240.195312 209.457031 240.464844 207.914062 C 240.558594 207.417969 240.601562 207.34375 240.972656 207.269531 C 241.214844 207.222656 244.703125 207.222656 248.726562 207.253906 C 256.777344 207.3125 256.777344 207.3125 258.554688 208.136719 C 259.558594 208.601562 259.859375 208.8125 260.964844 209.816406 C 262.414062 211.125 262.28125 211.335938 262.191406 208.0625 C 262.132812 206.140625 262.058594 204.957031 261.921875 204.476562 C 261.414062 202.628906 261.292969 202.242188 260.851562 201.3125 C 259.382812 198.089844 256.933594 195.804688 253.785156 194.707031 L 252.554688 194.257812 L 247.5625 194.1875 L 242.578125 194.113281 L 242.640625 192.355469 C 242.699219 190.042969 242.699219 185.46875 242.625 183.117188 L 242.5625 181.226562 L 249.234375 181.199219 C 254.953125 181.167969 256.015625 181.199219 256.644531 181.363281 C 258.527344 181.886719 259.800781 182.609375 261.355469 184.035156 L 262.226562 184.84375 L 262.226562 182.625 C 262.226562 179.984375 262.089844 178.8125 261.550781 177.074219 C 260.480469 173.546875 258.394531 170.921875 255.402344 169.300781 C 253.457031 168.25 253.335938 168.226562 246.648438 168.175781 C 242.730469 168.144531 240.675781 168.085938 240.574219 167.996094 C 240.476562 167.894531 240.417969 167.773438 240.410156 167.632812 C 240.273438 166.929688 240.097656 166.234375 239.882812 165.546875 C 236.386719 153.132812 229.855469 143.273438 220.289062 135.972656 C 218.988281 134.964844 215.800781 132.847656 214.503906 132.128906 C 214.007812 131.84375 213.472656 131.542969 213.335938 131.453125 C 212.707031 131.109375 209.101562 129.335938 208.195312 128.964844 C 207.65625 128.722656 206.953125 128.421875 206.640625 128.300781 C 201.359375 126.007812 192.503906 123.832031 185.738281 123.144531 C 184.628906 123.042969 183.167969 122.878906 182.488281 122.816406 C 179.445312 122.484375 175.21875 122.4375 151.171875 122.4375 C 130.855469 122.4375 125.753906 122.484375 125.828125 122.585938 Z M 175.308594 129.097656 C 180.367188 129.394531 183.480469 129.785156 187.117188 130.671875 C 198.21875 133.300781 206.027344 138.882812 211.699219 148.214844 C 212.222656 149.074219 214.4375 153.617188 214.765625 154.53125 C 216.335938 158.78125 217.101562 161.300781 217.765625 164.628906 C 217.929688 165.4375 218.152344 166.519531 218.257812 167.03125 C 218.363281 167.542969 218.410156 167.992188 218.363281 168.035156 C 218.320312 168.082031 203.265625 168.125 184.949219 168.113281 L 151.65625 168.082031 L 151.609375 148.796875 C 151.597656 138.203125 151.609375 129.394531 151.65625 129.230469 L 151.714844 128.945312 L 162.355469 128.945312 C 168.191406 128.945312 174.023438 129.007812 175.296875 129.082031 Z M 219.824219 181.660156 C 219.929688 182.304688 219.929688 193.261719 219.824219 193.800781 L 219.734375 194.203125 L 185.695312 194.175781 L 151.667969 194.132812 L 151.640625 187.769531 C 151.609375 184.273438 151.640625 181.359375 151.667969 181.300781 C 151.699219 181.242188 166.214844 181.179688 185.738281 181.179688 L 219.734375 181.179688 Z M 218.296875 207.425781 C 218.371094 207.640625 218.015625 209.496094 217.28125 212.5 C 216.441406 215.875 215.304688 219.28125 214.152344 221.800781 C 213.585938 223.089844 212.164062 225.882812 211.820312 226.40625 C 211.65625 226.648438 211.175781 227.414062 210.75 228.089844 C 207.882812 232.515625 204.242188 236.160156 199.824219 239.027344 C 198.226562 240.046875 194.933594 241.789062 194.042969 242.058594 C 193.878906 242.097656 193.730469 242.164062 193.589844 242.253906 C 193.488281 242.34375 192.125 242.855469 190.539062 243.425781 C 187.621094 244.460938 182.070312 245.585938 177.613281 246.050781 C 174.726562 246.335938 174.261719 246.351562 163.144531 246.351562 L 151.652344 246.351562 L 151.652344 207.402344 L 184.707031 207.339844 C 202.886719 207.3125 217.863281 207.265625 217.984375 207.234375 C 218.128906 207.226562 218.234375 207.285156 218.296875 207.417969 Z M 218.296875 207.425781 "/>
                                    </g>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total AED Sales</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($salesData['total_aed'], 2) }} AED</p>
                        <p class="text-xs text-gray-500 mt-1">From first transaction</p>
                        </div>
                    </div>
            </div>

            <!-- Total USD Sales -->
            <div class="overflow-hidden rounded-xl bg-white px-4 py-6 shadow-sm ring-1 ring-gray-900/5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-500">
                            <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total USD Sales</p>
                        <p class="text-2xl font-semibold text-gray-900">${{ number_format($salesData['total_usd'], 2) }}</p>
                        <p class="text-xs text-gray-500 mt-1">From first transaction</p>
                    </div>
                </div>
            </div>

            <!-- Peak Sales Months -->
            <div class="overflow-hidden rounded-xl bg-white px-4 py-6 shadow-sm ring-1 ring-gray-900/5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-yellow-500">
                            <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Peak Months</p>
                        <p class="text-lg font-semibold text-gray-900">{{ count($salesData['peak_months']) }}</p>
                        <p class="text-xs text-gray-500 mt-1">Highest sales periods</p>
                    </div>
                </div>
            </div>

            <!-- Total Combined Sales -->
            <div class="overflow-hidden rounded-xl bg-white px-4 py-6 shadow-sm ring-1 ring-gray-900/5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-purple-500">
                            <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Combined Sales</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($salesData['total_combined'], 2) }} AED</p>
                        <p class="text-xs text-gray-500 mt-1">AED + USD converted</p>
                    </div>
                </div>
            </div>

            <!-- Zero Sales Months -->
            <div class="overflow-hidden rounded-xl bg-white px-4 py-6 shadow-sm ring-1 ring-gray-900/5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-red-500">
                            <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Zero Sales Months</p>
                        <p class="text-lg font-semibold text-gray-900">{{ count($salesData['zero_months']) }}</p>
                        <p class="text-xs text-gray-500 mt-1">Months with no sales</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Advanced Filters and Controls -->
    <div class="mb-8 filter-section">
        <div class="bg-gradient-to-r from-white to-gray-50 rounded-2xl shadow-lg ring-1 ring-gray-200/50 p-8">
            <div class="mb-6">
                <h3 class="text-xl font-bold text-gray-900 mb-2">📊 Advanced Analytics Filters</h3>
                <p class="text-sm text-gray-600">Customize your sales analysis with powerful filtering options</p>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                <!-- Time Period Toggle -->
                <div class="space-y-3">
                    <label class="block text-sm font-semibold text-gray-700">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Time Period
                        </span>
                    </label>
                    <div class="flex rounded-xl bg-gray-100 p-1 shadow-inner">
                        <button type="button" class="period-toggle flex-1 px-4 py-2.5 text-sm font-semibold rounded-lg transition-all duration-200 ease-in-out" data-period="daily">
                            <span class="flex items-center justify-center">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Daily
                            </span>
                        </button>
                        <button type="button" class="period-toggle flex-1 px-4 py-2.5 text-sm font-semibold rounded-lg transition-all duration-200 ease-in-out active" data-period="monthly">
                            <span class="flex items-center justify-center">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                Monthly
                            </span>
                        </button>
                        <button type="button" class="period-toggle flex-1 px-4 py-2.5 text-sm font-semibold rounded-lg transition-all duration-200 ease-in-out" data-period="quarterly">
                            <span class="flex items-center justify-center">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                Quarterly
                            </span>
                        </button>
                    </div>
                </div>

                <!-- Currency Filter -->
                <div class="space-y-3">
                    <label class="block text-sm font-semibold text-gray-700">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                            Currency
                        </span>
                    </label>
                    <select id="currency-filter" class="w-full px-4 py-3 bg-white border-2 border-gray-200 rounded-xl shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 text-sm font-medium">
                        <option value="all">💰 All Currencies</option>
                        @foreach($filterOptions['currencies'] as $currency)
                            <option value="{{ $currency }}">{{ $currency === 'AED' ? '🇦🇪 AED' : '🇺🇸 USD' }} {{ $currency }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Customer Filter -->
                <div class="space-y-3">
                    <label class="block text-sm font-semibold text-gray-700">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Customer
                        </span>
                    </label>
                    <select id="customer-filter" class="w-full px-4 py-3 bg-white border-2 border-gray-200 rounded-xl shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 text-sm font-medium">
                        <option value="all">👥 All Customers</option>
                        @foreach($filterOptions['customers'] as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Category Filter -->
                <div class="space-y-3">
                    <label class="block text-sm font-semibold text-gray-700">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            Product Category
                        </span>
                    </label>
                    <select id="category-filter" class="w-full px-4 py-3 bg-white border-2 border-gray-200 rounded-xl shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 text-sm font-medium">
                        <option value="all">📦 All Categories</option>
                        @foreach($filterOptions['categories'] as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Date Range -->
                <div class="space-y-3">
                    <label class="block text-sm font-semibold text-gray-700">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Date Range
                        </span>
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <input type="date" id="start-date" class="px-4 py-3 bg-white border-2 border-gray-200 rounded-xl shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 text-sm font-medium">
                        <input type="date" id="end-date" class="px-4 py-3 bg-white border-2 border-gray-200 rounded-xl shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 text-sm font-medium">
                    </div>
                </div>

                <!-- Export Buttons -->
                <div class="space-y-3">
                    <label class="block text-sm font-semibold text-gray-700">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Export Options
                        </span>
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <button id="export-png" class="inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold rounded-xl shadow-lg hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-200 transition-all duration-200 transform hover:scale-105">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            PNG
                        </button>
                        <button id="export-pdf" class="inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white font-semibold rounded-xl shadow-lg hover:from-red-600 hover:to-red-700 focus:outline-none focus:ring-4 focus:ring-red-200 transition-all duration-200 transform hover:scale-105">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Chart -->
    <div class="mb-8">
        <div class="overflow-hidden rounded-xl bg-white px-6 py-8 shadow-sm ring-1 ring-gray-900/5">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Sales Trends Analysis</h3>
                    <p class="text-sm text-gray-600 mt-1">Interactive sales data from {{ $salesData['labels'][0] ?? 'N/A' }} to {{ end($salesData['labels']) ?? 'N/A' }}</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-sm text-gray-500">
                        <span class="font-medium">Total Sales:</span>
                        <span id="total-sales" class="ml-1 font-semibold text-gray-900">Loading...</span>
                    </div>
                    <div class="text-sm text-gray-500">
                        <span class="font-medium">Periods:</span>
                        <span id="period-count" class="ml-1 font-semibold text-gray-900">-</span>
                    </div>
                </div>
            </div>

            <!-- Loading indicator -->
            <div id="chart-loading" class="flex items-center justify-center h-96">
                <div class="flex items-center space-x-2">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
                    <span class="text-gray-600">Loading chart data...</span>
                </div>
            </div>

            <!-- Chart container -->
            <div id="chart-container" class="relative h-96" style="display: none;">
                <canvas id="salesChart"></canvas>
            </div>

            <!-- No data message -->
            <div id="no-data-message" class="flex items-center justify-center h-96" style="display: none;">
                <div class="text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No sales data</h3>
                    <p class="mt-1 text-sm text-gray-500">Try adjusting your filters to see sales data.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Peak Sales Analysis -->
    @if(count($salesData['peak_months']) > 0)
    <div class="mb-8">
        <div class="overflow-hidden rounded-xl bg-white px-6 py-8 shadow-sm ring-1 ring-gray-900/5">
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Peak Sales Analysis</h3>
                <p class="text-sm text-gray-600 mt-1">Months with highest sales performance</p>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($salesData['peak_months'] as $peakMonth)
                <div class="rounded-lg bg-yellow-50 px-4 py-3 border border-yellow-200">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-yellow-400 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />
                                </svg>
                        <span class="text-sm font-medium text-yellow-800">{{ $peakMonth }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Zero Sales Alert -->
    @if(count($salesData['zero_months']) > 0)
    <div class="mb-8">
        <div class="overflow-hidden rounded-xl bg-white px-6 py-8 shadow-sm ring-1 ring-gray-900/5">
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Zero Sales Alert</h3>
                <p class="text-sm text-gray-600 mt-1">Months with no sales activity requiring attention</p>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($salesData['zero_months'] as $zeroMonth)
                <div class="rounded-lg bg-red-50 px-4 py-3 border border-red-200">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-red-400 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                </svg>
                        <span class="text-sm font-medium text-red-800">{{ $zeroMonth }}</span>
                    </div>
            </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let salesChart = null;
    let currentFilters = {
        period: 'monthly',
        currency: 'all',
        customer_id: 'all',
        category_id: 'all',
        start_date: null,
        end_date: null
    };

    // Initialize date inputs
    initializeDateInputs();
    
    // Load initial data
    loadChartData();

    // Event listeners
    document.querySelectorAll('.period-toggle').forEach(button => {
        button.addEventListener('click', function() {
            document.querySelectorAll('.period-toggle').forEach(btn => btn.classList.remove('active', 'bg-white', 'text-gray-900', 'shadow-sm'));
            this.classList.add('active', 'bg-white', 'text-gray-900', 'shadow-sm');
            currentFilters.period = this.dataset.period;
            loadChartData();
        });
    });

    document.getElementById('currency-filter').addEventListener('change', function() {
        currentFilters.currency = this.value;
        loadChartData();
    });

    document.getElementById('customer-filter').addEventListener('change', function() {
        currentFilters.customer_id = this.value;
        loadChartData();
    });

    document.getElementById('category-filter').addEventListener('change', function() {
        currentFilters.category_id = this.value;
        loadChartData();
    });

    document.getElementById('start-date').addEventListener('change', function() {
        currentFilters.start_date = this.value;
        loadChartData();
    });

    document.getElementById('end-date').addEventListener('change', function() {
        currentFilters.end_date = this.value;
        loadChartData();
    });

    // Export functionality
    document.getElementById('export-png').addEventListener('click', exportChartAsPNG);
    document.getElementById('export-pdf').addEventListener('click', exportChartAsPDF);

    function initializeDateInputs() {
        const endDate = new Date();
        const startDate = new Date();
        
        // Set default date range based on current period
        switch (currentFilters.period) {
            case 'daily':
                startDate.setDate(endDate.getDate() - 30);
                break;
            case 'quarterly':
                startDate.setMonth(endDate.getMonth() - 12);
                break;
            default: // monthly
                startDate.setMonth(endDate.getMonth() - 12);
                break;
        }
        
        document.getElementById('start-date').value = startDate.toISOString().split('T')[0];
        document.getElementById('end-date').value = endDate.toISOString().split('T')[0];
        
        currentFilters.start_date = startDate.toISOString().split('T')[0];
        currentFilters.end_date = endDate.toISOString().split('T')[0];
    }

    function loadChartData() {
        showLoading();
        
        const params = new URLSearchParams();
        Object.keys(currentFilters).forEach(key => {
            if (currentFilters[key] !== null && currentFilters[key] !== '') {
                params.append(key, currentFilters[key]);
            }
        });

        fetch(`{{ route('admin.dashboard.sales-data') }}?${params.toString()}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateChart(data.data);
                    updateSummary(data.data.summary);
                } else {
                    showNoData();
                }
            })
            .catch(error => {
                console.error('Error loading chart data:', error);
                showNoData();
            });
    }

    function updateChart(data) {
        const ctx = document.getElementById('salesChart').getContext('2d');
        
        if (salesChart) {
            salesChart.destroy();
        }

        // Process data for Chart.js
        const processedData = processChartData(data);
        
        if (processedData.datasets.length === 0) {
            showNoData();
            return;
        }

        salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: processedData.datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: getChartTitle(),
                        font: {
                            size: 16,
                            weight: 'bold'
                        },
                        color: '#374151'
                    },
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                size: 12,
                                weight: '500'
                            }
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#e5e7eb',
                        borderWidth: 1,
                        cornerRadius: 8,
                        displayColors: true,
                        callbacks: {
                            label: function(context) {
                                const value = context.parsed.y;
                                const currency = context.dataset.label.includes('USD') ? 'USD' : 'AED';
                                return `${context.dataset.label}: ${currency} ${value.toLocaleString()}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        display: true,
                        title: {
                            display: true,
                            text: getXAxisTitle(),
                            font: {
                                size: 14,
                                weight: 'bold'
                            },
                            color: '#374151'
                        },
                        grid: {
                            display: true,
                            color: 'rgba(0, 0, 0, 0.1)'
                        },
                        ticks: {
                            color: '#6b7280',
                            font: {
                                size: 11
                            }
                        }
                    },
                    y: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Sales Amount',
                            font: {
                                size: 14,
                                weight: 'bold'
                            },
                            color: '#374151'
                        },
                        grid: {
                            display: true,
                            color: 'rgba(0, 0, 0, 0.1)'
                        },
                        ticks: {
                            color: '#6b7280',
                            font: {
                                size: 11
                            },
                            callback: function(value) {
                                return value.toLocaleString();
                            }
                        }
                    }
                },
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false
                },
                elements: {
                    point: {
                        hoverBackgroundColor: '#fff'
                    }
                }
            }
        });

        showChart();
    }

    function processChartData(data) {
        const datasets = [];
        
        // This is a simplified version - in a real implementation, you'd process the actual data
        // For now, we'll use the original data structure
        if (data.datasets && data.datasets.length > 0) {
            return { datasets: data.datasets };
        }
        
        // Fallback to original data if new format is not available
        return {
            datasets: [
                {
                    label: 'Final Sales (AED)',
                    data: {!! json_encode($salesData['aed_data']) !!},
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    borderWidth: 3,
                    fill: false,
                    tension: 0.4,
                    pointBackgroundColor: 'rgb(34, 197, 94)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8
                },
                {
                    label: 'Final Sales (USD)',
                    data: {!! json_encode($salesData['usd_data']) !!},
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 3,
                    fill: false,
                    tension: 0.4,
                    pointBackgroundColor: 'rgb(59, 130, 246)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8
                },
                {
                    label: 'Proforma Only (AED)',
                    data: {!! json_encode($salesData['proforma_data'] ?? []) !!},
                    borderColor: 'rgb(251, 146, 60)',
                    backgroundColor: 'rgba(251, 146, 60, 0.1)',
                    borderWidth: 2,
                    fill: false,
                    tension: 0.4,
                    pointBackgroundColor: 'rgb(251, 146, 60)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    borderDash: [10, 5]
                },
                {
                    label: 'Total Sales (AED)',
                    data: {!! json_encode($salesData['combined_data']) !!},
                    borderColor: 'rgb(147, 51, 234)',
                    backgroundColor: 'rgba(147, 51, 234, 0.1)',
                    borderWidth: 4,
                    fill: false,
                    tension: 0.4,
                    pointBackgroundColor: 'rgb(147, 51, 234)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 3,
                    pointRadius: 7,
                    pointHoverRadius: 9,
                    borderDash: [5, 5]
                }
            ]
        };
    }

    function updateSummary(summary) {
        document.getElementById('total-sales').textContent = `AED ${summary.total.toLocaleString()}`;
        document.getElementById('period-count').textContent = summary.count;
    }

    function getChartTitle() {
        const period = currentFilters.period.charAt(0).toUpperCase() + currentFilters.period.slice(1);
        return `${period} Sales Trends`;
    }

    function getXAxisTitle() {
        switch (currentFilters.period) {
            case 'daily': return 'Date';
            case 'quarterly': return 'Quarter';
            default: return 'Month';
        }
    }

    function showLoading() {
        document.getElementById('chart-loading').style.display = 'flex';
        document.getElementById('chart-container').style.display = 'none';
        document.getElementById('no-data-message').style.display = 'none';
    }

    function showChart() {
        document.getElementById('chart-loading').style.display = 'none';
        document.getElementById('chart-container').style.display = 'block';
        document.getElementById('no-data-message').style.display = 'none';
    }

    function showNoData() {
        document.getElementById('chart-loading').style.display = 'none';
        document.getElementById('chart-container').style.display = 'none';
        document.getElementById('no-data-message').style.display = 'flex';
        document.getElementById('total-sales').textContent = 'AED 0';
        document.getElementById('period-count').textContent = '0';
    }

    function exportChartAsPNG() {
        if (!salesChart) return;
        
        const canvas = document.getElementById('salesChart');
        const link = document.createElement('a');
        link.download = `sales-chart-${currentFilters.period}-${new Date().toISOString().split('T')[0]}.png`;
        link.href = canvas.toDataURL();
        link.click();
    }

    function exportChartAsPDF() {
        if (!salesChart) return;
        
        const canvas = document.getElementById('salesChart');
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF('landscape', 'mm', 'a4');
        
        const imgData = canvas.toDataURL('image/png');
        const imgWidth = 280;
        const imgHeight = (canvas.height * imgWidth) / canvas.width;
        
        pdf.addImage(imgData, 'PNG', 10, 10, imgWidth, imgHeight);
        pdf.save(`sales-chart-${currentFilters.period}-${new Date().toISOString().split('T')[0]}.pdf`);
    }
});
</script>

<style>
/* Custom styles for better chart appearance */
#salesChart {
    max-height: 400px;
}

/* Enhanced Period toggle button styles */
.period-toggle {
    color: #6b7280;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.period-toggle:hover {
    color: #374151;
    transform: translateY(-1px);
}

.period-toggle.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
    transform: translateY(-2px);
}

.period-toggle.active::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
}

.period-toggle.active:hover::before {
    left: 100%;
}

/* Enhanced form controls */
select, input[type="date"] {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

select:focus, input[type="date"]:focus {
    transform: translateY(-1px);
    box-shadow: 0 10px 25px rgba(99, 102, 241, 0.15);
}

/* Enhanced export buttons */
#export-png, #export-pdf {
    position: relative;
    overflow: hidden;
}

#export-png::before, #export-pdf::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
}

#export-png:hover::before, #export-pdf:hover::before {
    left: 100%;
}

/* Filter section animations */
.filter-section {
    animation: slideInUp 0.6s ease-out;
}

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Gradient background animation */
.bg-gradient-to-r {
    background-size: 200% 200%;
    animation: gradientShift 8s ease infinite;
}

@keyframes gradientShift {
    0% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
    100% {
        background-position: 0% 50%;
    }
}

/* Loading animation */
@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.animate-spin {
    animation: spin 1s linear infinite;
}

/* Enhanced responsive design for filters */
@media (max-width: 1024px) {
    .grid.grid-cols-1.lg\\:grid-cols-2.xl\\:grid-cols-3 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 768px) {
    .grid.grid-cols-1.lg\\:grid-cols-2.xl\\:grid-cols-3 {
        grid-template-columns: repeat(1, minmax(0, 1fr));
    }
    
    .filter-section .p-8 {
        padding: 1.5rem;
    }
    
    .period-toggle {
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
    }
    
    .grid.grid-cols-2.gap-3 {
        grid-template-columns: repeat(1, minmax(0, 1fr));
        gap: 0.75rem;
    }
}

@media (max-width: 640px) {
    .filter-section .p-8 {
        padding: 1rem;
    }
    
    .period-toggle span {
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .period-toggle svg {
        margin-right: 0;
        margin-bottom: 0.25rem;
    }
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .grid-cols-1 {
        grid-template-columns: repeat(1, minmax(0, 1fr));
    }
}

@media (min-width: 641px) and (max-width: 1024px) {
    .sm\\:grid-cols-2 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (min-width: 1025px) {
    .lg\\:grid-cols-4 {
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }
    
    .lg\\:grid-cols-3 {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
}
</style>
@endcan
@endsection