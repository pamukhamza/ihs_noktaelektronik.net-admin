
<!-- Menu -->
 <?php 
$selectedSite = $_GET['w'] ?? 'noktanet'; 
 ?>
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <input hidden value="<?= $_SESSION['user_session']['id']; ?>">
    <div class="app-brand demo ">
    <a  class="menu-link">
                <i class="menu-icon tf-icons ti ti-smart-home"></i>
                <form method="get" action="dashboard">
                    <select class="form-select" name="w" onchange="this.form.submit()">
                        <option value="noktanet" <?= $selectedSite === 'noktanet' ? 'selected' : '' ?>>Nokta Net</option>
                        <option value="noktab2b" <?= $selectedSite === 'noktab2b' ? 'selected' : '' ?>>Nokta B2B</option>
                        <option value="noktacn" <?= $selectedSite === 'noktacn' ? 'selected' : '' ?>>Nokta CN</option>
                        <option value="indata" <?= $selectedSite === 'indata' ? 'selected' : '' ?>>InData</option>
                    </select>
                </form>
            </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="ti menu-toggle-icon d-none d-xl-block align-middle"></i>
            <i class="ti ti-x d-block d-xl-none ti-md align-middle"></i>
        </a>
    </div>
    <div class="menu-inner-shadow"></div>
    <ul class="menu-inner py-1">
        <!-- General Apps -->
        <li class="menu-header small">
            <span class="menu-header-text" data-i18n="Genel Yönetim">Genel Yönetim</span>
        </li>
        <!-- Dashboards -->
        <li class="menu-item <?= $currentPage === 'dashboard' ? 'active' : '' ?>">
            <a href="dashboard" class="menu-link">
                <i class="menu-icon tf-icons ti ti-smart-home"></i>
                <div data-i18n="Dashboard">Dashboard</div>
            </a>
        </li>
        <!-- Products -->
        <li class="menu-item <?= in_array($currentPage, ['products', 'categories', 'add-product', 'brands', 'filters']) ? 'active open' : '' ?>">
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
        <!-- Users -->
        <li class="menu-item <?= $currentPage === 'users' ? 'active' : '' ?>">
            <a href="users" class="menu-link">
                <i class="menu-icon tf-icons ti ti-user"></i>
                <div data-i18n="Kullanıcılar">Kullanıcılar</div>
            </a>
        </li>
        <!-- Dosyalar -->
        <li class="menu-item <?= $currentPage === 'documents' ? 'active' : '' ?>">
            <a href="documents" class="menu-link">
                <i class="menu-icon tf-icons ti ti-folder"></i>
                <div data-i18n="Dosyalar">Dosyalar</div>
            </a>
        </li>
        <!-- Katalog -->
        <li class="menu-item <?= $currentPage === 'catalog' ? 'active' : '' ?>">
            <a href="catalog" class="menu-link">
                <i class="menu-icon tf-icons ti ti-file"></i>
                <div data-i18n="Katalog">Katalog</div>
            </a>
        </li>        
        <!-- Teknik Destek -->
        <li class="menu-item <?= $currentPage === 'teknik_destek' ? 'active' : '' ?>">
            <a href="teknik_destek" class="menu-link">
                <i class="menu-icon tf-icons ti ti-headset"></i>
                <div data-i18n="Teknik Destek">Teknik Destek</div>
            </a>
        </li>
        <!-- Slider -->
        <li class="menu-item <?= $currentPage === 'slider' ? 'active' : '' ?>">
            <a href="slider" class="menu-link">
                <i class="menu-icon tf-icons ti ti-arrows-horizontal"></i>
                <div data-i18n="Slider">Slider</div>
            </a>
        </li>
        <!-- Settings -->
        <li class="menu-item <?= $currentPage === 'settings' ? 'active' : '' ?>">
            <a href="settings" class="menu-link">
                <i class="menu-icon tf-icons ti ti-settings"></i>
                <div data-i18n="Genel Ayarlar">Genel Ayarlar</div>
            </a>
        </li>
        <!-- Apps & Pages -->
        <li class="menu-header small">
            <span class="menu-header-text" data-i18n="Detay Yönetim">Detay Yönetim</span>
        </li>
        <?php if ($selectedSite === 'noktanet') { ?>        
            <li class="menu-item <?= $currentPage === 'net-bulten' ? 'active' : '' ?>">
                <a href="net-bulten" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-mail"></i>
                    <div data-i18n="E-Bülten">E-Bülten</div>
                </a>
            </li>
            <li class="menu-item <?= $currentPage === 'net-offer' ? 'active' : '' ?>">
                <a href="net-offer" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-book"></i>
                    <div data-i18n="Teklifler">Teklifler</div>
                </a>
            </li><?php
        }elseif($selectedSite === 'noktacn'){ ?>
            <li class="menu-item <?= $currentPage === 'cn-bulten' ? 'active' : '' ?>">
                <a href="cn-bulten" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-mail"></i>
                    <div data-i18n="E-Bülten">E-Bülten</div>
                </a>
            </li>
            <li class="menu-item <?= $currentPage === 'cn-offer' ? 'active' : '' ?>">
                <a href="cn-offer" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-book"></i>
                    <div data-i18n="Teklifler">Teklifler</div>
                </a>
            </li><?php
        }elseif($selectedSite === 'indata'){ ?>
            <li class="menu-item <?= $currentPage === 'projeler' ? 'active' : '' ?>">
                <a href="projeler?w=indata" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-mail"></i>
                    <div data-i18n="Projeler">Projeler</div>
                </a>
            </li><?php
        }elseif($selectedSite === 'noktab2b'){ ?>
            <li class="menu-item <?= $currentPage === 'eksik-bilgi' ? 'active' : '' ?>">
                <a href="eksik-bilgi" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-exclamation-circle"></i>
                    <div data-i18n="Eksik Ürün Bilgisi">Eksik Ürün Bilgisi</div>
                </a>
            </li>
            <li class="menu-item <?= $currentPage === 'kampanya' ? 'active' : '' ?>">
                <a href="kampanya" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-tag"></i>
                    <div data-i18n="Kampanyalar">Kampanyalar</div>
                </a>
            </li>
            <li class="menu-item <?= $currentPage === 'teklifler' ? 'active' : '' ?>">
                <a href="teklifler" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-basket"></i>
                    <div data-i18n="Teklifler">Teklifler</div>
                </a>
            </li>
            <li class="menu-item <?= $currentPage === 'sertifika-olustur' ? 'active' : '' ?>">
                <a href="sertifika-olustur" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-certificate"></i>
                    <div data-i18n="Sertifika Oluşturma">Sertifika Oluşturma</div>
                </a>
            </li>
            <li class="menu-item <?= $currentPage === 'newsletter' ? 'active' : '' ?>">
                <a href="newsletter" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-mail"></i>
                    <div data-i18n="E-Bülten">E-Bülten</div>
                </a>
            </li>
            <li class="menu-item <?= $currentPage === 'siparisler' ? 'active' : '' ?>">
                <a href="siparisler" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-shopping-cart"></i>
                    <div data-i18n="Siparişler">Siparişler</div>
                </a>
            </li>
            <li class="menu-item <?= $currentPage === 'muhasebe' ? 'active' : '' ?>">
                <a href="muhasebe" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-credit-card"></i>
                    <div data-i18n="Muhasebe">Muhasebe</div>
                </a>
            </li>
            <li class="menu-item <?= $currentPage === 'uyeler' ? 'active' : '' ?>">
                <a href="uyeler" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-user"></i>
                    <div data-i18n="Üyeler">Üyeler</div>
                </a>
            </li>
            <li class="menu-item <?= $currentPage === 'kargo-firmalari' ? 'active' : '' ?>">
                <a href="kargo-firmalari" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-truck"></i>
                    <div data-i18n="Kargo Firmaları">Kargo Firmaları</div>
                </a>
            </li><?php
        }  ?>
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
                                        <h6 class="mb-0">NOKTA Admin</h6>
                                        <small class="text-muted">Admin</small>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li>
                            <div class="dropdown-divider my-1 mx-n2"></div>
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