import { baseUrl } from '../../config.js';
import { goBack } from '../main.js';

class PrintpenerimaVoucer {
  constructor(dataprint) {
    this.dataprint = dataprint;
    this.root = document.createElement('root');
  }

async render() {
    const { custname, sotransacid, jumlah, keterarngan, username, kode,date_berikan } = this.dataprint;
    const root = this.root;

    // Ambil data detail voucher
    const datadatail = await this.getdatadetailprint({ kode });
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    // === Buat konten tanda terima ===
    doc.setFontSize(18);
    doc.text("TANDA TERIMA VOUCHER", 14, 20);
    const labelX = 14;
    const valueX = 60; // semua nilai mulai di X=60

    doc.setFont("helvetica", "normal");
    doc.setFontSize(12);

    doc.text("Nama", labelX, 30);
    doc.text(":", valueX - 4, 30);
    doc.text(custname, valueX, 30);

    doc.text("Tanggal", labelX, 42);
    doc.text(":", valueX - 4, 42);
    doc.text(date_berikan, valueX, 42);

    doc.text("No SO", labelX, 48);
    doc.text(":", valueX - 4, 48);
    doc.text(sotransacid, valueX, 48);

    doc.text("Jumlah", labelX, 54);
    doc.text(":", valueX - 4, 54);
    doc.text(jumlah, valueX, 54);

    doc.setFontSize(11);

    // Jika datadatail berisi array voucher, masukkan ke tabel
    const headers = [["No", "Kode Voucher"]];
    const data = (datadatail || []).map((item, index) => [
      (index + 1).toString(),
      item.Kode_voucher
    ]);
    doc.autoTable({ startY: 55, head: headers, body: data });

    const finalY = doc.lastAutoTable.finalY;
    doc.setFontSize(12);
    doc.text(`Keterangan: ${keterarngan}`, 14, finalY + 15);
    doc.text("Hormat Kami,", 14, finalY + 23);
    doc.text(`(${username})`, 14, finalY + 45);
    doc.text(`Received by`, 150, finalY + 23);
    doc.text("(                    )", 150, finalY + 45);
    doc.text(`${new Date().toLocaleDateString()}`, 152, finalY + 51);

    // === Buat blob URL (bukan data URI) ===
   // const pdfUrl = doc.output("bloburl");

    // Pastikan root ada di DOM
    if (!root) {
      console.error("Elemen #root tidak ditemukan");
      return;
    }

    // Bersihkan root
    root.innerHTML = '';

    // Tombol kembali
    const backBtn = document.createElement("button");
    backBtn.innerText = "← Kembali ke List";
    backBtn.style.display = "block";
    backBtn.style.marginBottom = "10px";
    backBtn.className = "btn btn-secondary";
    backBtn.onclick = () => goBack();

    // Tombol Cetak PDF
    const printBtn = document.createElement("button");
    printBtn.innerText = "Cetak PDF";
    printBtn.style.display = "block";
    printBtn.style.marginBottom = "10px";
    printBtn.className = "btn btn-primary";
    printBtn.onclick = () => window.open(pdfUrl, '_blank');

    // Iframe untuk preview PDF
    iframe.style.width = "100%";
    iframe.style.height = "600px";
    iframe.style.border = "1px solid #ccc";
    iframe.src = pdfUrl + "#random=" + Date.now(); // anti-cache

    // Jika mau hemat memori: hapus URL setelah selesai load
   // Misalnya Anda memiliki URL PDF yang dihasilkan
const pdfUrl = URL.createObjectURL(pdfBlob); // Membuat URL

// Elemen iframe
const iframe = document.getElementById('pdfIframe');
iframe.src = pdfUrl; // Menetapkan URL ke iframe

// Event onload untuk menghapus URL setelah load
iframe.onload = () => {
    URL.revokeObjectURL(pdfUrl); // Menghapus URL untuk menghemat memori
};


    // Masukkan ke root
    root.appendChild(backBtn);
    root.appendChild(printBtn);
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
