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
                    <!-- <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex" href="#" id="languageDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img src="https://nobleui.com/html/template/assets/images/flags/us.svg" class="w-20px" title="us" alt="flag">
                            <span class="ms-2 d-none d-md-inline-block">English</span>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="languageDropdown">
                            <a href="javascript:;" class="dropdown-item py-2 d-flex"><img src="https://nobleui.com/html/template/assets/images/flags/us.svg" class="w-20px" title="us" alt="us"> <span class="ms-2"> English </span></a>
                            <a href="javascript:;" class="dropdown-item py-2 d-flex"><img src="https://nobleui.com/html/template/assets/images/flags/fr.svg" class="w-20px" title="fr" alt="fr"> <span class="ms-2"> French </span></a>
                            <a href="javascript:;" class="dropdown-item py-2 d-flex"><img src="https://nobleui.com/html/template/assets/images/flags/de.svg" class="w-20px" title="de" alt="de"> <span class="ms-2"> German </span></a>
                            <a href="javascript:;" class="dropdown-item py-2 d-flex"><img src="https://nobleui.com/html/template/assets/images/flags/pt.svg" class="w-20px" title="pt" alt="pt"> <span class="ms-2"> Portuguese </span></a>
                            <a href="javascript:;" class="dropdown-item py-2 d-flex"><img src="https://nobleui.com/html/template/assets/images/flags/es.svg" class="w-20px" title="es" alt="es"> <span class="ms-2"> Spanish </span></a>
                        </div>
                    </li> -->
                    <!-- <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="appsDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i data-lucide="layout-grid"></i>
                        </a>
                        <div class="dropdown-menu p-0" aria-labelledby="appsDropdown">
                            <div class="px-3 py-2 d-flex align-items-center justify-content-between border-bottom">
                                <p class="mb-0 fw-bold">Web Apps</p>
                                <a href="javascript:;" class="text-secondary">Edit</a>
                            </div>
                            <div class="row g-0 p-1">
                                <div class="col-3 text-center">
                                    <a href="pages/apps/chat.html" class="dropdown-item d-flex flex-column align-items-center justify-content-center w-70px h-70px"><i data-lucide="message-square" class="icon-lg mb-1"></i>
                                        <p class="fs-12px">Chat</p>
                                    </a>
                                </div>
                                <div class="col-3 text-center">
                                    <a href="pages/apps/calendar.html" class="dropdown-item d-flex flex-column align-items-center justify-content-center w-70px h-70px"><i data-lucide="calendar" class="icon-lg mb-1"></i>
                                        <p class="fs-12px">Calendar</p>
                                    </a>
                                </div>
                                <div class="col-3 text-center">
                                    <a href="pages/email/inbox.html" class="dropdown-item d-flex flex-column align-items-center justify-content-center w-70px h-70px"><i data-lucide="mail" class="icon-lg mb-1"></i>
                                        <p class="fs-12px">Email</p>
                                    </a>
                                </div>
                                <div class="col-3 text-center">
                                    <a href="pages/general/profile.html" class="dropdown-item d-flex flex-column align-items-center justify-content-center w-70px h-70px"><i data-lucide="instagram" class="icon-lg mb-1"></i>
                                        <p class="fs-12px">Profile</p>
                                    </a>
                                </div>
                            </div>
                            <div class="px-3 py-2 d-flex align-items-center justify-content-center border-top">
                                <a href="javascript:;">View all</a>
                            </div>
                        </div>
                    </li> -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="messageDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i data-lucide="mail"></i>
                        </a>
                        <div class="dropdown-menu p-0" aria-labelledby="messageDropdown">
                            <div class="px-3 py-2 d-flex align-items-center justify-content-between border-bottom">
                                <p>{{ $recentMessages->count() }} New Message{{ $recentMessages->count() !== 1 ? 's' : '' }}</p>
                                <a href="javascript:;" class="text-secondary">Clear all</a>
                            </div>
                            <div class="p-1">
                                @forelse ($recentMessages as $msg)
                                <a href="{{ route('chat.index') }}" class="dropdown-item d-flex align-items-center py-2">
                                    <div class="me-3">
                                        <img class="w-30px h-30px rounded-circle" src="{{ $msg->sender->profile ? asset($msg->sender->profile) : asset('assets/avatars/' . rand(1, 17) . '.avif') }}" alt="user">
                                    </div>
                                    <div class="d-flex justify-content-between flex-grow-1">
                                        <div class="me-4">
                                            <p class="mb-0">{{ $msg->sender->name }}</p>
                                            <p class="fs-12px text-secondary mb-0">{{ \Illuminate\Support\Str::limit($msg->text, 30) }}</p>
                                        </div>
                                        <p class="fs-12px text-secondary mb-0">{{ $msg->created_at->diffForHumans() }}</p>
                                    </div>
                                </a>
                                @empty
                                <div class="dropdown-item py-2 text-center text-muted">No new messages</div>
                                @endforelse
                            </div>
                            <div class="px-3 py-2 d-flex align-items-center justify-content-center border-top">
                                <a href="{{ route('chat.index') }}">View all</a>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i data-lucide="bell"></i>
                            <div class="indicator">
                                <div class="circle"></div>
                            </div>
                        </a>
                        <div class="dropdown-menu p-0" aria-labelledby="notificationDropdown">
                            <div class="px-3 py-2 d-flex align-items-center justify-content-between border-bottom">
                                <p>6 New Notifications</p>
                                <a href="javascript:;" class="text-secondary">Clear all</a>
                            </div>
                            <div class="p-1">
                                <a href="javascript:;" class="dropdown-item d-flex align-items-center py-2">
                                    <div class="w-30px h-30px d-flex align-items-center justify-content-center bg-primary rounded-circle me-3">
                                        <i class="icon-sm text-white" data-lucide="gift"></i>
                                    </div>
                                    <div class="flex-grow-1 me-2">
                                        <p>New Order Recieved</p>
                                        <p class="fs-12px text-secondary">30 min ago</p>
                                    </div>
                                </a>
                                <a href="javascript:;" class="dropdown-item d-flex align-items-center py-2">
                                    <div class="w-30px h-30px d-flex align-items-center justify-content-center bg-primary rounded-circle me-3">
                                        <i class="icon-sm text-white" data-lucide="alert-circle"></i>
                                    </div>
                                    <div class="flex-grow-1 me-2">
                                        <p>Server Limit Reached!</p>
                                        <p class="fs-12px text-secondary">1 hrs ago</p>
                                    </div>
                                </a>
                                <a href="javascript:;" class="dropdown-item d-flex align-items-center py-2">
                                    <div class="w-30px h-30px d-flex align-items-center justify-content-center bg-primary rounded-circle me-3">
                                        <img class="w-30px h-30px rounded-circle" src="{{ asset('assets/dashboard/images/faces/face6.jpg') }}" alt="userr">
                                    </div>
                                    <div class="flex-grow-1 me-2">
                                        <p>New customer registered</p>
                                        <p class="fs-12px text-secondary">2 sec ago</p>
                                    </div>
                                </a>
                                <a href="javascript:;" class="dropdown-item d-flex align-items-center py-2">
                                    <div class="w-30px h-30px d-flex align-items-center justify-content-center bg-primary rounded-circle me-3">
                                        <i class="icon-sm text-white" data-lucide="layers"></i>
                                    </div>
                                    <div class="flex-grow-1 me-2">
                                        <p>Apps are ready for update</p>
                                        <p class="fs-12px text-secondary">5 hrs ago</p>
                                    </div>
                                </a>
                                <a href="javascript:;" class="dropdown-item d-flex align-items-center py-2">
                                    <div class="w-30px h-30px d-flex align-items-center justify-content-center bg-primary rounded-circle me-3">
                                        <i class="icon-sm text-white" data-lucide="download"></i>
                                    </div>
                                    <div class="flex-grow-1 me-2">
                                        <p>Download completed</p>
                                        <p class="fs-12px text-secondary">6 hrs ago</p>
                                    </div>
                                </a>
                            </div>
                            <div class="px-3 py-2 d-flex align-items-center justify-content-center border-top">
                                <a href="javascript:;">View all</a>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img class="w-30px h-30px ms-1 rounded-circle" src="{{ asset('assets/dashboard/images/faces/face1.jpg') }}" alt="profile">
                        </a>
                        <div class="dropdown-menu p-0" aria-labelledby="profileDropdown">
                            <div class="d-flex flex-column align-items-center border-bottom px-5 py-3">
                                <div class="mb-3">
                                    <img class="w-80px h-80px rounded-circle" src="{{ asset('assets/dashboard/images/faces/face1.jpg') }}" alt="">
                                </div>
                                <div class="text-center">
                                    <p class="fs-16px fw-bolder">Amiah Burton</p>
                                    <p class="fs-12px text-secondary">amiahburton@gmail.com</p>
                                </div>
                            </div>
                            <ul class="list-unstyled p-1">
                                <li>
                                    <a href="pages/general/profile.html" class="dropdown-item py-2 text-body ms-0">
                                        <i class="me-2 icon-md" data-lucide="user"></i>
                                        <span>Profile</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:;" class="dropdown-item py-2 text-body ms-0">
                                        <i class="me-2 icon-md" data-lucide="edit"></i>
                                        <span>Edit Profile</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:;" class="dropdown-item py-2 text-body ms-0">
                                        <i class="me-2 icon-md" data-lucide="repeat"></i>
                                        <span>Switch User</span>
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

                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="link-icon" data-lucide="users"></i>
                        <span class="menu-title">B2B Customers</span>
                    </a>
                </li>

                <li class="nav-item {{ Route::is('salesofficer.purchase-requests.index') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('salesofficer.purchase-requests.index') }}">
                        <i class="link-icon" data-lucide="box"></i>
                        <span class="menu-title">Pending Purchase Request</span>
                    </a>
                </li>

                <li class="nav-item {{ Route::is('salesofficer.sent-quotations.index') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('salesofficer.send-quotations.index') }}">
                        <i class="link-icon" data-lucide="box"></i>
                        <span class="menu-title">Sent Quotations</span>
                    </a>
                </li>

                <li class="nav-item {{ Route::is('salesofficer.submitted-order.index') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('salesofficer.submitted-order.index') }}">
                        <i class="link-icon" data-lucide="box"></i>
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

                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="link-icon" data-lucide="star"></i>
                        <span class="menu-title">Ratings</span>
                    </a>
                </li>

                @else
                @endif

                <!-- <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="link-icon" data-lucide="mail"></i>
                        <span class="menu-title">Apps</span>
                        <i class="link-arrow"></i>
                    </a>
                    <div class="submenu">
                        <ul class="submenu-item">
                            <li class="category-heading">Email</li>
                            <li class="nav-item"><a class="nav-link" href="pages/email/inbox.html">Inbox</a></li>
                            <li class="nav-item"><a class="nav-link" href="pages/email/read.html">Read</a></li>
                            <li class="nav-item"><a class="nav-link" href="pages/email/compose.html">Compose</a></li>
                            <li class="category-heading">Other
                            <li>
                            <li class="nav-item"><a class="nav-link" href="pages/apps/chat.html">Chat</a></li>
                            <li class="nav-item"><a class="nav-link" href="pages/apps/calendar.html">Calendar</a></li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item mega-menu">
                    <a href="#" class="nav-link">
                        <i class="link-icon" data-lucide="feather"></i>
                        <span class="menu-title">UI Kit</span>
                        <i class="link-arrow"></i>
                    </a>
                    <div class="submenu">
                        <div class="col-group-wrapper row">
                            <div class="col-group col-md-9">
                                <div class="row">
                                    <div class="col-12">
                                        <p class="category-heading">Basic</p>
                                        <div class="submenu-item">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <ul>
                                                        <li class="nav-item"><a class="nav-link" href="pages/ui-components/accordion.html">Accordion</a></li>
                                                        <li class="nav-item"><a class="nav-link" href="pages/ui-components/alerts.html">Alerts</a></li>
                                                        <li class="nav-item"><a class="nav-link" href="pages/ui-components/badges.html">Badges</a></li>
                                                        <li class="nav-item"><a class="nav-link" href="pages/ui-components/breadcrumbs.html">Breadcrumbs</a></li>
                                                        <li class="nav-item"><a class="nav-link" href="pages/ui-components/buttons.html">Buttons</a></li>
                                                        <li class="nav-item"><a class="nav-link" href="pages/ui-components/button-group.html">Buttn Group</a></li>
                                                        <li class="nav-item"><a class="nav-link" href="pages/ui-components/cards.html">Cards</a></li>
                                                        <li class="nav-item"><a class="nav-link" href="pages/ui-components/carousel.html">Carousel</a></li>
                                                        <li class="nav-item"><a class="nav-link" href="pages/ui-components/collapse.html">Collapse</a></li>
                                                    </ul>
                                                </div>
                                                <div class="col-md-4">
                                                    <ul>
                                                        <li class="nav-item"><a class="nav-link" href="pages/ui-components/dropdowns.html">Dropdowns</a></li>
                                                        <li class="nav-item"><a class="nav-link" href="pages/ui-components/list-group.html">List group</a></li>
                                                        <li class="nav-item"><a class="nav-link" href="pages/ui-components/media-object.html">Media object</a></li>
                                                        <li class="nav-item"><a class="nav-link" href="pages/ui-components/modal.html">Modal</a></li>
                                                        <li class="nav-item"><a class="nav-link" href="pages/ui-components/navs.html">Navs</a></li>
                                                        <li class="nav-item"><a class="nav-link" href="pages/ui-components/offcanvas.html">Offcanvas</a></li>
                                                        <li class="nav-item"><a class="nav-link" href="pages/ui-components/pagination.html">Pagination</a></li>
                                                        <li class="nav-item"><a class="nav-link" href="pages/ui-components/placeholders.html">Placeholders</a></li>
                                                        <li class="nav-item"><a class="nav-link" href="pages/ui-components/popover.html">Popovers</a></li>
                                                    </ul>
                                                </div>
                                                <div class="col-md-4">
                                                    <ul>
                                                        <li class="nav-item"><a class="nav-link" href="pages/ui-components/progress.html">Progress</a></li>
                                                        <li class="nav-item"><a class="nav-link" href="pages/ui-components/scrollbar.html">Scrollbar</a></li>
                                                        <li class="nav-item"><a class="nav-link" href="pages/ui-components/scrollspy.html">Scrollspy</a></li>
                                                        <li class="nav-item"><a class="nav-link" href="pages/ui-components/spinners.html">Spinners</a></li>
                                                        <li class="nav-item"><a class="nav-link" href="pages/ui-components/tabs.html">Tabs</a></li>
                                                        <li class="nav-item"><a class="nav-link" href="pages/ui-components/toasts.html">Toasts</a></li>
                                                        <li class="nav-item"><a class="nav-link" href="pages/ui-components/tooltips.html">Tooltips</a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-group col-md-3">
                                <div class="row">
                                    <div class="col-12">
                                        <p class="category-heading">Advanced</p>
                                        <div class="submenu-item">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <ul>
                                                        <li class="nav-item"><a class="nav-link" href="pages/advanced-ui/cropper.html">Cropper</a></li>
                                                        <li class="nav-item"><a class="nav-link" href="pages/advanced-ui/owl-carousel.html">Owl carousel</a></li>
                                                        <li class="nav-item"><a class="nav-link" href="pages/advanced-ui/sortablejs.html">SortableJs</a></li>
                                                        <li class="nav-item"><a class="nav-link" href="pages/advanced-ui/sweet-alert.html">Sweetalert</a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="link-icon" data-lucide="inbox"></i>
                        <span class="menu-title">Forms</span>
                        <i class="link-arrow"></i>
                    </a>
                    <div class="submenu">
                        <ul class="submenu-item">
                            <li class="nav-item"><a class="nav-link" href="pages/forms/basic-elements.html">Basic Elements</a></li>
                            <li class="nav-item"><a class="nav-link" href="pages/forms/advanced-elements.html">Advanced Elements</a></li>
                            <li class="nav-item"><a class="nav-link" href="pages/forms/editors.html">Editors</a></li>
                            <li class="nav-item"><a class="nav-link" href="pages/forms/wizard.html">Wizard</a></li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="link-icon" data-lucide="pie-chart"></i>
                        <span class="menu-title">Data</span>
                        <i class="link-arrow"></i>
                    </a>
                    <div class="submenu">
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="submenu-item pe-md-0">
                                    <li class="category-heading">Charts</li>
                                    <li class="nav-item"><a class="nav-link" href="pages/charts/apex.html">Apex</a></li>
                                    <li class="nav-item"><a class="nav-link" href="pages/charts/chartjs.html">ChartJs</a></li>
                                    <li class="nav-item"><a class="nav-link" href="pages/charts/flot.html">Float</a></li>
                                    <li class="nav-item"><a class="nav-link" href="pages/charts/peity.html">Peity</a></li>
                                    <li class="nav-item"><a class="nav-link" href="pages/charts/sparkline.html">Sparkline</a></li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="submenu-item ps-md-0">
                                    <li class="category-heading">Tables</li>
                                    <li class="nav-item"><a class="nav-link" href="pages/tables/basic-table.html">Basic Tables</a></li>
                                    <li class="nav-item"><a class="nav-link" href="pages/tables/data-table.html">Data Table</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="link-icon" data-lucide="smile"></i>
                        <span class="menu-title">Icons</span>
                        <i class="link-arrow"></i>
                    </a>
                    <div class="submenu">
                        <ul class="submenu-item">
                            <li class="nav-item"><a class="nav-link" href="pages/icons/lucide-icons.html">Lucide Icons</a></li>
                            <li class="nav-item"><a class="nav-link" href="pages/icons/flag-icons.html">Flag Icons</a></li>
                            <li class="nav-item"><a class="nav-link" href="pages/icons/mdi-icons.html">Mdi Icons</a></li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item mega-menu">
                    <a href="#" class="nav-link">
                        <i class="link-icon" data-lucide="book"></i>
                        <span class="menu-title">Sample Pages</span>
                        <i class="link-arrow"></i>
                    </a>
                    <div class="submenu">
                        <div class="col-group-wrapper row">
                            <div class="col-group col-md-6">
                                <p class="category-heading">Special Pages</p>
                                <div class="submenu-item">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <ul>
                                                <li class="nav-item"><a class="nav-link" href="pages/general/blank-page.html">Blank page</a></li>
                                                <li class="nav-item"><a class="nav-link" href="pages/general/faq.html">Faq</a></li>
                                                <li class="nav-item"><a class="nav-link" href="pages/general/invoice.html">Invoice</a></li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <ul>
                                                <li class="nav-item"><a class="nav-link" href="pages/general/profile.html">Profile</a></li>
                                                <li class="nav-item"><a class="nav-link" href="pages/general/pricing.html">Pricing</a></li>
                                                <li class="nav-item"><a class="nav-link" href="pages/general/timeline.html">Timeline</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-group col-md-3">
                                <p class="category-heading">Auth Pages</p>
                                <ul class="submenu-item">
                                    <li class="nav-item"><a class="nav-link" href="pages/auth/login.html">Login</a></li>
                                    <li class="nav-item"><a class="nav-link" href="pages/auth/register.html">Register</a></li>
                                </ul>
                            </div>
                            <div class="col-group col-md-3">
                                <p class="category-heading">Error Pages</p>
                                <ul class="submenu-item">
                                    <li class="nav-item"><a class="nav-link" href="pages/error/404.html">404</a></li>
                                    <li class="nav-item"><a class="nav-link" href="pages/error/500.html">500</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="nav-item">
                    <a href="https://nobleui.com/html/documentation/docs.html" target="_blank" class="nav-link">
                        <i class="link-icon" data-lucide="hash"></i>
                        <span class="menu-title">Documentation</span></a>
                </li> -->
            </ul>
        </div>
    </nav>
</div>
<!-- partial -->