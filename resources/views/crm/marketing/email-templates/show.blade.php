@extends('layouts.crm')

@section('title', 'Email Template: ' . $emailTemplate->name)

@section('content')
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $emailTemplate->name }}</h1>
                <p class="text-gray-600 mt-2">Email template details and preview</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('crm.marketing.email-templates.edit', $emailTemplate) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-indigo-700">
                    Edit Template
                </a>
                <a href="{{ route('crm.marketing.email-templates.index') }}" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-50">
                    Back to Templates
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-1">
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Template Information</h3>
                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            @if($emailTemplate->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Inactive</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Category</dt>
                        <dd class="mt-1 text-sm text-gray-900 capitalize">{{ $emailTemplate->category }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Subject Line</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $emailTemplate->subject }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Created</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $emailTemplate->created_at->format('M j, Y g:i A') }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Template Preview</h3>
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="text-sm text-gray-600 mb-4">
                        <strong>Subject:</strong> {{ $emailTemplate->subject }}
                    </div>
                    <div class="prose max-w-none">
                        {!! $emailTemplate->html_content !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 