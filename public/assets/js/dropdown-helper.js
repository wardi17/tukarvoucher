
 export class DropdownHelper {
    constructor(tahunSelector, bulanSelector) {
      this.tahunSelector = tahunSelector;
      this.bulanSelector = bulanSelector;
    }

    getTahun(startYear = 2023, rangePlus = 4) {
      const currentYear = new Date().getFullYear();
      const endYear = currentYear + rangePlus;
      const select = $(this.tahunSelector);
      select.empty(); // Clear if previously filled

      for (let i = startYear; i <= endYear; i++) {
        const selected = i === currentYear;
        select.append($("<option />").val(i).html(i).prop("selected", selected));
      }
    }

    getBulan() {
      const bulan = [
        "Januari", "Februari", "Maret", "April",
        "Mei", "Juni", "Juli", "Agustus",
        "September", "Oktober", "November", "Desember"
      ];

      const bulanSekarang = new Date().getMonth() + 1;
      const select = $(this.bulanSelector);
      select.empty();

      $.each(bulan, function(index, value) {
        let nomorBulan = index + 1;
        let selected = (nomorBulan === bulanSekarang);
        select.append($(`<option />`).val(nomorBulan).html(value).prop("selected", selected));
      });
    }

    // Optional: Auto-Init
    init(startYear = 2023, rangePlus = 4) {
      this.getTahun(startYear, rangePlus);
      this.getBulan();
    }
  }

  


