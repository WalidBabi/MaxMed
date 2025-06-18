@extends('admin.layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="-mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8">
    
    {{-- Dashboard Header --}}
    <x-dashboard-header 
        title="Welcome to MaxMed Admin"
        subtitle="Manage your store operations and business processes"
    />

    {{-- Sales Section --}}
    <x-dashboard-section title="Sales">
        <x-metric-card
            title="Customers"
            value="View & Manage"
            :icon="'<x-icons name=\"users\" class=\"h-8 w-8\" />'"
            subtitle="Customer database"
            href="{{ route('crm.customers.index') }}"
            color="blue"
        />

        <x-metric-card
            title="Quotes"
            value="Create & Send"
            :icon="'<x-icons name=\"documents\" class=\"h-8 w-8\" />'"
            subtitle="Price quotations"
            href="{{ route('admin.quotes.index') }}"
            color="green"
        />

        <x-metric-card
            title="Invoices"
            value="Billing & Payments"
            :icon="'<x-icons name=\"invoices\" class=\"h-8 w-8\" />'"
            subtitle="Financial documents"
            href="{{ route('admin.invoices.index') }}"
            color="yellow"
        />

        <x-metric-card
            title="Orders"
            value="Process & Track"
            :icon="'<x-icons name=\"shopping-cart\" class=\"h-8 w-8\" />'"
            subtitle="Order management"
            href="{{ route('admin.orders.index') }}"
            color="red"
        />
    </x-dashboard-section>

    {{-- Operations Section --}}
    <x-dashboard-section title="Operations" :columns="4">
        <x-metric-card
            title="Deliveries"
            value="Track Shipments"
            :icon="'<x-icons name=\"truck\" class=\"h-8 w-8\" />'"
            subtitle="Logistics management"
            href="{{ route('admin.deliveries.index') }}"
            color="blue"
        />
    </x-dashboard-section>

    {{-- User Management Section --}}
    <x-dashboard-section title="User Management">
        <x-metric-card
            title="Users"
            value="Manage Accounts"
            :icon="'<x-icons name=\"users\" class=\"h-8 w-8\" />'"
            subtitle="User administration"
            href="{{ route('admin.users.index') }}"
            color="green"
        />

        <x-metric-card
            title="Roles"
            value="Permissions"
            :icon="'<x-icons name=\"tag\" class=\"h-8 w-8\" />'"
            subtitle="Access control"
            href="{{ route('admin.roles.index') }}"
            color="yellow"
        />
    </x-dashboard-section>

    {{-- Catalog Management Section --}}
    <x-dashboard-section title="Catalog Management">
        <x-metric-card
            title="Products"
            value="Inventory"
            :icon="'<x-icons name=\"cube\" class=\"h-8 w-8\" />'"
            subtitle="Product catalog"
            href="{{ route('admin.products.index') }}"
            color="red"
        />

        <x-metric-card
            title="Categories"
            value="Organization"
            :icon="'<x-icons name=\"squares\" class=\"h-8 w-8\" />'"
            subtitle="Product grouping"
            href="{{ route('admin.categories.index') }}"
            color="blue"
        />

        <x-metric-card
            title="Brands"
            value="Manufacturers"
            :icon="'<x-icons name=\"tag\" class=\"h-8 w-8\" />'"
            subtitle="Brand management"
            href="{{ route('admin.brands.index') }}"
            color="green"
        />

        <x-metric-card
            title="News"
            value="Content & Updates"
            :icon="'<x-icons name=\"documents\" class=\"h-8 w-8\" />'"
            subtitle="News management"
            href="{{ route('admin.news.index') }}"
            color="purple"
        />
    </x-dashboard-section>

</div>
@endsection 