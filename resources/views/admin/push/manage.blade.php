@extends('admin.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-bold">Push Subscriptions</h1>
            <form method="GET" class="flex items-center space-x-2">
                <input type="number" name="user_id" value="{{ request('user_id') }}" placeholder="Filter by user_id" class="px-3 py-2 border rounded w-40">
                <button class="px-3 py-2 bg-gray-800 text-white rounded">Filter</button>
            </form>
        </div>

        <div class="mb-4 p-4 bg-gray-50 rounded border">
            <form id="broadcastForm" class="space-y-3">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <input type="text" name="title" placeholder="Title (optional)" class="px-3 py-2 border rounded">
                    <input type="text" name="body" placeholder="Message (optional)" class="px-3 py-2 border rounded">
                    <input type="text" name="url" placeholder="Click URL (optional)" class="px-3 py-2 border rounded">
                </div>
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-600">Send to selected subscriptions</div>
                    <button type="submit" class="px-3 py-2 bg-indigo-600 text-white rounded">Send to Selected</button>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left border-b">
                        <th class="py-2 pr-2"><input type="checkbox" id="selectAll"></th>
                        <th class="py-2 pr-4">ID</th>
                        <th class="py-2 pr-4">User</th>
                        <th class="py-2 pr-4">Enabled</th>
                        <th class="py-2 pr-4">Device</th>
                        <th class="py-2 pr-4">Endpoint</th>
                        <th class="py-2 pr-4">Last Received</th>
                        <th class="py-2 pr-4">Updated</th>
                        <th class="py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subs as $s)
                        <tr class="border-b">
                            <td class="py-2 pr-2"><input type="checkbox" class="row-check" value="{{ $s->id }}"></td>
                            <td class="py-2 pr-4">{{ $s->id }}</td>
                            <td class="py-2 pr-4">{{ $s->user_id ?? '—' }}</td>
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
                    @empty
                        <tr><td class="py-4" colspan="8">No subscriptions found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $subs->links() }}
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

// Broadcast selected
document.getElementById('broadcastForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const ids = Array.from(document.querySelectorAll('.row-check:checked')).map(i => parseInt(i.value, 10));
    if (ids.length === 0) { alert('Select at least one subscription'); return; }
    const form = e.target;
    const payload = {
        ids,
        title: form.title.value || null,
        body: form.body.value || null,
        url: form.url.value || null,
    };
    const btn = form.querySelector('button[type="submit"]');
    btn.disabled = true; btn.textContent = 'Sending...';
    const resp = await fetch("{{ route('admin.push.broadcast') }}", {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
        body: JSON.stringify(payload)
    });
    btn.disabled = false; btn.textContent = 'Send to Selected';
    const data = await resp.json().catch(() => ({}));
    if (!resp.ok) {
        alert(data.message || data.error || 'Broadcast failed');
    } else {
        alert(`Sent to ${data.sent} subscription(s)`);
    }
});

// Select all
document.getElementById('selectAll')?.addEventListener('change', (e) => {
    const checked = e.target.checked;
    document.querySelectorAll('.row-check').forEach(cb => { cb.checked = checked; });
});
</script>
@endsection


