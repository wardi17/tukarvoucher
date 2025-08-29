import { baseUrl } from '../../config.js';
import { FOLDER } from '../../config.js';
import { goBack } from '../main.js';
import AttachAdd from './AttachAdd.js';
let uploadedFiles = []; 
class EditMsKategori {
  constructor(datas) {
    this.datas = datas;
    this.rootElement = this.initializeRootElement();
    this.appendCustomStyles();
    this.showModalEdit();
   new AttachAdd(uploadedFiles);
  }

  initializeRootElement() {
    let rootElement = document.getElementById('root');
    if (!rootElement) {
      rootElement = document.createElement('div');
      rootElement.id = 'root';
      document.body.appendChild(rootElement);
    }
    return rootElement;
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

  renderInput(id, label, type = 'text', value = '', disabled = false, extraClass = '') {
       let clsno = id ==="KategoriID" ? 8 : 6;
    return `
      <div class="row mb-2">
        <label for="${id}" class="col-sm-4 col-form-label">${label}</label>
        <div class="col-sm-${clsno}">
          <input type="${type}" id="${id}" name="${id}" class="form-control ${extraClass}" value="${value}" ${disabled ? 'disabled' : ''}>
          <span id="${id}Error" class="error"></span>
        </div>
      </div>
    `;
  }

  renderFormFields(data) {
    console.log(data)
    return `
      ${this.renderInput('KategoriID_Edit', 'ID Kriteria', 'text', data.id || '', true)}
      ${this.renderInput('NamaKategori_Edit', 'Nama Kriteria', 'text', data.namakategori || '')}
     
    `;
  }


  showModalEdit() {
    this.removeOldModal();
    const modal = this.createModal();
    this.rootElement.appendChild(modal);
    this.initializeModal(modal);
    this.renderKategori();
  }

  removeOldModal() {
    const oldModal = document.getElementById('modal-trans-Kategoriedit');
    if (oldModal) oldModal.remove();
  }

  createModal() {
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.id = 'modal-trans-Kategoriedit';
    modal.setAttribute('tabindex', '-1');
    modal.setAttribute('aria-hidden', 'true');
    modal.setAttribute('data-bs-backdrop', 'static'); // modal tidak bisa ditutup dengan klik luar
    modal.setAttribute('data-bs-keyboard', 'false');  // modal tidak bisa ditutup dengan Esc

    modal.innerHTML = this.getModalContent();
    return modal;
  }

  getModalContent() {
    return `
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Edit Kategori</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="edit-form">
               <div class="row g-3">
               <input type="hidden" id="document_files_old" class="form-control">
                  <div class="col-12 col-md-12">
                    ${this.renderFormFields(this.datas)}
                  </div>
          
                </div>
         
            
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            <button type="submit" form="edit-form" class="btn btn-primary">Simpan Perubahan</button>
          </div>
        </div>
      </div>
    `;
  }

    renderKategori() {
      
      let datas =this.datas;
      const ktgID = datas.kategoriid;
      $.ajax({
        url: `${baseUrl}/router/seturl`,
        method: "GET",
        dataType: "json",
        headers: { 'url': 'msinv/getkatgori' },
        success: function (result) {
          const data = result.data;
          const $select = $('#kategoriSelectEdit');
          $select.empty();
         
          data.forEach(item => {
            $select.append(`<option value="${item.id}"  ${ktgID == item.id ? "selected" : ""} >${item.name}</option>`);
          });
        },
        error: function () {
          console.error("Gagal mengambil data kategori");
        }
      });
    }



  initializeModal(modal) {
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();

    const form = document.getElementById('edit-form');
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      await this.handleEditSubmit(e);
    });

    // document.getElementById('deletedata').addEventListener('click', async () => {
    //   await this.handleDelete();
    // });
  }

  async handleEditSubmit(event) {
    const dataInput = await this.validateInput(event);
    if (!dataInput) return;

    try {
      await this.sendDataToApi(dataInput);
      this.showSuccessMessage();
    } catch (error) {
      this.showErrorMessage(error);
    }
  }

  async validateInput(event) {
    let valid = true;
    const getValue = id => $(`#${id}`).val();
    const showError = (id, message) => {
      $(`#${id}Error`).text(message);
      valid = false;
    };

    const fields = [
      { id: 'NamaKategori_Edit', name: 'Nama Kriteria' },
    ];

    fields.forEach(field => {
      const value = getValue(field.id);
      if (!value || (field.checkZero && value === "0")) {
        showError(field.id, `${field.name} harus diisi`);
      } else {
        $(`#${field.id}Error`).text('');
      }
    });

    if (!valid) {
      event.preventDefault();
      return false;
    }

    const data= {
      KategoriID: getValue('KategoriID_Edit'),
      NamaKategori: getValue('NamaKategori_Edit'),
    };

    // console.log(this.getAttachedFileNames()); return;
        const  formData = new FormData();
       formData.append("datahider",JSON.stringify(data));

       //console.log(uploadedFiles);
       return formData;
  }

  getAttachedFileNames() {
  const container = document.getElementById('tampil_foto');
  if(!container) return [];
  const fileItems = container.querySelectorAll('.file-item');

  const fileNames = Array.from(fileItems).map(item => item.dataset.filename.trim());

  return fileNames;
}


  async sendDataToApi(data) {
    return new Promise((resolve, reject) => {
      $.ajax({
        url: `${baseUrl}/router/seturl`,
        method: "POST",
        dataType: "json",
        data:data,
          processData: false,        // Penting: Jangan proses FormData
          contentType: false,         
         headers: { 'url': 'msktg/updatedata' },
        beforeSend: this.showLoading,
        success: (result) => {
          this.hideLoading();
          if (!result.error) {
            resolve(result);
          } else {
            reject(new Error("Data gagal diperbarui."));
          }
        },
        error: () => {
          this.hideLoading();
          reject(new Error("Koneksi ke server gagal."));
        }
      });
    });
  }

  showSuccessMessage() {
    Swal.fire({
      position: 'center',
      icon: 'success',
      showConfirmButton: false,
      timer: 1000,
      text: "Data berhasil diperbarui."
    }).then(() => {
      const modalElement = document.getElementById('modal-trans-Kategoriedit');
      const modalInstance = bootstrap.Modal.getInstance(modalElement);
      if (modalInstance) {
        modalInstance.hide();
      }
      goBack();
    });
  }

  showErrorMessage(error) {
    Swal.fire({
      icon: 'error',
      title: 'Gagal Menyimpan',
      text: error?.message || 'Terjadi kesalahan saat menyimpan data.',
      showConfirmButton: true
    });
  }

  showLoading() {
    Swal.fire({
      title: 'Menyimpan...',
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      }
    });
  }

  hideLoading() {
    Swal.close();
  }

  async handleDelete() {
    const confirm = await Swal.fire({
      title: 'Yakin ingin menghapus?',
      text: "Data akan dihapus secara permanen!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Ya, hapus!',
      cancelButtonText: 'Batal'
    });

    if (confirm.isConfirmed) {
      const id = $('#KategoriID_Edit').val();
      try {
        await this.deleteDataFromApi(id);
        this.showDeleteSuccessMessage();
      } catch (error) {
        this.showErrorMessage(error);
      }
    }
  }

  async deleteDataFromApi(id) {
    return new Promise((resolve, reject) => {
      $.ajax({
        url: `${baseUrl}/router/seturl`,
        method: "POST",
        dataType: "json",
        data: JSON.stringify({ KategoriID: id }),
        contentType: "application/x-www-form-urlencoded; charset=UTF-8",
        headers: { 'url': 'msinv/deletedata' },
        beforeSend: this.showLoading,
        success: (result) => {
          this.hideLoading();
          if (!result.error) {
            resolve(result);
          } else {
            reject(new Error("Data gagal dihapus."));
          }
        },
        error: () => {
          this.hideLoading();
          reject(new Error("Koneksi ke server gagal."));
        }
      });
    });
  }

  showDeleteSuccessMessage() {
    Swal.fire({
      icon: 'success',
      title: 'Terhapus!',
      text: 'Data berhasil dihapus.',
      timer: 1500,
      showConfirmButton: false
    }).then(() => {
      const modalElement = document.getElementById('modal-trans-Kategoriedit');
      const modalInstance = bootstrap.Modal.getInstance(modalElement);
      if (modalInstance) modalInstance.hide();
      goBack();
    });
  }
}

export default EditMsKategori;
