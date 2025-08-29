<?php 
$page = isset($data['page']) ? $data['page'] : '';
$pages = isset($data['pages']) ? $data['pages'] : '';
$level = isset($_SESSION['level']) ? $_SESSION['level'] : '';
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
                    
                    <!-- ADMIN DASHBOARD -->
                    <li class="sidebar-item <?= ($pages == 'admin_dashboard') ? 'active' : '' ?>">
                        <a href="<?= base_url; ?>/admin_dashboard" class='sidebar-link'>
                            <i class="bi bi-grid-fill"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                  <li class="sidebar-item <?= ($pages == 'berikan') ? 'active' : '' ?>">
                        <a href="<?= base_url; ?>/tsberikanvoucher/" class='sidebar-link'>
                            <i class="fa-solid fa-box"></i>
                            <span>Berikan Voucher</span>
                        </a>
                    </li>
                  
                    <!-- USER DASHBOARD -->
             
                     <li class="sidebar-item <?= ($pages == 'tukar') ? 'active' : '' ?>">
                        <a href="<?= base_url; ?>/tstukarvoucher" class='sidebar-link'>
                            <i class="fa-solid fa-paper-plane"></i>
                            <span>Tukar Voucher</span>
                        </a>
                    </li>
         

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