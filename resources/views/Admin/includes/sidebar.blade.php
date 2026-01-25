<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('admin.dashboard') }}">
        <div class="sidebar-brand-icon rotate-n-15"><i class="fas fa-laugh-wink"></i></div>
        <div class="sidebar-brand-text mx-3">Apka Budget</div>
    </a>

    <hr class="sidebar-divider my-0">

    <!-- Dashboard -->
    @if(hasPermission('dashboard'))
    <li class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.dashboard') }}">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>
    @endif

    <hr class="sidebar-divider">

    <!-- User Management -->
    @if(hasPermission('users') || hasPermission('providers'))
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUser" aria-expanded="true">
            <i class="fas fa-users"></i>
            <span>User Management</span>
        </a>
        <div id="collapseUser" class="collapse" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                @if(hasPermission('users'))
                <a class="collapse-item {{ request()->routeIs('admin.users') ? 'active' : '' }}" href="{{ route('admin.users') }}">Users</a>
                @endif
                @if(hasPermission('providers'))
                <a class="collapse-item {{ request()->routeIs('admin.providers') ? 'active' : '' }}" href="{{ route('admin.providers') }}">Service Providers</a>
                @endif
            </div>
        </div>
    </li>
    @endif

    <!-- Services & Media -->
    @if(hasPermission('categories') || hasPermission('banners') || hasPermission('service_videos'))
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseServices" aria-expanded="true">
            <i class="fas fa-tags"></i>
            <span>Services & Media</span>
        </a>
        <div id="collapseServices" class="collapse" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                @if(hasPermission('categories'))
                <a class="collapse-item {{ request()->routeIs('admin.categories') ? 'active' : '' }}" href="{{ route('admin.categories') }}">Services</a>
                @endif
                @if(hasPermission('banners'))
                <a class="collapse-item {{ request()->routeIs('admin.banners') ? 'active' : '' }}" href="{{ route('admin.banners') }}">Banners</a>
                @endif
                @if(hasPermission('service_videos'))
                <a class="collapse-item {{ request()->routeIs('admin.service_videos') ? 'active' : '' }}" href="{{ route('admin.service_videos') }}">Service Videos</a>
                @endif
            </div>
        </div>
    </li>
    @endif

    <!-- CMS Pages -->
    @if(hasPermission('about_us') || hasPermission('privacy_policy') || hasPermission('contact_us') || hasPermission('terms'))
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseCMS" aria-expanded="true">
            <i class="fas fa-info-circle"></i>
            <span>CMS Pages</span>
        </a>
        <div id="collapseCMS" class="collapse" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                @if(hasPermission('about_us'))
                <a class="collapse-item {{ request()->routeIs('admin.about_us') ? 'active' : '' }}" href="{{ route('admin.about_us') }}">About Us</a>
                @endif
                @if(hasPermission('privacy_policy'))
                <a class="collapse-item {{ request()->routeIs('admin.privacy_policy') ? 'active' : '' }}" href="{{ route('admin.privacy_policy') }}">Privacy Policy</a>
                @endif
                @if(hasPermission('contact_us'))
                <a class="collapse-item {{ request()->routeIs('admin.contact_us') ? 'active' : '' }}" href="{{ route('admin.contact_us') }}">Contact Us</a>
                @endif
                @if(hasPermission('terms'))
                <a class="collapse-item {{ request()->routeIs('admin.terms.index') ? 'active' : '' }}" href="{{ route('admin.terms.index') }}">Terms & Condition</a>
                @endif
            </div>
        </div>
    </li>
    @endif

    <!-- Operations -->
    @if(hasPermission('all_bookings') || hasPermission('complaints') || hasPermission('transaction'))
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseOperations" aria-expanded="true">
            <i class="fas fa-cogs"></i>
            <span>Operations</span>
        </a>
        <div id="collapseOperations" class="collapse" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                @if(hasPermission('all_bookings'))
                <a class="collapse-item {{ request()->routeIs('admin.all_bookings') ? 'active' : '' }}" href="{{ route('admin.all_bookings') }}">Bookings</a>
                @endif
                @if(hasPermission('complaints'))
                <a class="collapse-item {{ request()->routeIs('admin.complaints') ? 'active' : '' }}" href="{{ route('admin.complaints') }}">Complaints</a>
                @endif
                @if(hasPermission('transaction'))
                <a class="collapse-item {{ request()->routeIs('admin.transaction') ? 'active' : '' }}" href="{{ route('admin.transaction') }}">Transactions</a>
                @endif
            </div>
        </div>
    </li>
    @endif

    <!-- Location Settings -->
    @if(hasPermission('zones') || hasPermission('countries'))
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseLocation" aria-expanded="true">
            <i class="fas fa-map-marker-alt"></i>
            <span>Location Settings</span>
        </a>
        <div id="collapseLocation" class="collapse" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                @if(hasPermission('zones'))
                <a class="collapse-item {{ request()->routeIs('admin.zones') ? 'active' : '' }}" href="{{ route('admin.zones') }}">Zones</a>
                @endif
                @if(hasPermission('countries'))
                <a class="collapse-item {{ request()->routeIs('admin.countries') ? 'active' : '' }}" href="{{ route('admin.countries') }}">GeoZone</a>
                @endif
            </div>
        </div>
    </li>
    @endif

    <!-- Subadmins -->
    @if(hasPermission('subadmins'))
    <li class="nav-item {{ request()->routeIs('admin.subadmins') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.subadmins') }}">
            <i class="fas fa-user-shield"></i>
            <span>Subadmins</span>
        </a>
    </li>
    @endif
    
    <!-- All Report -->
    @if(hasPermission('all_report') || hasPermission('provider_report'))
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseReport" aria-expanded="true">
            <i class="fas fa-file"></i>
            <span>All Reports</span>
        </a>
        <div id="collapseReport" class="collapse" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                @if(hasPermission('all_report'))
                <a class="collapse-item {{ request()->routeIs('admin.all-report') ? 'active' : '' }}" href="{{ route('admin.all-report') }}">All Reports Datewise</a>
                @endif
                @if(hasPermission('provider_report'))
                <a class="collapse-item {{ request()->routeIs('admin.provider-report') ? 'active' : '' }}" href="{{ route('admin.provider-report') }}">Provider Reports</a>
                @endif
            </div>
        </div>
    </li>
    @endif


       <!-- Rating Review -->
    @if(hasPermission('rating_review'))
    <li class="nav-item {{ request()->routeIs('admin.rating_review') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.rating_review') }}">
            <i class="fas fa-user-shield"></i>
            <span>Rating & Review</span>
        </a>
    </li>
    @endif
    
     <!-- Quotations -->
    @if(hasPermission('quotation'))
    <li class="nav-item {{ request()->routeIs('admin.quotations') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.quotations') }}">
            <i class="fas fa-clipboard-list"></i> 
            <span>Quotation</span>
        </a>
    </li>
    @endif
    
    <li class="nav-item {{ request()->routeIs('admin.partners_data') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.partners_data') }}">
            <i class="fas fa-user-shield"></i>
            <span>Partners Data</span>
        </a>
    </li>
    
    <li class="nav-item {{ request()->routeIs('admin.partners_data') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.partners_contactlist') }}">
            <i class="fas fa-user-shield"></i>
            <span>Partners Contact List</span>
        </a>
    </li>
    
    <li class="nav-item {{ request()->routeIs('admin.attendances') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.attendances') }}">
            <i class="fas fa-user-shield"></i>
            <span>Attendances</span>
        </a>
    </li>


    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
