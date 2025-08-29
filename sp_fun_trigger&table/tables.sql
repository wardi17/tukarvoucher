
USE [crm-bmi]
--Table: ms_voucher
  CREATE TABLE ms_voucher(
    Kode_voucher CHAR(15) PRIMARY KEY,
    Date_voucher DATETIME DEFAULT GETDATE(),
    Jumlah_voucher INT NOT NULL,
    User_Input VARCHAR(100) NOT NULL,
    CustomerID  VARCHAR(100)  NULL,
    User_kasih_voucher VARCHAR(100)  NULL,
    Date_kasih_voucher DATETIME ,
    Toko_merchant VARCHAR(100)  NULL,
    User_tukar_voucher VARCHAR(100) NULL,
    Date_tukar_voucher DATETIME NULL,
    jumlah_tukar_voucher INT  DEFAULT 1 ,
  );

  INSERT INTO ms_voucher (Kode_voucher, Jumlah_voucher, User_Input) VALUES
  (1001, 1, 'admin'),
  (1002, 1, 'admin'),
   (1003, 1, 'admin'),
   (1004, 1, 'admin'),
  (1005, 1, 'admin'),
  (1006, 1, 'admin');


--Table: Ts_Berikan_Voucher
  CREATE TABLE Ts_Berikan_Voucher(
    Kode_Berikan VARCHAR(100) PRIMARY KEY,
    cabang VARCHAR(100) NOT NULL,
    CustomerID  char(10)  NULL,
    CustName VARCHAR(200) NULL,
    SOTransacID char(15)  NULL,
    Jumlah_berikan_voucher INT NOT NULL,
    Keterangan NVARCHAR(3000) NULL,
    User_kasih_voucher VARCHAR(100) NOT NULL,
    Date_kasih_voucher DATETIME DEFAULT GETDATE(),
    Status_posting CHAR(1) DEFAULT 'N',
    User_posting VARCHAR(100) NULL,
    Date_posting DATETIME NULL
  );


  --Table: Ts_Tukar_VoucherDetail
  CREATE TABLE Ts_Berikan_VoucherDetail(
    ID INT IDENTITY(1,1),
    Kode_Berikan VARCHAR(100) NOT NULL,
    Kode_voucher CHAR(15) NOT NULL,
    Date_Detail DATETIME ,
    FOREIGN KEY (Kode_Berikan) REFERENCES Ts_Berikan_Voucher(Kode_Berikan),
    FOREIGN KEY (Kode_voucher) REFERENCES ms_voucher(Kode_voucher)
  );


  --Table: Ts_Tukar_Voucher
 CREATE TABLE Ts_Tukar_Voucher(
    Kode_Tukar VARCHAR(100) PRIMARY KEY,
    Toko_merchant VARCHAR(100) NOT NULL,
    CustomerName  VARCHAR(150) NOT NULL,
    NoTelp VARCHAR(15) NULL,
    Keterangan NVARCHAR(3000) NULL,
    User_tukar_voucher VARCHAR(100) NOT NULL,
    Date_tukar_voucher DATETIME DEFAULT GETDATE(),
  );

    --Table: Ts_Tukar_VoucherDetail
    CREATE TABLE Ts_Tukar_VoucherDetail(
    ItemNo INT IDENTITY(1,1),
    Kode_Tukar VARCHAR(100) NOT NULL,
    Kode_voucher CHAR(15) NOT NULL,  
    Date_voucher DATETIME,
    Date_Detail DATETIME ,
    FOREIGN KEY (Kode_Tukar) REFERENCES Ts_Tukar_Voucher(Kode_Tukar),
    FOREIGN KEY (Kode_voucher) REFERENCES ms_voucher(Kode_voucher)
  );

-- use [crm-bmi]
-- CREATE TABLE level (
--     id_level INT IDENTITY(1,1) PRIMARY KEY,
--     nama_level VARCHAR(20) NOT NULL,  --'full / giver / redeemer',
--     keterangan VARCHAR(100) NULL --'Deskripsi akses menu'
-- );


-- INSERT INTO level (nama_level, keterangan) VALUES 
-- ('full', 'Akses semua menu'),
-- ('giver', 'Hanya bisa memberikan voucher'),
-- ('redeemer', 'Hanya bisa menukar voucher');


-- ALTER TABLE [um_db].[dbo].a_user
-- ADD id_level int

-- UPDATE  [um_db].[dbo].a_user SET id_level = 1 WHERE id_cust='wardi';   -- full
-- UPDATE  [um_db].[dbo].a_user SET id_level = 2 WHERE id_cust='herman';  -- giver
-- UPDATE  [um_db].[dbo].a_user SET id_level = 3 WHERE id_cust='weelan';  -- redeemer