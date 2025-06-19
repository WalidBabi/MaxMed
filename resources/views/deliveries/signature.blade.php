@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Delivery Confirmation</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h5>Please review and sign below to confirm delivery</h5>
                        <p class="mb-0">Your signature confirms that you have received the items in good condition.</p>
                    </div>

                    <div class="mb-4">
                        <h5>Delivery Details</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Delivery #:</strong> {{ $delivery->id }}</p>
                                <p class="mb-1"><strong>Order #:</strong> {{ $delivery->order_id }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Date:</strong> {{ nowDubai('M d, Y') }}</p>
                                <p class="mb-1"><strong>Status:</strong> 
                                    <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $delivery->status)) }}</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h5>Delivery Conditions</h5>
                        <div class="form-check mb-2">
                            <input class="form-check-input condition-check" type="checkbox" id="condition1" name="conditions[]" value="received_undamaged" required>
                            <label class="form-check-label" for="condition1">
                                I confirm that I have received the items in good condition
                            </label>
                        </div>
                        
                    </div>

                    <div class="mb-4">
                        <h5>Signature</h5>
                        <p class="text-muted">Please sign in the box below</p>
                        <div id="signature-pad" class="signature-pad border rounded">
                            <div class="signature-pad--body">
                                <canvas></canvas>
                            </div>
                            <div class="signature-pad--footer">
                                <div class="signature-pad--actions">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-action="clear">
                                        <i class="fas fa-eraser me-1"></i> Clear
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-action="undo">
                                        <i class="fas fa-undo me-1"></i> Undo
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="form-text">By signing, you confirm that you have received the items as described.</div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('admin.deliveries.show', $delivery) }}" class="btn btn-outline-secondary me-md-2">
                            <i class="fas fa-times me-1"></i> Cancel
                        </a>
                        <button type="button" id="submit-signature" class="btn btn-primary" disabled>
                            <i class="fas fa-check-circle me-1"></i> Confirm Delivery
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .signature-pad {
        position: relative;
        background: #fff;
        width: 100%;
        margin: 0 auto;
    }
    .signature-pad--body {
        position: relative;
        width: 100%;
        height: 0;
        padding-bottom: 56.25%; /* 16:9 Aspect Ratio */
    }
    .signature-pad--body canvas {
        position: absolute;
        width: 100% !important;
        height: 100% !important;
        left: 0;
        top: 0;
        height: 200px;
        touch-action: none;
    }
    .signature-pad--footer {
        padding: 8px;
        background: #f8f9fa;
        border-top: 1px solid #e9ecef;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const wrapper = document.getElementById('signature-pad');
        const canvas = wrapper.querySelector('canvas');
        const clearButton = wrapper.querySelector('[data-action=clear]');
        const undoButton = wrapper.querySelector('[data-action=undo]');
        const submitButton = document.getElementById('submit-signature');
        const conditionChecks = document.querySelectorAll('.condition-check');
        
        // Initialize signature pad
        const signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgb(255, 255, 255)',
            penColor: 'rgb(0, 0, 0)'
        });

        // Initialize canvas with responsive sizing
        function resizeCanvas() {
            // Set canvas size to match the container
            const container = wrapper.querySelector('.signature-pad--body');
            const width = container.offsetWidth;
            const height = container.offsetHeight;
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            
            // Set canvas size
            canvas.width = width * ratio;
            canvas.height = height * ratio;
            
            // Scale the canvas for high DPI displays
            canvas.style.width = width + 'px';
            canvas.style.height = height + 'px';
            
            const ctx = canvas.getContext('2d');
            ctx.scale(ratio, ratio);
            
            // Clear and redraw signature if it exists
            if (signaturePad) {
                const data = signaturePad.toData();
                signaturePad.clear();
                signaturePad.fromData(data);
            }
        }
        
        // Initial resize
        resizeCanvas();
        
        // Handle window resize and orientation change
        window.addEventListener('resize', resizeCanvas);
        window.addEventListener('orientationchange', resizeCanvas);

        // Button handlers
        clearButton.addEventListener('click', () => {
            signaturePad.clear();
            updateSubmitButtonState();
        });

        undoButton.addEventListener('click', () => {
            const data = signaturePad.toData();
            if (data) {
                data.pop(); // Remove the last dot or line
                signaturePad.fromData(data);
            }
            updateSubmitButtonState();
        });

        // Handle form submission
        submitButton.addEventListener('click', () => {
            if (signaturePad.isEmpty()) {
                alert('Please provide a signature first.');
                return;
            }

            // Check if all conditions are accepted
            const allConditionsChecked = Array.from(conditionChecks).every(checkbox => checkbox.checked);
            if (!allConditionsChecked) {
                alert('Please accept all delivery conditions.');
                return;
            }

            // Get signature data URL
            const signatureData = signaturePad.toDataURL('image/png');
            
            // Get checked conditions
            const conditions = Array.from(document.querySelectorAll('.condition-check:checked'))
                .map(checkbox => checkbox.value);

            // Show loading state
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Processing...';

            // Send data to server
            fetch(`/deliveries/${@json($delivery->id)}/sign`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    signature: signatureData,
                    conditions: conditions
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = data.redirect;
                } else {
                    throw new Error(data.error || 'Failed to save signature');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error saving signature. Please try again.');
                submitButton.disabled = false;
                submitButton.innerHTML = '<i class="fas fa-check-circle me-1"></i> Confirm Delivery';
            });
        });

        // Update submit button state based on signature and conditions
        function updateSubmitButtonState() {
            const hasSignature = !signaturePad.isEmpty();
            const allConditionsChecked = Array.from(conditionChecks).every(checkbox => checkbox.checked);
            submitButton.disabled = !(hasSignature && allConditionsChecked);
        }

        // Add event listeners for signature and conditions
        signaturePad.addEventListener('endStroke', updateSubmitButtonState);
        conditionChecks.forEach(checkbox => {
            checkbox.addEventListener('change', updateSubmitButtonState);
        });
    });
</script>
@endpush
