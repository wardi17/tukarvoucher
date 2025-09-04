// Import optional goBack function if needed
import { goBack } from '../main.js';
import { baseUrl } from '../../config.js';

class TransaksiForm {
  constructor() {
    this.form = document.createElement('form');
    this.appendCustomStyles();
  }

  appendCustomStyles() {
    const style = document.createElement('style');
    style.textContent = `
      input[type=number]::-webkit-outer-spin-button,
      input[type=number]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
      }
      input[type=number] {
        -moz-appearance: textfield;
      }
      .text-success {
        color: green;
        font-weight: bold;
      }
      .error {
        color: red;
        font-size: 0.875rem;
         font-weight: bold;
      }
      .icon-check {
        margin-left: 5px;
        color: green;
      }
    `;
    document.head.appendChild(style);
  }

  async render() {
    const form = this.form;
    if (!form) {
      console.error("Form belum diinisialisasi.");
      return;
    }

    // Set isi form
    this.setFormContent();

    //tampil merchat
     this.getTokoMerchat();
    //and tampil merchat

    // Validasi input 
    this.keypresinput();
    this.validasiKodeVoucher();
    this.tomboladdketable();
    this.simpanData();

    return form;
  }

  setFormContent() {
    this.form.innerHTML = `
      <div class="row col-md-12">
        <div class="col-md-1">
          <button id="kembalihider" type="button" class="btn btn-lg text-start">
            <i class="fa-solid fa-chevron-left"></i>
          </button>
        </div>
        <div class="col-md-11">
          <h5 class="text-center">Form Tukar Voucher</h5>
        </div>
      </div>
    
        <div class="row mb-3">
        <label for="Toko_merchant" class="col-sm-1 col-form-label pe-0">Merchant</label>
        <div class="col-sm-3">
          <select id="Toko_merchant" name="Toko_merchant" class="form-select">
            <option value="">Memuat...</option>
          </select>
          <span id="Toko_merchantError" class="error"></span>
        </div>
      </div>
      <div class="row mb-3">
        <label for="custname" class="col-sm-1 col-form-label pe-0">Nama</label>
        <div class="col-sm-3">
          <input type="text" id="custname" class="form-control" />
          <span id="custnameError" class="error"></span>
        </div>
      </div>
      <div class="row mb-3">
        <label for="notelpon" class="col-sm-1 col-form-label pe-0">No Hp</label>
        <div class="col-sm-2">
          <input type="number" id="notelpon" class="form-control text-start"/>
          <span id="notelponError" class="error"></span>
        </div>
      </div>
      <div class="row mb-3">
        <label for="keterangan" class="col-sm-1 col-form-label pe-0">Ket</label>
        <div class="col-sm-5">
          <textarea id="keterangan" class="form-control" style="width:150%; height:80px;"></textarea>
          <span id="keteranganError" class="error"></span>
        </div>
      </div>
      <div class="row mb-3 align-items-center">
        <label for="kodevoucher" class="col-sm-1 col-form-label pe-0">Kode Voucher</label>
        <div class="col-sm-3 d-flex">
          <input type="text" id="kodevoucher" class="form-control" style="text-transform: uppercase;"/>
          <button type="button" id="addvoucher" class="btn btn-info ms-2">+</button>
        </div>
        <div class="col-sm-4">
          <span id="kodevoucherError" class="error"></span>
        </div>
      </div>
      <div id="voucherContainer" style="display:none;" class="col-6">
          <h6>Detail Voucher</h6>
          <table id="voucherTable" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th style="width:50px;">No</th>
                <th>Kode Voucher</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
          <div>
            <button class="btn btn-primary" id="Simpdandata" style="display:none;">
                Save
            </button>
        </div>
        </div>
    `;
  }


//tampilgetokoMerchat
   async getTokoMerchat(){
    const selectElement = this.form.querySelector("#Toko_merchant");
    const tokomerchant  = document.getElementById("tokomerchant").value;

    selectElement.innerHTML = '<option disable value="">-- Memuat... --</option>';
     try{
        if(tokomerchant !=="full"){
                  selectElement.disabled = true;
                  selectElement.innerHTML = `<option selected  value="${tokomerchant}">${tokomerchant}</option>`;
        }else{
        const datatoko = await this.TokoMerchat();
        datatoko.forEach(item =>{
          const option = document.createElement('option');
          option.value =item;
          option.textContent = item;
          selectElement.appendChild(option);
        })

        }
    
       } catch (error) {
        console.log('Gagal memuat  Merchat:', error);
        selectElement.innerHTML = '<option value="">-- Gagal memuat data --</option>';
      }

      $("#Toko_merchant").select2({
          theme: "bootstrap-5",
        });
  }

  async TokoMerchat(){
     return new Promise((resolve, reject) => {
      $.ajax({
        url: `${baseUrl}/router/seturl`,
        method: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded; charset=UTF-8",
        headers: { 'url': 'vouchertukar/gettokomerchat' },
        success: (result) => {
          const datas = result.data;
          if (!result.error) {
            resolve(datas);
          } else {
            reject(new Error(result.error || "Unexpected response format"));
          }
        },
        error: (jqXHR) => {
          const errorMessage = jqXHR.responseJSON?.error || "Failed to fetch data";
          reject(new Error(errorMessage));
        }
      });
    });
  }
//and tampilgettokomerchat


  keypresinput() {
    this.validateInput("#Toko_merchant", "#Toko_merchantError", "toko merchat harus di pilih");
    this.validateInput("#custname", "#custnameError", "name harus di isi");
 
  }

validateInput(inputSelector, errorSelector, errorMessage) {
  $(document).on("input change blur", inputSelector, function () {
    const value = $(this).val().trim();
    if (!value) {
      $(errorSelector).text(errorMessage);
      $(this).addClass('focusedInput');
    } else {
      $(errorSelector).text("");
      $(this).removeClass('focusedInput');
    }
  });
}


  showLoading() {
    Swal.fire({
      title: 'Loading',
      html: 'Please wait...',
      allowEscapeKey: false,
      allowOutsideClick: false,
      didOpen: () => Swal.showLoading()
    });
  }

  showErrorMessage() {
    Swal.fire({
      icon: "error",
      title: "Error!",
      text: "Terjadi kesalahan saat Simpan data."
    });
  }

  cekkodeVoucher(kodevoucher) {
    return new Promise((resolve, reject) => {
      if (!kodevoucher) {
        $("#kodevoucherError")
        .removeClass("text-success")
         .addClass("error")
        .text("Kode voucher harus diisi");
        resolve(false);
        return;
      }

      $.ajax({
        url: `${baseUrl}/router/seturl`,
        method: "POST",
        data: JSON.stringify({ kodevoucher }),
        dataType: "json",
        contentType: "application/x-www-form-urlencoded; charset=UTF-8",
        headers: { 'url': 'vouchertukar/cekkodevoucher'},
        success: (result) => {
          const datas = result.data;
          
          if (!result.error) {
            // tampilkan pesan berdasarkan status
            if(datas.status ==="already"){
               $("#kodevoucherError")
                .removeClass("text-success")
                .addClass("error")
                .text(datas.message);
                 resolve(false);
            }else if (datas.status === "ok") {
              $("#kodevoucherError")
                .removeClass("error")
                .addClass("text-success")
                .html(`${datas.message} <span class="icon-check">&#10004;</span>`); 
              resolve(datas);
            } else if (datas.status === "not_found") {
              $("#kodevoucherError")
                .removeClass("text-success")
                .addClass("error")
                .text(datas.message);
              resolve(false);
            } else if (datas.status === "error") {
              $("#kodevoucherError")
                .removeClass("text-success")
                .addClass("error")
                .text(datas.message || "Terjadi kesalahan saat memeriksa kode voucher.");
              resolve(false);
            }

          } else {
            $("#kodevoucherError").text(result.error || "Kode voucher tidak valid");
            resolve(false);
          }
        },
        error: (jqXHR) => {
          const errorMessage = jqXHR.responseJSON?.error || "Gagal memeriksa kode voucher";
          $("#kodevoucherError").text(errorMessage);
          resolve(false);
        }
      });
    });
  }

  validasiKodeVoucher() {
    $(document).on("blur", "#kodevoucher", async (event)  => {
      event.preventDefault();
      const kodevoucher = this.form.querySelector("#kodevoucher").value.trim();
       await this.cekkodeVoucher(kodevoucher);
       
    });


  }




  //set data ke table detail

tomboladdketable() {
    let counter = 0; 
    $(document).on("click", "#addvoucher", async (event) => {
        event.preventDefault();
        const kodevoucher = this.form.querySelector("#kodevoucher").value.trim();
        
        // Validasi kode voucher
        const cekvoucher = await this.cekkodeVoucher(kodevoucher);
        if (!cekvoucher) {
            return;
        }

        // Cek apakah kode voucher sudah ada di tabel
      let $tbody = $(this.form).find('#voucherTable tbody');
      if ($tbody.length === 0) $tbody = $('#voucherTable tbody'); // fallback global

          let sudahAda = false;
          $tbody.find('tr').each(function() {
            // coba ambil dari data attribute dulu (jika ada), lalu input.value, lalu text
            let kode = $(this).data('kode');
            if (!kode) {
              const $input = $(this).find('input.kodevoucher-input');
              kode = $input.length ? $input.val() : $(this).find('td').eq(1).text();
            }
            if (String(kode).trim().toUpperCase() === kodevoucher.toUpperCase()) {
              sudahAda = true;
              return false; // break loop
            }
          });

          if (sudahAda) {
            $("#kodevoucherError")
              .removeClass("text-success")
              .addClass("error")
              .text("Kode voucher ini sudah ada di tabel.");
            return;
          }


        // Jika semua validasi lolos, tambahkan kode voucher ke tabel
        counter++;
        this.setdatatbody(kodevoucher);

        // Tampilkan tabel jika belum muncul
        $("#voucherContainer").show();
         $("#Simpdandata").show();
        // Reset input
        $("#kodevoucher").val("");
        $("#kodevoucherError").text("");
    });

    // Hapus voucher dari tabel
    $(document).on("click", ".deletevoucher", function() {
       $(this).closest("tr").remove(); // hapus baris tombol ini


        // Segarkan nomor urut setelah baris dihapus
        refreshRowNumbers();
        // Jika tabel kosong, sembunyikan lagi
        if ($("#voucherTable tbody tr").length === 0) {
            $("#voucherContainer").hide();
             $("#Simpdandata").hide();
        }
    });

    const refreshRowNumbers = () => {
        $("#voucherTable tbody tr").each(function(index) {
            $(this).find("td:eq(0)").text(index + 1); // Kolom pertama berisi nomor urut
        });

    };
}



    setdatatbody(kodevoucher) {
      const row = `
        <tr>
          <td></td>
          <td>
            <input type="text" class="form-control kodevoucher-input" value="${kodevoucher}" readonly />
          </td>
        </tr>`;
      $("#voucherTable tbody").append(row);
     
        $("#voucherTable tbody tr").each(function(index) {
            $(this).find("td:eq(0)").text(index + 1); // Kolom pertama berisi nomor urut
        });
    
  }
 

  //fungsi untuk tombol simpandata
  simpanData(){
    $(document).on("click", "#Simpdandata", async (event) => {
      event.preventDefault();
      // Ambil nilai dari form
      const username     = document.getElementById("username").value.trim();
      const userid       = document.getElementById("userid").value.trim();
      const Toko_merchant = this.form.querySelector("#Toko_merchant").value.trim();
      const custname = this.form.querySelector("#custname").value.trim();
      const notelpon = this.form.querySelector("#notelpon").value.trim();
      const keterangan = this.form.querySelector("#keterangan").value.trim();

      
      let isValid = true;

      if (!Toko_merchant) {
        $("#Toko_merchantError").text("toko merchat harus di pilih");
        isValid = false;
      } else {
        $("#Toko_merchantError").text("");
      }
      if (!custname) {
        $("#custnameError").text("nama harus diisi");
        isValid = false;
      } else {
        $("#custnameError").text("");
      }

      // Ambil data voucher dari tabel
      const voucherData = [];
      $("#voucherTable tbody tr").each(function() {
        const kodevoucher = $(this).find("input.kodevoucher-input").val().trim();
        if (kodevoucher) {
          voucherData.push({ kodevoucher });
        }
      });

      if (voucherData.length === 0) {
        $("#kodevoucherError").text("Minimal satu kode voucher harus ditambahkan");
        isValid = false;
      } else {
        $("#kodevoucherError").text("");
      }

      if (!isValid) {
        return; // Hentikan proses jika ada validasi yang gagal
      }

      // Siapkan data untuk dikirim ke backend
      const postData = {
        userid:userid,
        username:username,
        toko_merchant:Toko_merchant,
        custname: custname,
        notelpon: notelpon,
        keterangan: keterangan,
        vouchers: voucherData
      };

      // console.log("Data yang akan dikirim:", postData); // Debugging
      // return
   this.ProsessSimpanData(postData)
     // return postData;
      //console.log("Data yang akan dikirim:", postData); // Debugging 
      //return; // Hentikan eksekusi di sini untuk debugging
     });
  }


  ProsessSimpanData(postData) {
    this.showLoading();
    $.ajax({
      url: `${baseUrl}/router/seturl`,
      method: "POST",
      data: JSON.stringify(postData),
      dataType: "json",
      contentType: "application/x-www-form-urlencoded; charset=UTF-8",
      headers: { 'url': 'vouchertukar/savedata' },
      success: (result) => {
        Swal.close(); // Tutup loading
        if (!result.error) {
          Swal.fire({
            icon: "success",
            title: "Sukses!",
            showConfirmButton: false,
            timer: 1000,
            text: result.message || "Data berhasil disimpan."
          }).then(() => {
            // Kembali ke halaman sebelumnya atau lakukan tindakan lain
            goBack();
          });
        } else {
          this.showErrorMessage();
        }
      },
      error: (jqXHR) => {
        Swal.close(); // Tutup loading
        const errorMessage = jqXHR.responseJSON?.error || "Gagal menyimpan data";
        this.showErrorMessage(errorMessage);
      }
    });
  }
}

export default TransaksiForm;
