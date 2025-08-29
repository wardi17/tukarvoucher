// Import optional goBack function if needed
import {goBack} from '../main.js';
import {baseUrl} from '../../config.js';

class TransaksiFormPosting {
  constructor(editData) {
    this.form = document.createElement('form');
    this.appendCustomStyles();
    this.editData = editData; 
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
    this.addbuttonPostingListener();


    return form;
  }

 async setFormContent() {
  const cabang = this.editData ? this.editData.cabang : '';
  const customerid = this.editData ? this.editData.customerid : '';
  const sotransacid = this.editData ? this.editData.sotransacid : '';
  const custname = this.editData ? this.editData.custname : '';
  const kode = this.editData ? this.editData.kode : '';
  const jumlah = this.editData ? this.editData.jumlah : '';
  const keterarngan = this.editData ? this.editData.keterarngan : '';
  let cust = customerid + ' | ' + custname;
 
    const datas = { "kode": kode };
        const getdatadetail = await this.getdatadetail(datas);
    this.form.innerHTML = `
      <div class="row col-md-12">
        <div class="col-md-1">
          <button id="kembalihider" type="button" class="btn btn-lg text-start">
            <i class="fa-solid fa-chevron-left"></i>
          </button>
        </div>
        <div class="col-md-11">
          <h5 class="text-center">Form Posting Berikan Voucher</h5>
        </div>
      </div>
      <div class="row mb-3">
        <label for="Cabang" class="col-sm-1 col-form-label pe-0">Cabang</label>
        <div class="col-sm-3">
          <select disabled id="cabang" name="cabang" class="form-select">
            <option value="">${cabang}</option>
          </select>
          <span id="cabangError" class="error"></span>
        </div>
      </div>
      <div class="row mb-3">
        <label for="customerid" class="col-sm-1 col-form-label pe-0">CustomerID</label>
        <div class="col-sm-5">
          <select disabled id="customerid" name="customerid" class="form-select">
            <option value="">${cust}</option>
          </select>
          <span id="customeridError" class="error"></span>
        </div>
      </div>
      <div class="row mb-3">
        <label for="noso" class="col-sm-1 col-form-label pe-0">NO SO</label>
        <div class="col-sm-3">
          <input type="text" disabled id="noso" value="${sotransacid}" class="form-control" />
          <span id="nosoError" class="error"></span>
        </div>
      </div>
      <div class="row mb-3">
        <label for="jumlah" class="col-sm-1 col-form-label pe-0">Jumlah</label>
        <div class="col-sm-2">
          <input type="number" disabled value="${jumlah}" id="jumlah" class="form-control text-end" />
          <span id="jumlahError" class="error"></span>
        </div>
      </div>
      <div class="row mb-3">
        <label for="keterangan" class="col-sm-1 col-form-label pe-0">Keterangan</label>
        <div class="col-sm-5">
          <textarea id="keterangan" disabled class="form-control" style="width:150%; height:80px;">${keterarngan}</textarea>
          <span id="keteranganError" class="error"></span>
        </div>
      </div>
      <div class="row mb-3 align-items-center" style="display:none;">
        <label for="kodevoucher" class="col-sm-1 col-form-label pe-0">Kode Voucher</label>
        <div class="col-sm-3 d-flex">
          <input type="text" id="kodevoucher" class="form-control" />
          <button type="button" id="addvoucher" class="btn btn-info ms-2">+</button>
        </div>
        <div class="col-sm-4">
          <span id="kodevoucherError" class="error"></span>
        </div>
      </div>
      <div id="voucherContainer"  class="col-6">
          <h6>Detail Voucher</h6>
          <table id="voucherTable" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th style="width:50px;">No</th>
                <th>Kode Voucher</th>
              </tr>
            </thead>
            <tbody>${this.SetRowData(getdatadetail)}</tbody>
          </table>
          <div>
              <button id="kembalihider" type="button" class="btn btn-secondary">
                Batal
              </button>
            <button class="btn btn-primary" id="Posting" >
              Posting
            </button>
        </div>
        </div>
    `;
  }

  SetRowData(data) {
    if (!Array.isArray(data) || data.length === 0) {
      return `<tr><td colspan="2">Tidak ada data</td></tr>`;
    }
    let rows = '';
    data.forEach((item, index) => {
      rows += `
        <tr>
          <td class="text-center">${index + 1}</td>
          <td>${item.Kode_voucher}</td>
        </tr>
      `;
    });
    return rows;
  }

async getdatadetail(datas){
    return new Promise((resolve, reject) => {
      $.ajax({
        url: `${baseUrl}/router/seturl`,  
        method: "POST",
        dataType: "json",
        data: JSON.stringify(datas),
        contentType: "application/x-www-form-urlencoded; charset=UTF-8",
        headers: { 'url': 'voucherberi/getdetailberivoucher' },
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
        console.log("AJAX Error:", jqXHR); return
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


  //fungsi untuk proses posting
  addbuttonPostingListener() {
    //const root = document.getElementById('root');
    $(document).off('click', '#Posting').on('click', '#Posting', async (e) => {
        e.preventDefault();
      const kode = this.editData ? this.editData.kode : '';
         const datas = { "kode": kode };

         this.ProsesPosting(datas);
    });
    //
  }

  ProsesPosting(data){
    $.ajax({
        url: `${baseUrl}/router/seturl`,  
        method: "POST",
        dataType: "json",
        data: JSON.stringify(data),
        contentType: "application/x-www-form-urlencoded; charset=UTF-8",
        headers: { 'url': 'voucherberi/postingdata' },
       // beforeSend: () => this.showLoading(), // Ensure this.showLoading is a method
        success: (result) => {
          const datas = result.data;
          if (!result.error) {
            Swal.fire({
              icon: "success",
              title: "Sukses!",
              text: "Posting Berhasil",
               showConfirmButton: false,
            timer: 1000,
            }).then(() => {
              goBack(); // Navigate back to the previous view
            });
          } else {
            this.showErrorMessage();
          }
        },
        error: (jqXHR) => {
          this.showErrorMessage();
        }
      });
  }
} //and class

export default TransaksiFormPosting;
