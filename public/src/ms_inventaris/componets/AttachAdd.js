// import {url_store} from '../config.js';
// import {pageMode} from "./main.js";
class AttachAdd {
  constructor(uploadedFiles) {
  
    this.uploadedFiles = uploadedFiles;
    this.renderData();
    document.body.insertAdjacentHTML("beforeend", this.renderModalViewer());
    // const isEditMode = pageMode === "edit" || pageMode === "post" || pageMode ==="detail" || pageMode ==="lap_d";

    // if(isEditMode){
    //   this.setdoducument();
    // }
    
  }
  //ini fungsi untuk upload gambar
    renderData() {
      const tampilAttach = $("#tampil_foto");
      this.uploadedFiles = this.uploadedFiles || []; // pastikan array ada

      $("#attach").on("change", (event) => {
        const files = Array.from(event.target.files);

        files.forEach((file) => {
          const maxSize = 2 * 1024 * 1024; // 2 MB
          if (file.size > maxSize) {
            alert(`Gambar "${file.name}" terlalu besar (maksimum 2 MB)`);
            return;
          }

          // Buat ID unik supaya tidak bentrok
          const fileId = `file-${Date.now()}-${Math.floor(Math.random() * 1000)}`;
          this.uploadedFiles.push({ id: fileId, file });

          // Buat URL sementara untuk preview
          const fileURL = URL.createObjectURL(file);

          // Template preview gambar
          const filePreview = `
            <div class="col-8 col-md-10 position-relative p-2 d-flex flex-column align-items-start" id="${fileId}">
              <img src="${fileURL}" class="img-fluid rounded border mb-1 preview-clickable" 
              data-preview="${fileURL}" 
              style="cursor: zoom-in;" alt="preview">
              <button class="btn btn-sm text-danger remove-filedata" data-id="${fileId}" style="border:none;background:transparent;">
                <i class="fa-solid fa-xmark"></i> Hapus
              </button>
            </div>
          `;

          tampilAttach.append(filePreview);
        });

        // Reset input supaya bisa upload file sama lagi
        $("#attach").val("");
      });

      // Event hapus file preview
      $(document).on("click", ".remove-filedata", (event) => {
        event.preventDefault();

        const fileId = $(event.currentTarget).data("id");
        $("#" + fileId).remove();
        this.uploadedFiles = this.uploadedFiles.filter(f => f.id !== fileId);
      });

      // Event untuk klik gambar agar membesar
        $(document).on("click", ".preview-clickable", function () {
          const imgSrc = $(this).data("preview");
           // alert(imgSrc); return;
          $("#previewImage").attr("src", imgSrc);
          $("#previewModal").modal("show");
        });
  
    }
  //and 

    renderModalViewer(){
      return `<div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-transparent border-0">
              <div class="modal-body p-0 text-center">
                <img id="previewImage" src="" class="img-fluid rounded shadow" alt="preview besar">
              </div>
            </div>
          </div>
        </div>
        `;
    }




  //ini untuk upload files
  renderData_uploadfiles() {
    const tampilAttach = $("#tampil_attach");

    $("#attach").on("change", (event) => {
      //console.log("jalan"); return;
      let files = Array.from(event.target.files);

      files.forEach((file, index) => {
        const maxSize = 2 * 1024 * 1024;
        if (file.size > maxSize) {
          alert("gambar " + file.name + " terlalu besar (max 2 MB)");
          return;
        }

    
        let fileId = `file-${this.uploadedFiles.length + 2}`;
        this.uploadedFiles.push({ id: fileId, file });

        let fileURL = URL.createObjectURL(file);

        let filePreview = `
          <div class="col-md-3 position-relative text-start p-2 d-inline-flex align-items-center me-3" id="${fileId}">
            <a href="${fileURL}" target="_blank" class="text-decoration-none">${file.name}</a>
            <button class="btn btn-sm text-danger ms-2 remove-file" data-id="${fileId}" style="border: none; background: transparent;">
              <i class="fa-solid fa-xmark"></i>
            </button>
          </div>
        `;

        tampilAttach.append(filePreview);
      });

      // Reset input file agar bisa pilih file sama lagi
      $("#attach").val("");
    });

    // Event hapus file
    $(document).on("click", ".remove-file", (event) => {
      event.preventDefault();
      $("#uploadfile").fadeIn();

      let fileId = $(event.currentTarget).data("id");
      $("#" + fileId).remove();
      this.uploadedFiles = this.uploadedFiles.filter(f => f.id !== fileId);
    });
  }
 // and


}

export default AttachAdd;
