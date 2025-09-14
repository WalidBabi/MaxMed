<!-- Sidebar component -->
<div class="flex grow flex-col gap-y-5 overflow-y-auto crm-sidebar px-6 pb-4 shadow-lg">
    <!-- Logo -->
    <div class="flex h-16 shrink-0 items-center">
        <img class="mb-1 h-12 w-auto" src="{{ asset('Images/logo.png') }}" alt="MaxMed">
        <div class="ml-3">
            <div class="text-sm font-semibold text-gray-600">{{ \App\Helpers\DashboardHelper::crmPortalHeaderName() }}</div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex flex-1 flex-col">
        <ul role="list" class="flex flex-1 flex-col gap-y-7">
            <li>
                <ul role="list" class="-mx-2 space-y-1">
                    <!-- Dashboard -->
                    <li>
                        <a href="{{ route('crm.dashboard') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('crm.dashboard') ? 'sidebar-active' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }}">
                            <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                            </svg>
                            Dashboard
                        </a>
                    </li>

                    <!-- Sales Pipeline - Lead to Deal Conversion -->
                    @if(Auth::user()->hasPermission('crm.leads.view') || Auth::user()->isAdmin())
                    <li>
                        <a href="{{ route('crm.leads.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('crm.leads.*') ? 'sidebar-active' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }}">
                            <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l-1-3m1 3l-1-3m-16.5-3h16.5" />
                            </svg>
                            Sales Pipeline
                        </a>
                    </li>
                    @endif

                    <!-- Customer Interactions Section -->
                    @if(Auth::user()->hasPermission('customers.view') || Auth::user()->hasPermission('quotations.view') || Auth::user()->isAdmin())
                    <li x-data="{ open: {{ request()->routeIs('crm.contact-submissions.*') || request()->routeIs('crm.quotation-requests.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" class="group flex w-full items-center gap-x-3 rounded-md p-2 text-left text-sm leading-6 font-semibold {{ request()->routeIs('crm.contact-submissions.*') || request()->routeIs('crm.quotation-requests.*') ? 'text-indigo-600 bg-gray-50' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }}">
                            <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                            </svg>
                            Customer Interactions
                            <svg :class="{ 'rotate-90': open }" class="ml-auto h-5 w-5 shrink-0 transition-transform duration-200" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <ul x-show="open" x-transition class="mt-1 px-2 space-y-1">
                            @if(Auth::user()->hasPermission('customers.view') || Auth::user()->isAdmin())
                            <li>
                                <a href="{{ route('crm.contact-submissions.index') }}" class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('crm.contact-submissions.*') ? 'text-indigo-600 bg-gray-50 font-semibold' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">
                                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                    </svg>
                                    Contact Submissions
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->hasPermission('quotations.view') || Auth::user()->isAdmin())
                            <li>
                                <a href="{{ route('crm.quotation-requests.index') }}" class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('crm.quotation-requests.*') ? 'text-indigo-600 bg-gray-50 font-semibold' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">
                                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                    </svg>
                                    Quotation Requests
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif

                    <!-- Customer Management -->
                    @if(Auth::user()->hasPermission('customers.view') || Auth::user()->isAdmin())
                    <li>
                        <a href="{{ route('crm.customers.index') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('crm.customers.*') ? 'sidebar-active' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }}">
                            <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                            </svg>
                            Customer Management
                        </a>
                    </li>
                    @endif

                    <!-- Marketing Section -->
                    @if(Auth::user()->hasPermission('marketing.view') || Auth::user()->hasPermission('marketing.campaigns.view') || Auth::user()->isAdmin())
                    <li x-data="{ open: {{ request()->routeIs('crm.marketing.*') ? 'true' : 'false' }} }">
                        <button @click="open = !open" class="group flex w-full items-center gap-x-3 rounded-md p-2 text-left text-sm leading-6 font-semibold {{ request()->routeIs('crm.marketing.*') ? 'text-indigo-600 bg-gray-50' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }}">
                            <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 110-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 01-1.44-4.282m3.102.069a18.03 18.03 0 01-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 018.835 2.535M10.34 6.66a23.847 23.847 0 008.835-2.535m0 0A23.74 23.74 0 0018.795 3m.38 1.125a23.91 23.91 0 011.014 5.395m-1.014 8.855c-.118.38-.245.754-.38 1.125m.38-1.125a23.91 23.91 0 001.014-5.395m0 0c.624-2.685.389-5.49-.38-7.65m0 7.65a24.526 24.526 0 01-5.478 3.985" />
                            </svg>
                            Marketing
                            <svg :class="{ 'rotate-90': open }" class="ml-auto h-5 w-5 shrink-0 transition-transform duration-200" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <ul x-show="open" x-transition class="mt-1 px-2 space-y-1">
                            @if(Auth::user()->hasPermission('marketing.view') || Auth::user()->isAdmin())
                            <li>
                                <a href="{{ route('crm.marketing.dashboard') }}" class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('crm.marketing.dashboard') ? 'text-indigo-600 bg-gray-50 font-semibold' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">
                                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                                    </svg>
                                    Dashboard
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->hasPermission('marketing.contacts.view') || Auth::user()->isAdmin())
                            <li>
                                <a href="{{ route('crm.marketing.contacts.index') }}" class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('crm.marketing.contacts.*') ? 'text-indigo-600 bg-gray-50 font-semibold' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">
                                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                    </svg>
                                    Contacts
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->hasPermission('marketing.contact-lists.view') || Auth::user()->isAdmin())
                            <li>
                                <a href="{{ route('crm.marketing.contact-lists.index') }}" class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('crm.marketing.contact-lists.*') ? 'text-indigo-600 bg-gray-50 font-semibold' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">
                                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 17.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                    </svg>
                                    Contact Lists
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->hasPermission('marketing.campaigns.view') || Auth::user()->isAdmin())
                            <li>
                                <a href="{{ route('crm.marketing.campaigns.index') }}" class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('crm.marketing.campaigns.*') ? 'text-indigo-600 bg-gray-50 font-semibold' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">
                                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 110-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 01-1.44-4.282m3.102.069a18.03 18.03 0 01-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 018.835 2.535M10.34 6.66a23.847 23.847 0 008.835-2.535m0 0A23.74 23.74 0 0018.795 3m.38 1.125a23.91 23.91 0 011.014 5.395m-1.014 8.855c-.118.38-.245.754-.38 1.125m.38-1.125a23.91 23.91 0 001.014-5.395m0 0c.624-2.685.389-5.49-.38-7.65m0 7.65a24.526 24.526 0 01-5.478 3.985" />
                                    </svg>
                                    Campaigns
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->hasPermission('marketing.email-templates.view') || Auth::user()->isAdmin())
                            <li>
                                <a href="{{ route('crm.marketing.email-templates.index') }}" class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('crm.marketing.email-templates.*') ? 'text-indigo-600 bg-gray-50 font-semibold' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">
                                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                    </svg>
                                    Email Templates
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->hasPermission('marketing.analytics.view') || Auth::user()->isAdmin())
                            <li>
                                <a href="{{ route('crm.marketing.analytics.index') }}" class="group flex gap-x-3 rounded-md py-2 pl-9 pr-2 text-sm leading-6 {{ request()->routeIs('crm.marketing.analytics.*') ? 'text-indigo-600 bg-gray-50 font-semibold' : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50' }}">
                                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                                    </svg>
                                    Analytics
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif

                  
                </ul>
            </li>

            <!-- Portal Access -->
            <li class="mt-auto">
                <ul role="list" class="-mx-2 space-y-1">
                    <li x-data="{ open: false }">
                        <button @click="open = !open" class="group flex w-full items-center gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold text-gray-700 hover:text-indigo-600 hover:bg-gray-50">
                            <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="flex-1 text-left">Portals</span>
                            <svg class="h-4 w-4 shrink-0 transition-transform" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </button>
                        <ul x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="ml-6 mt-1 space-y-1" style="display: none;">
                            @if(\App\Services\AccessControlService::canAccessAdmin(Auth::user()))
                            <li>
                                <a href="{{ route('admin.dashboard') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 text-gray-600 hover:text-indigo-600 hover:bg-gray-50">
                                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    {{ \App\Helpers\DashboardHelper::adminPortalHeaderName() }}
                                </a>
                            </li>
                            @endif
                            @if(\App\Services\AccessControlService::canAccessSupplier(Auth::user()))
                            <li>
                                <a href="{{ route('supplier.dashboard') }}" class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 text-gray-600 hover:text-indigo-600 hover:bg-gray-50">
                                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                                    </svg>
                                    {{ \App\Helpers\DashboardHelper::supplierPortalHeaderName() }}
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
</div> 