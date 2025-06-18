@extends('admin.layouts.app')

@section('title', 'System Feedback Details')

@section('content')
<div class="-mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <nav class="flex" aria-label="Breadcrumb">
                    <ol role="list" class="flex items-center space-x-4">
                        <li>
                            <div>
                                <a href="{{ route('admin.feedback.index') }}" class="text-gray-400 hover:text-gray-500">
                                    <svg class="flex-shrink-0 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 011.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.56.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.893.149c-.425.07-.765.383-.93.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 01-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.397.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 01-.12-1.45l.527-.737c.25-.35.272-.806.108-1.204-.165-.397-.505-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.107-1.204l-.527-.738a1.125 1.125 0 01.12-1.45l.773-.773a1.125 1.125 0 011.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894z" clip-rule="evenodd" />
                                        <path fill-rule="evenodd" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="sr-only">System Feedback</span>
                                </a>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z" />
                                </svg>
                                <a href="{{ route('admin.feedback.index', ['tab' => 'system']) }}" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">System Feedback</a>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z" />
                                </svg>
                                <span class="ml-4 text-sm font-medium text-gray-500">Feedback Details</span>
                            </div>
                        </li>
                    </ol>
                </nav>
                <h1 class="mt-4 text-3xl font-bold text-gray-900">System Feedback Details</h1>
                <p class="text-gray-600 mt-2">{{ $feedback->subject ?? 'System feedback report' }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.feedback.index', ['tab' => 'system']) }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    Back to System Feedback
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Feedback Details -->
            <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Feedback Details</h3>
                        <div class="flex items-center space-x-2">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                @if($feedback->type === 'bug') bg-red-100 text-red-800
                                @elseif($feedback->type === 'feature') bg-blue-100 text-blue-800
                                @elseif($feedback->type === 'improvement') bg-green-100 text-green-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($feedback->type ?? 'general') }}
                            </span>
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                @if($feedback->priority === 'high') bg-red-100 text-red-800
                                @elseif($feedback->priority === 'medium') bg-yellow-100 text-yellow-800
                                @else bg-green-100 text-green-800 @endif">
                                {{ ucfirst($feedback->priority ?? 'low') }} Priority
                            </span>
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                @if($feedback->status === 'completed') bg-green-100 text-green-800
                                @elseif($feedback->status === 'in_progress') bg-blue-100 text-blue-800
                                @elseif($feedback->status === 'rejected') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst(str_replace('_', ' ', $feedback->status ?? 'pending')) }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="space-y-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 mb-2">Subject</h4>
                            <p class="text-gray-700">{{ $feedback->subject ?? 'No subject provided' }}</p>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-900 mb-2">Description</h4>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $feedback->description ?? 'No description provided' }}</p>
                            </div>
                        </div>

                        @if($feedback->steps_to_reproduce)
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 mb-2">Steps to Reproduce</h4>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $feedback->steps_to_reproduce }}</p>
                                </div>
                            </div>
                        @endif

                        @if($feedback->expected_behavior)
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 mb-2">Expected Behavior</h4>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="text-gray-700 leading-relaxed">{{ $feedback->expected_behavior }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Admin Response -->
            <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Admin Response</h3>
                </div>
                <div class="p-6">
                    @if($feedback->admin_response)
                        <div class="bg-blue-50 rounded-lg p-4 mb-4">
                            <h4 class="text-sm font-medium text-blue-900 mb-2">Current Response</h4>
                            <p class="text-blue-800 whitespace-pre-wrap">{{ $feedback->admin_response }}</p>
                            @if($feedback->responded_at)
                                <p class="text-xs text-blue-600 mt-2">Responded on {{ $feedback->responded_at->format('F j, Y \a\t g:i A') }}</p>
                            @endif
                        </div>
                    @endif

                    <form action="{{ route('admin.feedback.update-system', $feedback->id) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="pending" {{ $feedback->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="in_progress" {{ $feedback->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="completed" {{ $feedback->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="rejected" {{ $feedback->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="priority" class="block text-sm font-medium text-gray-700">Priority</label>
                                <select id="priority" name="priority" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="low" {{ $feedback->priority === 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="medium" {{ $feedback->priority === 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="high" {{ $feedback->priority === 'high' ? 'selected' : '' }}>High</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label for="admin_response" class="block text-sm font-medium text-gray-700">Admin Response</label>
                            <textarea id="admin_response" name="admin_response" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Enter your response to this feedback...">{{ $feedback->admin_response }}</textarea>
                            <p class="mt-2 text-sm text-gray-500" id="char-count">
                                <span id="current-count">{{ strlen($feedback->admin_response ?? '') }}</span>/500 characters
                            </p>
                        </div>

                        <div class="flex justify-end space-x-3">
                            <button type="submit" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                                Update Response
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-8">
            <!-- User Information -->
            <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">User Information</h3>
                </div>
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="flex-shrink-0 h-12 w-12">
                            <div class="h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center">
                                <span class="text-lg font-medium text-indigo-700">{{ substr($feedback->user->name ?? 'U', 0, 2) }}</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-sm font-medium text-gray-900">{{ $feedback->user->name ?? 'Unknown User' }}</h4>
                            <p class="text-sm text-gray-500">{{ $feedback->user->email ?? 'No email' }}</p>
                        </div>
                    </div>

                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">User Role</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $feedback->user && $feedback->user->role ? ucfirst($feedback->user->role->name) : 'Customer' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Member Since</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $feedback->user && $feedback->user->created_at ? $feedback->user->created_at->format('F j, Y') : 'N/A' }}
                            </dd>
                        </div>
                        @if($feedback->user && $feedback->user->isSupplier())
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Supplier Performance</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ number_format($feedback->user->overall_performance_score ?? 0, 1) }}/5.0
                                </dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Feedback Timeline -->
            <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Timeline</h3>
                </div>
                <div class="p-6">
                    <div class="flow-root">
                        <ul role="list" class="-mb-8">
                            <li>
                                <div class="relative pb-8">
                                    @if($feedback->responded_at)
                                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                    @endif
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                                <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm text-gray-500">Feedback submitted</p>
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                {{ $feedback->created_at ? $feedback->created_at->format('M j, Y g:i A') : 'N/A' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            
                            @if($feedback->responded_at)
                                <li>
                                    <div class="relative pb-8">
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                                    <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                <div>
                                                    <p class="text-sm text-gray-500">Admin response provided</p>
                                                </div>
                                                <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                    {{ $feedback->responded_at->format('M j, Y g:i A') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
                </div>
                <div class="p-6 space-y-3">
                    @if($feedback->user && $feedback->user->email)
                        <a href="mailto:{{ $feedback->user->email }}" class="w-full inline-flex justify-center items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                            <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                            </svg>
                            Contact User
                        </a>
                    @endif
                    
                    <form action="{{ route('admin.feedback.update-system', $feedback->id) }}" method="POST" class="w-full">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="completed">
                        <button type="submit" class="w-full inline-flex justify-center items-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500">
                            <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                            Mark as Completed
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.getElementById('admin_response');
    const currentCount = document.getElementById('current-count');
    const maxLength = 500;

    if (textarea && currentCount) {
        textarea.addEventListener('input', function() {
            const length = this.value.length;
            currentCount.textContent = length;
            
            if (length > maxLength) {
                currentCount.parentElement.className = 'mt-2 text-sm text-red-500';
            } else if (length > maxLength * 0.8) {
                currentCount.parentElement.className = 'mt-2 text-sm text-yellow-500';
            } else {
                currentCount.parentElement.className = 'mt-2 text-sm text-gray-500';
            }
        });
    }
});
</script>
@endsection 