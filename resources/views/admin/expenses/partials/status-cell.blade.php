@php
    $now = \Illuminate\Support\Carbon::now();
    $currentYear = (int) $now->format('Y');
    $currentMonth = (int) $now->format('n');
@endphp

@if($expense->isActiveInMonth($currentMonth))
    @if($expense->isPaidForMonth($currentYear, $currentMonth))
        <span class="inline-flex items-center rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800">
            Paid
        </span>
    @else
        <form
            action="{{ route('admin.business-expenses.mark-paid', $expense) }}"
            method="POST"
            class="inline"
            data-ajax="form"
            data-loading-text="Marking..."
            data-success-message="Marked as paid."
            data-success-replace="#expense-status-{{ $expense->id }}"
        >
            @csrf
            <input type="hidden" name="year" value="{{ $currentYear }}">
            <input type="hidden" name="month" value="{{ $currentMonth }}">
            <button type="submit" class="text-indigo-600 hover:text-indigo-800 text-xs font-semibold">
                Mark as paid
            </button>
        </form>
    @endif
@else
    <span class="text-xs text-gray-400">Not due</span>
@endif

