import { baseUrl } from '../../config.js';
import { goBack } from '../main.js';
import AttachAdd from './AttachAdd.js';

let uploadedFiles = []; 
class MsInventaris {
  constructor(containerSelector) {
    this.container = document.querySelector(containerSelector);
    this.currentModal = null;
    this.setAddData();
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
      .error {
        color: red;
        font-size: 0.875rem;
      }
    `;
    document.head.appendChild(style);
  }

  setAddData() {
    const buttonWrapper = document.createElement('div');
    buttonWrapper.className = 'w-100 d-flex justify-content-end mb-3';

    const addButton = document.createElement('button');
    addButton.className = 'btn btn-primary btn-sm';
    addButton.id = 'tambahdata';
    addButton.innerHTML = '<i class="fa-solid fa-file-circle-plus"></i> Tambah Barang';
    addButton.addEventListener('click', () => this.handleAddClick());

    buttonWrapper.appendChild(addButton);
    this.container.prepend(buttonWrapper);
  }

  handleAddClick() {
    if (this.currentModal) document.body.removeChild(this.currentModal);

    this.currentModal = this.createModal();
    document.body.appendChild(this.currentModal);

    const modalInstance = new bootstrap.Modal(this.currentModal);

    // Close buttons
    this.currentModal.querySelectorAll('.btn-close, .btn-secondary').forEach(button =>
      button.addEventListener('click', () => modalInstance.hide())
    );

    // Submit form
    this.currentModal.querySelector('#add-form').addEventListener('submit', async (e) => {
      e.preventDefault();
      await this.saveDataFrom(e);
     
    });

    modalInstance.show();
    this.renderKategori();
    new AttachAdd(uploadedFiles);
  }

    createModal() {
      const idinventaris = this.generateUniqueId();

      const modal = document.createElement('div');
      modal.className = 'modal fade';
      modal.id = 'modal-trans-inventaris';
      modal.setAttribute('tabindex', '-1');
      modal.setAttribute('aria-hidden', 'true');
      modal.setAttribute('data-bs-backdrop', 'static'); // modal tidak bisa ditutup dengan klik luar
      modal.setAttribute('data-bs-keyboard', 'false');  // modal tidak bisa ditutup dengan Esc

      modal.innerHTML = `
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Tambah Inventaris</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <form id="add-form">
                <div class="row g-3">
                  <div class="col-12 col-md-6">
                  <!-- Kolom kiri -->
                    ${this.renderFormFields(idinventaris)}
                  </div>

                  <!-- Kolom kanan untuk upload foto -->
                  <div class="col-12 col-md-6">
                    <div class="mb-3 row">
                      <label for="attach" class="col-sm-3 col-form-label">Foto</label>
                      <div class="col-sm-9">
                        <label style="cursor: pointer;" for="attach">
                          <i class="fa-solid fa-file-arrow-up fa-2x"></i>
                        </label>
                        <input id="attach" type="file" class="form-control" accept="image/*"  multiple style="display:none;">
                        <div id="tampil_foto" class="row mt-2 g-2"></div>
                      </div>
                    </div>
                  </div>

                </div>
              </form>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
              <button type="submit" form="add-form" class="btn btn-primary">Tambah</button>
            </div>
          </div>
        </div>
      `;

      return modal;
    }



  renderKategori() {
      $.ajax({
        url: `${baseUrl}/router/seturl`,
        method: "GET",
        dataType: "json",
        headers: { 'url': 'msinv/getkatgori' },
        success: function (result) {
          const data = result.data;
          const $select = $('#kategoriSelect');
          $select.empty();
          $select.append(`<option value="">-- Pilih Kategori --</option>`);
          data.forEach(item => {
            $select.append(`<option value="${item.id}">${item.name}</option>`);
          });
        },
        error: function () {
          console.error("Gagal mengambil data kategori");
        }
      });
    }



  renderFormFields(idinventaris) {
    return `
      ${this.renderInput('InventarisID', 'Inventaris ID', 'text', idinventaris, true)}
      ${this.renderSelect('kategoriSelect','Kategori')}
      ${this.renderInput('NamaBarang', 'Nama Barang')}
      ${this.renderInput('StokMinimum', 'Stok Minimum', 'number', '', false, 'text-end')}
      ${this.renderInput('StokMaksimum', 'Stok Maksimum', 'number', '', false, 'text-end')}
      ${this.renderInput('HargaPokok', 'Harga Pokok', 'number', '', false, 'text-end')}
    `;
  }

renderSelect(id,label){
  return `       <div class="mb-3 row">
                      <label for="${id}" class="col-sm-4 col-form-label">${label}</label>
                      <div class="col-sm-8">
                        <select id="${id}" name="${id}" class="form-select">
                          <option value="">Memuat...</option>
                        </select>
                        <span id="${id}Error" class="error"></span>
                      </div>
                    </div>`;
}

  renderInput(id, label, type = 'text', value = '', disabled = false, extraClass = '') {
   let clsno = id ==="InventarisID" ? 8 : 6;
   
    return `
      <div class="row mb-12 mb-2">
        <label for="${id}" class="col-sm-4 col-form-label">${label}</label>
        <div class="col-sm-${clsno}">
          <input type="${type}" id="${id}" value="${value}" class="form-control ${extraClass}" ${disabled ? 'disabled' : ''}>
          <span id="${id}Error" class="error"></span>
        </div>
      </div>
    `;
  }


  async saveDataFrom(event) {
    const dataInput = await this.validateInput(event);

    if (!dataInput) return;

    try {
      await this.sendDataToApi(dataInput);
      this.showSuccessMessage();
    } catch (error) {
      this.showErrorMessage();
    }
  }

  async sendDataToApi(data) {
    return new Promise((resolve, reject) => {
      $.ajax({
        url: `${baseUrl}/router/seturl`,
        method: "POST",
        dataType: "json",
        data: data,
          processData: false,        // Penting: Jangan proses FormData
          contentType: false,        // Penting: Biar browser set otomatis
        headers: { 'url': 'msinv/savedata' },
        beforeSend: this.showLoading,
        success: function (result) {
          if (!result.error) {
            resolve(result);
          } else {
            reject(new Error("Unexpected response format"));
          }
        },
        error: function () {
          reject(new Error("Failed to save data"));
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

  showSuccessMessage() {
    Swal.fire({
      position: 'center',
      icon: 'success',
      showConfirmButton: false,
      timer: 1000,
      text: "Data saved successfully."
    }).then(() => {
       const modalElement = document.getElementById('modal-trans-inventaris');
        const modalInstance = bootstrap.Modal.getInstance(modalElement);
        if (modalInstance) {
          modalInstance.hide(); //tutup modal dengan benar
        }
      goBack();
    });
  }

  showErrorMessage() {
    Swal.fire({
      icon: "error",
      title: "Error!",
      text: "Terjadi kesalahan saat Simpan data."
    });
  }

  async validateInput(event) {
    let valid = true;

    const getValue = id => $(`#${id}`).val();
    const showError = (id, message) => {
      $(`#${id}Error`).text(message);
      valid = false;
    };

    const fields = [
      { id: 'NamaBarang', name: 'NamaBarang' },
      { id: 'kategoriSelect', name: 'kategoriSelect' },
      { id: 'StokMinimum', name: 'StokMinimum', checkZero: true },
      { id: 'StokMaksimum', name: 'StokMaksimum', checkZero: true },
      { id: 'HargaPokok', name: 'HargaPokok', checkZero: true },
    ];

    fields.forEach(field => {
      const value = getValue(field.id);
      if (!value || (field.checkZero && value === "0")) {
        showError(field.id, `${field.name} harus di isi`);
      } else {
        $(`#${field.id}Error`).text('');
      }
    });

    if (!valid) {
      event.preventDefault();
      return false;
    }

    const data ={
      InventarisID: getValue('InventarisID'),
      NamaBarang: getValue('NamaBarang'),
      Kategori: getValue('kategoriSelect'),
      StokMinimum: getValue('StokMinimum'),
      StokMaksimum: getValue('StokMaksimum'),
      HargaPokok: getValue('HargaPokok')
    };

    const  formData = new FormData();
       formData.append("datahider",JSON.stringify(data));
       uploadedFiles.forEach(fileObj =>{
            formData.append("files[]",fileObj.file);
       })

       return formData;
  }

  generateUniqueId() {
      // data dasar: waktu + random
      const hashHex =Date.now()+ Math.random().toString(36).substring(2, 10);
      // ambil sebagian supaya pendek (misalnya 8 karakter)
      return `TRX-${hashHex.substring(0, 8)}`;
  }

   

  // untuk  upload gambar baru 15/08/2025


  //and 
}

export default MsInventaris;
