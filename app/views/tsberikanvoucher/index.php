
<?php

$userlog = $data['userid'];
$username = $data['username'];
?>
<style>
/* Hover effect & pointer */
.fc-daygrid-event {
  transition: 0.2s;
}
.fc-event-title-container:hover {
  background-color:rgb(242, 245, 240) !important;
  color:rgb(11, 12, 12) !important;
  cursor: pointer;
}

/* Dot warna di kiri */
.fc-event-title-container {
  background-color:white!important;
  color:rgb(11, 12, 12) !important;
}

/* Ubah warna tombol default */
.fc-button {
  background-color:  #343a40 !important;
  color: white;
  border: none;
}

/* Ubah warna saat hover */
.fc-button:hover {
  background-color:rgb(242, 245, 240) !important;
  color:rgb(11, 12, 12) !important;
}

/* Ubah warna tombol aktif */
.fc-button-active {
  background-color: #007bff !important;
  color: #fff !important;
}

.hover-putih:hover {
  color: white;
}

.fc-toolbar .fc-button {
    margin-right: 10px; /* atur sesuai kebutuhan */
  }

/* Hilangkan border default dari event */
.fc .fc-event {
  border: none !important;
  box-shadow: none !important;
}

/* Hilangkan outline (garis biru saat diklik atau difokus) */
.fc .fc-event:focus {
  outline: none !important;
}

/* Pastikan FullCalendar memanfaatkan lebar penuh */
.fc {
    width: 100% !important;
}

/* Sesuaikan ukuran font dan padding untuk mobile */
@media (max-width: 768px) {
    .fc-header-toolbar {
        font-size: 12px; /* Mengurangi ukuran font untuk tampilan mobile */
    }
    .fc-day-grid .fc-day {
        padding: 5px; /* Mengurangi padding untuk tampilan lebih kompak */
    }
    .fc-event {
        font-size: 12px; /* Mengurangi ukuran font acara */
        padding: 2px; /* Menyempitkan ruang antar acara */
    }

    .fc-toolbar-title {
        font-size: 14px !important;  /* Ukuran font lebih kecil dengan !important */
    }
    .fc-header-title {
        font-size: 14px !important;  /* Memastikan ukuran font title tetap kecil */
    }
}

</style>



<div id="main">
       <header class="mb-3">
                <a href="#" class="burger-btn d-block d-xl-none">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>
    <!-- Content Header (Page header) -->
    <div class ="col-md-12 col-12">
            <!-- Default box -->
            <div class="card">
              <input type="hidden" class="control-form" id="username" value="<?=$username?>">
      <div class="card-body">
       <div id="root"></div>

      </div>
    </div>
 
                <!-- /.card-body -->
  </div>
      </div> 
<script type="module" src="<?= base_url; ?>/src/ts_berikanvoucher/main.js"></script>
