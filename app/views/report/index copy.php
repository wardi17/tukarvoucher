<?php

$userid = $data["userid"];

?>
<style>
  #thead {
    background-color: #E7CEA6 !important;
  }

  .table-hover tbody tr:hover td,
  .table-hover tbody tr:hover th {
    background-color: #F3FEB8;
  }

  .dataTables_filter {
    padding-bottom: 20px !important;
  }

  form {
    width: 100%;
    height: 2% !important;
    margin: 0 auto;
  }

  @media (max-width: 768px) {
    #filterdata .form-group {
      margin-bottom: 1rem;
    }

    #filterdata .form-group label,
    #filterdata .form-group input {
      width: 100% !important;
    }

    #filterdata button {
      width: 100%;
    }
  }
  .error {
        color: red;
        font-size: 0.875rem;
      }
  @media (max-width: 576px) {
    .form-group label {
        font-size: 14px; /* Mengatur ukuran font label pada perangkat kecil */
    }
    .btn {
        width: 100%; /* Membuat tombol memenuhi lebar form pada perangkat kecil */
    }
}
</style>
<div id="main">
            <header class="mb-3">
                <a href="#" class="burger-btn d-block d-xl-none">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>

            <div class="container-fluid">
    <div class="card">
      <div class="card-body">
        <div class="page-heading mb-3">
          <div class="page-title">
            <h6 class="text-start">Report In Out</h6>
          </div>
        </div>

        <div id="filterdata" class="row align-items-end">
          <input type="hidden" id="username" value="<?=$userid?>" />
        </div>
        <div class="row align-items-end">
              <!-- From -->
              <div class="col-12 col-md-4 col-lg-2 mb-2">
                  <div class="form-group">
                      <label for="tgl_from" class="form-label">From</label>
                      <input type="date" class="form-control" id="tgl_from" name="tgl_from">
                  </div>
              </div>

              <!-- To -->
              <div class="col-12 col-md-4 col-lg-2 mb-2">
                  <div class="form-group">
                      <label for="tgl_to" class="form-label">To</label>
                      <input type="date" class="form-control" id="tgl_to" name="tgl_to">
                  </div>
              </div>
              <div class="col-12 col-md-4 col-lg-2 mb-2">
                  <div class="form-group">
                     <select name="inventaris_type" id="inventaris_type"  class="form-select">
                      <option value="" disabled selected>SelectType</option>
                      <option value="+">IN</option>
                      <option value="-">OUT</option>
                    </select>
                      <span id="inventaris_typeError" class="error"></span>
                </div>
              </div>
              <!-- Submit Button -->
              <div class="col-12 col-md-4 col-lg-2 mb-2">
                  <div class="form-group d-flex justify-content-start">
                      <button type="submit" class="btn btn-primary" id="Createdata">Submit</button>
                  </div>
              </div>
          </div>



        <div id="tabellist"></div>
      </div>
    </div>
  </div>
</div>

  <script>
     
    $(document).ready(function(){
      gettanggal();


        $("#Createdata").on("click",function(event){
            event.preventDefault();
            let tgl_to = $("#tgl_to").val();
            let tgl_from = $("#tgl_from").val();
          
            let  userid = $("#username").val();
            const inventaris_type = $("#inventaris_type").val();

            if(!inventaris_type){
              $("#inventaris_typeError").text("Type harus di pilih dulu ");
              return;
            }else{
              $("#inventaris_typeError").text("");
            }
      
             let datas ={
                "tgl_from":tgl_from,
                "tgl_to"  :tgl_to,
                "userid"  :userid,
                inventaris_type:inventaris_type 
             }

             
          
            getData(datas);
        })
      
        $("#inventaris_type").on("change",function(e){
          e.preventDefault();
          $("#inventaris_typeError").text("");
        })
    });// batas document ready

    function  gettanggal(){
	  let currentDate = new Date();
    // Mengatur tanggal pada objek Date ke 1 untuk mendapatkan awal bulan
    currentDate.setDate(1);
    // Membuat format tanggal YYYY-MM-DD
    let tgl_from = currentDate.toISOString().slice(0,10);
    // Menampilkan hasil
    let id_from ="tgl_from";


    let d = new Date();
      let month = d.getMonth()+1;
      let day = d.getDate();
      let  tgl_to =  d.getFullYear() +'-'+
					(month<10 ? '0' : '') + month + '-' +
				 (day<10 ? '0' : '') + day;

      let id_tgl_to ="tgl_to";
      SetTanggal(id_from,tgl_from)
      SetTanggal(id_tgl_to,tgl_to)


}
SetTanggal=(id,tanggal)=>{
  
    let setid ="#"+id;
    flatpickr(setid, {
                dateFormat: "d/m/Y", // Format yang diinginkan
                allowInput: true ,// Memungkinkan input manual
                defaultDate: new Date(tanggal)
            });
      
  }

    function  getData(datas){
      const   url="<?=base_url?>/router/seturl";
      //   console.log(url); return;
      $.ajax({
          url:url,
                method:"POST",
                dataType: "json",
                headers:{
                  'url':'trans/listlaporan'
                },
                data:JSON.stringify(datas),
                  success:function(result){
                    const data = result.data;
                    //console.log(result); return;
                    Set_Tabel(data);
                    
                  }
      })
    }

   
    
        function Set_Tabel(result) {
          // Generate main detail table
          const tableDetailHTML = generateDetailTable(result);
          const finalHTML = `
              ${tableDetailHTML}
          `;

          // Tampilkan ke halaman
          $("#tabellist").empty().html(finalHTML);
          Tampildatatabel();
      }

    // Fungsi untuk membuat tabel detail transaksi
    function generateDetailTable(data) {
        let rowsHTML = "";
        let no = 1;

        $.each(data, function(_, item) {
            const {
                InventarisID, NamaUser,Stok,Qty,
                TanggalMasuk,TransaksiIDDetail, NamaBarang, NamaKategori
            } = item;

            rowsHTML += `
                <tr>
                    <td>${no++}</td>
                    <td>${setforamtdate(TanggalMasuk)}</td>
                    <td>${InventarisID}</td>
                    <td>${NamaKategori}</td>
                    <td>${NamaBarang}</td>
                    <td>${parseFloat(Qty)}</td>
                    <td>${NamaUser}</td>
                </tr>
            `;
        });

        return `
            <table id="tabel1" class="table table-striped table-hover" style="width:100%">
                <thead class="thead">
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Inventaris ID</th>
                        <th>Kategori</th>
                        <th>Nama Barang</th>
                        <th>Qty</th>
                        <th>Nama User</th>
                    </tr>
                </thead>
                <tbody>
                    ${rowsHTML}
                </tbody>
            </table>
        `;
    }

    
  
    setforamtdate=(tanggal)=>{
         if (!tanggal) return '';
        // Format tanggal + jam: DD-MM-YY HH:mm:ss
      // return moment(tanggal).format("DD-MM-YY HH:mm:ss");
           let formatTanggal = moment(tanggal).format("DD-MM-YYYY");
                let split_tgl = formatTanggal.split("-");
                let t = split_tgl[0];
                let m = split_tgl[1];
                let y = split_tgl[2];
                let sub_y = y.substr(2,2);
                let new_tgl = t+'-'+m+'-'+sub_y;
                return new_tgl;
    }
  function  Tampildatatabel(){

    const tabel1 = "#tabel1";
    $(tabel1).DataTable({
        order: [[0, 'asc']],
          responsive: true,
          "ordering": true,
          "destroy":true,
          pageLength: 5,
          lengthMenu: [[5, 10, 20, -1], [5, 10, 20, 'All']],
          fixedColumns:   {
              // left: 1,
                right: 1
            },
            
        })
   }


  </script>