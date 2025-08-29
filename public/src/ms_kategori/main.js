
import MsKategori from './componets/Ms_Kategori.js';
import MsKategoriList from './componets/MsKategoriList.js';

$(document).ready(function () {
   
const url = new URL(window.location.href);
const pathSegments = url.pathname.split("/");
const lastSegment = pathSegments.filter(Boolean).pop(); // filter untuk hilangkan elemen kosong
// Kondisi berdasarkan segmen terakhir URL
   new MsKategori("#root");
   new MsKategoriList("#rootlist");



  
     
       
   //and 
});


export function goBack() {
    new MsKategoriList("#rootlist");
}