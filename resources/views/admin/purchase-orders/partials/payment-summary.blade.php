<div class="bg-gray-50 rounded-lg p-4 mb-6">
    <div class="grid grid-cols-3 gap-4 text-center">
        <div>
            <div class="text-sm text-gray-500">Total Amount</div>
            <div class="text-lg font-semibold text-gray-900">{{ $purchaseOrder->currency }} {{ number_format($purchaseOrder->total_amount, 2) }}</div>
        </div>
        <div>
            <div class="text-sm text-gray-500">Paid Amount</div>
            <div class="text-lg font-semibold text-green-600">{{ $purchaseOrder->currency }} {{ number_format($purchaseOrder->paid_amount ?? 0, 2) }}</div>
        </div>
        <div>
            <div class="text-sm text-gray-500">Remaining</div>
            <div class="text-lg font-semibold text-red-600">{{ $purchaseOrder->currency }} {{ number_format(($purchaseOrder->total_amount - ($purchaseOrder->paid_amount ?? 0)), 2) }}</div>
        </div>
    </div>
</div>

