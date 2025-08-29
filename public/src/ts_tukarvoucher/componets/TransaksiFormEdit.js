// Import optional goBack function if needed
import {goBack} from '../main.js';
import {baseUrl} from '../../config.js';
class TransaksiFormEdit {
  constructor(editData) {
    this.form = document.createElement('form');
    this.masterInventaris = []; // Store master inventory data
    this.editData = editData; 
  }

  async render() {
    const form = this.form;

    if (!form) {
      console.error("Form belum diinisialisasi.");
      return;
    }

    // Set isi form
    this.setFormContent();

    // Ambil data inventaris
    await this.loadInventarisData();

    // Tambahkan event listener untuk submit form
    this.addSubmitListener();

    // Tambahkan event listener untuk tombol kembali jika ada
    this.addBackButtonListener();

    return form;
  }

  setFormContent() {
    this.form.innerHTML = `
    <div class="row col-md-12">
    <div class="col-md-1">
              <button id="kembalihider" type="button" class="btn btn-lg text-start"><i class="fa-solid fa-chevron-left"></i></button>
            </div>
            <div class="col-md-11">
              <h5 class="text-center">Form Edit Transaksi Inventaris</h5>
            </div>
    </div>
      <div class="row mb-3">
        <label for="inventaris" class="col-sm-1 col-form-label pe-0">Inventaris</label>
        <div class="col-sm-3">
          <select    disabled id="inventaris_Edit" name="inventaris" class="form-select">
            <option value="">Memuat...</option>
          </select>
          <span id="inventarisError" class="error"></span>
        </div>
      </div>
      <div id="detailBarang" style="margin-bottom: 15px; display: none;"></div>
    `;
  }

  async loadInventarisData() {
    const selectInventaris = this.form.querySelector("#inventaris_Edit");
    const inventarisid = this.editData ? this.editData.inventarisid : null;


    try {
      // Ambil data master inventaris dari backend
      this.masterInventaris = await this.getDataInventaris();

      // Kosongkan isi select dan tambahkan opsi default
      selectInventaris.innerHTML = '<option disable value="" >-- Pilih Barang --</option>';

      // Tambahkan data inventaris ke dropdown
      this.masterInventaris.forEach(item => {
        const option = document.createElement('option');
        option.value = item.id;
        option.textContent = item.namabarang;
         if (item.id === inventarisid) {
          option.selected = true;
        }
        selectInventaris.appendChild(option);
        `1`
      });
      this.setInputandetai(inventarisid);
    } catch (error) {
      console.log('Gagal memuat data inventaris:', error);
      selectInventaris.innerHTML = '<option value="">-- Gagal memuat data --</option>';
    }
  }


  //set inputan detail

  setInputandetai=(inventarisid)=>{
    //console.log(inventarisid)
      const datas ={
      "IDinventaris":inventarisid
     }
     this.setInputData(datas);
   
  }
//and set inputan detail

  addSubmitListener() {
  const selectElement = this.form.querySelector("#inventaris_Edit");

  if (!selectElement) {
    console.error("Select element with id 'inventaris' not found");
    return;
  }

  // Event saat pilihan berubah
  selectElement.addEventListener("change",(event) => {
    const value = event.target.value;
    
    if(!value){
      alert('Inventari harus di pilih');
      return
    }
    
     const datas ={
      "IDinventaris": value
     }
     this.setInputData(datas);
   
  });

  // Event submit form update
  this.form.addEventListener('submit', (e) => {
    e.preventDefault();

    const selectElement = document.getElementById('inventaris_Edit');

    const InventariID = selectElement.value;
    const InventariNama = selectElement.options[selectElement.selectedIndex].text;

   const Stok =parseFloat(this.setvalue('Stok'));
   const qty = parseFloat(this.setvalue('qty'));

   const ket     = this.setvalue('ket');
   const idtrans = this.setvalue('idtrans');
   const oldqty  = parseFloat(this.setvalue('oldqty'));
   const oldinventarsiid = this.setvalue('oldinventarsiid');
   const transaksiiddetail = this.setvalue('transaksiiddetail');
    if (!InventariID) {
      alert("Silakan pilih barang terlebih dahulu.");
      return;
    }

    if(qty > (Stok+oldqty)){
       alert("qty tidak boleh melebih Stok");
       return;
    }

    const data = {
      InventariID: InventariID,
      Invernama: InventariNama,
      Stok:Stok,
      qty:qty,
      ket:ket,
      idtrans:idtrans,
      oldqty:oldqty,
      oldinventarsiid:oldinventarsiid,
      transaksiiddetail:transaksiiddetail
    };
    this.Updatedata(data);

  });
//and event submit form

//Event Delete 
  this.form.addEventListener("click",(event)=> {
    // cek apakah yang diklik adalah tombol DeleteBtn
    const target = event.target.closest("#DeleteBtn");
    if (!target) return; // kalau bukan tombol delete, abaikan
    event.preventDefault();
       this.confirmDelete(); // panggil fungsi konfirmasi hapus di sini
  });

 //and event delete

}

setInputData= async (datas)=>{
   const datadtail = await this.getdataDetail(datas);
   const jumlah = this.editData.jumlah;
   const keterarngan = this.editData.keterarngan;
   const id          = this.editData.id;
   const old_qty      = parseFloat(this.editData.jumlah);
    const oldinventarsiid      = this.editData.inventarisid;
    const transaksiiddetail    = this.editData.transaksiiddetail;
    const stok  = parseFloat(datadtail.Stok);
    if (datadtail) {
      const detailDiv = this.form.querySelector("#detailBarang");
      detailDiv.style.display = "block";
      detailDiv.innerHTML = `
        <div class="row g-3">
           <div class="col-12 col-md-6">
           <input type="hidden"  id="transaksiiddetail" value="${transaksiiddetail}" class="form-control">
            <input type="hidden"  id="idtrans" value="${id}" class="form-control">
            <input type="hidden"  id="oldqty" value="${old_qty}" class="form-control">
            <input type="hidden"  id="oldinventarsiid" value="${oldinventarsiid}" class="form-control">
            <div class="row mb-3">
            <label for="Stok" class="col-sm-3 col-form-label pe-0">Stok</label>
            <div class="col-sm-3">
             <input  type="text" disabled  value="${stok}" id="Stok" class="form-control">
              <span id="StokError" class="error"></span>
            </div>
          </div>
            <div class="row mb-3">
            <label for="qty" class="col-sm-3 col-form-label pe-0">qty</label>
            <div class="col-sm-3">
             <input  type="text"  value="${parseFloat(jumlah)}" id="qty" class="form-control">
              <span id="qtyError" class="error"></span>
            </div>
          </div>
             <div class="row mb-3">
            <label for="ket" class="col-sm-3 col-form-label pe-0">Keteangan</label>
            <div class="col-sm-8">
             <textarea  type="text"  value="${keterarngan}" id="ket" class="form-control">${keterarngan}</textarea>
              <span id="ketError" class="error"></span>
            </div>
          </div>
      <button type="submit" style="padding: 8px 12px;" class="btn btn-success" >Simpan</button>
      <button type="button" id="DeleteBtn" style="padding: 8px 12px; margin-left: 8px;" class="btn btn-danger" >Delete</button>
    <button type="button" id="BtnBatal" style="padding: 8px 12px; margin-left: 8px;" class="btn btn-secondary" >Batal</button>
          </div>
       <div class="col-12 col-md-6">
        ${this.renderImage(datadtail)}
       </div>

          
      `;
    }

}
//untuk tampil img
  renderImage=(datadtail)=>{
    const document_files = datadtail.document_files;
    if(!document_files){
      return[];
    } 

    const documentArr = document_files.split(",");
    let html= `<div id="tampil_foto" class="row mt-2 g-2">`;
       $.each(documentArr, function(a, b) {
        let fileURL = `${FOLDER}${b}`;
     html+= `
          <div class="col-8 col-md-10 position-relative p-2 d-flex flex-column align-items-start file-item" data-id="${a}" data-filename="${b}">
            <img src="${fileURL}" class="img-fluid rounded border mb-1 preview-clickable" 
             >
          </div>
        `;
       });
     html +=`</div>`

     return html;
  }
//and tampil img
 setvalue=(id)=>{
    return document.getElementById(id).value;
 }
Updatedata(datas){
  //console.log(datas); return;
          $.ajax({
            url: `${baseUrl}/router/seturl`,
            method: "POST",
            dataType: "json",
            data:JSON.stringify(datas),
            contentType: "application/x-www-form-urlencoded; charset=UTF-8",
            headers: { url:'trans/updatetransaksi' },
            beforeSend: function () {
                Swal.fire({
                    title: 'Loading',
                    html: 'Please wait...',
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            },
            success: function (result) {
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    showConfirmButton: false,
                    timer: 1000,
                    text: result.error,
                }).then(function () {
                    goBack();
                });
            },
            error: function () {
                Swal.fire({
                    icon: "error",
                    title: "Error!",
                    text: "Terjadi kesalahan saat Simpan data."
                });
            }
        });
}
async getdataDetail(datas){
    return new Promise((resolve, reject) => {
      $.ajax({
        url: `${baseUrl}/router/seturl`,
        method: "POST",
        dataType: "json",
        data: JSON.stringify(datas),
        contentType: "application/x-www-form-urlencoded; charset=UTF-8",
        headers: { 'url': 'trans/getdetailinventaris' },
       // beforeSend: () => this.showLoading(), // Ensure this.showLoading is a method
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



  addBackButtonListener() {
    const btnKembali = this.form.querySelector('#btnKembali');
    if (btnKembali) {
      btnKembali.addEventListener('click', () => {
        import('./TransaksiList.js').then((module) => {
          const TransaksiList = module.default;
          const list = new TransaksiList();
          list.render();
        });
      });
    }
  }

  async getDataInventaris() { 

  
    return new Promise((resolve, reject) => {
      $.ajax({
        url: `${baseUrl}/router/seturl`,
        method: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded; charset=UTF-8",
        headers: { 'url': 'trans/getdatainventaris' },
       // beforeSend: () => this.showLoading(), // Ensure this.showLoading is a method
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


  //fungsi untuk proses delete
    async confirmDelete() {

      const idtrans = document.getElementById("idtrans")?.value;
      const transaksiiddetail = document.getElementById("transaksiiddetail")?.value;

      const vardatas ={
        idtrans:idtrans,
        transaksiiddetail:transaksiiddetail
      }
      if (!idtrans) {
      this.showAlert("warning", "ID Tidak Ditemukan!", "Tidak ada data yang dipilih untuk dihapus.");
        return;
      }
      const result = await this.showConfirmationDialog();
      if (result.isConfirmed) {
        // console.log(idtrans); return
        await this.processDelete(vardatas);
      }
    }

     showAlert(icon, title, text) {
        Swal.fire({
          icon,
          title,
          text
        });
      }
  showConfirmationDialog() {
      return Swal.fire({
        title: "Apakah Anda Yakin?",
        text: "Hapus data ini!",
        icon: "warning",
        showDenyButton: true,
        confirmButtonColor: "#DD6B55",
        denyButtonColor: "#757575",
        confirmButtonText: "Ya, Hapus!",
        denyButtonText: "Tidak, Batal!"
      });
  }

      async  processDelete(vardatas) {
          this.showLoading();

          try {
            const response = await fetch(`${baseUrl}/router/seturl`, {
              method: "POST",
              headers: {
                "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8",
                "url": "trans/deletetransaksi"
              },
              body:JSON.stringify(vardatas),
            });

            if (!response.ok) {
              throw new Error('Network response was not ok');
            }

            const result = await response.json();
            this.handleDeleteResponse(result);
          } catch (error) {
            console.error("Error during delete:", error); // Tambahkan log untuk debugging
            this.showAlert("error", "Error!", "Terjadi kesalahan saat menghapus data.");
          }
        }
          
      showLoading() {
          Swal.fire({
            title: 'Loading',
            html: 'Please wait...',
            allowEscapeKey: false, 
            allowOutsideClick: false,
            didOpen: () => {
              Swal.showLoading();
            }
          });
        }

       handleDeleteResponse(result) {
        const status = result.error || "Data berhasil dihapus";
        Swal.fire({
          position: 'center',
          icon: 'success',
          title: status,
          showConfirmButton: false,
          timer: 1000
        }).then(() => {
         goBack();
        });
      }
  //and fungsi untuk proses delete
} //and class

export default TransaksiFormEdit;
