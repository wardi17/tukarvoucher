<link rel="stylesheet" href="<?= base_url; ?>/assets/scss/modalforminput.scss">

<style>
@media print {
  body * {
    visibility: hidden;
  }

  .modal-print, .modal-print * {
    visibility: visible;
  }

  .modal-print {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: auto;
    background: white;
    padding: 20px;
    margin: 0;
    box-sizing: border-box;
    z-index: 9999;
    font-size: 12pt;
        /* Tambahan untuk memenuhi halaman */
        page-break-before: always;
  }

   /* Hapus border dan shadow dari modal */
   .modal-dialog, .modal-content {
    width: 100% !important;
    max-width: 100% !important;
    margin: 0 !important;
    box-shadow: none !important;
    border: none !important;
    border-radius: 0 !important;
  }
  /* Sembunyikan tombol saat print */
  .modal-print .btn,.modal-header,.modal-footer
  .modal-print button {
    display: none !important;
  }

    /* Hilangkan scroll */
    html, body {
    height: 100%;
    margin: 0;
    padding: 0;
    overflow: hidden !important;
  }
}
</style>

<!-- Modal -->
<div class="modal fade modal-print" id="ModalDetail" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="false">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="ModalDetailLabel">Detail Data</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
          <!-- Konten dari file lain akan dimuat di sini -->
        <div class="row col-mt-4">
                  <!-- Logo -->
                 <div class="col-3 bg-white gariskotak">
                        <img id="modalLogo" class="img-thumbnail" alt="Deskripsi Gambar">
                    </div>
                 <!-- Judul -->
                 <div class="col-6 bg-white text-black gariskotak2 d-flex justify-content-center align-items-center text-center">
                        <h2 id="modalTitleContent"></h2>
                    </div>

                    <!-- Info Dokumen -->
                    <div class="col-3 bg-white text-black gariskotak3">
                        <table id="tablekecil" class="mt-2">
                            <tr><td>No. Doc</td><td id="noDoc"></td></tr>
                            <tr><td>Tgl</td><td id="docDate"></td></tr>
                            <tr><td>Revisi</td><td id="docRevision"></td></tr>
                        </table>
                    </div>
        </div>

             <!-- Form akan dimasukkan di sini -->
             <table class="table2">
                        <td style="width:24%;">Tanggal</td>
                        <td><input disabled type="date" id="tanggal" name="tanggal"></td>
                    </tr>
                    <tr>
                        <td>Jam</td>
                        <td> 
                        <label for="startTime">Jam Mulai:</label>
                          <select disabled id="startHour"></select> :
                          <select disabled id="startMinute"></select> :
                          <select disabled id="startSecond"></select>  
                          &nbsp;
                          <label for="endTime">Jam Akhir:</label>
                          <select disabled id="endHour"></select> :
                          <select disabled id="endMinute"></select> :
                          <select disabled id="endSecond"></select>                    
                      </td>
                    </tr>
                    <tr>
                        <td>Lokasi</td>
                        <td><input disabled type="text" id="lokasi"></td>
                    </tr>
                    <tr>
                        <td>Platform</td>
                        <td><input disabled type="text" id="platform"></td>
                    </tr>
                    <tr>
                        <td>Host</td>
                        <td><input disabled type="text" id="host"></td>
                    </tr>
                    <tr>
                        <td>Topik & Materi Utama</td>
                        <td><textarea disabled id="topik" rows="3"></textarea></td>
                    </tr>
                    <tr>
                        <td>Promosi Yang Ditampilkan</td>
                        <td><textarea disabled id="promosi" rows="3"></textarea></td>
                    </tr>
                    <tr>
                        <td>Catatan</td>
                        <td><textarea disabled id="catatan" rows="3"></textarea></td>
                    </tr>
                </table>
      

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-info" onclick="Print()">print</button>
      </div>
    </div>
  </div>
</div>


<script>
      // Event saat modal ditampilkan
      $('#ModalDetail').on('show.bs.modal', function () {
        const modalData = {
            title: "FROM LAPORAN HARIAN KEGIATAN LIVE STREAMING",
            logo: "<?=base_url?>/assets/img/logobest.png",
            documentInfo: {
                noDoc: "FO-BMI-DGM-01",
                date: "14 Maret 2025",
                page: "1",
                revision: "0"
            }
        };

        $('#modalTitleContent').text(modalData.title);
        $('#modalLogo').attr('src', modalData.logo);
        $('#noDoc').text(": " + modalData.documentInfo.noDoc);
        $('#docDate').text(": " + modalData.documentInfo.date);
        $('#docRevision').text(": " + modalData.documentInfo.revision);




    });
    // Fungsi untuk mengisi dropdown jam, menit, detik
    function populateTimeOptions(id, start, end) {
        let select = document.getElementById(id);
        select.innerHTML = ""; // Kosongkan isi sebelumnya

        for (let i = start; i <= end; i++) {
            let option = document.createElement("option");
            option.value = i.toString().padStart(2, '0');
            option.textContent = i.toString().padStart(2, '0');
            select.appendChild(option);
        }
    }

    // Fungsi untuk set waktu default
    function setDefaultTimes() {
        let now = new Date();
        let currentHour = now.getHours();
        let currentMinute = now.getMinutes();
        let currentSecond = now.getSeconds();

        let nextHour = currentHour + 1;
        if (nextHour > 23) nextHour = 23; // Batas maksimal jam 23

        // Isi dropdown dengan data waktu
        populateTimeOptions("startHour", 0, 23);
        populateTimeOptions("startMinute", 0, 59);
        populateTimeOptions("startSecond", 0, 59);
        populateTimeOptions("endHour", 0, 23);
        populateTimeOptions("endMinute", 0, 59);
        populateTimeOptions("endSecond", 0, 59);

        // Set nilai default untuk jam mulai dan jam akhir
        document.getElementById("startHour").value = currentHour.toString().padStart(2, '0');
        document.getElementById("startMinute").value = currentMinute.toString().padStart(2, '0');
        document.getElementById("startSecond").value = currentSecond.toString().padStart(2, '0');

        document.getElementById("endHour").value = nextHour.toString().padStart(2, '0');
        document.getElementById("endMinute").value = currentMinute.toString().padStart(2, '0');
        document.getElementById("endSecond").value = currentSecond.toString().padStart(2, '0');
    }

    // Jalankan fungsi setDefaultTimes saat halaman dimuat
    setDefaultTimes();


    Print=()=>{
      window.print();
    }
</script>