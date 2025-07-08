@extends('admin.layouts.app')

@section('title', 'Create Sales Target')

@section('content')
<div class="-mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8">
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Create Sales Target</h1>
                <p class="text-gray-600 mt-2">Set up a new sales goal or target</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.sales-targets.index') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Targets
                </a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Target Details</h3>
            <p class="text-sm text-gray-600 mt-1">Fill in the details for your new sales target</p>
        </div>
        <form method="POST" action="{{ route('admin.sales-targets.store') }}" class="p-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Target Name -->
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Target Name *</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required 
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('name') border-red-500 @enderror" 
                           placeholder="e.g., Q1 Revenue Target">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" id="description" rows="3" 
                              class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('description') border-red-500 @enderror" 
                              placeholder="Optional description of the target">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Target Type -->
                <div>
                    <label for="target_type" class="block text-sm font-medium text-gray-700 mb-2">Target Type *</label>
                    <select name="target_type" id="target_type" required 
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('target_type') border-red-500 @enderror">
                        <option value="">Select Target Type</option>
                        @foreach(\App\Models\SalesTarget::TARGET_TYPES as $value => $label)
                            <option value="{{ $value }}" {{ old('target_type') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('target_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Period Type -->
                <div>
                    <label for="period_type" class="block text-sm font-medium text-gray-700 mb-2">Period Type *</label>
                    <select name="period_type" id="period_type" required 
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('period_type') border-red-500 @enderror">
                        <option value="">Select Period Type</option>
                        @foreach(\App\Models\SalesTarget::PERIOD_TYPES as $value => $label)
                            <option value="{{ $value }}" {{ old('period_type') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('period_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Start Date -->
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Start Date *</label>
                    <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}" required 
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('start_date') border-red-500 @enderror">
                    @error('start_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- End Date -->
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">End Date *</label>
                    <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}" required 
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('end_date') border-red-500 @enderror">
                    @error('end_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Target Amount -->
                <div>
                    <label for="target_amount" class="block text-sm font-medium text-gray-700 mb-2">Target Amount (AED) *</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">AED</span>
                        </div>
                        <input type="number" name="target_amount" id="target_amount" value="{{ old('target_amount') }}" 
                               step="0.01" min="0" required 
                               class="block w-full pl-12 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('target_amount') border-red-500 @enderror" 
                               placeholder="0.00">
                    </div>
                    @error('target_amount')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Assigned To -->
                <div>
                    <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-2">Assigned To</label>
                    <select name="assigned_to" id="assigned_to" 
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('assigned_to') border-red-500 @enderror">
                        <option value="">Unassigned</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('assigned_to')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="mt-8 flex items-center justify-end space-x-3">
                <a href="{{ route('admin.sales-targets.index') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Create Target
                </button>
            </div>
        </form>
    </div>
</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set default start date to today
    if (!document.getElementById('start_date').value) {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('start_date').value = today;
    }
    
    // Auto-calculate end date based on period type
    document.getElementById('period_type').addEventListener('change', function() {
        const startDate = document.getElementById('start_date').value;
        if (!startDate) return;
        
        const start = new Date(startDate);
        const periodType = this.value;
        let end = new Date(start);
        
        switch(periodType) {
            case 'daily':
                end.setDate(start.getDate() + 1);
                break;
            case 'weekly':
                end.setDate(start.getDate() + 7);
                break;
            case 'monthly':
                end.setMonth(start.getMonth() + 1);
                break;
            case 'quarterly':
                end.setMonth(start.getMonth() + 3);
                break;
            case 'yearly':
                end.setFullYear(start.getFullYear() + 1);
                break;
        }
        
        // Subtract one day to make it inclusive
        end.setDate(end.getDate() - 1);
        document.getElementById('end_date').value = end.toISOString().split('T')[0];
    });
    
    // Update end date when start date changes
    document.getElementById('start_date').addEventListener('change', function() {
        const periodType = document.getElementById('period_type').value;
        if (periodType) {
            document.getElementById('period_type').dispatchEvent(new Event('change'));
        }
    });
});
</script>
@endsection 