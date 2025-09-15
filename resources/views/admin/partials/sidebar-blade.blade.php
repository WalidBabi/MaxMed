<!-- Sidebar component with Feature-Based Access Control -->
<div class="flex grow flex-col gap-y-5 overflow-y-auto crm-sidebar px-6 pb-4 shadow-lg">
    <!-- Logo -->
    <div class="flex h-16 shrink-0 items-center">
        <img class="mb-1 h-12 w-auto" src="{{ asset('Images/logo.png') }}" alt="MaxMed">
        <div class="ml-3">
            <div class="text-xs font-semibold text-gray-600">{{ \App\Helpers\DashboardHelper::adminPortalHeaderName() }}</div>
        </div>
    </div>
    
    <!-- Navigation -->
    <nav class="flex flex-1 flex-col">
        <ul role="list" class="flex flex-1 flex-col gap-y-7">
            <li>
                <ul role="list" class="-mx-2 space-y-1">
                    <!-- Primary Navigation - Always show for all users, redirects to appropriate landing page -->
                    <li class="menu-item">
                        <a href="{{ \App\Services\UserRedirectService::getRedirectUrl() }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.dashboard') ? 'sidebar-active' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }}">
                            <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                            </svg>
                            {{ \App\Services\UserRedirectService::getLandingPageTitle() }}
                        </a>
                    </li>

                    <!-- Sales Management - Show for users with sales permissions (superadmins always allowed) -->
                    @if((\App\Services\AccessControlService::canAccess(Auth::user(), 'sales.orders.view') || \App\Services\AccessControlService::canAccess(Auth::user(), 'sales.quotations.view')) && (Auth::user()->isAdmin() || !Auth::user()->hasPermission('purchasing.dashboard.access')))
                    <li x-data="{ open: {{ request()->routeIs('admin.quotes.*', 'admin.invoices.*', 'admin.orders.*', 'admin.deliveries.*', 'admin.cash-receipts.*', 'admin.sales-targets.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" class="group flex w-full items-center gap-x-3 rounded-md p-2 text-left text-sm leading-6 font-semibold text-gray-700 hover:text-indigo-600 hover:bg-gray-50">
                            <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                            </svg>
                            Sales Management
                            <svg :class="{ 'rotate-90': open }" class="ml-auto h-5 w-5 shrink-0 transition-transform duration-200" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <ul x-show="open" x-transition class="mt-1 px-2 space-y-1">
                            @if(\App\Services\AccessControlService::canAccess(Auth::user(), 'sales.quotations.view'))
                            <li><a href="{{ route('admin.quotes.index') }}" class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('admin.quotes.*') ? 'text-indigo-600 bg-gray-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">Quotes</a></li>
                            @endif
                            @if(\App\Services\AccessControlService::canAccess(Auth::user(), 'sales.orders.view'))
                            <li><a href="{{ route('admin.invoices.index') }}" class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('admin.invoices.*') ? 'text-indigo-600 bg-gray-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">Invoices</a></li>
                            <li><a href="{{ route('admin.orders.index') }}" class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('admin.orders.*') ? 'text-indigo-600 bg-gray-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">Orders</a></li>
                            <li><a href="{{ route('admin.deliveries.index') }}" class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('admin.deliveries.*') ? 'text-indigo-600 bg-gray-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">Deliveries</a></li>
                            <li><a href="{{ route('admin.cash-receipts.index') }}" class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('admin.cash-receipts.*') ? 'text-indigo-600 bg-gray-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">Cash Receipts</a></li>
                            <li><a href="{{ route('admin.sales-targets.index') }}" class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('admin.sales-targets.*') ? 'text-indigo-600 bg-gray-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">Sales Targets</a></li>
                            @endif
                        </ul>
                    </li>
                    @endif

                    <!-- Suppliers Management -->
                    @if(\App\Services\AccessControlService::canAccess(Auth::user(), 'suppliers.view') || \App\Services\AccessControlService::canAccess(Auth::user(), 'purchasing.orders.view'))
                    <li x-data="{ open: {{ request()->routeIs('admin.supplier-categories.*', 'admin.supplier-invitations.*', 'admin.supplier-profiles.*', 'admin.inquiries.*', 'admin.quotations.*', 'admin.inquiry-quotations.*', 'admin.purchase-orders.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" class="group flex w-full items-center gap-x-3 rounded-md p-2 text-left text-sm leading-6 font-semibold text-gray-700 hover:text-indigo-600 hover:bg-gray-50">
                            <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.75m-16.5 0H2.25m0 0h-.75a.75.75 0 01-.75-.75V6.75a.75.75 0 01.75-.75H21a.75.75 0 01.75.75V19.5a.75.75 0 01-.75.75H2.25z" />
                            </svg>
                            @if(\App\Services\AccessControlService::canAccess(Auth::user(), 'suppliers.view'))
                            Suppliers Management
                            @elseif(\App\Services\AccessControlService::canAccess(Auth::user(), 'purchasing.orders.view'))
                            Purchasing Management
                            @endif
                            <svg :class="{ 'rotate-90': open }" class="ml-auto h-5 w-5 shrink-0 transition-transform duration-200" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <ul x-show="open" x-transition class="mt-1 px-2 space-y-1">
                            @if(\App\Services\AccessControlService::canAccess(Auth::user(), 'suppliers.view'))
                            <li><a href="{{ route('admin.supplier-invitations.index') }}" class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('admin.supplier-invitations.*') ? 'text-indigo-600 bg-gray-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">Supplier Invitations</a></li>
                            <li><a href="{{ route('admin.supplier-profiles.index') }}" class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('admin.supplier-profiles.*') ? 'text-indigo-600 bg-gray-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">Supplier Profiles</a></li>
                            <li><a href="{{ route('admin.supplier-categories.index') }}" class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('admin.supplier-categories.*') ? 'text-indigo-600 bg-gray-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">Supplier Categories</a></li>
                            @endif
                            @if(\App\Services\AccessControlService::canAccess(Auth::user(), 'purchasing.quotations.view'))
                            <li><a href="{{ route('admin.inquiry-quotations.index') }}" class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('admin.inquiry-quotations.*') || request()->routeIs('admin.inquiries.*') || request()->routeIs('admin.quotations.*') ? 'text-indigo-600 bg-gray-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">Inquiries & Quotations</a></li>
                            @endif
                            @if(\App\Services\AccessControlService::canAccess(Auth::user(), 'purchasing.orders.view'))
                            <li><a href="{{ route('admin.purchase-orders.index') }}" class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('admin.purchase-orders.*') ? 'text-indigo-600 bg-gray-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">Purchase Orders</a></li>
                            @endif
                        </ul>
                    </li>
                    @endif

                    <!-- Catalog Management -->
                    @if(\App\Services\AccessControlService::canAccess(Auth::user(), 'products.view') || \App\Services\AccessControlService::canAccess(Auth::user(), 'categories.view') || \App\Services\AccessControlService::canAccess(Auth::user(), 'brands.view'))
                    <li x-data="{ open: {{ request()->routeIs('admin.products.*', 'admin.categories.*', 'admin.brands.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" class="group flex w-full items-center gap-x-3 rounded-md p-2 text-left text-sm leading-6 font-semibold text-gray-700 hover:text-indigo-600 hover:bg-gray-50">
                            <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                            </svg>
                            Catalog Management
                            <svg :class="{ 'rotate-90': open }" class="ml-auto h-5 w-5 shrink-0 transition-transform duration-200" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <ul x-show="open" x-transition class="mt-1 px-2 space-y-1">
                            @if(\App\Services\AccessControlService::canAccess(Auth::user(), 'products.view'))
                            <li><a href="{{ route('admin.products.index') }}" class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('admin.products.*') ? 'text-indigo-600 bg-gray-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">Products</a></li>
                            @endif
                            @if(\App\Services\AccessControlService::canAccess(Auth::user(), 'categories.view'))
                            <li><a href="{{ route('admin.categories.index') }}" class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('admin.categories.*') ? 'text-indigo-600 bg-gray-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">Categories</a></li>
                            @endif
                            @if(\App\Services\AccessControlService::canAccess(Auth::user(), 'brands.view'))
                            <li><a href="{{ route('admin.brands.index') }}" class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('admin.brands.*') ? 'text-indigo-600 bg-gray-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">Brands</a></li>
                            @endif
                        </ul>
                    </li>
                    @endif

                    <!-- Content Management -->
                    @if(\App\Services\AccessControlService::canAccess(Auth::user(), 'news.manage'))
                    <li>
                        <a href="{{ route('admin.news.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.news.*') ? 'sidebar-active' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }}">
                            <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5M5.25 19.5a2.25 2.25 0 01-2.25-2.25V6.75A2.25 2.25 0 015.25 4.5h13.5A2.25 2.25 0 0121 6.75v10.5a2.25 2.25 0 01-2.25 2.25H5.25z" />
                            </svg>
                            News & Content
                        </a>
                    </li>
                    @endif

                    <!-- Feedback Management -->
                    @if(\App\Services\AccessControlService::canAccess(Auth::user(), 'feedback.view'))
                    <li>
                        <a href="{{ route('admin.feedback.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.feedback.*') ? 'sidebar-active' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }}">
                            <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.76 1.24 3.25 2.91 3.54v2.13a.75.75 0 001.02.69L8.16 14.1c.26-.08.53-.1.8-.1h5.42c.47 0 .93-.06 1.36-.17 1.01-.26 1.76-1.15 1.76-2.25V8.46C17.5 6.7 16.26 5.25 14.59 5.25H5.91C4.24 5.25 3 6.7 3 8.46v3.12z" />
                            </svg>
                            Feedback
                        </a>
                    </li>
                    @endif

                    <!-- Analytics & Insights -->
                    @if(\App\Services\AccessControlService::canAccess(Auth::user(), 'dashboard.analytics') || \App\Services\AccessControlService::canAccess(Auth::user(), 'analytics.view'))
                    <li x-data="{ open: {{ request()->routeIs('admin.analytics.*', 'admin.user-behavior.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" class="group flex w-full items-center gap-x-3 rounded-md p-2 text-left text-sm leading-6 font-semibold text-gray-700 hover:text-indigo-600 hover:bg-gray-50">
                            <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                            </svg>
                            Analytics & Insights
                            <svg :class="{ 'rotate-90': open }" class="ml-auto h-5 w-5 shrink-0 transition-transform duration-200" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <ul x-show="open" x-transition class="mt-1 px-2 space-y-1">
                            @if(\App\Services\AccessControlService::canAccess(Auth::user(), 'dashboard.analytics'))
                            <li><a href="{{ route('admin.analytics.dashboard') }}" class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('admin.analytics.*') ? 'text-indigo-600 bg-gray-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">Sales Analytics Dashboard</a></li>
                            @endif
                            @if(\App\Services\AccessControlService::canAccess(Auth::user(), 'analytics.view'))
                            <li><a href="{{ route('admin.user-behavior.index') }}" class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('admin.user-behavior.*') ? 'text-indigo-600 bg-gray-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">User Behavior Analytics</a></li>
                            @endif
                        </ul>
                    </li>
                    @endif

                    <!-- User Management -->
                    @if(\App\Services\AccessControlService::canAccess(Auth::user(), 'admin.users.manage') || \App\Services\AccessControlService::canAccess(Auth::user(), 'admin.roles.manage'))
                    <li x-data="{ open: {{ request()->routeIs('admin.users.*', 'admin.roles.*', 'admin.permissions.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" class="group flex w-full items-center gap-x-3 rounded-md p-2 text-left text-sm leading-6 font-semibold text-gray-700 hover:text-indigo-600 hover:bg-gray-50">
                            <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                            </svg>
                            User Management
                            <svg :class="{ 'rotate-90': open }" class="ml-auto h-5 w-5 shrink-0 transition-transform duration-200" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <ul x-show="open" x-transition class="mt-1 px-2 space-y-1">
                            @if(\App\Services\AccessControlService::canAccess(Auth::user(), 'admin.users.manage'))
                            <li><a href="{{ route('admin.users.index') }}" class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('admin.users.*') ? 'text-indigo-600 bg-gray-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">Users</a></li>
                            @endif
                            @if(\App\Services\AccessControlService::canAccess(Auth::user(), 'admin.roles.manage'))
                            <li><a href="{{ route('admin.roles.index') }}" class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('admin.roles.*') ? 'text-indigo-600 bg-gray-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">Roles</a></li>
                            <li><a href="{{ route('admin.permissions.index') }}" class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('admin.permissions.*') ? 'text-indigo-600 bg-gray-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">Permissions</a></li>
                            @endif
                        </ul>
                    </li>
                    @endif
                </ul>
            </li>

            <!-- Settings -->
            <li class="mt-auto">
                <ul role="list" class="-mx-2 space-y-1">
                    @if(\App\Services\AccessControlService::canAccessCrm(Auth::user()))
                    <li>
                        <a href="{{ route('crm.dashboard') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold text-gray-700 hover:text-indigo-600 hover:bg-gray-50">
                            <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                            </svg>
                            {{ \App\Helpers\DashboardHelper::crmPortalHeaderName() }}
                        </a>
                    </li>
                    @endif
                    @if(\App\Services\AccessControlService::canAccess(Auth::user(), 'supplier.dashboard'))
                    <li>
                        <a href="{{ route('supplier.dashboard') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold text-gray-700 hover:text-indigo-600 hover:bg-gray-50">
                            <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                            </svg>
                            {{ \App\Helpers\DashboardHelper::supplierPortalHeaderName() }}
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
        </ul>
    </nav>
</div>