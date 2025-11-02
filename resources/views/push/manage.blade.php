@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-5xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold mb-4">My Push Subscriptions</h1>

            @if($subs->isEmpty())
                <div class="p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                    <p class="text-yellow-800">No subscriptions yet. Visit the <a href="{{ route('push.test-page') }}" class="text-indigo-600 underline">Push Test</a> page to enable notifications.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left border-b">
                                <th class="py-2 pr-4">Enabled</th>
                                <th class="py-2 pr-4">Device (User-Agent)</th>
                                <th class="py-2 pr-4">Endpoint</th>
                                <th class="py-2 pr-4">Last Received</th>
                                <th class="py-2 pr-4">Updated</th>
                                <th class="py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subs as $s)
                                <tr class="border-b">
                                    <td class="py-2 pr-4">
                                        <button data-id="{{ $s->id }}" data-enabled="{{ $s->is_enabled ? 1 : 0 }}" class="toggle-btn px-2 py-1 rounded text-white {{ $s->is_enabled ? 'bg-green-600' : 'bg-gray-400' }}">
                                            {{ $s->is_enabled ? 'On' : 'Off' }}
                                        </button>
                                    </td>
                                    <td class="py-2 pr-4 whitespace-nowrap max-w-[20rem] overflow-hidden text-ellipsis">{{ $s->user_agent }}</td>
                                    <td class="py-2 pr-4 whitespace-nowrap max-w-[24rem] overflow-hidden text-ellipsis" title="{{ $s->endpoint }}">{{ \Illuminate\Support\Str::limit($s->endpoint, 60) }}</td>
                                    <td class="py-2 pr-4">{{ $s->last_received_at ? \Carbon\Carbon::parse($s->last_received_at)->diffForHumans() : '—' }}</td>
                                    <td class="py-2 pr-4">{{ \Carbon\Carbon::parse($s->updated_at)->diffForHumans() }}</td>
                                    <td class="py-2 space-x-2">
                                        <button data-id="{{ $s->id }}" class="test-btn px-3 py-1 rounded bg-indigo-600 text-white">Send Test</button>
                                        <button data-id="{{ $s->id }}" class="delete-btn px-3 py-1 rounded bg-red-600 text-white">Remove</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('click', async (e) => {
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    if (e.target.matches('.toggle-btn')) {
        const id = e.target.getAttribute('data-id');
        e.target.disabled = true;
        const resp = await fetch(`/push/subscriptions/${id}/toggle`, { method: 'PATCH', headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' } });
        const data = await resp.json();
        e.target.disabled = false;
        if (resp.ok) {
            const on = !!data.is_enabled;
            e.target.textContent = on ? 'On' : 'Off';
            e.target.classList.toggle('bg-green-600', on);
            e.target.classList.toggle('bg-gray-400', !on);
        } else {
            alert(data.error || 'Failed to toggle');
        }
    }
    if (e.target.matches('.delete-btn')) {
        if (!confirm('Remove this subscription?')) return;
        const id = e.target.getAttribute('data-id');
        e.target.disabled = true;
        const resp = await fetch(`/push/subscriptions/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' } });
        e.target.disabled = false;
        if (resp.ok) {
            window.location.reload();
        } else {
            const data = await resp.json().catch(() => ({}));
            alert(data.error || 'Failed to remove');
        }
    }
    if (e.target.matches('.test-btn')) {
        const id = e.target.getAttribute('data-id');
        e.target.disabled = true;
        const resp = await fetch(`/push/subscriptions/${id}/test`, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' } });
        e.target.disabled = false;
        const data = await resp.json().catch(() => ({}));
        if (!resp.ok || !data.ok) {
            alert('Send failed');
        } else {
            alert('Sent');
        }
    }
});
</script>
@endsection


