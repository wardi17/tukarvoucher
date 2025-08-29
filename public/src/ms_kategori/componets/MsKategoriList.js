import { baseUrl } from '../../config.js';
import EditMsKategori from './FormEditKategori.js';
import TambahStokKategori from './TambahStokKategori.js';

class MsKategoriList {
  constructor(containerSelector) {
    this.container = document.querySelector(containerSelector);
    this.loadData();
    this.appendCustomStyles();
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
  async loadData() {
    try {
      const data = await this.fetchData();
      let responsdata =data.data;
      this.renderTable(responsdata);
    } catch (error) {
      console.error('Gagal memuat data:', error);
      this.container.innerHTML = '<p>Gagal memuat data.</p>';
    }
  }

  async fetchData() {
    return new Promise((resolve, reject) => {
      $.ajax({
        url: `${baseUrl}/router/seturl`,
        method: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded; charset=UTF-8",
        headers: { 'url': 'msktg/listdata' },
        beforeSend: () => this.showLoading(), // Ensure this.showLoading is a method
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

renderTable(data) {
  const table = document.createElement('table');
  table.className = 'table table-striped';
  table.id = 'table1';

  const thead = `
    <thead id="thead" style="width:50;">
      <tr>
        <th>No</th>
        <th>ID</th>
        <th>Kriteria</th>
        <th>Action</th>
      </tr>
    </thead>
  `;

  const tbodyRows = data.map((item,index) => `
    <tr>
      <td>${index+1}</td>
      <td>${item.KategoriID}</td>
      <td>${item.NamaKategori}</td>
      <td>
        <button type="button" class="btn btn-info edit-data btn-sm" 
          data-id="${item.KategoriID}"
          data-namakategori="${item.NamaKategori}">
          Edit
        </button>      
      </td>
    </tr>
  `).join('');

  table.innerHTML = thead + `<tbody>${tbodyRows}</tbody>`;
  this.container.innerHTML = '';
  this.container.appendChild(table);

  // Aktifkan DataTables
  this.Tampildatatabel();

  // Gunakan event delegation dengan jQuery (supaya tetap hidup setelah DataTables re-render)
  $(document).off('click', '.edit-data').on('click', '.edit-data', (e) => {
    const el = e.currentTarget;
    const datas = {
      id: el.getAttribute('data-id'),
      namakategori: el.getAttribute('data-namakategori'),
    
    };
    new EditMsKategori(datas);
  });



}



  showLoading() {
    // Implement your loading logic here, e.g., show a spinner or loading message
    this.container.innerHTML = '<p>Loading...</p>';
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

export default MsKategoriList;



