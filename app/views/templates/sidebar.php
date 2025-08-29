<?php 
$page = isset($data['page']) ? $data['page'] : '';
$pages = isset($data['pages']) ? $data['pages'] : '';
$level = isset($_SESSION['level']) ? $_SESSION['level'] : '';

$menus = [
    'giver' => [
        ['page' => 'berikan', 'link' => base_url.'/tsberikanvoucher/', 'icon' => 'fa-solid fa-box', 'label' => 'Berikan Voucher']
    ],
    'redeemer' => [
        ['page' => 'tukar', 'link' => base_url.'/tstukarvoucher', 'icon' => 'fa-solid fa-paper-plane', 'label' => 'Tukar Voucher']
    ],
    'full' => [
        ['page' => 'admin_dashboard', 'link' => base_url.'/admin_dashboard', 'icon' => 'bi bi-grid-fill', 'label' => 'Dashboard'],
        ['page' => 'berikan', 'link' => base_url.'/tsberikanvoucher/', 'icon' => 'fa-solid fa-box', 'label' => 'Berikan Voucher'],
        ['page' => 'tukar', 'link' => base_url.'/tstukarvoucher', 'icon' => 'fa-solid fa-paper-plane', 'label' => 'Tukar Voucher']
    ]
];

$menuToShow = isset($menus[$level]) ? $menus[$level] : [];
?>

<div id="app">
    <div id="sidebar" class="active">
        <div class="sidebar-wrapper active">
            <div class="sidebar-header position-relative">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="logo">
                        <h5><a href="<?= base_url ?>/home"><?= $data['username'] ?></a></h5>
                    </div>
                    <div class="theme-toggle d-flex gap-2 align-items-center mt-2">
                        <div class="form-check form-switch fs-6">
                            <input class="me-0" type="hidden" id="toggle-dark">
                        </div>
                    </div>
                    <div class="sidebar-toggler x">
                        <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                    </div>
                </div>
            </div>
            <div class="sidebar-menu">
               <ul class="menu">
                    <li class="sidebar-title">Menu</li>
                    
                    <?php foreach ($menuToShow as $m): ?>
                        <li class="sidebar-item <?= ($pages == $m['page']) ? 'active' : '' ?>">
                            <a href="<?= $m['link'] ?>" class='sidebar-link'>
                                <i class="<?= $m['icon'] ?>"></i>
                                <span><?= $m['label'] ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>     
                <ul class="menu">
                    <li class="sidebar-item">
                        <a href="<?= base_url; ?>/logout" class='sidebar-link text-danger'>
                            <i class="fa-solid fa-right-from-bracket"></i>
                            <span>Sign Out</span>
                        </a>      
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const burgerBtn = document.querySelector('.burger-btn');
        const sidebar = document.querySelector('.sidebar-wrapper');
        const sidebarHideBtn = document.querySelector('.sidebar-hide');
        const logoutLink = document.querySelector('a[href*="/logout"]');
        const sidebarWrapper = document.querySelector('.sidebar-wrapper');
        const activeItem = sidebarWrapper?.querySelector('.sidebar-item.active');
        const cardElement = document.querySelector('.card');

        // Toggle tampilkan sidebar saat tombol burger diklik
        if (burgerBtn && sidebar) {
            burgerBtn.addEventListener('click', function(e) {
                e.preventDefault();
                sidebar.classList.toggle('show-sidebar');
            });
        }

        // Sembunyikan sidebar saat tombol close (X) diklik
        if (sidebarHideBtn && sidebar) {
            sidebarHideBtn.addEventListener('click', function(e) {
                e.preventDefault();
                sidebar.classList.remove('show-sidebar');
            });
        }

        // Scroll otomatis ke menu aktif di sidebar
        if (activeItem) {
            const sidebarTop = sidebarWrapper.scrollTop;
            const itemTop = activeItem.offsetTop;
            const itemBottom = itemTop + activeItem.offsetHeight;
            const wrapperHeight = sidebarWrapper.clientHeight;

            if (itemBottom > sidebarTop + wrapperHeight) {
                sidebarWrapper.scrollTo({
                    top: itemTop - 60,
                    behavior: "smooth"
                });
            }

            if (itemTop < sidebarTop) {
                sidebarWrapper.scrollTo({
                    top: itemTop - 20,
                    behavior: "smooth"
                });
            }
        }

        // Sembunyikan scrollbar jika menu aktif atau card muncul
        if ((activeItem || cardElement) && sidebarWrapper) {
            sidebarWrapper.classList.add("hide-scrollbar");
        }

        // Konfirmasi logout
        if (logoutLink) {
            logoutLink.addEventListener("click", function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Yakin ingin logout?',
                    text: "Anda akan keluar dari aplikasi. Inventarsi Marketing ..",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, logout!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = logoutLink.href;
                    }
                });
            });
        }


    });
</script>