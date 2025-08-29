import { baseUrl } from '../../config.js';
import { FOLDER } from '../../config.js';
import { goBack } from '../main.js';
import AttachAdd from './AttachAdd.js';
let uploadedFiles = []; 
class EditMsInventaris {
  constructor(datas) {
    this.datas = datas;
    this.rootElement = this.initializeRootElement();
    this.appendCustomStyles();
    this.showModalEdit();
    this.showfiledocument();
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

   // untuk tampildata document 
        async showfiledocument() {
  const respons = await this.fetchDatadocument();
  const document = respons.data;
  
  const document_files = document.document_files ?? "";
  const tampilAttach = $("#tampil_foto");
  
  // Bersihkan container dan event listener sebelumnya
  tampilAttach.empty();
  $(document).off("click", ".remove-file");

  if (document_files.trim() !== "") {
      $("#document_files_old").val(document_files);
      const documentArr = document_files.split(",");

      $.each(documentArr, function(a, b) {
        let fileURL = `${FOLDER}${b}`;
        const filePreview = `
          <div class="col-8 col-md-10 position-relative p-2 d-flex flex-column align-items-start file-item" data-id="${a}" data-filename="${b}">
            <img src="${fileURL}" class="img-fluid rounded border mb-1 preview-clickable" 
              data-preview="${fileURL}" 
              style="cursor: zoom-in;" alt="preview">
            <button type="button" class="btn btn-sm text-danger remove-file" data-id="${a}" data-filename="${b}" 
              style="border:none;background:transparent;cursor:pointer;z-index:10;">
              <i class="fa-solid fa-xmark"></i> Hapus
            </button>
          </div>
        `;
        tampilAttach.append(filePreview);
      });

      // Setup event listener setelah elemen dibuat
      this.setupDeleteEvents();
  }
}

// Buat method terpisah untuk handle delete events
setupDeleteEvents() {
  const self = this; // Simpan reference ke this
  
  // Pastikan event hanya didefinisikan sekali
  $(document).off("click", ".remove-file").on("click", ".remove-file", function(event) {
    event.preventDefault();
    event.stopPropagation();
  
    const fileId = $(this).data("id");
 

    // Konfirmasi sebelum hapus
    //if (confirm("Apakah Anda yakin ingin menghapus file ini?")) {
      // Hapus elemen dari DOM
      $(this).closest('.file-item').fadeOut(300, function() {
        $(this).remove();
      });
      // Hapus dari array jika ada
      if (self.uploadedFiles) {
        self.uploadedFiles = self.uploadedFiles.filter(f => f.id !== fileId);
      }
      
      //console.log("File deleted successfully"); // Debug
   // }
  });
}




    async fetchDatadocument(){
       const idinvetarsi = this.datas.id;
       const datas = {
          "idinvetarsi":idinvetarsi
       }
      
         return new Promise((resolve, reject) => {
             $.ajax({
               url: `${baseUrl}/router/seturl`,
               method: "POST",
               dataType: "json",
               data:JSON.stringify(datas),
               contentType: "application/x-www-form-urlencoded; charset=UTF-8",
               headers: { 'url': 'msinv/getdocumentbyid' },
               //beforeSend: () => this.showLoading(), // Ensure this.showLoading is a method
               success: (result) => {
                 if (!result.error) {
                   resolve(result);
                 } else {
                   reject(new Error(result.error || "Unexpected response format"));
                 }
               },
               error: (jqXHR, textStatus, errorThrown) => {
                 const errorMessage = jqXHR.responseJSON?.error || "Failed to fetch data";
                 reject(new Error(errorMessage));
               }
             });
           });
    }
   //and datadocument
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
       let clsno = id ==="InventarisID" ? 8 : 6;
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
    return `
      ${this.renderInput('InventarisID_Edit', 'Inventaris ID', 'text', data.id || '', true)}
      ${this.renderSelect('kategoriSelectEdit','Kategori')}
      ${this.renderInput('NamaBarang_Edit', 'Nama Barang', 'text', data.namabarang || '')}
      ${this.renderInput('StokMinimum_Edit', 'Stok Minimum', 'number', data.stokminimum || '', false, 'text-end')}
      ${this.renderInput('StokMaksimum_Edit', 'Stok Maksimum', 'number', data.stokmaksimum || '', false, 'text-end')}
      ${this.renderInput('HargaPokok_Edit', 'Harga Pokok', 'number', data.hargapokok || '', false, 'text-end')}
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
  showModalEdit() {
    this.removeOldModal();
    const modal = this.createModal();
    this.rootElement.appendChild(modal);
    this.initializeModal(modal);
    this.renderKategori();
  }

  removeOldModal() {
    const oldModal = document.getElementById('modal-trans-inventarisedit');
    if (oldModal) oldModal.remove();
  }

  createModal() {
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.id = 'modal-trans-inventarisedit';
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
            <h5 class="modal-title">Edit Inventaris</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="edit-form">
               <div class="row g-3">
               <input type="hidden" id="document_files_old" class="form-control">
                  <div class="col-12 col-md-6">
                    ${this.renderFormFields(this.datas)}
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
         
            
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            <button type="submit" form="edit-form" class="btn btn-primary">Simpan Perubahan</button>
            <button type="button" id="deletedata" class="btn btn-danger">Hapus</button>
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

    document.getElementById('deletedata').addEventListener('click', async () => {
      await this.handleDelete();
    });
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
      { id: 'NamaBarang_Edit', name: 'Nama Barang' },
      { id: 'kategoriSelectEdit', name: 'Kategori' },
      { id: 'StokMinimum_Edit', name: 'Stok Minimum', checkZero: true },
      { id: 'StokMaksimum_Edit', name: 'Stok Maksimum', checkZero: true },
      { id: 'HargaPokok_Edit', name: 'Harga Pokok', checkZero: true },
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
      InventarisID: getValue('InventarisID_Edit'),
      NamaBarang: getValue('NamaBarang_Edit'),
      Kategori: getValue('kategoriSelectEdit'),
      StokMinimum: getValue('StokMinimum_Edit'),
      StokMaksimum: getValue('StokMaksimum_Edit'),
      HargaPokok: getValue('HargaPokok_Edit'),
      document_files_old: this.getAttachedFileNames()
    };

    // console.log(this.getAttachedFileNames()); return;
        const  formData = new FormData();
       formData.append("datahider",JSON.stringify(data));
       uploadedFiles.forEach(fileObj =>{
            formData.append("files[]",fileObj.file);
       })

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
         headers: { 'url': 'msinv/updatedata' },
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
      const modalElement = document.getElementById('modal-trans-inventarisedit');
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
      const id = $('#InventarisID_Edit').val();
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
        data: JSON.stringify({ InventarisID: id }),
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
      const modalElement = document.getElementById('modal-trans-inventarisedit');
      const modalInstance = bootstrap.Modal.getInstance(modalElement);
      if (modalInstance) modalInstance.hide();
      goBack();
    });
  }
}

export default EditMsInventaris;
