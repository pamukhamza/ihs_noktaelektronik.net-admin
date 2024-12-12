
<!-- Menu -->
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <input hidden value="<?= $_SESSION['user_session']['id']; ?>">
    <div class="app-brand demo ">
        <a href="/" class="app-brand-link">
            <span class="app-brand-logo"><img src="../../assets/images/logo/logo_new.png" width="90%"></span>
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="ti menu-toggle-icon d-none d-xl-block align-middle"></i>
            <i class="ti ti-x d-block d-xl-none ti-md align-middle"></i>
        </a>
    </div>
    <div class="menu-inner-shadow"></div>
    <ul class="menu-inner py-1">
        <!-- Dashboards -->
        <li class="menu-item <?= $currentPage === 'dashboard' ? 'active' : '' ?>">
            <a href="dashboard" class="menu-link">
                <i class="menu-icon tf-icons ti ti-smart-home"></i>
                <div data-i18n="Dashboard">Dashboard</div>
            </a>
        </li>
        <!-- Front Pages -->
        <li class="menu-item <?= in_array($currentPage, ['pages', 'settings', 'slider']) ? 'active open' : '' ?>">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class='menu-icon tf-icons ti ti-files'></i>
                <div data-i18n="Ön Sayfalar & Ayarlar">Ön Sayfalar & Ayarlar</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item <?= $currentPage === 'pages' ? 'active' : '' ?>">
                    <a href="pages" class="menu-link" >
                        <div data-i18n="Hakkımızda, İletişim, Politikalar">Hakkımızda, İletişim, Politikalar</div>
                    </a>
                </li>
                <li class="menu-item <?= $currentPage === 'slider' ? 'active' : '' ?>">
                    <a href="slider" class="menu-link" >
                        <div data-i18n="Slider, Banner, Poster">Slider, Banner, Poster</div>
                    </a>
                </li>
                <li class="menu-item <?= $currentPage === 'settings' ? 'active' : '' ?>">
                    <a href="settings" class="menu-link" >
                        <div data-i18n="Ayarlar & Sosyal Medya">Ayarlar & Sosyal Medya</div>
                    </a>
                </li>
            </ul>
        </li>
        <!-- Products -->
        <li class="menu-item <?= in_array($currentPage, ['products', 'categories', 'add-product', 'brands']) ? 'active open' : '' ?>">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class='menu-icon tf-icons ti ti-box'></i>
                <div data-i18n="Ürünler">Ürünler</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item <?= $currentPage === 'products' ? 'active' : '' ?>">
                    <a href="products" class="menu-link">
                        <div data-i18n="Ürün Listesi">Ürün Listesi</div>
                    </a>
                </li>
                <li class="menu-item <?= $currentPage === 'add-product' ? 'active' : '' ?>">
                    <a href="add-product" class="menu-link">
                        <div data-i18n="Yeni Ürün Ekle">Yeni Ürün Ekle</div>
                    </a>
                </li>
                <li class="menu-item <?= $currentPage === 'categories' ? 'active' : '' ?>">
                    <a href="categories" class="menu-link" >
                        <div data-i18n="Kategoriler">Kategoriler</div>
                    </a>
                </li>
                <li class="menu-item <?= $currentPage === 'brands' ? 'active' : '' ?>">
                    <a href="brands" class="menu-link" >
                        <div data-i18n="Markalar">Markalar</div>
                    </a>
                </li>
                <li class="menu-item <?= $currentPage === 'filters' ? 'active' : '' ?>">
                    <a href="filters" class="menu-link" >
                        <div data-i18n="Filtreler">Filtreler</div>
                    </a>
                </li>
            </ul>
        </li>
        <!-- Apps & Pages -->
        <li class="menu-header small">
            <span class="menu-header-text" data-i18n="Apps ">Apps  </span>
        </li>
        <li class="menu-item <?= $currentPage === 'newsletter' ? 'active' : '' ?>">
            <a href="newsletter" class="menu-link">
                <i class="menu-icon tf-icons ti ti-mail"></i>
                <div data-i18n="E-Bülten">E-Bülten</div>
            </a>
        </li>
        <li class="menu-item <?= $currentPage === 'blog' ? 'active' : '' ?>">
            <a href="blog" class="menu-link">
                <i class="menu-icon tf-icons ti ti-book"></i>
                <div data-i18n="Bloglar">Bloglar</div>
            </a>
        </li>
        <li class="menu-item <?= $currentPage === 'languages' ? 'active' : '' ?>">
            <a href="languages" class="menu-link">
                <i class="menu-icon tf-icons ti ti-language"></i>
                <div data-i18n="Diller">Diller</div>
            </a>
        </li>
    </ul>
</aside>
<!-- Layout container -->
<div class="layout-page">
    <!-- Navbar -->
    <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
        <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0   d-xl-none ">
            <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                <i class="ti ti-menu-2 ti-md"></i>
            </a>
        </div>
        <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
            <ul class="navbar-nav flex-row align-items-center ms-auto">
                <!-- User -->
                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                    <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);" data-bs-toggle="dropdown">
                        <div class="avatar avatar-online">
                            <img src="../assets/img/avatars/1.png" alt class="rounded-circle">
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item mt-0" href="pages-account-settings-account.html">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-2">
                                        <div class="avatar avatar-online">
                                            <img src="../assets/img/avatars/1.png" alt class="rounded-circle">
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0">Lahora Admin</h6>
                                        <small class="text-muted">Admin</small>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li>
                            <div class="dropdown-divider my-1 mx-n2"></div>
                        </li>
                        <li>
                            <a class="dropdown-item" href="pages-account-settings-account.html">
                                <i class="ti ti-settings me-3 ti-md"></i><span class="align-middle">Settings</span>
                            </a>
                        </li>
                        <li>
                            <div class="d-grid px-2 pt-2 pb-1">
                                <a class="btn btn-sm btn-danger d-flex" href="../functions/logout.php" >
                                    <small class="align-middle">Logout</small>
                                    <i class="ti ti-logout ms-2 ti-14px"></i>
                                </a>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>