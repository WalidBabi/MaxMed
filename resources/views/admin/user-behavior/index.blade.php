@extends('admin.layouts.app')

@section('title', 'User Behavior Analytics')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">User Behavior Analytics</h1>
        <p class="text-gray-600">Monitor user interactions, clicks, and engagement across your site.</p>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center gap-4">
            <label for="dateRange" class="text-sm font-medium text-gray-700">Date Range:</label>
            <select id="dateRange" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                <option value="7">Last 7 days</option>
                <option value="30" selected>Last 30 days</option>
                <option value="90">Last 90 days</option>
            </select>
        </div>
        <button id="refreshData" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
            Refresh Data
        </button>
    </div>

    <!-- Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6 flex items-center">
            <div class="flex-shrink-0">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-600">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                </div>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total Events</p>
                <p class="text-2xl font-bold text-gray-900" id="totalEvents">-</p>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6 flex items-center">
            <div class="flex-shrink-0">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-600">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Page Views</p>
                <p class="text-2xl font-bold text-gray-900" id="pageViews">-</p>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6 flex items-center">
            <div class="flex-shrink-0">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-yellow-600">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.122 2.122" /></svg>
                </div>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Clicks</p>
                <p class="text-2xl font-bold text-gray-900" id="totalClicks">-</p>
            </div>
        </div>
    </div>

    <!-- Enhanced Analytics Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- Event Type Breakdown -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Event Type Breakdown</h3>
            <canvas id="eventTypeChart" height="180"></canvas>
        </div>
        <!-- Device/Browser/OS Stats -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Device, Browser & OS</h3>
            <div class="flex flex-wrap gap-4">
                <div><canvas id="deviceChart" width="120" height="120"></canvas></div>
                <div><canvas id="browserChart" width="120" height="120"></canvas></div>
                <div><canvas id="osChart" width="120" height="120"></canvas></div>
            </div>
        </div>
    </div>
    <!-- Scroll Depth & Time on Page -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Scroll Depth Stats</h3>
            <ul class="text-sm text-gray-700">
                <li>Average: <span id="avgScrollDepth">-</span>%</li>
                <li>Max: <span id="maxScrollDepth">-</span>%</li>
                <li>Min: <span id="minScrollDepth">-</span>%</li>
            </ul>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Time on Page</h3>
            <ul class="text-sm text-gray-700">
                <li>Average: <span id="avgTimeOnPage">-</span> sec</li>
            </ul>
        </div>
    </div>
    <!-- Top Pages & Referrers -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Pages</h3>
            <ul id="topPages" class="text-sm text-gray-700"></ul>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Referrers</h3>
            <ul id="topReferrers" class="text-sm text-gray-700"></ul>
        </div>
    </div>
    <!-- Export Button -->
    <div class="mb-8">
        <button id="exportBtn" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition">Export Data (CSV)</button>
    </div>
    <!-- Session Timeline & Heatmap Placeholder -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Session Timeline & Click Heatmap</h3>
        <div class="text-gray-500">(Coming soon: Visualize a user's session and click heatmap here.)</div>
    </div>

    <!-- Most Clicked Elements -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Most Clicked Elements</h3>
        <div class="space-y-3" id="mostClickedElements">
            <div class="text-gray-500 text-center py-4">Loading...</div>
        </div>
    </div>

    <!-- Recent Events Table -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent User Events</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Page</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="recentEventsTable">
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">Loading...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateRange = document.getElementById('dateRange');
    const refreshBtn = document.getElementById('refreshData');

    function loadAnalyticsData() {
        const days = dateRange.value;
        fetch(`/api/user-behavior/analytics?days=${days}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateDashboard(data.data);
                } else {
                    console.error('Failed to load analytics data');
                }
            })
            .catch(error => {
                console.error('Error loading analytics:', error);
            });
    }

    function updateDashboard(data) {
        // Update overview cards
        document.getElementById('totalEvents').textContent = data.total_events?.toLocaleString() || '0';
        document.getElementById('pageViews').textContent = data.page_views?.toLocaleString() || '0';
        document.getElementById('totalClicks').textContent = data.clicks?.toLocaleString() || '0';

        // Update most clicked elements
        updateMostClickedElements(data.most_clicked_elements || []);

        // Update recent events table
        updateRecentEventsTable(data.recent_events || []);

        // Enhanced: Event Type Breakdown
        renderBarChart('eventTypeChart', data.event_type_breakdown);
        // Enhanced: Device/Browser/OS
        renderPieChart('deviceChart', data.device_stats);
        renderPieChart('browserChart', data.browser_stats);
        renderPieChart('osChart', data.os_stats);
        // Enhanced: Scroll Depth & Time on Page
        document.getElementById('avgScrollDepth').textContent = Math.round(data.scroll_depth_stats?.average_depth || 0);
        document.getElementById('maxScrollDepth').textContent = Math.round(data.scroll_depth_stats?.max_depth || 0);
        document.getElementById('minScrollDepth').textContent = Math.round(data.scroll_depth_stats?.min_depth || 0);
        document.getElementById('avgTimeOnPage').textContent = Math.round(data.average_time_on_page || 0);
        // Enhanced: Top Pages & Referrers
        updateList('topPages', data.top_pages, 'page_url');
        updateList('topReferrers', data.top_referrers, 'referrer_url');
    }

    function updateMostClickedElements(elements) {
        const container = document.getElementById('mostClickedElements');
        if (elements.length === 0) {
            container.innerHTML = '<div class="text-gray-500 text-center py-4">No click data available</div>';
            return;
        }
        container.innerHTML = elements.map(element => `
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div class="flex-1">
                    <div class="text-sm font-medium text-gray-900">${element.text || element.selector}</div>
                    <div class="text-xs text-gray-500">${element.selector}</div>
                </div>
                <div class="text-sm font-semibold text-blue-600">${element.click_count} clicks</div>
            </div>
        `).join('');
    }

    function updateRecentEventsTable(events) {
        const table = document.getElementById('recentEventsTable');
        if (events.length === 0) {
            table.innerHTML = '<tr><td colspan="4" class="px-6 py-4 text-center text-gray-500">No recent events found</td></tr>';
            return;
        }
        table.innerHTML = events.map(event => `
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${event.timestamp ? new Date(event.timestamp).toLocaleString() : ''}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-700 font-semibold">${event.event_type}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${event.page_url || ''}</td>
                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">${event.details || ''}</td>
            </tr>
        `).join('');
    }

    function renderBarChart(canvasId, dataObj) {
        const ctx = document.getElementById(canvasId).getContext('2d');
        if (window[canvasId]) window[canvasId].destroy();
        window[canvasId] = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: Object.keys(dataObj || {}),
                datasets: [{
                    label: 'Events',
                    data: Object.values(dataObj || {}),
                    backgroundColor: 'rgba(59,130,246,0.7)',
                }]
            },
            options: {responsive: true, plugins: {legend: {display: false}}}
        });
    }
    function renderPieChart(canvasId, dataObj) {
        const ctx = document.getElementById(canvasId).getContext('2d');
        if (window[canvasId]) window[canvasId].destroy();
        window[canvasId] = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: Object.keys(dataObj || {}),
                datasets: [{
                    data: Object.values(dataObj || {}),
                    backgroundColor: [
                        '#2563eb','#16a34a','#f59e42','#eab308','#f43f5e','#a21caf','#0ea5e9','#f472b6','#64748b','#facc15'
                    ]
                }]
            },
            options: {responsive: true, plugins: {legend: {position: 'bottom'}}}
        });
    }
    function updateList(listId, items, key) {
        const ul = document.getElementById(listId);
        if (!items || items.length === 0) {
            ul.innerHTML = '<li class="text-gray-400">No data available</li>';
            return;
        }
        ul.innerHTML = items.map(item => `<li>${item[key] || '(none)'} <span class="text-xs text-gray-500">(${item.count})</span></li>`).join('');
    }

    // Initial load
    loadAnalyticsData();
    dateRange.addEventListener('change', loadAnalyticsData);
    refreshBtn.addEventListener('click', loadAnalyticsData);

    // Export Data (CSV)
    document.getElementById('exportBtn').addEventListener('click', function() {
        fetch(`/api/user-behavior/analytics?days=${dateRange.value}`)
            .then(r => r.json())
            .then(data => {
                if (data.success && data.data.export_data) {
                    const rows = data.data.export_data;
                    if (!rows.length) return alert('No data to export!');
                    const header = Object.keys(rows[0]);
                    const csv = [header.join(',')].concat(rows.map(row => header.map(h => JSON.stringify(row[h] ?? '')).join(','))).join('\n');
                    const blob = new Blob([csv], {type: 'text/csv'});
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `user_behavior_export.csv`;
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                }
            });
    });
});
</script>
@endsection 