<?php 
include __DIR__ .'/../../functions/user.php';
$user = new User($database);
$selectedSite = $_GET['w'] ?? 'net'; 
?>
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <input hidden value="<?= $_SESSION['user_session']['id']; ?>">
    <div class="app-brand demo ">
            <a class="menu-link">
                <i class="menu-icon tf-icons ti ti-smart-home"></i>
                <form method="get" action="pages/genel/dashboard">
                    <select class="form-select" name="w" id="siteSelect" onchange="this.form.submit()">
                        <option value="net" <?= $selectedSite === 'net' ? 'selected' : '' ?>>Nokta Net</option>
                        <option value="b2b" <?= ($selectedSite === 'b2b' || $selectedSite === 'noktab2b') ? 'selected' : '' ?>>Nokta B2B</option>
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
    <script>
        // Sayfanın yolunu al
        const path = window.location.pathname;

        // path = "/pages/indata/product.php"
        // 'pages/' sonrasındaki kısmı al
        const siteName = path.split('/')[3]; // pages/[siteName]/...

        // Eğer siteName, select2'de varsa seçili yap
        const selectElement = document.getElementById('siteSelect');
        Array.from(selectElement.options).forEach(option => {
            if (option.value === siteName) {
                option.selected = true;
            }
        });
    </script>
    <div class="menu-inner-shadow"></div>
    <ul class="menu-inner py-1">
        <!-- General Apps -->
        <li class="menu-header small">
            <span class="menu-header-text" data-i18n="Genel Yönetim">Genel Yönetim</span>
        </li>
        <!-- Dashboards -->
        <?php if ($user->hasPermission(11)): ?>
            <li class="menu-item <?= $currentPage === 'dashboard' ? 'active' : '' ?>">
                <a href="pages/genel/dashboard" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-smart-home"></i>
                    <div data-i18n="Dashboard">Dashboard</div>
                </a>
            </li>
        <?php endif; ?>
        <!-- Products -->
        <?php if ($user->hasPermission(12)): ?>
            <li class="menu-item <?= in_array($currentPage, ['products','add-product', 'filters','ikons']) ? 'active open' : '' ?>">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class='menu-icon tf-icons ti ti-box'></i>
                    <div data-i18n="Ürünler">Ürünler</div>
                </a>
                <ul class="menu-sub">
                    <?php if ($user->hasPermission(30)): ?>
                        <li class="menu-item <?= $currentPage === 'products' ? 'active' : '' ?>">
                            <a href="pages/genel/products" class="menu-link">
                                <div data-i18n="Ürün Listesi">Ürün Listesi</div>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if ($user->hasPermission(29)): ?>
                        <li class="menu-item <?= $currentPage === 'add-product' ? 'active' : '' ?>">
                            <a href="pages/genel/add-product" class="menu-link">
                                <div data-i18n="Yeni Ürün Ekle">Yeni Ürün Ekle</div>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if ($user->hasPermission(28)): ?>
                        <li class="menu-item <?= $currentPage === 'filters' ? 'active' : '' ?>">
                            <a href="pages/genel/filters" class="menu-link" >
                                <div data-i18n="Filtreler">Filtreler</div>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if ($user->hasPermission(27)): ?>
                        <li class="menu-item <?= $currentPage === 'ikons' ? 'active' : '' ?>">
                            <a href="pages/genel/ikons" class="menu-link" >
                                <div data-i18n="İkonlar">İkonlar</div>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </li>
        <?php endif; ?>
        <!-- Categories -->
        <?php if ($user->hasPermission(24)): ?>
            <li class="menu-item <?= $currentPage === 'categories' ? 'active' : '' ?>">
                <a href="pages/genel/categories" class="menu-link" >
                <i class="menu-icon tf-icons ti ti-list"></i>
                    <div data-i18n="Kategoriler">Kategoriler</div>
                </a>
            </li>
        <?php endif; ?>
        <!-- Brands -->
        <?php if ($user->hasPermission(23)): ?>
            <li class="menu-item <?= $currentPage === 'brands' ? 'active' : '' ?>">
                <a href="pages/genel/brands" class="menu-link" >
                <i class="menu-icon tf-icons ti ti-ticket"></i>
                    <div data-i18n="Markalar">Markalar</div>
                </a>
            </li>
        <?php endif; ?>
        <!-- Users -->
        <?php if ($user->hasPermission(14)): ?>
            <li class="menu-item <?= $currentPage === 'users' ? 'active' : '' ?>">
                <a href="pages/genel/users" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-user"></i>
                    <div data-i18n="Kullanıcılar">Kullanıcılar</div>
                </a>
            </li>
        <?php endif; ?>
        <!-- Documents -->
        <?php if ($user->hasPermission(15)): ?>
            <li class="menu-item <?= $currentPage === 'documents' ? 'active' : '' ?>">
                <a href="pages/genel/documents" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-folder"></i>
                    <div data-i18n="Dosyalar">Dosyalar</div>
                </a>
            </li>
        <?php endif; ?>
        <!-- Catalog -->
        <?php if ($user->hasPermission(16)): ?>
            <li class="menu-item <?= $currentPage === 'catalog' ? 'active' : '' ?>">
                <a href="pages/genel/catalog" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-file"></i>
                    <div data-i18n="Katalog">Katalog</div>
                </a>
            </li>
        <?php endif; ?>
        <!-- Technical Support -->
        <?php if ($user->hasPermission(17)): ?>
            <li class="menu-item <?= $currentPage === 'teknik_destek' ? 'active' : '' ?>">
                <a href="pages/genel/teknik_destek" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-headset"></i>
                    <div data-i18n="Teknik Destek">Teknik Destek</div>
                </a>
            </li>
        <?php endif; ?>
        <!-- Slider -->
        <?php if ($user->hasPermission(18)): ?>
            <li class="menu-item <?= $currentPage === 'slider' ? 'active' : '' ?>">
                <a href="pages/genel/slider" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-arrows-horizontal"></i>
                    <div data-i18n="Slider">Slider</div>
                </a>
            </li>
            <li class="menu-item <?= $currentPage === 'b2b-banner' ? 'active' : '' ?>">
                <a href="pages/b2b/b2b-banner" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-arrows-horizontal"></i>
                    <div data-i18n="B2B Banner">B2B Banner</div>
                </a>
            </li>
        <?php endif; ?>
        <!-- Social Media -->
        <?php if ($user->hasPermission(19)): ?>
            <li class="menu-item <?= $currentPage === 'settings' ? 'active' : '' ?>">
                <a href="pages/genel/settings" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-settings"></i>
                    <div data-i18n="Sosyal Medya">Sosyal Medya</div>
                </a>
            </li>
        <?php endif; ?>
        <!-- Technical Support -->
        <?php if ($user->hasPermission(40)): ?>
                    <li class="menu-item  <?= in_array($currentPage, ['b2b-doviz-ayarlari','b2b-odemeler', 'b2b-banka-komisyonları', 'b2b-banka-bilgileri', 'b2b-sanal-pos-odeme']) ? 'active open' : '' ?>">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class='menu-icon tf-icons ti ti-box'></i>
                            <div data-i18n="Muhasebe">Muhasebe</div>
                        </a>
                        <ul class="menu-sub">
                            <?php if ($user->hasPermission(47)): ?>
                                <li class="menu-item <?= $currentPage === 'b2b-doviz-ayarlari' ? 'active' : '' ?>">
                                    <a href="pages/b2b/b2b-doviz?w=noktab2b" class="menu-link">
                                        <div data-i18n="Döviz Ayarları">Döviz Ayarları</div>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if ($user->hasPermission(48)): ?>
                                <li class="menu-item <?= $currentPage === 'b2b-odemeler' ? 'active' : '' ?>">
                                    <a href="pages/b2b/b2b-odeme?w=noktab2b" class="menu-link">
                                        <div data-i18n="Ödemeler">Ödemeler</div>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if ($user->hasPermission(49)): ?>
                                <li class="menu-item <?= $currentPage === 'b2b-banka-komisyonları' ? 'active' : '' ?>">
                                    <a href="pages/b2b/b2b-komisyon?w=noktab2b" class="menu-link">
                                        <div data-i18n="Banka Komisyonları">Banka Komisyonları</div>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if ($user->hasPermission(50)): ?>
                                <li class="menu-item <?= $currentPage === 'b2b-banka-bilgileri' ? 'active' : '' ?>">
                                    <a href="pages/b2b/b2b-banka-hesap?w=noktab2b" class="menu-link">
                                        <div data-i18n="Banka Hesap Bilgileri">Banka Hesap Bilgileri</div>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if ($user->hasPermission(51)): ?>
                                <li class="menu-item <?= $currentPage === 'b2b-sanal-pos-odeme' ? 'active' : '' ?>">
                                    <a href="pages/b2b/b2b-sanalpos?w=noktab2b" class="menu-link">
                                        <div data-i18n="Sanal Pos Ödeme">Sanal Pos Ödeme</div>
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php if ($user->hasPermission(53)): ?>
                                <li class="menu-item <?= $currentPage === 'b2b-vadesi-gecmis' ? 'active' : '' ?>">
                                    <a href="pages/b2b/b2b-vadesi-gecmis?w=noktab2b" class="menu-link">
                                        <div data-i18n="Sanal Pos Ödeme">Sanal Pos Ödeme</div>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>
        <?php if ($user->hasPermission(52)): ?>
            <li class="menu-item <?= $currentPage === 's_havuz_bayiler' ? 'active' : '' ?>">
                <a href="pages/genel/satis_havuzbayileri" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-users-group"></i>
                    <div data-i18n="Havuz Bayileri">Havuz Bayileri</div>
                </a>
            </li>
            <li class="menu-item <?= $currentPage === 's_müsteriler' ? 'active' : '' ?>">
                <a href="pages/genel/satis_musteriler" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-user-circle"></i>
                    <div data-i18n="Müşteriler">Müşteriler</div>
                </a>
            </li>
            <li class="menu-item <?= $currentPage === 's_siparisler' ? 'active' : '' ?>">
                <a href="pages/genel/satis_siparisler" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-file-invoice"></i>
                    <div data-i18n="Siparişler">Siparişler</div>
                </a>
            </li>
            <li class="menu-item <?= $currentPage === 's_sepetler' ? 'active' : '' ?>">
                <a href="pages/genel/satis_sepetler" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-shopping-cart"></i>
                    <div data-i18n="Sepetler">Sepetler</div>
                </a>
            </li>
            <li class="menu-item <?= $currentPage === 's_odemeler' ? 'active' : '' ?>">
                <a href="pages/genel/satis_odemeler" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-credit-card"></i>
                    <div data-i18n="Ödemeler">Ödemeler</div>
                </a>
            </li>
            <li class="menu-item <?= $currentPage === 's_urunler' ? 'active' : '' ?>">
                <a href="pages/genel/satis_urunler" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-package"></i>
                    <div data-i18n="Ürünler">Ürünler</div>
                </a>
            </li>
            <li class="menu-item <?= $currentPage === 's_teknikservis' ? 'active' : '' ?>">
                <a href="pages/genel/satis_teknikservis" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-tools"></i>
                    <div data-i18n="Teknik Servis">Teknik Servis</div>
                </a>
            </li>
            <li class="menu-item <?= $currentPage === 's_uyegirisyap' ? 'active' : '' ?>">
                <a href="https://noktaelektronik.com.tr/tr/giris" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-login"></i>
                    <div data-i18n="Üye Olarak Giriş">Üye Olarak Giriş</div>
                </a>
            </li>
        <?php endif; ?>
        <!-- Apps & Pages -->
        <li class="menu-header small">
            <span class="menu-header-text" data-i18n="Detay Yönetim">Detay Yönetim</span>
        </li>
        
        <?php if ($selectedSite === 'net'): ?>
            
            <?php if ($user->hasPermission(1)): ?>
                <?php if ($user->hasPermission(2)): ?>
                    <li class="menu-item <?= $currentPage === 'net-bulten' ? 'active' : '' ?>">
                        <a href="pages/net/net-bulten" class="menu-link">
                            <i class="menu-icon tf-icons ti ti-mail"></i>
                            <div data-i18n="E-Bülten">E-Bülten</div>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if ($user->hasPermission(32)): ?>
                    <li class="menu-item <?= $currentPage === 'net-offer' ? 'active' : '' ?>">
                        <a href="pages/net/net-offer" class="menu-link">
                            <i class="menu-icon tf-icons ti ti-book"></i>
                            <div data-i18n="Teklifler">Teklifler</div>
                        </a>
                    </li>
                <?php endif; ?>
            <?php endif; ?>
        <?php elseif($selectedSite === 'noktacn'): ?>
            <?php if ($user->hasPermission(9)): ?>
                <li class="menu-item <?= $currentPage === 'cn-bulten' ? 'active' : '' ?>">
                    <a href="pages/genel/cn-bulten" class="menu-link">
                        <i class="menu-icon tf-icons ti ti-mail"></i>
                        <div data-i18n="E-Bülten">E-Bülten</div>
                    </a>
                </li>
                <li class="menu-item <?= $currentPage === 'cn-offer' ? 'active' : '' ?>">
                    <a href="pages/genel/cn-offer" class="menu-link">
                        <i class="menu-icon tf-icons ti ti-book"></i>
                        <div data-i18n="Teklifler">Teklifler</div>
                    </a>
                </li>
            <?php endif; ?>
        <?php elseif($selectedSite === 'indata'): ?>
            <?php if ($user->hasPermission(33)): ?>
                <li class="menu-item <?= $currentPage === 'indata-projeler' ? 'active' : '' ?>">
                    <a href="pages/indata/projeler?w=indata" class="menu-link">
                        <i class="menu-icon tf-icons ti ti-mail"></i>
                        <div data-i18n="Projeler">Projeler</div>
                    </a>
                </li>
            <?php endif; ?>
        <?php elseif($selectedSite === 'b2b' || $selectedSite === 'noktab2b'): ?>
            <?php if ($user->hasPermission(8)): ?>
                <!-- B2B menu items -->
                <?php if ($user->hasPermission(34)): ?>
                    <li class="menu-item <?= $currentPage === 'eksik-bilgi' ? 'active' : '' ?>">
                        <a href="pages/b2b/eksik-bilgi?w=noktab2b" class="menu-link">
                            <i class="menu-icon tf-icons ti ti-exclamation-circle"></i>
                            <div data-i18n="Eksik Ürün Bilgisi">Eksik Ürün Bilgisi</div>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if ($user->hasPermission(35)): ?>
                    <li class="menu-item <?= $currentPage === 'b2b-kampanya' ? 'active' : '' ?>">
                        <a href="pages/b2b/b2b-kampanya?w=noktab2b" class="menu-link">
                            <i class="menu-icon tf-icons ti ti-tag"></i>
                            <div data-i18n="Kampanyalar">Kampanyalar</div>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if ($user->hasPermission(36)): ?>
                    <li class="menu-item <?= $currentPage === 'b2b-teklif' ? 'active' : '' ?>">
                        <a href="pages/b2b/b2b-teklif?w=noktab2b" class="menu-link">
                            <i class="menu-icon tf-icons ti ti-basket"></i>
                            <div data-i18n="Teklifler">Teklifler</div>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if ($user->hasPermission(37)): ?>
                    <li class="menu-item <?= $currentPage === 'b2b-sertifika' ? 'active' : '' ?>">
                        <a href="pages/b2b/b2b-sertifika?w=noktab2b" class="menu-link">
                            <i class="menu-icon tf-icons ti ti-certificate"></i>
                            <div data-i18n="Sertifika Oluşturma">Sertifika Oluşturma</div>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if ($user->hasPermission(38)): ?>
                    <li class="menu-item <?= $currentPage === 'b2b-bulten' ? 'active' : '' ?>">
                        <a href="pages/b2b/b2b-bulten?w=noktab2b" class="menu-link">
                            <i class="menu-icon tf-icons ti ti-mail"></i>
                            <div data-i18n="E-Bülten">E-Bülten</div>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if ($user->hasPermission(39)): ?>
                    <li class="menu-item <?= in_array($currentPage, ['b2b-siparisler','b2b-iade', 'b2b-sepetler', 'b2b-promosyon']) ? 'active open' : '' ?>">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class='menu-icon tf-icons ti ti-box'></i>
                            <div data-i18n="Siparişler">Siparişler</div>
                        </a>
                        <ul class="menu-sub">
                            <?php if ($user->hasPermission(43)): ?>
                                <li class="menu-item <?= $currentPage === 'b2b-siparisler' ? 'active' : '' ?>">
                                    <a href="pages/b2b/b2b-siparisler?sDurum=0&w=noktab2b" class="menu-link">
                                        <div data-i18n="Sipariş Listesi">Sipariş Listesi</div>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if ($user->hasPermission(44)): ?>
                                <li class="menu-item <?= $currentPage === 'b2b-iade' ? 'active' : '' ?>">
                                    <a href="pages/b2b/b2b-iadedegisim?w=noktab2b" class="menu-link">
                                        <div data-i18n="İade / Değişim">İade / Değişim</div>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if ($user->hasPermission(45)): ?>
                                <li class="menu-item <?= $currentPage === 'b2b-sepetler' ? 'active' : '' ?>">
                                    <a href="pages/b2b/b2b-sepetler?w=noktab2b" class="menu-link">
                                        <div data-i18n="Sepetler">Sepetler</div>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if ($user->hasPermission(46)): ?>
                                <li class="menu-item <?= $currentPage === 'b2b-promosyon' ? 'active' : '' ?>">
                                    <a href="pages/b2b/b2b-promosyon?w=noktab2b" class="menu-link">
                                        <div data-i18n="Promosyon">Promosyon</div>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>
                
                <?php if ($user->hasPermission(41)): ?>
                    <li class="menu-item <?= $currentPage === 'b2b-uyeler' ? 'active' : '' ?>">
                        <a href="pages/b2b/b2b-uyeler?w=noktab2b" class="menu-link">
                            <i class="menu-icon tf-icons ti ti-user"></i>
                            <div data-i18n="Üyeler">Üyeler</div>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if ($user->hasPermission(42)): ?>
                    <li class="menu-item <?= $currentPage === 'kargo-firmalari' ? 'active' : '' ?>">
                        <a href="pages/b2b/b2b-kargo-firmalari?w=noktab2b" class="menu-link">
                            <i class="menu-icon tf-icons ti ti-truck"></i>
                            <div data-i18n="Kargo Firmaları">Kargo Firmaları</div>
                        </a>
                    </li>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>
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
                            <img src="assets/img/avatars/1.png" alt class="rounded-circle">
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item mt-0" href="pages-account-settings-account.html">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-2">
                                        <div class="avatar avatar-online">
                                            <img src="assets/img/avatars/1.png" alt class="rounded-circle">
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0"><?= $_SESSION['user_session']['full_name']; ?></h6>
                                        <small class="text-muted">
                                            <?php 
                                                $role = $_SESSION['user_session']['roles'];
                                                echo match ($role) {
                                                    1 => 'Admin',
                                                    2 => 'Satış Temsilcisi',
                                                    3 => 'Tekniker',
                                                    4 => 'Muhasebe',
                                                    default => 'Diğer'
                                                };
                                            ?>
                                        </small>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li>
                            <div class="dropdown-divider my-1 mx-n2"></div>
                        </li>
                        <li>
                            <div class="d-grid px-2 pt-2 pb-1">
                                <a class="btn btn-sm btn-danger d-flex" href="functions/logout.php" >
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