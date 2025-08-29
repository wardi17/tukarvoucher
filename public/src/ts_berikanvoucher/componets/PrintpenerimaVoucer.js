import { baseUrl } from '../../config.js';
import { goBack } from '../main.js';

class PrintpenerimaVoucer {
  constructor(dataprint) {
    this.dataprint = dataprint;
    this.root = document.createElement('root');
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

    doc.text("Tanggal", labelX, 38);
    doc.text(":", valueX - 4, 38);
    doc.text(`${date_berikan}`, valueX, 38);

    doc.text("No SO", labelX, 44);
    doc.text(":", valueX - 4, 44);
    doc.text(`${sotransacid}`, valueX, 44);

    doc.text("Jumlah", labelX, 50);
    doc.text(":", valueX - 4, 50);
    doc.text(`${jumlah}`, valueX, 50);

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
      columnStyles: {
        0: { cellWidth: 12 },
        1: { cellWidth: 150 }
      },
      theme: 'grid',
      didDrawPage: function (data) {
        const pageCount = doc.internal.getNumberOfPages();
        const pageCurrent = data.pageNumber;
        const pageWidth = doc.internal.pageSize.getWidth();
        const pageText = `Halaman ${pageCurrent} dari ${pageCount}`;
        doc.setFontSize(10);
        doc.setFont(undefined, "italic");
        doc.text(pageText, pageWidth - 35, 55, { align: "right" });
      }
    });

    // 5. Footer: keterangan dan tanda tangan
    const finalY = doc.lastAutoTable.finalY || 58;
    const maxWidth = 150;
    doc.text("Ket : ",14, finalY + 15);

    // wrap isi keterangan saja
      doc.setFontSize(12);

      // label ditulis sekali
      doc.text("Ket : ", 14, finalY + 15);

      // wrap isi keterangan saja
      const textLines = doc.splitTextToSize(keterangan, maxWidth);
      const lineHeight = doc.getLineHeight() / doc.internal.scaleFactor;
      doc.text(textLines, 14, finalY + 15 + lineHeight); 

      // hitung tinggi untuk menentukan posisi teks berikutnya
      const keteranganHeight = textLines.length * lineHeight;

      // contoh: posisi tanda tangan otomatis di bawah keterangan
      const nextY = finalY + 15 + lineHeight + keteranganHeight + 5;
      doc.text("Hormat Kami,", 14, nextY);
      doc.text(`(${username})`, 14, nextY + 20);


    doc.text("Received by", 150, nextY);
    doc.text("(                    )", 150, nextY + 22);
    doc.text(`${new Date().toLocaleDateString()}`, 152, nextY + 28);

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
