@extends('admin.layouts.app')

@section('title', 'Expense Forecast')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Expense Forecast</h1>
        <p class="text-gray-600">Projected totals for upcoming months.</p>
    </div>
    <a href="{{ route('admin.business-expenses.index') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Back</a>
    </div>

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($forecast as $key => $month)
            <div class="rounded-lg bg-white p-4 shadow">
                <div class="mb-3 flex items-center justify-between">
                    <div class="text-sm font-medium text-gray-700">{{ $month['date']->format('F Y') }}</div>
                    <div class="text-sm font-semibold text-gray-900">{{ number_format($month['total'], 2) }} AED</div>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($month['items'] as $item)
                        <div class="py-2 text-sm flex items-center justify-between">
                            <div>
                                <div class="font-medium text-gray-900">{{ $item['name'] }}</div>
                                <div class="text-gray-500">{{ $item['vendor'] }}</div>
                            </div>
                            <div class="text-gray-900">{{ number_format($item['amount'], 2) }} {{ $item['currency'] }}</div>
                        </div>
                    @empty
                        <div class="py-4 text-sm text-gray-500">No expenses this month</div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
@endsection


