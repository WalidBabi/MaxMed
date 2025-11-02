@extends('admin.layouts.app')

@section('title', 'Edit Business Expense')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Edit Business Expense</h1>
    <p class="text-gray-600">Update the recurring expense template.</p>
    </div>

    <div class="rounded-lg bg-white p-6 shadow">
        <form method="POST" action="{{ route('admin.business-expenses.update', $expense) }}">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" name="name" value="{{ old('name', $expense->name) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                    @error('name')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Vendor</label>
                    <input type="text" name="vendor" value="{{ old('vendor', $expense->vendor) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    @error('vendor')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Unit Amount</label>
                    <input type="number" step="0.01" name="unit_amount" value="{{ old('unit_amount', $expense->unit_amount) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                    @error('unit_amount')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Quantity</label>
                    <input type="number" name="quantity" value="{{ old('quantity', $expense->quantity) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                    @error('quantity')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Currency</label>
                    <input type="text" name="currency" value="{{ old('currency', $expense->currency) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                    @error('currency')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Frequency</label>
                    <select name="frequency" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                        @php $freqs=['monthly'=>'Monthly','yearly'=>'Yearly','quarterly'=>'Quarterly','weekly'=>'Weekly']; @endphp
                        @foreach($freqs as $val=>$label)
                            <option value="{{ $val }}" {{ old('frequency', $expense->frequency)===$val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('frequency')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Repeats Every</label>
                    <input type="number" name="repeats_every" value="{{ old('repeats_every', $expense->repeats_every) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                    @error('repeats_every')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                        @php $statuses=['active'=>'Active','paused'=>'Paused','ended'=>'Ended']; @endphp
                        @foreach($statuses as $val=>$label)
                            <option value="{{ $val }}" {{ old('status', $expense->status)===$val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Is Installment?</label>
                    <label class="inline-flex items-center gap-2 mt-2">
                        <input type="checkbox" name="is_installment" value="1" class="rounded border-gray-300" {{ old('is_installment', $expense->is_installment) ? 'checked' : '' }}>
                        <span class="text-sm text-gray-700">Mark as installment (limited months within a cycle)</span>
                    </label>
                    @error('is_installment')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Start Date</label>
                    <input type="date" name="start_date" value="{{ old('start_date', optional($expense->start_date)->format('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    @error('start_date')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Next Due Date</label>
                    <input type="date" name="next_due_date" value="{{ old('next_due_date', optional($expense->next_due_date)->format('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    @error('next_due_date')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                @php
                    $mask = (int) old('active_months_mask', $expense->active_months_mask);
                    $selectedMonths = [];
                    for($i=1;$i<=12;$i++){ if ($mask===0 || ($mask & (1 << ($i-1)))) { $selectedMonths[]=$i; } }
                @endphp
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Active Months</label>
                    <div class="mt-2 grid grid-cols-2 gap-2 sm:grid-cols-6">
                        @php $months=['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']; @endphp
                        @for($i=1;$i<=12;$i++)
                            <label class="inline-flex items-center gap-2">
                                <input type="checkbox" name="months[]" value="{{ $i }}" class="rounded border-gray-300" {{ in_array($i, $selectedMonths) ? 'checked' : '' }}>
                                <span class="text-sm text-gray-700">{{ $months[$i-1] }}</span>
                            </label>
                        @endfor
                    </div>
                    <input type="hidden" name="active_months_mask" id="active_months_mask" value="{{ old('active_months_mask', $expense->active_months_mask) }}">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea name="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('notes', $expense->notes) }}</textarea>
                    @error('notes')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="mt-6 flex items-center justify-between">
                <a href="{{ route('admin.business-expenses.index') }}" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Back</a>
                <div class="flex items-center gap-3">
                    <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Update</button>
                </div>
            </div>
        </form>
    </div>

    <script>
        (function(){
            function computeMask(){
                const boxes = document.querySelectorAll('input[name="months[]"]');
                let mask = 0;
                boxes.forEach((box) => { if (box.checked) { mask |= (1 << (parseInt(box.value,10)-1)); } });
                document.getElementById('active_months_mask').value = mask;
            }
            document.querySelectorAll('input[name="months[]"]').forEach((el)=> el.addEventListener('change', computeMask));
        })();
    </script>
@endsection


