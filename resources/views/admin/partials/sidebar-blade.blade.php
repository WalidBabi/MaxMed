<!-- Sidebar component with Feature-Based Access Control -->
<div class="flex grow flex-col gap-y-5 overflow-y-auto crm-sidebar px-6 pb-4 shadow-lg">
    <!-- Logo -->
    <div class="flex h-16 shrink-0 items-center">
        <img class="mb-1 h-12 w-auto" src="{{ asset('Images/logo.png') }}" alt="MaxMed">
        <div class="ml-3">
            <div class="text-sm font-semibold text-gray-600">Admin Portal</div>
        </div>
    </div>
    
    <!-- Navigation -->
    <nav class="flex flex-1 flex-col">
        <ul role="list" class="flex flex-1 flex-col gap-y-7">
            <li>
                <ul role="list" class="-mx-2 space-y-1">
                    <!-- Dashboard -->
                    @canAccessFeature('dashboard.view')
                    <li class="menu-item">
                        <a href="{{ route('admin.dashboard') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.dashboard') ? 'sidebar-active' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }}">
                            <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                            </svg>
                            Dashboard
                        </a>
                    </li>
                    @endcanAccessFeature

                    <!-- Sales Management (Pure Sales Features Only) -->
                    @canAccessAnyFeature('cash_receipts.view', 'cash_receipts.create', 'sales_targets.view', 'sales_targets.create')
                    <li x-data="{ open: {{ request()->routeIs('admin.cash-receipts.*', 'admin.sales-targets.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" class="group flex w-full items-center gap-x-3 rounded-md p-2 text-left text-sm leading-6 font-semibold text-gray-700 hover:text-indigo-600 hover:bg-gray-50">
                            <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Sales Management
                            <svg :class="{ 'rotate-90': open }" class="ml-auto h-5 w-5 shrink-0 transition-transform duration-200" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <ul x-show="open" x-transition class="mt-1 px-2 space-y-1">
                            @canAccessFeature('cash_receipts.view')
                            <li><a href="{{ route('admin.cash-receipts.index') }}" class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('admin.cash-receipts.*') ? 'text-indigo-600 bg-gray-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">Cash Receipts</a></li>
                            @endcanAccessFeature
                            @canAccessFeature('sales_targets.view')
                            <li><a href="{{ route('admin.sales-targets.index') }}" class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('admin.sales-targets.*') ? 'text-indigo-600 bg-gray-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">Sales Targets</a></li>
                            @endcanAccessFeature
                        </ul>
                    </li>
                    @endcanAccessAnyFeature

                    <!-- Purchasing & Procurement -->
                    @canAccessAnyFeature('purchase_orders.view', 'purchase_orders.create', 'purchase_orders.edit', 'suppliers.view', 'suppliers.create', 'suppliers.edit', 'inquiries.view', 'inquiries.create', 'quotations.view', 'quotations.create', 'invoices.view', 'orders.view_all', 'deliveries.view')
                    <li x-data="{ open: {{ request()->routeIs('admin.supplier-payments.*', 'admin.supplier-categories.*', 'admin.supplier-invitations.*', 'admin.supplier-profiles.*', 'admin.inquiries.*', 'admin.quotations.*', 'admin.inquiry-quotations.*', 'admin.purchase-orders.*', 'admin.quotes.*', 'admin.invoices.*', 'admin.orders.*', 'admin.deliveries.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" class="group flex w-full items-center gap-x-3 rounded-md p-2 text-left text-sm leading-6 font-semibold text-gray-700 hover:text-indigo-600 hover:bg-gray-50">
                            <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.75m-16.5 0H2.25m0 0h-.75a.75.75 0 01-.75-.75V6.75a.75.75 0 01.75-.75H21a.75.75 0 01.75.75V19.5a.75.75 0 01-.75.75H2.25z" />
                            </svg>
                            Purchasing & Procurement
                            <svg :class="{ 'rotate-90': open }" class="ml-auto h-5 w-5 shrink-0 transition-transform duration-200" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <ul x-show="open" x-transition class="mt-1 px-2 space-y-1">
                            @canAccessFeature('suppliers.view')
                            <li><a href="{{ route('admin.supplier-invitations.index') }}" class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('admin.supplier-invitations.*') ? 'text-indigo-600 bg-gray-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">Supplier Invitations</a></li>
                            <li><a href="{{ route('admin.supplier-profiles.index') }}" class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('admin.supplier-profiles.*') ? 'text-indigo-600 bg-gray-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">Supplier Profiles</a></li>
                            <li><a href="{{ route('admin.supplier-categories.index') }}" class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('admin.supplier-categories.*') ? 'text-indigo-600 bg-gray-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">Supplier Categories</a></li>
                            @endcanAccessFeature
                            @canAccessAnyFeature('inquiries.view', 'inquiries.create', 'quotations.view', 'quotations.create')
                            <li><a href="{{ route('admin.inquiry-quotations.index') }}" class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('admin.inquiry-quotations.*') || request()->routeIs('admin.inquiries.*') || request()->routeIs('admin.quotations.*') ? 'text-indigo-600 bg-gray-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">Inquiries & Quotations</a></li>
                            @endcanAccessAnyFeature
                            @canAccessAnyFeature('purchase_orders.view', 'purchase_orders.create', 'purchase_orders.edit')
                            <li x-data="{ open: false }" class="relative">
                                <div class="flex items-center">
                                    <a href="{{ route('admin.purchase-orders.index') }}" class="group flex flex-1 gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('admin.purchase-orders.*') ? 'text-indigo-600 bg-gray-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">Purchase Orders</a>
                                    @canAccessAnyFeature('purchase_orders.create', 'purchase_orders.edit')
                                    <button @click="open = !open" class="p-1 rounded text-gray-400 hover:text-indigo-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                    </button>
                                    @endcanAccessAnyFeature
                                </div>
                                @canAccessAnyFeature('purchase_orders.create', 'purchase_orders.edit')
                                <ul x-show="open" x-transition class="mt-1 ml-4 space-y-1">
                                    @canAccessFeature('purchase_orders.create')
                                    <li><a href="{{ route('admin.purchase-orders.create') }}" class="block px-3 py-1 text-xs text-gray-500 hover:text-indigo-600">+ Create New</a></li>
                                    @endcanAccessFeature
                                </ul>
                                @endcanAccessAnyFeature
                            </li>
                            @endcanAccessAnyFeature
                            @canAccessFeature('suppliers.view')
                            <li><a href="{{ route('admin.supplier-payments.index') }}" class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('admin.supplier-payments.*') ? 'text-indigo-600 bg-gray-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">Supplier Payments</a></li>
                            @endcanAccessFeature
                            
                            <!-- Operational Items (Quotes, Invoices, Orders, Deliveries) -->
                            @canAccessAnyFeature('quotations.view', 'quotations.create', 'quotations.edit')
                            <li x-data="{ open: false }" class="relative">
                                <div class="flex items-center">
                                    <a href="{{ route('admin.quotes.index') }}" class="group flex flex-1 gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('admin.quotes.*') ? 'text-indigo-600 bg-gray-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">Quotes</a>
                                    @canAccessAnyFeature('quotations.create', 'quotations.edit')
                                    <button @click="open = !open" class="p-1 rounded text-gray-400 hover:text-indigo-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                    </button>
                                    @endcanAccessAnyFeature
                                </div>
                                @canAccessAnyFeature('quotations.create', 'quotations.edit')
                                <ul x-show="open" x-transition class="mt-1 ml-4 space-y-1">
                                    @canAccessFeature('quotations.create')
                                    <li><a href="{{ route('admin.quotes.create') }}" class="block px-3 py-1 text-xs text-gray-500 hover:text-indigo-600">+ Create New</a></li>
                                    @endcanAccessFeature
                                </ul>
                                @endcanAccessAnyFeature
                            </li>
                            @endcanAccessAnyFeature
                            @canAccessFeature('invoices.view')
                            <li><a href="{{ route('admin.invoices.index') }}" class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('admin.invoices.*') ? 'text-indigo-600 bg-gray-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">Invoices</a></li>
                            @endcanAccessFeature
                            @canAccessAnyFeature('orders.view_all', 'orders.create', 'orders.edit')
                            <li x-data="{ open: false }" class="relative">
                                <div class="flex items-center">
                                    <a href="{{ route('admin.orders.index') }}" class="group flex flex-1 gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('admin.orders.*') ? 'text-indigo-600 bg-gray-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">Orders</a>
                                    @canAccessAnyFeature('orders.create', 'orders.edit')
                                    <button @click="open = !open" class="p-1 rounded text-gray-400 hover:text-indigo-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                    </button>
                                    @endcanAccessAnyFeature
                                </div>
                                @canAccessAnyFeature('orders.create', 'orders.edit')
                                <ul x-show="open" x-transition class="mt-1 ml-4 space-y-1">
                                    @canAccessFeature('orders.create')
                                    <li><a href="{{ route('admin.orders.create') }}" class="block px-3 py-1 text-xs text-gray-500 hover:text-indigo-600">+ Create New</a></li>
                                    @endcanAccessFeature
                                </ul>
                                @endcanAccessAnyFeature
                            </li>
                            @endcanAccessAnyFeature
                            @canAccessFeature('deliveries.view')
                            <li><a href="{{ route('admin.deliveries.index') }}" class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('admin.deliveries.*') ? 'text-indigo-600 bg-gray-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">Deliveries</a></li>
                            @endcanAccessFeature
                        </ul>
                    </li>
                    @endcanAccessAnyFeature

                    <!-- Product Catalog Management -->
                    @canAccessAnyFeature('products.view', 'products.create', 'products.edit', 'products.delete', 'categories.view', 'categories.create', 'categories.edit', 'categories.delete', 'brands.view', 'brands.create', 'brands.edit', 'brands.delete')
                    <li x-data="{ open: {{ request()->routeIs('admin.products.*', 'admin.categories.*', 'admin.brands.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" class="group flex w-full items-center gap-x-3 rounded-md p-2 text-left text-sm leading-6 font-semibold text-gray-700 hover:text-indigo-600 hover:bg-gray-50">
                            <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                            </svg>
                            Product Catalog
                            <svg :class="{ 'rotate-90': open }" class="ml-auto h-5 w-5 shrink-0 transition-transform duration-200" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <ul x-show="open" x-transition class="mt-1 px-2 space-y-1">
                            @canAccessFeature('products.view')
                            <li><a href="{{ route('admin.products.index') }}" class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('admin.products.*') ? 'text-indigo-600 bg-gray-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">Products</a></li>
                            @endcanAccessFeature
                            @canAccessFeature('categories.view')
                            <li><a href="{{ route('admin.categories.index') }}" class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('admin.categories.*') ? 'text-indigo-600 bg-gray-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">Categories</a></li>
                            @endcanAccessFeature
                            @canAccessFeature('brands.view')
                            <li><a href="{{ route('admin.brands.index') }}" class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('admin.brands.*') ? 'text-indigo-600 bg-gray-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">Brands</a></li>
                            @endcanAccessFeature
                        </ul>
                    </li>
                    @endcanAccessAnyFeature

                    <!-- CRM System -->
                    @canAccessAnyFeature('crm.access', 'customers.view', 'customers.create', 'customers.edit', 'crm.leads.view', 'crm.leads.create', 'crm.leads.edit', 'crm.contacts.view', 'crm.contacts.create', 'crm.contacts.edit', 'crm.activities.view', 'crm.activities.create', 'crm.tasks.view', 'crm.tasks.create')
                    <li x-data="{ open: {{ request()->routeIs('crm.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" class="group flex w-full items-center gap-x-3 rounded-md p-2 text-left text-sm leading-6 font-semibold text-gray-700 hover:text-indigo-600 hover:bg-gray-50">
                            <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                            </svg>
                            CRM System
                            <svg :class="{ 'rotate-90': open }" class="ml-auto h-5 w-5 shrink-0 transition-transform duration-200" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <ul x-show="open" x-transition class="mt-1 px-2 space-y-1">
                            @canAccessFeature('crm.access')
                            <li><a href="{{ route('crm.dashboard') }}" class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('crm.dashboard') ? 'text-indigo-600 bg-gray-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">CRM Dashboard</a></li>
                            @endcanAccessFeature
                            @canAccessFeature('customers.view')
                            <li><a href="{{ route('crm.customers.index') }}" class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('crm.customers.*') ? 'text-indigo-600 bg-gray-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">Customers</a></li>
                            @endcanAccessFeature
                            @canAccessFeature('crm.leads.view')
                            <li><a href="{{ route('crm.leads.index') }}" class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('crm.leads.*') ? 'text-indigo-600 bg-gray-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">Leads</a></li>
                            @endcanAccessFeature
                            @canAccessFeature('crm.contacts.view')
                            <li><a href="{{ route('crm.contacts.index') }}" class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('crm.contacts.*') ? 'text-indigo-600 bg-gray-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">Contacts</a></li>
                            @endcanAccessFeature
                            @canAccessFeature('crm.activities.view')
                            <li><a href="{{ route('crm.activities.index') }}" class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('crm.activities.*') ? 'text-indigo-600 bg-gray-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">Activities</a></li>
                            @endcanAccessFeature
                            @canAccessFeature('crm.tasks.view')
                            <li><a href="{{ route('crm.tasks.index') }}" class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('crm.tasks.*') ? 'text-indigo-600 bg-gray-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">Tasks</a></li>
                            @endcanAccessFeature
                        </ul>
                    </li>
                    @endcanAccessAnyFeature

                    <!-- User & Role Management -->
                    @canAccessAnyFeature('users.view', 'users.create', 'users.edit', 'users.delete', 'roles.view', 'roles.create', 'roles.edit', 'roles.delete')
                    <li x-data="{ open: {{ request()->routeIs('admin.users.*', 'admin.roles.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" class="group flex w-full items-center gap-x-3 rounded-md p-2 text-left text-sm leading-6 font-semibold text-gray-700 hover:text-indigo-600 hover:bg-gray-50">
                            <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                            </svg>
                            User Management
                            <svg :class="{ 'rotate-90': open }" class="ml-auto h-5 w-5 shrink-0 transition-transform duration-200" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <ul x-show="open" x-transition class="mt-1 px-2 space-y-1">
                            @canAccessFeature('users.view')
                            <li><a href="{{ route('admin.users.index') }}" class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('admin.users.*') ? 'text-indigo-600 bg-gray-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">Users</a></li>
                            @endcanAccessFeature
                            @canAccessFeature('roles.view')
                            <li><a href="{{ route('admin.roles.index') }}" class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('admin.roles.*') ? 'text-indigo-600 bg-gray-50' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">Roles & Permissions</a></li>
                            @endcanAccessFeature
                        </ul>
                    </li>
                    @endcanAccessAnyFeature

                    <!-- Feedback Management -->
                    @canAccessFeature('feedback.view')
                    <li>
                        <a href="{{ route('admin.feedback.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('admin.feedback.*') ? 'sidebar-active' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }}">
                            <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                            </svg>
                            Feedback Management
                        </a>
                    </li>
                    @endcanAccessFeature

                    <!-- Analytics -->
                    @canAccessFeature('analytics.view')
                    <li>
                        <a href="{{ route('crm.marketing.analytics.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('crm.marketing.analytics.*') ? 'sidebar-active' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }}">
                            <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                            </svg>
                            Analytics & Reports
                        </a>
                    </li>
                    @endcanAccessFeature
                </ul>
            </li>
        </ul>
    </nav>
</div>
