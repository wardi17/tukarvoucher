// TransaksiList.js
import ButtonTambah from './ButtonTambah.js'; // asumsikan kamu punya ini
import TransaksiForm from './TransaksiForm.js';
import TransaksiFormPosting  from './TransaksiFormPosting.js';
import PrintpenerimaVoucer from './PrintpenerimaVoucer.js';
import {baseUrl} from '../../config.js';
import TransaksiFormView  from './TransaksiFormView.js';
import TransaksiFormEdit from './TransaksiFormEdit.js';

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
                                    <th class="text-center" style="width:5%">No</th>
                                    <th class="text-start" style="width:8%">Tgl</th>
                                    <th class="text-start" style="width:9%">Cabang</th>
                                    <th class="text-start" style="width:9%">Customer</th>
                                    <th class=" text-start" style="width:9%">No SO</th>
                                    <th class="text-end"    style="width:8%">Jumlah</th>
                                    <th class="text-center" style="width:19%">Ket</th>
                                    <th class="text-center" style="width:8%">User</th>
                                    <th class="text-center" style="width:8%">lihat</th>
                                    <th class="text-center" style="width:8%">Posting</th>
                                    <th class=" text-center" style="width:8%">Action</th>
                                   
                                </tr>
                            </thead>
                            <tbody>
                              ${this.generateTableRows(data)}
                            </tbody>
                            </table>
     `;

     return html;
     
  }

  generateTableRows(data) {
      if (!Array.isArray(data) || data.length === 0) {
        return `<tr><td colspan="12">Tidak ada data</td></tr>`;
      }

      const createButton = (text, cssClass, dataset = {}) => {
        const attrs = Object.entries(dataset)
          .map(([key, val]) => `data-${key}="${val}"`)
          .join(' ');
        return `<button class="btn btn-sm ${cssClass}" ${attrs}>${text}</button>`;
      };

      return data.map((item, index) => {
        const viewBtn = createButton('View', 'btn-secondary btn-view', {
          kode: item.Kode_Berikan,
          cabang: item.cabang,
          customerid: item.CustomerID,
          custname: item.CustName,
          sotransacid: item.SOTransacID,
          jumlah: item.Jumlah_berikan_voucher,
          keterangan: item.Keterangan
        });

        const postingBtn = (item.Status_posting !== 'Y')
          ? createButton('Posting', 'btn-info btn-posting', {
              kode: item.Kode_Berikan,
              cabang: item.cabang,
              customerid: item.CustomerID,
              custname: item.CustName,
              sotransacid: item.SOTransacID,
              jumlah: item.Jumlah_berikan_voucher,
              keterangan: item.Keterangan
            })
          : '';

        const printBtn = (item.Status_posting === 'Y')
          ? createButton('Print', 'btn-success btn-print', {
              kode2: item.Kode_Berikan,
              dateberikan: item.date_berikan,
              cabang: item.cabang,
              customerid: item.CustomerID,
              custname: item.CustName,
              sotransacid: item.SOTransacID,
              jumlah: item.Jumlah_berikan_voucher,
              keterangan: item.Keterangan,
              username: item.username
            })
          : '';

          const editBtn = (item.Status_posting === 'N')
          ? createButton('Edit', 'btn-warning btn-edit', {
              kode: item.Kode_Berikan,
              dateberikan: item.date_berikan,
              cabang: item.cabang,
              customerid: item.CustomerID,
              custname: item.CustName,
              sotransacid: item.SOTransacID,
              jumlah: item.Jumlah_berikan_voucher,
              keterangan: item.Keterangan,
              username: item.username
            })
          : '';


          const actionBtn = (()=>{
            if(item.Status_posting === 'N' && item.Status_print === 'N') return editBtn;
            if (item.Status_posting === 'Y' && item.Status_print === 'N') return printBtn;
            if(item.Status_posting === 'Y' && item.Status_print === 'Y') return '';
            return '';
          })();

         
        return `
          <tr>
            <td class="text-center" style="width:5%">${index + 1}</td>
            <td class="text-start" style="width:8%">${item.Date_kasih_voucher}</td>
            <td class="text-start" style="width:9%">${item.cabang}</td>
            <td class="text-start" style="width:9%">${item.CustName}</td>
            <td class="text-start" style="width:9%">${item.SOTransacID}</td>
            <td class="text-end" style="width:8%">${parseFloat(item.Jumlah_berikan_voucher)}</td>
            <td class="text-start" style="width:19%">${item.Keterangan}</td>
             <td class="text-start" style="width:8%">${item.User_kasih_voucher}</td>
            <td class="text-start" style="width:8%">${viewBtn}</td>
            <td style="width:8%">${postingBtn}</td>
            <td style="width:8%">${actionBtn}</td>
          </tr>
        `;
      }).join('');
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

    //button Edit
    $(document).off('click', '.btn-edit').on('click', '.btn-edit', async  function() {
      //     Swal.fire({
      //     icon: "info",
      //     title: "Info",
      //     text: "Tombol Edit Masih dalam Pengembangan lanjutan."
      //   });
   
      // return;
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

    //console.log(editdata); return;
    root.innerHTML = '';
    const form = new  TransaksiFormEdit(editdata);
    root.appendChild( await form.render());

  });

  //and Edit
  //button print    
   const self = this;
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

        
            
           root.innerHTML = '';
          const form = new PrintpenerimaVoucer(dataprint);
          root.appendChild( await form.render());
      const codeprint ={
              kode:kode
            }
          self.UpdataSatusPrint(codeprint);
  });
  //and print

  //button view
    $(document).off('click', '.btn-view').on('click', '.btn-view', async  function() {
    const kode = $(this).data('kode');
    const cabang = $(this).data('cabang');
    const customerid = $(this).data('customerid');
    const custname = $(this).data('custname');
    const sotransacid = $(this).data('sotransacid');
    const jumlah       = $(this).data('jumlah');
    const keterarngan  = $(this).data('keterangan');

    const viewdata ={
      kode:kode,
      cabang:cabang,
      customerid:customerid,
      custname:custname,
      sotransacid:sotransacid,
      jumlah:jumlah,
      keterarngan:keterarngan
    }

    root.innerHTML = '';
    const form = new  TransaksiFormView(viewdata);
    root.appendChild( await form.render());

  });
  //and view
}
  
   async UpdataSatusPrint(codeprint){
      return new Promise((resolve, reject) => {
        $.ajax({
          url: `${baseUrl}/router/seturl`,
          method: "POST",
          dataType: "json",
          data:JSON.stringify(codeprint),
          contentType: "application/x-www-form-urlencoded; charset=UTF-8",
          headers: { 'url': 'voucherberi/updatesatusprint' },
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
