@extends('layouts.crm')

@section('title', 'Import Preview - Marketing Contacts')

@section('content')
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Import Preview</h1>
                <p class="text-gray-600 mt-2">Review and confirm column mappings before importing contacts</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('crm.marketing.contacts.index') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M7.72 12.53a.75.75 0 010-1.06L10.94 8.25H6.5a.75.75 0 010-1.5h4.44L7.72 3.53a.75.75 0 011.06-1.06l4.25 4.25a.75.75 0 010 1.06l-4.25 4.25a.75.75 0 01-1.06 0z" clip-rule="evenodd" />
                    </svg>
                    Cancel Import
                </a>
            </div>
        </div>
    </div>

    <form action="{{ route('crm.marketing.contacts.import') }}" method="POST" enctype="multipart/form-data" id="importForm">
        @csrf
        <input type="hidden" name="step" value="confirm">
        @if($contactListId)
            <input type="hidden" name="contact_list_id" value="{{ $contactListId }}">
        @endif

        <!-- File is already uploaded and stored temporarily -->

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">
                <!-- File Information -->
                <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">File Information</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="font-medium text-gray-700">Columns Found:</span>
                                <span class="text-gray-900 ml-2">{{ count($previewData['headers']) }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Sample Rows:</span>
                                <span class="text-gray-900 ml-2">{{ $previewData['totalPreviewRows'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Column Mappings -->
                <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">Column Mappings</h3>
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                                    <svg class="h-3 w-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                    </svg>
                                    Auto-detected
                                </span>
                                <button type="button" onclick="resetMappings()" class="text-xs text-gray-500 hover:text-gray-700">
                                    Reset to auto-detect
                                </button>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 mt-1">
                            We've automatically detected the best column mappings. Review and adjust as needed.
                        </p>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            @foreach([
                                'email' => 'Email Address *',
                                'first_name' => 'First Name',
                                'last_name' => 'Last Name',
                                'phone' => 'Phone Number',
                                'company' => 'Company',
                                'job_title' => 'Job Title',
                                'industry' => 'Industry',
                                'country' => 'Country',
                                'city' => 'City',
                                'notes' => 'Notes'
                            ] as $dbColumn => $label)
                                <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-b-0">
                                    <div class="flex-1">
                                        <label class="block text-sm font-medium text-gray-700">
                                            {{ $label }}
                                            @if($dbColumn === 'email')
                                                <span class="text-red-500">*</span>
                                            @endif
                                        </label>
                                    </div>
                                    <div class="flex-1 ml-4">
                                        <select name="column_mappings[{{ $dbColumn }}]" 
                                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm {{ $dbColumn === 'email' ? 'required' : '' }}"
                                                {{ $dbColumn === 'email' ? 'required' : '' }}>
                                            <option value="">-- No mapping --</option>
                                            @foreach($previewData['headers'] as $index => $header)
                                                <option value="{{ $index }}" 
                                                        {{ isset($detectedMappings[$dbColumn]) && $detectedMappings[$dbColumn] == $index ? 'selected' : '' }}>
                                                    {{ $header }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="flex-1 ml-4">
                                        @if(isset($detectedMappings[$dbColumn]))
                                            @php
                                                $sampleValue = '';
                                                if (!empty($previewData['sampleRows'])) {
                                                    $headerName = $previewData['headers'][$detectedMappings[$dbColumn]] ?? '';
                                                    $sampleValue = $previewData['sampleRows'][0][$headerName] ?? '';
                                                }
                                            @endphp
                                            <span class="text-xs text-gray-500 italic">
                                                Sample: {{ Str::limit($sampleValue, 30) ?: 'N/A' }}
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-400">No mapping detected</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Data Preview -->
                <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Data Preview</h3>
                        <p class="text-sm text-gray-600 mt-1">
                            Preview of the first {{ count($previewData['sampleRows']) }} rows from your CSV file
                        </p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    @foreach($previewData['headers'] as $header)
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                            {{ $header }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($previewData['sampleRows'] as $row)
                                    <tr>
                                        @foreach($previewData['headers'] as $header)
                                            <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                                {{ Str::limit($row[$header] ?? '', 50) }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Contact List Selection -->
                @if($contactLists->count() > 0)
                    <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Contact List</h3>
                        </div>
                        <div class="p-6">
                            <select name="contact_list_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">No specific list</option>
                                @foreach($contactLists as $list)
                                    <option value="{{ $list->id }}" {{ $contactListId == $list->id ? 'selected' : '' }}>
                                        {{ $list->name }} ({{ $list->contacts()->count() }} contacts)
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-2 text-xs text-gray-500">
                                Optional: Add imported contacts to a specific list
                            </p>
                        </div>
                    </div>
                @endif

                <!-- Import Summary -->
                <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Import Summary</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Mapped Columns:</span>
                            <span class="font-medium text-gray-900" id="mappedCount">{{ count(array_filter($detectedMappings)) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Email Column:</span>
                            <span class="font-medium" id="emailStatus">
                                @if(isset($detectedMappings['email']))
                                    <span class="text-green-600">✓ Detected</span>
                                @else
                                    <span class="text-red-600">✗ Required</span>
                                @endif
                            </span>
                        </div>
                        
                        <div class="pt-4 border-t border-gray-200">
                            <button type="submit" 
                                    class="w-full inline-flex justify-center items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 disabled:bg-gray-300 disabled:cursor-not-allowed"
                                    id="importButton">
                                <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 3a.75.75 0 01.55.24l3.25 3.5a.75.75 0 11-1.1 1.02L10 4.852 7.3 7.76a.75.75 0 01-1.1-1.02l3.25-3.5A.75.75 0 0110 3zm-3.76 9.2a.75.75 0 011.06.04l2.7 2.908 2.7-2.908a.75.75 0 111.1 1.02l-3.25 3.5a.75.75 0 01-1.1 0l-3.25-3.5a.75.75 0 01.04-1.06z" clip-rule="evenodd" />
                                </svg>
                                Import Contacts
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Help & Tips -->
                <div class="card-hover rounded-xl bg-blue-50 shadow-sm ring-1 ring-blue-200">
                    <div class="px-6 py-4 border-b border-blue-200">
                        <h3 class="text-lg font-semibold text-blue-900">💡 Tips</h3>
                    </div>
                    <div class="p-6">
                        <ul class="text-sm text-blue-800 space-y-2">
                            <li>• Email column is required for import</li>
                            <li>• Duplicate emails will be skipped</li>
                            <li>• Empty rows will be ignored</li>
                            <li>• All contacts will be marked as "active"</li>
                            <li>• You can adjust mappings if auto-detection is incorrect</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('importForm');
    const mappingSelects = document.querySelectorAll('select[name^="column_mappings"]');
    const emailSelect = document.querySelector('select[name="column_mappings[email]"]');
    const importButton = document.getElementById('importButton');
    const mappedCountElement = document.getElementById('mappedCount');
    const emailStatusElement = document.getElementById('emailStatus');
    
    // Original auto-detected mappings for reset functionality
    const originalMappings = @json($detectedMappings);
    
    function updateSummary() {
        let mappedCount = 0;
        let hasEmail = false;
        
        mappingSelects.forEach(select => {
            if (select.value) {
                mappedCount++;
                if (select.name === 'column_mappings[email]') {
                    hasEmail = true;
                }
            }
        });
        
        mappedCountElement.textContent = mappedCount;
        
        if (hasEmail) {
            emailStatusElement.innerHTML = '<span class="text-green-600">✓ Detected</span>';
            importButton.disabled = false;
            importButton.classList.remove('disabled:bg-gray-300', 'disabled:cursor-not-allowed');
            importButton.classList.add('bg-indigo-600', 'hover:bg-indigo-500');
        } else {
            emailStatusElement.innerHTML = '<span class="text-red-600">✗ Required</span>';
            importButton.disabled = true;
            importButton.classList.add('disabled:bg-gray-300', 'disabled:cursor-not-allowed');
            importButton.classList.remove('bg-indigo-600', 'hover:bg-indigo-500');
        }
        
        updateSampleValues();
    }
    
    function updateSampleValues() {
        const sampleRows = @json($previewData['sampleRows']);
        const headers = @json($previewData['headers']);
        
        mappingSelects.forEach(select => {
            const sampleElement = select.closest('.flex').querySelector('.italic');
            if (sampleElement && select.value !== '') {
                const headerName = headers[select.value];
                const sampleValue = sampleRows.length > 0 ? (sampleRows[0][headerName] || '') : '';
                sampleElement.textContent = `Sample: ${sampleValue.substring(0, 30)}${sampleValue.length > 30 ? '...' : ''}` || 'Sample: N/A';
            } else if (sampleElement) {
                sampleElement.textContent = 'No mapping detected';
                sampleElement.classList.remove('text-gray-500');
                sampleElement.classList.add('text-gray-400');
            }
        });
    }
    
    // Prevent duplicate column mappings
    function preventDuplicates(changedSelect) {
        const selectedValues = new Set();
        
        mappingSelects.forEach(select => {
            if (select.value && select !== changedSelect) {
                selectedValues.add(select.value);
            }
        });
        
        mappingSelects.forEach(select => {
            if (select !== changedSelect) {
                Array.from(select.options).forEach(option => {
                    if (option.value && selectedValues.has(option.value) && option.value !== select.value) {
                        option.disabled = true;
                        option.style.color = '#9CA3AF';
                    } else {
                        option.disabled = false;
                        option.style.color = '';
                    }
                });
            }
        });
    }
    
    // Add change listeners
    mappingSelects.forEach(select => {
        select.addEventListener('change', function() {
            preventDuplicates(this);
            updateSummary();
        });
    });
    
    // Initialize
    updateSummary();
    preventDuplicates(null);
    
    // Reset mappings function
    window.resetMappings = function() {
        mappingSelects.forEach(select => {
            const fieldName = select.name.match(/\[([^\]]+)\]/)[1];
            if (originalMappings[fieldName] !== undefined) {
                select.value = originalMappings[fieldName];
            } else {
                select.value = '';
            }
        });
        preventDuplicates(null);
        updateSummary();
    };
    
    // Handle file input for form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Create a new file input with the original file
        const fileInput = document.createElement('input');
        fileInput.type = 'file';
        fileInput.name = 'file';
        fileInput.style.display = 'none';
        
        // We need to handle the file submission differently since we can't programmatically set file input values
        // For now, we'll submit the form data and handle file in the controller session
        this.submit();
    });
});
</script>
@endpush 