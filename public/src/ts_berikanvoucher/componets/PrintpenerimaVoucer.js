import { baseUrl } from '../../config.js';
import { goBack } from '../main.js';

class PrintpenerimaVoucer {
  constructor(dataprint) {
    this.dataprint = dataprint;
    this.root = document.createElement('root');
    this.button;
  }
   
  async render() {
    const { custname, sotransacid, jumlah, keterangan, username, kode, date_berikan } = this.dataprint;
    const root = this.root;

    // 1. Ambil data detail voucher dari server
    const datadatail = await this.getdatadetailprint({ kode });
    const { jsPDF } = window.jspdf;

    // 2. Inisialisasi PDF
    const doc = new jsPDF({
      orientation: "portrait",
      unit: "mm",
      format: "A4"
    });
   const totalPagesExp = "{total_pages_count_string}"; // <-- WAJIB: didefinisikan dulu
    // 3. Header PDF
    const pageWidth = doc.internal.pageSize.getWidth();  // biasanya 210 mm untuk A4 potrait
    doc.setFont("helvetica", "normal");
    doc.setFontSize(18);
    doc.text("TANDA TERIMA VOUCHER", pageWidth / 2, 20,{ align: "center" });

    const labelX = 14;
    const valueX = 60;
    doc.setFontSize(12);

    doc.text("Nama", labelX, 30);
  
    doc.text(":", valueX - 4, 30);
    doc.text(`${custname}`, valueX, 30);

    doc.text("Tanggal", labelX, 36);
    doc.text(":", valueX - 4, 36);
    doc.text(`${date_berikan}`, valueX, 36);

    doc.text("No SO", labelX, 42);
    doc.text(":", valueX - 4, 42);
    doc.text(`${sotransacid}`, valueX, 42);

    doc.text("Jumlah", labelX, 48);
    doc.text(":", valueX - 4, 48);
    doc.text(`${jumlah}`, valueX,48);

    // 4. Tabel data voucher
    const headers = [["No", "Kode Voucher"]];
    const data = (datadatail || []).map((item, index) => [
      (index + 1).toString(),
      item.Kode_voucher
    ]);

    doc.autoTable({
      head: headers,
      body: data,
      startY: 58,
      styles: {
        lineColor: [0, 0, 0],
        lineWidth: 0.5,
        fontSize: 10,
        cellPadding: 3,
      },
      headStyles: {
        fillColor: [52, 152, 219],
        textColor: [0, 0, 0],
        lineWidth: 0.8,
        fontStyle: 'bold'
      },
      bodyStyles: {
        fillColor: [255, 255, 255],
        textColor: [0, 0, 0]
      },
       margin: { top: 20, left: 10, right: 10 },
      columnStyles: {
        0: { cellWidth: 12 },
        // 1: { cellWidth:70}
      },
       tableWidth: 'auto',
      theme: 'grid',
      didDrawPage: function (data) {
          const pageCount = doc.internal.getNumberOfPages();
          const pageCurrent = data.pageNumber;
          const pageWidth = doc.internal.pageSize.getWidth();

          //console.log(pageWidth)
          let  pageText = "Halaman " + pageCurrent;
          if(typeof doc.putTotalPages === 'function'){
              pageText += " dari " + totalPagesExp;
          }
          // Tentukan posisi vertikal teks halaman
          const posisiY = (pageCount === 1) ? 55 : 16;
     
          doc.setFontSize(10);
          doc.setFont(undefined, "italic");
          doc.text(pageText, pageWidth +27, posisiY, { align: "right" });
      }

    });

    // Ganti placeholder dengan angka total halaman
    if (typeof doc.putTotalPages === "function") {
      doc.putTotalPages(totalPagesExp);
    }

    // 5. Footer: keterangan dan tanda tangan
      let finalY = doc.lastAutoTable.finalY || 58;
      const maxWidth = 150;
      const lineHeight = doc.getLineHeight() / doc.internal.scaleFactor;
      const textLines = doc.splitTextToSize(keterangan, maxWidth);
      const keteranganHeight = textLines.length * lineHeight;
      const footerHeight = 15 + lineHeight + keteranganHeight + 25 + 32; // total tinggi footer

      // Cek apakah footer muat di halaman sekarang
      const pageHeight = doc.internal.pageSize.getHeight();
      if (finalY + footerHeight > pageHeight - 10) {
        doc.addPage();
        finalY = 20; // mulai footer dari atas halaman baru
      }

      doc.setFontSize(12);
      doc.text("Ket : ", 14, finalY + 15);
      doc.text(textLines, 14, finalY + 15 + lineHeight);

      // posisi tanda tangan
      const nextY = finalY + 15 + lineHeight + keteranganHeight + 5;
      doc.text("Hormat Kami,", 14, nextY);
      doc.text(`(${username})`, 14, nextY + 25);

      doc.text("Received by", 150, nextY);
      doc.text("(                    )", 150, nextY + 27);
  
      const printDate = this.formatdateprint();
      doc.text(`Print date : ${printDate}`, 140, nextY + 38);


    // 6. Preview PDF di iframe
    const pdfUrl = doc.output("bloburl");

    if (!root) {
      console.error("Elemen root tidak ditemukan");
      return;
    }

    root.innerHTML = '';

    // tombol kembali
    const backBtn = document.createElement("button");
    backBtn.innerText = "← Kembali ke List";
    backBtn.className = "btn btn-secondary";
    backBtn.style.display = "block";
    backBtn.style.marginBottom = "10px";
    backBtn.onclick = () => goBack();

    // iframe preview
    const iframe = document.createElement("iframe");
    iframe.style.width = "100%";
    iframe.style.height = "600px";
    iframe.style.border = "1px solid #ccc";
    iframe.src = pdfUrl + "#random=" + Date.now();

    iframe.onload = () => {
      URL.revokeObjectURL(pdfUrl); // hemat memori
    };

    // masukkan ke DOM
    root.appendChild(backBtn);
    root.appendChild(iframe);


    return root;
  }

    formatdateprint=()=>{
      const now = new Date();

        const day = String(now.getDate()).padStart(2, '0');      // 01 - 31
        const month = String(now.getMonth() + 1).padStart(2, '0'); // 01 - 12
        const year = now.getFullYear();
        const time = now.toLocaleTimeString(); // 9:48:30 AM

       return `${day}/${month}/${year} ${time}`;
      
    }


  async getdatadetailprint(datas) {
    return new Promise((resolve, reject) => {
      $.ajax({
        url: `${baseUrl}/router/seturl`,
        method: "POST",
        dataType: "json",
        contentType: "application/json; charset=UTF-8",
        headers: { 'url': 'voucherberi/getdatadetailprint' },
        data: JSON.stringify(datas),
        success: (result) => {
          if (!result.error && result.data) {
            resolve(result.data);
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
}

export default PrintpenerimaVoucer;
