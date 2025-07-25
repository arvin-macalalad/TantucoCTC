<!-- partial:partials/_navbar.html -->
<div class="horizontal-menu">
    <nav class="navbar top-navbar">
        <div class="container">
            <div class="navbar-content">

                <a href="#" class="navbar-brand d-none d-lg-flex">
                    Tantuco<span>CTC</span>
                </a>

                <!-- Logo-mini for small screen devices (mobile/tablet) -->
                <div class="logo-mini-wrapper">
                    <img src="{{ asset('assets/dashboard/images/logo-mini-light.png') }}" class="logo-mini logo-mini-light" alt="logo">
                    <img src="{{ asset('assets/dashboard/images/logo-mini-dark.png') }}" class="logo-mini logo-mini-dark" alt="logo">
                </div>

                <!-- <form class="search-form">
                    <div class="input-group">
                        <div class="input-group-text">
                            <i data-lucide="search"></i>
                        </div>
                        <input type="text" class="form-control" id="navbarForm" placeholder="Search here...">
                    </div>
                </form> -->

                <ul class="navbar-nav">
                    <li class="theme-switcher-wrapper nav-item">
                        <input type="checkbox" value="" id="theme-switcher">
                        <label for="theme-switcher">
                            <div class="box">
                                <div class="ball"></div>
                                <div class="icons">
                                    <i data-lucide="sun"></i>
                                    <i data-lucide="moon"></i>
                                </div>
                            </div>
                        </label>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="messageDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i data-lucide="mail"></i>
                            <div class="indicator d-none" id="messageIndicator">
                                <div class="circle"></div>
                            </div>
                        </a>
                        <div class="dropdown-menu p-0" aria-labelledby="messageDropdown">
                            <div class="px-3 py-2 d-flex align-items-center justify-content-between border-bottom">
                                <p id="messageCount">0 New Messages</p>
                                <a href="javascript:;" class="text-secondary mx-2">Clear all</a>
                            </div>
                            <div class="p-1" id="recentMessagesList">
                                <div class="dropdown-item py-2 text-center text-muted">No new messages</div>
                            </div>
                            <div class="px-3 py-2 d-flex align-items-center justify-content-center border-top">
                                <a href="{{ route('chat.index') }}">Go to Messages</a>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i data-lucide="bell"></i>
                            <div class="indicator" id="notificationIndicator" style="display:none;">
                                <div class="circle"></div>
                            </div>
                        </a>
                        <div class="dropdown-menu p-0" aria-labelledby="notificationDropdown">
                            <div class="px-3 py-2 d-flex align-items-center justify-content-between border-bottom">
                                <p id="notificationCount">0 New Notifications</p>
                                <a href="javascript:;" class="text-secondary mx-2" onclick="markAllNotificationsRead()">Clear all</a>
                            </div>
                            <div class="p-1" id="notificationItems">
                                <div class="text-center py-2 text-muted">Loading...</div>
                            </div>
                            <div class="px-3 py-2 d-flex align-items-center justify-content-center border-top">
                                <a href="{{ route('notification.index') }}">View all</a>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img class="w-30px h-30px ms-1 rounded-circle profile-image" src="#" alt="profile">
                        </a>
                        <div class="dropdown-menu p-0" aria-labelledby="profileDropdown">
                            <div class="d-flex flex-column align-items-center border-bottom px-5 py-3">
                                <div class="mb-3">
                                    <img class="w-80px h-80px rounded-circle profile-image" src="#" alt="">
                                </div>
                                <div class="text-center">
                                    <p class="fs-16px fw-bolder profile-name"></p>
                                    <p class="fs-12px text-secondary profile-email"></p>
                                </div>
                            </div>
                            <ul class="list-unstyled p-1">
                                <li>
                                    <a href="{{ route('profile.settings') }}" class="dropdown-item py-2 text-body ms-0">
                                        <i class="me-2 icon-md" data-lucide="user"></i>
                                        <span>Profile</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form-admin').submit();" class="dropdown-item py-2 text-body ms-0">
                                        <i class="me-2 icon-md" data-lucide="log-out"></i>
                                        <span>Log Out</span>
                                    </a>

                                    <form id="logout-form-admin" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </li>
                </ul>

                <!-- navbar toggler for small devices -->
                <div data-toggle="horizontal-menu-toggle" class="navbar-toggler navbar-toggler-right d-lg-none align-self-center">
                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>
                </div>

            </div>
        </div>
    </nav>
    <nav class="bottom-navbar">
        <div class="container">
            <ul class="nav page-navigation">
                <li class="nav-item {{ Route::is('home') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('home') }}">
                        <i class="link-icon" data-lucide="layout-dashboard"></i>
                        <span class="menu-title">Dashboard</span>
                    </a>
                </li>

                @if(Auth::user()->role === 'salesofficer')

                <li class="nav-item {{ Route::is('b2b-creation.*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('b2b-creation.index') }}">
                        <i class="link-icon" data-lucide="users"></i>
                        <span class="menu-title">B2B Customers</span>
                    </a>
                </li>

                <li class="nav-item {{ Route::is('salesofficer.purchase-requests.index') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('salesofficer.purchase-requests.index') }}">
                        <i class="link-icon" data-lucide="shopping-bag"></i>
                        <span class="menu-title">Pending Purchase Request</span>
                    </a>
                </li>

                <li class="nav-item {{ Route::is('salesofficer.sent-quotations.index') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('salesofficer.send-quotations.index') }}">
                        <i class="link-icon" data-lucide="scroll-text"></i>
                        <span class="menu-title">Sent Quotations</span>
                    </a>
                </li>

                <li class="nav-item {{ Route::is('salesofficer.submitted-order.index') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('salesofficer.submitted-order.index') }}">
                        <i class="link-icon" data-lucide="list-ordered"></i>
                        <span class="menu-title">Submitted Purchase Orders</span>
                    </a>
                </li>

                @elseif(Auth::user()->role === 'deliveryrider')

                <li class="nav-item {{ Route::is('deliveryrider.delivery.orders') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('deliveryrider.delivery.orders') }}">
                        <i class="link-icon" data-lucide="box"></i>
                        <span class="menu-title">Delivery Orders</span>
                    </a>
                </li>

                <li class="nav-item {{ Route::is('deliveryrider.delivery.histories') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('deliveryrider.delivery.histories') }}">
                        <i class="link-icon" data-lucide="clock"></i>
                        <span class="menu-title">Delivery History</span>
                    </a>
                </li>

                <li class="nav-item  {{ Route::is('deliveryrider.delivery.location') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('deliveryrider.delivery.location') }}">
                        <i class="link-icon" data-lucide="truck"></i>
                        <span class="menu-title">Location Tracking</span>
                    </a>
                </li>

                <li class="nav-item  {{ Route::is('deliveryrider.delivery.ratings') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('deliveryrider.delivery.ratings') }}">
                        <i class="link-icon" data-lucide="star"></i>
                        <span class="menu-title">Ratings</span>
                    </a>
                </li>

                @else
                @endif
            </ul>
        </div>
    </nav>
</div>
<!-- partial -->