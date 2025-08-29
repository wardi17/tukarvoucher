export const  conversionMonth=(mounth)=>{

  const bulan = [
    "Januari", "Februari", "Maret", "April",
    "Mei", "Juni", "Juli", "Agustus",
    "September", "Oktober", "November", "Desember"
  ];

  let index = parseInt(mounth) - 1;
  return bulan[index] ?? 'Bulan tidak valid';
}