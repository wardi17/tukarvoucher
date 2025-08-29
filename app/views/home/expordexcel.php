<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Export Table to Excel</title>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
</head>

<body>
  <h1>Table 1</h1>
  <table id="table1" border="1">
    <thead>
      <tr>
        <th>Tanggal Faktur</th>
        <th>Jenis Faktur</th>
        <th>Kode Transaksi</th>
        <th>Keterangan Tambahan</th>
        <th>Dokumen Pendukung</th>
        <th>Referensi</th>
        <th>Cap Fasilitas</th>
        <th>ID TKU Penjual</th>
        <th>NPWP/NIK Pembeli</th>
        <th>Negara Pembeli</th>
        <th>Nomor Dokumen Pembeli</th>
        <th>Nama Pembeli </th>
        <th> Alamat Pembeli</th>
        <th>Email Pembeli</th>
        <th> ID TKU Pembeli</th>
        
      </tr>
    </thead>
    <tbody id="datatable1">
 
    </tbody>
  </table>

  <h1>Table 2</h1>
  <table id="table2" border="1">
    <thead>
      <tr>
        <th>Baris</th>
        <th>Barang/Jasa</th>
        <th>Nama Barang/Jasa</th>
        <th>Nama Satuan Ukur</th>
        <th>Harga Satuan</th>
        <th>Jumlah Barang Jasa</th>
        <th>Total Diskon</th>
        <th>DPP</th>
        <th>DPP Nilai Lain</th>
        <th>Tarif PPN</th>
        <th>PPN</th>
        <th>Tarif PPnBM</th>
        <th>PPnBM</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Apple</td>
        <td>1.00</td>
      </tr>
      <tr>
        <td>Banana</td>
        <td>0.50</td>
      </tr>
    </tbody>
  </table><br>

  <button id="exportBtn">Export to Excel</button>

  <script>

   // import header from "./datahider.js";
    $(document).ready(function () {

      const datahider =
 [
    {
      "Baris": 1,
      "Tanggal_Faktur": "2024-11-11",
      "Jenis_Faktur": "Normal",
      "Kode_Transaksi": "01",
      "Keterangan_Tambahan": "Test 01",
      "Dokumen_Pendukung": "rrfcvh",
      "Referensi": "1091031210910416000000",
      "Cap_Fasilitas": "ffrrb",
      "ID_TKU_Penjual": "3172022407800012",
      "NPWP_NIK_Pembeli": "TIN",
      "Jenis_ID_Pembeli": "IDN",
      "Negara_Pembeli": "rrffyf",
      "Nomor_Dokumen_Pembeli": "",
      "Nama_Pembeli": "Kevin",
      "Alamat_Pembeli": "Jalan Jakarta",
      "Email_Pembeli": "a2@some.com",
      "ID_TKU_Pembeli": "3172022407800012000000"
    },
    {
      "Baris": 2,
      "Tanggal_Faktur": "2024-11-12",
      "Jenis_Faktur": "Normal",
      "Kode_Transaksi": "01",
      "Keterangan_Tambahan": "Test 02",
      "Dokumen_Pendukung": "",
      "Referensi": "1091031210910416000000",
      "Cap_Fasilitas": "rrryv",
      "ID_TKU_Penjual": "0000000000000000",
      "NPWP_NIK_Pembeli": "National ID",
      "Jenis_ID_Pembeli": "IDN",
      "Negara_Pembeli": "3305202311840002",
      "Nomor_Dokumen_Pembeli": "fffryjj",
      "Nama_Pembeli": "Justin",
      "Alamat_Pembeli": "Jalan Bandung",
      "Email_Pembeli": "a2@some.com",
      "ID_TKU_Pembeli": "000000"
    },
    {
      "Baris": 3,
      "Tanggal_Faktur": "2024-11-13",
      "Jenis_Faktur": "Normal",
      "Kode_Transaksi": "01",
      "Keterangan_Tambahan": "Test 03",
      "Dokumen_Pendukung": "eeeet",
      "Referensi": "1091031210910416000000",
      "Cap_Fasilitas": "444gy5",
      "ID_TKU_Penjual": "0000000000000000",
      "NPWP_NIK_Pembeli": "Passport",
      "Jenis_ID_Pembeli": "IDN",
      "Negara_Pembeli": "TZ1229593",
      "Nomor_Dokumen_Pembeli": "",
      "Nama_Pembeli": "Rizky",
      "Alamat_Pembeli": "Jalan Ambon",
      "Email_Pembeli": "a2@some.com",
      "ID_TKU_Pembeli": "000000"
    },
    {
      "Baris": 4,
      "Tanggal_Faktur": "2024-11-14",
      "Jenis_Faktur": "Normal",
      "Kode_Transaksi": "01",
      "Keterangan_Tambahan": "Test 04",
      "Dokumen_Pendukung": "xxx",
      "Referensi": "1091031210910416000000",
      "Cap_Fasilitas": "xxx",
      "ID_TKU_Penjual": "0000000000000000",
      "NPWP_NIK_Pembeli": "Other ID",
      "Jenis_ID_Pembeli": "IDN",
      "Negara_Pembeli": "OtherID-0001",
      "Nomor_Dokumen_Pembeli": "",
      "Nama_Pembeli": "Witan",
      "Alamat_Pembeli": "Jalan Semarang",
      "Email_Pembeli": "a2@some.com",
      "ID_TKU_Pembeli": "000000"
    }
  ];
  let databody =``;
    $.each(datahider, function(a,b){
      console.log(b)
      databody +=`<tr>`;
       databody +=`<td>${b.Baris}</td>`;
       databody +=`<td>${b.Jenis_Faktur}</td>`;
       databody +=`<td>${b.Kode_Transaksi}</td>`;
       databody +=`<td>${b.Keterangan_Tambahan}</td>`;
       databody +=`<td>${b.Dokumen_Pendukung}</td>`;
       databody +=`<td>${b.Referensi}</td>`;
       databody +=`<td>${b.Cap_Fasilitas}</td>`;
       databody +=`<td>${b.ID_TKU_Penjual}</td>`;
       databody +=`<td>${b.NPWP_NIK_Pembeli}</td>`;
       databody +=`<td>${b.Negara_Pembeli}</td>`;
       databody +=`<td>${b.Nomor_Dokumen_Pembeli}</td>`;
       databody +=`<td>${b.Nama_Pembeli}</td>`;
       databody +=`<td>${b.Alamat_Pembeli}</td>`;
       databody +=`<td>${b.Email_Pembeli}</td>`;
       databody +=`<td>${b.ID_TKU_Pembeli}</td>`;
       databody +=`</tr>`;
    })
  
    $("#datatable1").empty().html(databody);

      $('#exportBtn').click(function () {
        // Ambil data dari tabel 1
        const table1 = document.getElementById('table1');
        const ws1 = XLSX.utils.table_to_sheet(table1);

        // Ambil data dari tabel 2
        const table2 = document.getElementById('table2');
        const ws2 = XLSX.utils.table_to_sheet(table2);

        // Buat workbook dan tambahkan kedua sheet
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws1, 'Sheet1');
        XLSX.utils.book_append_sheet(wb, ws2, 'Sheet2');

        // Ekspor workbook ke file Excel
        XLSX.writeFile(wb, 'Tables.xlsx');
      });
    });



    const x = 5;

    
    const myElement = <h1>{(x) < 10 ? "Hello" : "Goodbye"}</h1>
  </script>
</body>
</html>
