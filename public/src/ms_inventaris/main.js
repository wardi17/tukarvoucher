
import Ms_Inventaris from './componets/Ms_Inventaris.js';
import MsInventarisList from './componets/MsInventarisList.js';
import ListTambahStok from './componets/ListTambahStok.js';
$(document).ready(function () {
   
const url = new URL(window.location.href);
const pathSegments = url.pathname.split("/");
const lastSegment = pathSegments.filter(Boolean).pop(); // filter untuk hilangkan elemen kosong
// Kondisi berdasarkan segmen terakhir URL
console.log(lastSegment)
 if(lastSegment !=="tambahstok"){
    new Ms_Inventaris("#root");
   new MsInventarisList("#rootlist");
 }else{
      new ListTambahStok("#rootlist");
 }
   



  
     
       
   //and 
});


export function goBack() {
    new MsInventarisList("#rootlist");
}

export function goBackStok(){
    new ListTambahStok("#rootlist"); 
}