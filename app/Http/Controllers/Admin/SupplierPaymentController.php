<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupplierPayment;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SupplierPaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:suppliers.view')->only(['index', 'show']);
        $this->middleware('permission:suppliers.manage_payments')->only(['create', 'store', 'edit', 'update', 'destroy', 'approve', 'reject']);
    }

    /**
     * Display a listing of supplier payments
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        
        $query = SupplierPayment::with(['purchaseOrder', 'order']);
        
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        
        $payments = $query->latest()->paginate(15);
        
        // Get status counts
        $statusCounts = [
            'all' => SupplierPayment::count(),
            'pending' => SupplierPayment::where('status', 'pending')->count(),
            'processing' => SupplierPayment::where('status', 'processing')->count(),
            'completed' => SupplierPayment::where('status', 'completed')->count(),
            'failed' => SupplierPayment::where('status', 'failed')->count(),
        ];

        return view('admin.supplier-payments.index', compact('payments', 'status', 'statusCounts'));
    }

    /**
     * Display the specified supplier payment
     */
    public function show(SupplierPayment $supplierPayment)
    {
        $supplierPayment->load(['purchaseOrder', 'order', 'createdBy', 'updatedBy']);
        
        return view('admin.supplier-payments.show', compact('supplierPayment'));
    }

    /**
     * Show the form for editing the specified supplier payment
     */
    public function edit(SupplierPayment $supplierPayment)
    {
        $supplierPayment->load(['purchaseOrder', 'order']);
        
        return view('admin.supplier-payments.edit', compact('supplierPayment'));
    }

    /**
     * Update the specified supplier payment in storage
     */
    public function update(Request $request, SupplierPayment $supplierPayment)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string|max:50',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'bank_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'transaction_id' => 'nullable|string|max:255',
            'attachments.*' => 'nullable|file|max:5120', // 5MB max per file
        ]);

        try {
            DB::beginTransaction();

            $supplierPayment->update([
                'amount' => $request->amount,
                'payment_date' => $request->payment_date,
                'payment_method' => $request->payment_method,
                'reference_number' => $request->reference_number,
                'notes' => $request->notes,
                'bank_name' => $request->bank_name,
                'account_number' => $request->account_number,
                'transaction_id' => $request->transaction_id,
                'updated_by' => Auth::id(),
            ]);

            // Handle file attachments
            if ($request->hasFile('attachments')) {
                $attachments = $supplierPayment->attachments ?? [];
                
                foreach ($request->file('attachments') as $file) {
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('supplier-payments', $fileName, 'public');
                    
                    $attachments[] = [
                        'name' => $file->getClientOriginalName(),
                        'path' => $filePath,
                        'size' => $file->getSize(),
                        'type' => $file->getClientMimeType(),
                        'uploaded_at' => now()->toISOString()
                    ];
                }
                
                $supplierPayment->update(['attachments' => $attachments]);
            }

            DB::commit();
            
            return redirect()->route('admin.supplier-payments.show', $supplierPayment)
                           ->with('success', 'Supplier payment updated successfully.');
                           
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update supplier payment: ' . $e->getMessage());
            
            return back()->withInput()
                        ->withErrors(['error' => 'Failed to update supplier payment. Please try again.']);
        }
    }

    /**
     * Mark supplier payment as completed
     */
    public function markAsCompleted(SupplierPayment $supplierPayment)
    {
        try {
            DB::beginTransaction();

            $supplierPayment->update([
                'status' => 'completed',
                'processed_at' => now(),
                'updated_by' => Auth::id(),
            ]);

            // Update purchase order payment status
            if ($supplierPayment->purchaseOrder) {
                $totalPaid = $supplierPayment->purchaseOrder->supplierPayments()
                    ->where('status', 'completed')
                    ->sum('amount');
                
                $supplierPayment->purchaseOrder->update([
                    'paid_amount' => $totalPaid,
                    'payment_status' => $totalPaid >= $supplierPayment->purchaseOrder->total_amount 
                        ? 'fully_paid' 
                        : 'partially_paid'
                ]);
            }

            DB::commit();
            
            return back()->with('success', 'Payment marked as completed successfully.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to mark payment as completed: ' . $e->getMessage());
            
            return back()->withErrors(['error' => 'Failed to mark payment as completed. Please try again.']);
        }
    }

    /**
     * Mark supplier payment as failed
     */
    public function markAsFailed(SupplierPayment $supplierPayment)
    {
        try {
            DB::beginTransaction();

            $supplierPayment->update([
                'status' => 'failed',
                'updated_by' => Auth::id(),
            ]);

            // Update purchase order payment status
            if ($supplierPayment->purchaseOrder) {
                $totalPaid = $supplierPayment->purchaseOrder->supplierPayments()
                    ->where('status', 'completed')
                    ->sum('amount');
                
                $supplierPayment->purchaseOrder->update([
                    'paid_amount' => $totalPaid,
                    'payment_status' => $totalPaid > 0 
                        ? ($totalPaid >= $supplierPayment->purchaseOrder->total_amount ? 'fully_paid' : 'partially_paid')
                        : 'unpaid'
                ]);
            }

            DB::commit();
            
            return back()->with('success', 'Payment marked as failed.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to mark payment as failed: ' . $e->getMessage());
            
            return back()->withErrors(['error' => 'Failed to mark payment as failed. Please try again.']);
        }
    }
} 