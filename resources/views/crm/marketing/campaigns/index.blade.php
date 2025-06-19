@extends('layouts.crm')

@section('title', 'Email Campaigns')

@section('content')
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Email Campaigns</h1>
                <p class="text-gray-600 mt-2">Create, manage, and track your email marketing campaigns</p>
            </div>
            <div class="flex items-center space-x-3">
                <button type="button" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a.75.75 0 01.55.24l3.25 3.5a.75.75 0 11-1.1 1.02L10 4.852 7.3 7.76a.75.75 0 01-1.1-1.02l3.25-3.5A.75.75 0 0110 3zm-3.76 9.2a.75.75 0 011.06.04l2.7 2.908 2.7-2.908a.75.75 0 111.1 1.02l-3.25 3.5a.75.75 0 01-1.1 0l-3.25-3.5a.75.75 0 01.04-1.06z" clip-rule="evenodd" />
                    </svg>
                    Export Data
                </button>
                <a href="{{ route('crm.marketing.campaigns.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                    </svg>
                    Create Campaign
                </a>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5 mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Search & Filter Campaigns</h3>
        </div>
        <div class="p-6">
            <form method="GET" action="{{ route('crm.marketing.campaigns.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                        <input type="text" name="search" id="search" 
                               value="{{ request('search') }}"
                               placeholder="Campaign name or subject..."
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">All Statuses</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                            <option value="sending" {{ request('status') == 'sending' ? 'selected' : '' }}>Sending</option>
                            <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                            <option value="paused" {{ request('status') == 'paused' ? 'selected' : '' }}>Paused</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                        <select name="type" id="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">All Types</option>
                            <option value="one_time" {{ request('type') == 'one_time' ? 'selected' : '' }}>One Time</option>
                            <option value="recurring" {{ request('type') == 'recurring' ? 'selected' : '' }}>Recurring</option>
                            <option value="drip" {{ request('type') == 'drip' ? 'selected' : '' }}>Drip</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <div class="flex space-x-2">
                            <button type="submit" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                                <svg class="-ml-0.5 mr-1.5 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                                </svg>
                                Filter
                            </button>
                            <a href="{{ route('crm.marketing.campaigns.index') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                Clear
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

                <div class="card-body">
                    @if($campaigns->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>
                                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}">
                                                Campaign Name
                                                @if(request('sort') == 'name')
                                                    <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                                @endif
                                            </a>
                                        </th>
                                        <th>Subject</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Recipients</th>
                                        <th>Open Rate</th>
                                        <th>Click Rate</th>
                                        <th>
                                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}">
                                                Created
                                                @if(request('sort') == 'created_at')
                                                    <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                                @endif
                                            </a>
                                        </th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($campaigns as $campaign)
                                        <tr>
                                            <td>
                                                <a href="{{ route('crm.marketing.campaigns.show', $campaign) }}" class="text-decoration-none">
                                                    <strong>{{ $campaign->name }}</strong>
                                                </a>
                                                @if($campaign->description)
                                                    <br><small class="text-muted">{{ Str::limit($campaign->description, 50) }}</small>
                                                @endif
                                            </td>
                                            <td>{{ Str::limit($campaign->subject, 40) }}</td>
                                            <td>
                                                <span class="badge badge-info">{{ ucfirst(str_replace('_', ' ', $campaign->type)) }}</span>
                                            </td>
                                            <td>
                                                @if($campaign->status == 'draft')
                                                    <span class="badge badge-secondary">Draft</span>
                                                @elseif($campaign->status == 'scheduled')
                                                    <span class="badge badge-warning">Scheduled</span>
                                                @elseif($campaign->status == 'sending')
                                                    <span class="badge badge-primary">Sending</span>
                                                @elseif($campaign->status == 'sent')
                                                    <span class="badge badge-success">Sent</span>
                                                @elseif($campaign->status == 'paused')
                                                    <span class="badge badge-warning">Paused</span>
                                                @elseif($campaign->status == 'cancelled')
                                                    <span class="badge badge-danger">Cancelled</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ number_format($campaign->total_recipients) }}
                                                @if($campaign->sent_count > 0)
                                                    <br><small class="text-muted">{{ number_format($campaign->sent_count) }} sent</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($campaign->delivered_count > 0)
                                                    {{ $campaign->open_rate }}%
                                                    <br><small class="text-muted">{{ number_format($campaign->opened_count) }} opens</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($campaign->delivered_count > 0)
                                                    {{ $campaign->click_rate }}%
                                                    <br><small class="text-muted">{{ number_format($campaign->clicked_count) }} clicks</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ formatDubaiDate($campaign->created_at, 'M d, Y') }}
                                                <br><small class="text-muted">{{ $campaign->creator->name }}</small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('crm.marketing.campaigns.show', $campaign) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('crm.marketing.campaigns.preview', $campaign) }}" class="btn btn-sm btn-secondary" target="_blank">
                                                        <i class="fas fa-search"></i>
                                                    </a>
                                                    @if($campaign->isDraft())
                                                        <a href="{{ route('crm.marketing.campaigns.edit', $campaign) }}" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    @endif
                                                    
                                                    <div class="btn-group" role="group">
                                                        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-toggle="dropdown">
                                                            <i class="fas fa-ellipsis-v"></i>
                                                        </button>
                                                        <div class="dropdown-menu">
                                                            <form action="{{ route('crm.marketing.campaigns.duplicate', $campaign) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item">
                                                                    <i class="fas fa-copy"></i> Duplicate
                                                                </button>
                                                            </form>
                                                            
                                                            @if($campaign->isDraft())
                                                                <div class="dropdown-divider"></div>
                                                                <button type="button" class="dropdown-item" data-toggle="modal" data-target="#scheduleModal{{ $campaign->id }}">
                                                                    <i class="fas fa-clock"></i> Schedule
                                                                </button>
                                                            @elseif($campaign->isScheduled() || $campaign->isSending())
                                                                <form action="{{ route('crm.marketing.campaigns.pause', $campaign) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <button type="submit" class="dropdown-item">
                                                                        <i class="fas fa-pause"></i> Pause
                                                                    </button>
                                                                </form>
                                                            @elseif($campaign->isPaused())
                                                                <form action="{{ route('crm.marketing.campaigns.resume', $campaign) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <button type="submit" class="dropdown-item">
                                                                        <i class="fas fa-play"></i> Resume
                                                                    </button>
                                                                </form>
                                                            @endif
                                                            
                                                            @if(!$campaign->isSent())
                                                                <div class="dropdown-divider"></div>
                                                                <form action="{{ route('crm.marketing.campaigns.cancel', $campaign) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <button type="submit" class="dropdown-item text-warning" 
                                                                            onclick="return confirm('Are you sure you want to cancel this campaign?')">
                                                                        <i class="fas fa-stop"></i> Cancel
                                                                    </button>
                                                                </form>
                                                                
                                                                <form action="{{ route('crm.marketing.campaigns.destroy', $campaign) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="dropdown-item text-danger" 
                                                                            onclick="return confirm('Are you sure you want to delete this campaign?')">
                                                                        <i class="fas fa-trash"></i> Delete
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                Showing {{ $campaigns->firstItem() }} to {{ $campaigns->lastItem() }} of {{ $campaigns->total() }} campaigns
                            </div>
                            <div>
                                {{ $campaigns->links() }}
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-envelope fa-3x text-muted mb-3"></i>
                            <h4>No campaigns found</h4>
                            <p class="text-muted">Create your first email campaign to start reaching out to your contacts.</p>
                            <a href="{{ route('crm.marketing.campaigns.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Create First Campaign
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Schedule Modals -->
@foreach($campaigns->where('status', 'draft') as $campaign)
<div class="modal fade" id="scheduleModal{{ $campaign->id }}" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('crm.marketing.campaigns.schedule', $campaign) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Schedule Campaign: {{ $campaign->name }}</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="scheduled_at{{ $campaign->id }}">Schedule Date & Time</label>
                        <input type="datetime-local" name="scheduled_at" id="scheduled_at{{ $campaign->id }}" 
                               class="form-control" required min="{{ nowDubai('Y-m-d\TH:i') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Schedule Campaign</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-submit form when filters change
    $('select[name="status"], select[name="type"]').change(function() {
        $('#filterForm').submit();
    });
});
</script>
@endpush 