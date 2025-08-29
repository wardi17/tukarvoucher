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
            <h6 class="text-start">Report Tools</h6>
          </div>
        </div>

        <div id="filterdata" class="row align-items-end">
          <input type="hidden" id="username" value="<?=$userid?>" />
        </div>
        <div class="row align-items-end">
          
              <!-- To -->
              <div class="col-12 col-md-4 col-lg-2 mb-2">
                  <div class="form-group">
                      <label for="tanggal" class="form-label">Tanggal</label>
                      <input type="date" class="form-control" id="tanggal" name="tanggal">
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
            let tanggal = $("#tanggal").val();
            let  userid = $("#username").val();
      
             let datas ={
                "tanggal"  :tanggal,
                "userid"  :userid,
             }

             
          
            getData(datas);
        })
      
        $("#inventaris_type").on("change",function(e){
          e.preventDefault();
          $("#inventaris_typeError").text("");
        })
    });// batas document ready

    function  gettanggal(){

    let d = new Date();
      let month = d.getMonth()+1;
      let day = d.getDate();
      let  tgl_to =  d.getFullYear() +'-'+
					(month<10 ? '0' : '') + month + '-' +
				 (day<10 ? '0' : '') + day;

      let id_tanggal ="tanggal";
      SetTanggal(id_tanggal,tgl_to)


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
                  'url':'trans/laporan_tools'
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
                InventarisID, KategoriID,NamaBarang,NamaKategori,PersentaseTerhadapMax,PersentaseTerhadapMin,
                StokMaksimum,StokMinimum,Stok,SisaStok,StatusStok,TotalQtyKeluar,TotalQtyMasuk
            } = item;

            let colorstok ="";
            if( StatusStok !=="Aman") {
                colorstok = "table-danger";
            }else{
                colorstok = "table-light";
            }
            rowsHTML += `
                <tr class="${colorstok}">
                    <td>${no++}</td>
                    <td>${InventarisID}</td>
                    <td>${NamaKategori}</td>
                      <td>${NamaBarang}</td>
                    <td>${parseFloat(StokMaksimum)}</td>
                    <td>${parseFloat(StokMinimum)}</td>
                    <td>${parseFloat(TotalQtyMasuk)}</td>
                     <td>${parseFloat(TotalQtyKeluar)}</td>
                       <td>${parseFloat(SisaStok)}</td>
                    <td>${parseFloat(PersentaseTerhadapMax)} %</td>
                    <td>${parseFloat(PersentaseTerhadapMin)} %</td>
                  
                </tr>
            `;
        });

        return `
            <table id="tabel1" class="table table-striped table-hover" style="width:100%">
                <thead class="thead">
                    <tr>
                        <th>No</th>
                        <th>Inventaris ID</th>
                        <th>Kategori</th>
                        <th>Nama Barang</th>
                        <th>Max</th>
                        <th>Min</th>
                        <th>Stok In</th>
                        <th>Stok Out</th>
                         <th>Sisa Stok</th>
                        <th>Presentasi Max</th>
                        <th>Presentasi Min</th>
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