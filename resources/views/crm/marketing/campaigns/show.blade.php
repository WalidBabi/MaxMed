@extends('layouts.crm')

@section('title', 'Campaign Details')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <a href="{{ route('crm.marketing.campaigns.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Campaigns
                    </a>
                    <h1 class="h3 mt-2 mb-0">{{ $campaign->name }}</h1>
                    @if($campaign->description)
                        <p class="text-muted">{{ $campaign->description }}</p>
                    @endif
                </div>
                <div>
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
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Performance Overview -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Performance Overview</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3 mb-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h3 class="text-primary">{{ number_format($campaign->total_recipients) }}</h3>
                                    <small class="text-muted">Recipients</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h3 class="text-info">{{ number_format($campaign->sent_count) }}</h3>
                                    <small class="text-muted">Sent</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h3 class="text-success">{{ number_format($campaign->delivered_count) }}</h3>
                                    <small class="text-muted">Delivered</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h3 class="text-warning">{{ $campaign->delivery_rate }}%</h3>
                                    <small class="text-muted">Delivery Rate</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row text-center mt-3">
                        <div class="col-md-3 mb-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h3 class="text-primary">{{ number_format($campaign->opened_count) }}</h3>
                                    <small class="text-muted">Opens ({{ $campaign->open_rate }}%)</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h3 class="text-info">{{ number_format($campaign->clicked_count) }}</h3>
                                    <small class="text-muted">Clicks ({{ $campaign->click_rate }}%)</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h3 class="text-danger">{{ number_format($campaign->bounced_count) }}</h3>
                                    <small class="text-muted">Bounced ({{ $campaign->bounce_rate }}%)</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h3 class="text-secondary">{{ number_format($campaign->unsubscribed_count) }}</h3>
                                    <small class="text-muted">Unsubscribed ({{ $campaign->unsubscribe_rate }}%)</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Campaign Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Campaign Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Subject:</strong></td>
                                    <td>{{ $campaign->subject }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Type:</strong></td>
                                    <td><span class="badge badge-info">{{ ucfirst(str_replace('_', ' ', $campaign->type)) }}</span></td>
                                </tr>
                                @if($campaign->emailTemplate)
                                <tr>
                                    <td><strong>Template:</strong></td>
                                    <td>{{ $campaign->emailTemplate->name }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Creator:</strong></td>
                                    <td>{{ $campaign->creator->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Created:</strong></td>
                                    <td>{{ formatDubaiDate($campaign->created_at, 'M d, Y H:i') }}</td>
                                </tr>
                                @if($campaign->scheduled_at)
                                <tr>
                                    <td><strong>Scheduled:</strong></td>
                                    <td>{{ formatDubaiDate($campaign->scheduled_at, 'M d, Y H:i') }}</td>
                                </tr>
                                @endif
                                @if($campaign->sent_at)
                                <tr>
                                    <td><strong>Sent:</strong></td>
                                    <td>{{ formatDubaiDate($campaign->sent_at, 'M d, Y H:i') }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            @if($recentLogs->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Recent Activity</h5>
                </div>
                <div class="card-body">
                    @foreach($recentLogs as $log)
                    <div class="d-flex align-items-center mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <div class="mr-3">
                            @if($log->status == 'sent')
                                <span class="badge badge-primary"><i class="fas fa-paper-plane"></i></span>
                            @elseif($log->status == 'delivered')
                                <span class="badge badge-success"><i class="fas fa-check"></i></span>
                            @elseif($log->status == 'bounced')
                                <span class="badge badge-danger"><i class="fas fa-exclamation"></i></span>
                            @elseif($log->status == 'failed')
                                <span class="badge badge-danger"><i class="fas fa-times"></i></span>
                            @else
                                <span class="badge badge-secondary"><i class="fas fa-clock"></i></span>
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <div>
                                <strong>{{ $log->contact->first_name }} {{ $log->contact->last_name }}</strong>
                                <span class="text-muted">({{ $log->email }})</span>
                                - Email {{ $log->status }}
                            </div>
                            @if($log->opened_at)
                                <small class="text-success">
                                    <i class="fas fa-eye"></i> Opened {{ $log->opened_at->diffForHumans() }}
                                </small>
                            @endif
                            @if($log->clicked_at)
                                <small class="text-primary ml-3">
                                    <i class="fas fa-mouse-pointer"></i> Clicked {{ $log->clicked_at->diffForHumans() }}
                                </small>
                            @endif
                        </div>
                        <div class="text-muted small">
                            {{ formatDubaiDateForHumans($log->created_at) }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    @if($campaign->isDraft())
                        <button type="button" class="btn btn-primary btn-block mb-2" data-toggle="modal" data-target="#scheduleModal">
                            <i class="fas fa-clock"></i> Schedule Campaign
                        </button>
                        <a href="{{ route('crm.marketing.campaigns.edit', $campaign) }}" class="btn btn-secondary btn-block mb-2">
                            <i class="fas fa-edit"></i> Edit Campaign
                        </a>
                    @endif
                    
                    @if($campaign->isScheduled() || $campaign->isSending())
                        <form action="{{ route('crm.marketing.campaigns.pause', $campaign) }}" method="POST" class="mb-2">
                            @csrf
                            <button type="submit" class="btn btn-warning btn-block">
                                <i class="fas fa-pause"></i> Pause Campaign
                            </button>
                        </form>
                    @endif
                    
                    @if($campaign->isPaused())
                        <form action="{{ route('crm.marketing.campaigns.resume', $campaign) }}" method="POST" class="mb-2">
                            @csrf
                            <button type="submit" class="btn btn-success btn-block">
                                <i class="fas fa-play"></i> Resume Campaign
                            </button>
                        </form>
                    @endif
                    
                    <form action="{{ route('crm.marketing.campaigns.duplicate', $campaign) }}" method="POST" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-info btn-block">
                            <i class="fas fa-copy"></i> Duplicate Campaign
                        </button>
                    </form>
                    
                    @if(!$campaign->isSent())
                        <form action="{{ route('crm.marketing.campaigns.cancel', $campaign) }}" method="POST" 
                              onsubmit="return confirm('Are you sure you want to cancel this campaign?')" class="mb-2">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-block">
                                <i class="fas fa-ban"></i> Cancel Campaign
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Schedule Modal -->
@if($campaign->isDraft())
<div class="modal fade" id="scheduleModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('crm.marketing.campaigns.schedule', $campaign) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Schedule Campaign</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="scheduled_at">Schedule Date & Time</label>
                        <input type="datetime-local" name="scheduled_at" id="scheduled_at" 
                               class="form-control" required min="{{ nowDubai('Y-m-d\TH:i') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Schedule</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection 