/*marketing_inventory_flowchart_and_db.sql`
```sql
-- FLOWCHART DESCRIPTION (TEXT-BASED FLOW)

-- 1. USER LOGIN
--    -> Admin? Yes -> Admin Dashboard
--    -> User? Yes -> User Dashboard

-- 2. ADMIN DASHBOARD
--    a. Manage Inventaris (Add/Edit/Delete)
--    b. Manage SOP per item
--    c. Set stok minimum dan maksimum
--    d. Monitor stock levels / stock taking
--    e. View reports (stok, biaya marketing, penggunaan)

-- 3. USER DASHBOARD
--    a. View available inventaris (stok ready)
--    b. Ajukan permohonan barang
--    c. Sistem validasi stok cukup?
--         -> Yes: Potong stok & catat transaksi pengambilan
--         -> No: Tampilkan pesan stok tidak cukup
--    d. Laporan penggunaan per user (jika perlu)

-- 4. SISTEM PROSES
--    a. Stok berkurang saat pengambilan
--    b. Jika stok kurang dari minimum, notifikasi admin
--    c. Biaya marketing tercatat berdasarkan HPP produk yang dipakai
--    d. SOP penggunaan dapat diakses oleh user/admin

*/
-- DATABASE SCHEMA FOR SQL SERVER


USE [crm-bmi]
--Table: ms_KategoriInvenaris


  CREATE TABLE ms_KategoriInvenaris(
    ItemNo INT IDENTITY(1,1),
    KategoriID VARCHAR(100) PRIMARY KEY,
    NamaKategori  NVARCHAR(100) NOT NULL
  )


-- Table: ms_Inventaris
CREATE TABLE ms_Inventaris (
    InventarisID VARCHAR(100) PRIMARY KEY,
    NamaBarang NVARCHAR(250) NOT NULL,
    KategoriID VARCHAR(100) NOT NULL, -- e.g. Spanduk, Brosur, Sample, Bingkisan
    Stok INT NOT NULL DEFAULT 0,
    StokMinimum INT NOT NULL DEFAULT 0,
    StokMaksimum INT NOT NULL DEFAULT 0,
    HargaPokok DECIMAL(18,2) NOT NULL, -- HPP per unit
    Status BIT DEFAULT 1, -- 1=active, 0=inactive
    CreatedAt DATETIME DEFAULT GETDATE(),
    UpdatedAt DATETIME DEFAULT GETDATE(),
    userInput VARCHAR(100),
    userEdit VARCHAR(100),
    document_files VARCHAR(2000) NULL,
    FOREIGN KEY (KategoriID) REFERENCES ms_KategoriInvenaris(KategoriID)
);

--Table: ms_Inventarisdetail

CREATE TABLE ms_InventarisDetail (
    TransaksiIDDetail INT IDENTITY(1,1) PRIMARY KEY,
    InventarisID VARCHAR(100) NOT NULL,
    Qty FLOAT NOT NULL CHECK (Qty > 0),
    TanggalMasuk DATETIME DEFAULT GETDATE(),
    FlagUpdateStock char(1) NOT NULL, -- '+' atau '-'
    JenisTransaksi VARCHAR(10) NOT NULL, -- 'MASUK' atau 'KELUAR'
    Keterangan NVARCHAR(3000) NULL,
    UserID INT NOT NULL,
    FOREIGN KEY (InventarisID) REFERENCES ms_Inventaris(InventarisID)
);



-- Table: ts_Pengambilan_Inventaris
--drop table ts_Pengambilan_Inventaris
CREATE TABLE ts_Pengambilan_Inventaris (
    TransaksiID INT IDENTITY(1,1) PRIMARY KEY,
    InventarisID VARCHAR(100) NULL,
    UserID INT NOT NULL,
    Jumlah FLOAT NOT NULL CHECK (Jumlah > 0),
    TanggalPengambilan DATETIME DEFAULT GETDATE(),
    Keterangan NVARCHAR(500) NULL,
    UserID_update INT  NULL,
    Date_Update DATETIME NULL,
    statusposting char(1) DEFAULT 'N' NOT NULL ,
    UserID_Posting INT NULL,
    dateposting DATETIME
    FOREIGN KEY (InventarisID) REFERENCES ms_Inventaris(InventarisID)
    --FOREIGN KEY (UserID) REFERENCES ms_User(UserID)
);






