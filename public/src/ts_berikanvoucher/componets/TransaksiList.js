// TransaksiList.js
import ButtonTambah from './ButtonTambah.js'; // asumsikan kamu punya ini
import TransaksiForm from './TransaksiForm.js';
import TransaksiFormPosting  from './TransaksiFormPosting.js';
import PrintpenerimaVoucer from './PrintpenerimaVoucer.js';
import {baseUrl} from '../../config.js';
import { goBack } from '../main.js';
class TransaksiList {
  constructor() {
    this.root = document.getElementById('root');
    this.appendCustomStyles();
    this.render();
  }

   appendCustomStyles() {
    const style = document.createElement('style');
    style.textContent = `
     #thead{
        background-color:#E7CEA6 !important;
        /* font-size: 8px;
        font-weight: 100 !important; */
        /*color :#000000 !important;*/
      }
      .table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
      background-color: #F3FEB8;
    }

    /* .table-striped{
      background-color:#E9F391FF !important;
    } */
    .dataTables_filter{
     padding-bottom: 20px !important;
  }
    `;
    document.head.appendChild(style);
  }


  async render() {
    this.root.innerHTML = ''; // bersihkan konten

    // Buat container utama
    const container = document.createElement('div');
    container.style.padding = '20px';

    // Baris header: title + tombol tambah di kanan
    const headerBar = document.createElement('div');
    headerBar.style.display = 'flex';
    headerBar.style.justifyContent = 'space-between';
    headerBar.style.alignItems = 'center';
    headerBar.style.marginBottom = '20px';

    const title = document.createElement('h6');
    title.textContent = 'Daftar Transaksi';

    const buttonTambah = ButtonTambah({
      text: '+ Tambah',
      onClick: async () => {
        this.root.innerHTML = '';
        const form = new TransaksiForm();
        this.root.appendChild(await form.render());
      }
    });

    headerBar.appendChild(title);
  
    headerBar.appendChild(buttonTambah);

    container.appendChild(headerBar);

    // List transaksi (dummy contoh)
    const list = document.createElement('div');
    const datalist = await this.getdatalist();
     list.innerHTML = this.settable(datalist);
      // Aktifkan DataTables
    //list.innerHTML = '<p>Daftar isi transaksi akan muncul di sini...</p>';
    container.appendChild(list);

    this.root.appendChild(container);
    this.bindEvent();
    this.Tampildatatabel();
    //this.TampilCetak();
  }

  settable=(data)=>{

   let  html =`
      <table id="table1" class="table table-striped table-hover" id="table_Detailforwader">
                            <thead id="thead">
                                <tr>
                                    <th class="col-md-1 text-center">No</th>
                                    <th class="col-md-2 text-start">Tanggal</th>
                                    <th class="col-md-2 text-start">Cabang</th>
                                    <th class="col-md-2 text-start">Customer</th>
                                    <th class="col-md-2 text-start">No SO</th>
                                    <th class="col-md-4 text-end" >Jumlah</th>
                                    <th class="col-md-2 text-center">Ket</th>
                                    <th class="col-md-2 text-center">User</th>
                                    <th class="col-md-2 text-center">Posting</th>
                                    <th class="col-md-2 text-center">Cetak</th>
                                   
                                </tr>
                            </thead>
                            <tbody>
                              ${this.genereteTableRows(data)}
                            </tbody>
                            </table>
     `;

     return html;
     
  }

  genereteTableRows(data){

    if (!Array.isArray(data)) return `<tr><td colspan="11">Tidak ada data</td></tr>`;
    
    let hasil =``;
    $.each(data,function(index,item){
   
    hasil +=  `
        <tr>
            <td class="col-md-1 text-center">${index + 1}</td>
            <td class="col-md-2 text-start">${item.Date_kasih_voucher}</td>
            <td class="col-md-2 text-start">${item.cabang}</td>
            <td class="col-md-2 text-start">${item.CustName}</td>
            <td class="col-md-2 text-start">${item.SOTransacID}</td>
           <td class="col-md-2 text-end">${parseFloat(item.Jumlah_berikan_voucher)}</td>
           <td class="col-md-2 text-start">${item.Keterangan}</td>
           <td class="col-md-2 text-start">${item.User_kasih_voucher}</td>`;

          if(item.Status_posting !== 'Y'){
            
            hasil +=`<td>
                     <button class="btn btn-sm btn-info btn-posting"  
                     data-kode="${item.Kode_Berikan}" 
                     data-cabang="${item.cabang}"
                     data-customerid="${item.CustomerID}"
                     data-custname="${item.CustName}"
                     data-sotransacid="${item.SOTransacID}"
                     data-jumlah="${item.Jumlah_berikan_voucher}"
                    data-keterangan="${item.Keterangan}"
                     >Posting</button>
              </td>`;
          }else{
            hasil +=`<td></td>`; 
          }
        if(item.Status_posting === 'Y'){
            hasil +=  `
           <td>
                     <button class="btn btn-sm btn-success btn-print"  
                     data-kode2="${item.Kode_Berikan}" 
                     data-dateberikan="${item.date_berikan}"
                     data-cabang="${item.cabang}"
                     data-customerid="${item.CustomerID}"
                     data-custname="${item.CustName}"
                     data-sotransacid="${item.SOTransacID}"
                     data-jumlah="${item.Jumlah_berikan_voucher}"
                    data-keterangan="${item.Keterangan}"
                    data-username="${item.username}"
                     ><i class="fa-solid fa-print"></i></button>
              </td>`;
        } else{
          hasil +=`<td></td>`;
        }
    hasil +=  `
        </tr>`;
    })

    return hasil;
  }

  // === Setelah render tabel ===
bindEvent() {
  // simpan konteks
  const root = this.root;
  //button posting
    $(document).off('click', '.btn-posting').on('click', '.btn-posting', async  function() {
    const kode = $(this).data('kode');
    const cabang = $(this).data('cabang');
    const customerid = $(this).data('customerid');
    const custname = $(this).data('custname');
    const sotransacid = $(this).data('sotransacid');
    const jumlah       = $(this).data('jumlah');
    const keterarngan  = $(this).data('keterangan');

    const editdata ={
      kode:kode,
      cabang:cabang,
      customerid:customerid,
      custname:custname,
      sotransacid:sotransacid,
      jumlah:jumlah,
      keterarngan:keterarngan
    }

    root.innerHTML = '';
    const form = new  TransaksiFormPosting(editdata);
    root.appendChild( await form.render());

  });

  //and posting
  //button print    
  $(document).off('click', '.btn-print').on('click', '.btn-print', async function() {
        const kode = $(this).data('kode2');
        const cabang = $(this).data('cabang');
        const customerid = $(this).data('customerid');
        const custname = $(this).data('custname');
        const sotransacid = $(this).data('sotransacid');
        const jumlah = $(this).data('jumlah');
        const keterangan = $(this).data('keterangan');
        const username = $(this).data('username');
        const date_berikan = $(this).data('dateberikan');
        
            const dataprint ={
              kode:kode,
              cabang:cabang,
              customerid:customerid,
              custname:custname,
              sotransacid:sotransacid,
              jumlah:jumlah,
              keterangan:keterangan,
              username:username,
              date_berikan:date_berikan
            }

            //console.log(dataprint); return;
            root.innerHTML = '';
          const form = new PrintpenerimaVoucer(dataprint);
          root.appendChild( await form.render());
  });
  //and print
}
  
    async getdatalist() { 
      return new Promise((resolve, reject) => {
        $.ajax({
          url: `${baseUrl}/router/seturl`,
          method: "GET",
          dataType: "json",
          contentType: "application/x-www-form-urlencoded; charset=UTF-8",
          headers: { 'url': 'voucherberi/listdata' },
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

         Tampildatatabel(){
          const id = "#table1";
          $(id).DataTable({
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






}

export default TransaksiList;
