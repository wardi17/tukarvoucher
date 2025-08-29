<?php

$userid = $data["userid"];

?>
  <style>
 .monitoring-table {
    width: 100%;
    border-collapse: collapse;
    text-align: center;
    font-size: 12px;
  }

  .monitoring-table th,
  .monitoring-table td {
    border: 1px solid #000;
    padding: 2px 2px 2px 2px;
  }

  .monitoring-table thead th {
    background-color: #FFDF88;
    font-weight: bold;
  }

  .monitoring-table .total-row td {
    background-color: #FFDF88;
    font-weight: bold;
  }

  .monitoring-table td:nth-child(4),
  .monitoring-table td:nth-child(7) {
    text-align: right;
  }

  .section-title {
    font-weight: bold;
    margin-bottom: 10px;
    font-size: 16px;
  }

  @media (max-width: 768px) {
  .monitoring-table {
    font-size: 13px;
  }

  .monitoring-table th,
  .monitoring-table td {
    padding: 4px;
  }
}



  </style>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div id="main">
  <header class="mb-3">
    <a href="#" class="burger-btn d-block d-xl-none">
      <i class="bi bi-justify fs-3"></i>
    </a>
  </header>

  <div class="container-fluid">
    <div class="card">
      <div class="card-body">
        <!-- Judul Halaman -->
        <div class="mb-4">
          <h6 class="text-start">Laporan Detail Hasil</h6>
        </div>

        <!-- Filter -->
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

              <!-- Submit Button -->
              <div class="col-12 col-md-4 col-lg-2 mb-2">
                  <div class="form-group d-flex justify-content-start" id="Pilter">
                      <button type="submit" class="btn btn-primary me-3" id="Createdata">Submit</button>
                  </div>
              </div>
          </div>

        <!-- Monitoring Section id="Pilter" -->
        <div id="printArea" class="my-1">
        
          <!-- Tabel dan Revenue -->
          <div class="row">
            <div class="col-lg-6 col-12 mb-4 table-responsive">
              <div id="tabellist"></div>
            </div>
            <div class="col-lg-6 col-12 mb-4 chart">
              <canvas id="RevenueChart" height="150"></canvas>
            </div>
          </div>

          <!-- View dan Order Chart -->
          <div class="row chart2">
            <div class="col-lg-6 col-12 mb-4 ">
              <canvas id="viewChart" height="150"></canvas>
            </div>
            <div class="col-lg-6 col-12 mb-4">
              <canvas id="orderChart" height="150"></canvas>
            </div>
          </div>
        </div>

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
             let datas ={
                "tgl_from":tgl_from,
                "tgl_to"  :tgl_to,
                "userid"  :userid 
             }

             
          
            getData(datas);
        })
      
    });// batas document ready

    function  gettanggal(){
	  let currentDate = new Date();
    // Mengatur tanggal pada objek Date ke 1 untuk mendapatkan awal bulan
    currentDate.setDate(1);
    // Membuat format tanggal YYYY-MM-DD
    let tgl_from = currentDate.toISOString().slice(0,10);
    let id_from ="tgl_from";
    // Menampilkan hasil

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
      $('#PrintData').remove();
      $.ajax({
          url:"<?=base_url?>/router/seturl",
                method:"POST",
                dataType: "json",
                headers:{
                  'url':'lap/listlaporanhasil'
                },
                data:datas,
                  success:function(result){
                    Set_Tabel(result);
                     Set_tomboPrint();
                    
                  }
      })
    }


    Set_tomboPrint=()=>{
      $('#Pilter').append('<button class="btn btn-info" onclick="Print();" id="PrintData">Print</button>');
    }  
   


  function Print() {
        const printArea = document.getElementById("printArea").cloneNode(true);
        //console.log(document.getElementById("printArea").innerHTML);
        // Ganti canvas dengan image
        const originalCanvases = document.querySelectorAll("canvas");
        const clonedCanvases = printArea.querySelectorAll("canvas");

        const promises = [];

        originalCanvases.forEach((canvas, index) => {
          const dataUrl = canvas.toDataURL("image/png");
          const img = new Image();
          img.src = dataUrl;
          img.style.maxWidth = "100%";
          img.style.marginBottom = "15px";

          const promise = new Promise((resolve) => {
            img.onload = () => {
              if (clonedCanvases[index]) {
                clonedCanvases[index].replaceWith(img);
              }
              resolve();
            };
          });

    promises.push(promise);
    });

  // Setelah semua gambar selesai dibuat
  Promise.all(promises).then(() => {
    const printWindow = window.open('', '', 'height=1000,width=1200');
    printWindow.document.write(`
      <html>
        <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
          <title>Print Report</title>
          <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
          <style>
           .monitoring-table {
                width: 100%;
                border-collapse: collapse;
                text-align: center;
                font-size: 10px;
              }

           .chart{
              margin-top:5mm;
            }

          .chart2{
              padding: 1mm;
            }
          

              .monitoring-table th,
              .monitoring-table td {
                border: 1px solid #000;
                padding: 0px 5px;

              }

              .monitoring-table thead th {
                background-color: #FFDF88;
                font-weight: bold;
              }

              .monitoring-table .total-row td {
                background-color: #FFDF88;
                font-weight: bold;
              }

              .monitoring-table td:nth-child(4),
              .monitoring-table td:nth-child(7) {
                text-align: right;
              }

              .section-title {
                font-weight: bold;
                margin-bottom: 10px;
                font-size: 16px;
              }

              @media (max-width: 768px) {
              .monitoring-table {
                font-size: 12px;
              }

              .monitoring-table th,
              .monitoring-table td {
                padding: 4px;
              }
            }

            @media print {
            body {
              margin: 10mm; /* Tambahkan margin agar tidak nempel ke tepi kertas */
              margin-top:5mm;
            }

            #printArea {
              padding: 10mm;
            }

        

         
          }
          @media only screen and (max-width: 768px) {
                body {
                  font-size: 12px;
                }

                .monitoring-table {
                  font-size: 10px;
                  overflow-x: auto;
                }

                .chart,
                .chart2 {
                  width: 100%;
                }

                img {
                  max-width: 100%;
                  height: auto;
                }
              }

              @media print {
                body {
                  margin: 10mm;
                  font-size: 11px;
                }

                .monitoring-table {
                  font-size: 10px;
                  page-break-inside: auto;
                }

                .monitoring-table thead {
                  display: table-header-group;
                }

                .monitoring-table tr {
                  page-break-inside: avoid;
                  page-break-after: auto;
                }

                button, .no-print {
                  display: none !important;
                }
              }

      
          </style>
        </head>
        <body>${printArea.innerHTML}</body>
      </html>
    `);

    printWindow.document.close();
    printWindow.focus();

    setTimeout(() => {
      printWindow.print();
      printWindow.close();
    }, 1000);
  });
}

    
  function Set_Tabel(result){
    let datatabel = ``;

datatabel +=`
<div class="section-title">Monitoring Live Streaming</div>
<table id="tabel1" class="monitoring-table">
  <thead>
    <tr class="bg-title">
      <th rowspan="2">Tgl</th>
      <th colspan="3">Shopee</th>
      <th colspan="3">Tiktok</th>
      <th colspan="3">Bindexmall</th>
    </tr>
    <tr>
      <th>View</th>
      <th>Order</th>
      <th>Revenue (Rp)</th>
      <th>View</th>
      <th>Order</th>
      <th>Revenue (Rp)</th>
      <th>View</th>
      <th>Order</th>
      <th>Revenue (Rp)</th>
    </tr>
  </thead>
  <tbody>
`;

// Group data berdasarkan tanggal
const grouped = {};
let totalShopee = { views: 0, orders: 0, revenue: 0 };
let totalTiktok = { views: 0, orders: 0, revenue: 0 };
let totalBindexmall= { views: 0, orders: 0, revenue: 0 };
result.forEach(item => {
  const tgl = item.tanggal;
  if (!grouped[tgl]) grouped[tgl] = {};
  grouped[tgl][item.NamePlatfrom.toLowerCase()] = item;
});

// Generate row ke datatabel
;
for (let tgl in grouped) {
  const shopee = grouped[tgl].shopee || { views: 0, orders: 0, revenue: 0 };
  const tiktok = grouped[tgl].tiktok || { views: 0, orders: 0, revenue: 0 };
  const bindexmall = grouped[tgl].bindexmall || { views: 0, orders: 0, revenue: 0 };
    // Hitung total
    totalShopee.views += parseFloat(shopee.views);
    totalShopee.orders += parseFloat(shopee.orders);
    totalShopee.revenue += parseFloat(shopee.revenue);

    totalTiktok.views += parseFloat(tiktok.views);
    totalTiktok.orders += parseFloat(tiktok.orders);
    totalTiktok.revenue += parseFloat(tiktok.revenue);

    totalBindexmall.views += parseFloat(bindexmall.views);
    totalBindexmall.orders += parseFloat(bindexmall.orders);
    totalBindexmall.revenue += parseFloat(bindexmall.revenue);

    
  datatabel += `
    <tr>
      <td class="text-center" style="width:12%">${formatTgl(tgl)}</td>
      <td  style="width:9%">${parseFloat(shopee.views)}</td>
      <td  style="width:9%">${parseFloat(shopee.orders)}</td>
      <td class="text-end" style="width:12%">${formatRupiah(shopee.revenue)}</td>
      <td  style="width:9%">${parseFloat(tiktok.views)}</td>
      <td  style="width:9%">${parseFloat(tiktok.orders)}</td>
      <td   class="text-end" style="width:11%">${formatRupiah(tiktok.revenue)}</td>
       <td  style="width:9%">${parseFloat(bindexmall.views)}</td>
      <td  style="width:9%">${parseFloat(bindexmall.orders)}</td>
      <td  class="text-end" style="width:11%">${formatRupiah(bindexmall.revenue)}</td>
    </tr>
  `;
}
// Tambah footer total
datatabel += `
  <tr style="font-weight:bold; background-color:#f2f2f2;">
    <td>Total</td>
    <td>${totalShopee.views}</td>
    <td>${totalShopee.orders}</td>
    <td class="text-end">${formatRupiah(totalShopee.revenue)}</td>
    <td>${totalTiktok.views}</td>
    <td>${totalTiktok.orders}</td>
    <td class="text-end">${formatRupiah(totalTiktok.revenue)}</td>
    <td>${totalBindexmall.views}</td>
    <td>${totalBindexmall.orders}</td>
    <td class="text-end">${formatRupiah(totalBindexmall.revenue)}</td>
  </tr>
`;

datatabel += `</tbody></table>`;

// Tampilkan di HTML
$("#tabellist").empty().html(datatabel);   
//Tampildatatabel();
setGrafik(result);
}

function formatTgl(tanggalStr) {
  const bulanNama = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"];
  
  const [hari, bulan, tahun] = tanggalStr.split("-"); // misal: "16-04-2025"
  const bulanIndex = parseInt(bulan, 10) - 1; // index bulan mulai dari 0

  const tahunPendek = tahun.slice(-2); // ambil dua digit terakhir

  return `${hari} ${bulanNama[bulanIndex]} ${tahunPendek}`;
}

function formatRupiah(angka) {
  return parseFloat(angka).toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}


let chartView = null;
let chartOrder = null;
let chartRevenue = null;

setGrafik = (result) => {
  const labels = [];
  const shopeeRevenue = [];
  const tiktokRevenue = [];
  const bindexmallRevenue = [];
  const shopeeViews = [];
  const tiktokViews = [];
  const bindexmallViews = [];
  const shopeeOrders = [];
  const tiktokOrders = [];
  const bindexmallOrders = [];

  const grouped = {};

  result.forEach(item => {
    const tgl = item.tanggal;
    if (!grouped[tgl]) grouped[tgl] = {};
    grouped[tgl][item.NamePlatfrom.toLowerCase()] = item;
  });

  for (let tgl in grouped) {
    //labels.push(tgl);
    labels.push(formatTgl(tgl));
    const shopee = grouped[tgl].shopee || { views: 0, orders: 0, revenue: 0 };
    const tiktok = grouped[tgl].tiktok || { views: 0, orders: 0, revenue: 0 };
    const bindexmall = grouped[tgl].bindexmall || { views: 0, orders: 0, revenue: 0 };

    shopeeViews.push(parseFloat(shopee.views));
    tiktokViews.push(parseFloat(tiktok.views));
    bindexmallViews.push(parseFloat(bindexmall.views));
    shopeeOrders.push(parseFloat(shopee.orders));
    tiktokOrders.push(parseFloat(tiktok.orders));
    bindexmallOrders.push(parseFloat(bindexmall.orders));
    shopeeRevenue.push(parseFloat(shopee.revenue));
    tiktokRevenue.push(parseFloat(tiktok.revenue));
    bindexmallRevenue.push(parseFloat(bindexmall.revenue));
  }

  // Hapus chart lama
  if (chartView) chartView.destroy();
  if (chartOrder) chartOrder.destroy();
  if (chartRevenue) chartRevenue.destroy();

  // Konfigurasi gaya 3D-style
  const barStyle = {
    barThickness: 15,
    //borderRadius: 8,
    borderSkipped: false,
    hoverBackgroundColor: '#444',
    borderColor: '#999',
    borderWidth: 0.1,
  };

  // Chart View
  chartView = new Chart(document.getElementById('viewChart').getContext('2d'), {
    type: 'bar',
    data: {
      labels: labels,
      datasets: [
        {
          label: 'View Shopee',
          data: shopeeViews,
          backgroundColor: 'rgba(54, 162, 235, 0.8)',
          ...barStyle
        },
        {
          label: 'View Tiktok',
          data: tiktokViews,
          backgroundColor: 'rgba(255, 99, 132, 0.8)',
          ...barStyle
        },
        {
          label: 'View Bindexmall',
          data: bindexmallViews,
          backgroundColor: 'rgba(212, 223, 67, 0.8)',
          ...barStyle
        }
      ]
    },
    options: {
      responsive: true,
      plugins: {
        title: {
          display: true,
          text: 'View',
        },
        legend: {
        position: 'bottom' // 🔽 Legend di bawah grafik
        }
      },
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });

  // Chart Order
  chartOrder = new Chart(document.getElementById('orderChart').getContext('2d'), {
    type: 'bar',
    data: {
      labels: labels,
      datasets: [
        {
          label: 'Order Shopee',
          data: shopeeOrders,
          backgroundColor: 'rgba(54, 162, 235, 0.8)',
          ...barStyle
        },
        {
          label: 'Order Tiktok',
          data: tiktokOrders,
          backgroundColor: 'rgba(255, 99, 132, 0.8)',
          ...barStyle
        },
        {
          label: 'Order Bindexmall',
          data: bindexmallOrders,
          backgroundColor: 'rgba(212, 223, 67, 0.8)',
          ...barStyle
        }
      ]
    },
    options: {
      responsive: true,
      plugins: {
        title: {
          display: true,
          text: 'Order',
        },
        legend: {
        position: 'bottom' // 🔽 Legend di bawah grafik
        }
        
      },
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });

  // Chart Revenue
  chartRevenue = new Chart(document.getElementById('RevenueChart').getContext('2d'), {
    type: 'bar',
    data: {
      labels: labels,
      datasets: [
        {
          label: 'Revenue Shopee',
          data: shopeeRevenue,
          backgroundColor: 'rgba(54, 162, 235, 0.8)',
          ...barStyle
        },
        {
          label: 'Revenue Tiktok',
          data: tiktokRevenue,
          backgroundColor: 'rgba(255, 99, 132, 0.8)',
          ...barStyle
        },
        {
          label: 'Revenue Bindexmall',
          data: bindexmallRevenue,
          backgroundColor: 'rgba(212, 223, 67, 0.8)',
          ...barStyle
        }
      ]
    },
    options: {
      responsive: true,
      plugins: {
        title: {
          display: true,
          text: 'Revenue',
        },
        legend: {
        position: 'bottom' // 🔽 Legend di bawah grafik
        }
      },
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });
};






  function  Tampildatatabel(){

    const tabel1 = "#tabel1";
    $(tabel1).DataTable({
        order: [[0, 'asc']],
          responsive: true,
          "ordering": true,
          "destroy":true,
          pageLength:10,
          lengthMenu: [[10, 20, -1], [10, 20, 'All']],
          fixedColumns:   {
              // left: 1,
                right: 1
            },
            
        })
   }


 

  </script>