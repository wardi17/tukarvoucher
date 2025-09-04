
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
('BFGF25P5N8',1,'admin'),
('BFGF253T8K',1,'admin'),
('BFGF25Z10N',1,'admin'),
('BFGF2562CP',1,'admin'),
('BFGF25GFUM',1,'admin'),
('BFGF25UL1M',1,'admin'),
('BFGF25WI0U',1,'admin'),
('BFGF25FHU5',1,'admin'),
('BFGF25UCLY',1,'admin'),
('BFGF25X48T',1,'admin'),
('BFGF25KHOF',1,'admin'),
('BFGF25GFYW',1,'admin'),
('BFGF25WV2T',1,'admin'),
('BFGF25GBVJ',1,'admin'),
('BFGF25SCV9',1,'admin'),
('BFGF259BC3',1,'admin'),
('BFGF25IR5A',1,'admin'),
('BFGF25IU2L',1,'admin'),
('BFGF254Y5L',1,'admin'),
('BFGF25ZFBX',1,'admin'),
('BFGF25N7IO',1,'admin'),
('BFGF2558SD',1,'admin'),
('BFGF25LIUP',1,'admin'),
('BFGF25A1FN',1,'admin'),
('BFGF25O2PK',1,'admin'),
('BFGF2560K2',1,'admin'),
('BFGF2504EP',1,'admin'),
('BFGF25PHUS',1,'admin'),
('BFGF250H84',1,'admin'),
('BFGF2585MR',1,'admin'),
('BFGF25PT53',1,'admin'),
('BFGF25JV4Z',1,'admin'),
('BFGF255XLV',1,'admin'),
('BFGF250UXW',1,'admin'),
('BFGF25R98U',1,'admin'),
('BFGF25G7QC',1,'admin'),
('BFGF25MX65',1,'admin'),
('BFGF25FXGP',1,'admin'),
('BFGF252ZT7',1,'admin'),
('BFGF25M62A',1,'admin'),
('BFGF25AC3M',1,'admin'),
('BFGF25EDUW',1,'admin'),
('BFGF255HVU',1,'admin'),
('BFGF25T3VO',1,'admin'),
('BFGF258RCF',1,'admin'),
('BFGF25P6CT',1,'admin'),
('BFGF25YQR5',1,'admin'),
('BFGF25KJYW',1,'admin'),
('BFGF253PHM',1,'admin'),
('BFGF25EZFG',1,'admin'),
('BFGF25QZPR',1,'admin'),
('BFGF25MYQG',1,'admin'),
('BFGF25LYBT',1,'admin'),
('BFGF25EJYM',1,'admin'),
('BFGF25GHW1',1,'admin'),
('BFGF25TEZE',1,'admin'),
('BFGF25X3RJ',1,'admin'),
('BFGF25FA3Y',1,'admin'),
('BFGF25J3K1',1,'admin'),
('BFGF25EP8X',1,'admin'),
('BFGF25UWC7',1,'admin'),
('BFGF25MMIC',1,'admin'),
('BFGF2526VO',1,'admin'),
('BFGF25S7DB',1,'admin'),
('BFGF25PB5P',1,'admin'),
('BFGF25CJET',1,'admin'),
('BFGF25RBON',1,'admin'),
('BFGF25AJLS',1,'admin'),
('BFGF25MW5W',1,'admin'),
('BFGF25KB3H',1,'admin'),
('BFGF25HX95',1,'admin'),
('BFGF253G43',1,'admin'),
('BFGF25XD7H',1,'admin'),
('BFGF25ZFA1',1,'admin'),
('BFGF251BWD',1,'admin'),
('BFGF25HSHV',1,'admin'),
('BFGF25BTK0',1,'admin'),
('BFGF25ZDL6',1,'admin'),
('BFGF2577OT',1,'admin'),
('BFGF254EGI',1,'admin'),
('BFGF258MNY',1,'admin'),
('BFGF25SQCN',1,'admin'),
('BFGF25FK71',1,'admin'),
('BFGF253OX4',1,'admin'),
('BFGF25EEBI',1,'admin'),
('BFGF25QOZY',1,'admin'),
('BFGF25ZBAB',1,'admin'),
('BFGF25OXCB',1,'admin'),
('BFGF25B6GH',1,'admin'),
('BFGF25JA7Y',1,'admin'),
('BFGF25BAUI',1,'admin'),
('BFGF25FNAA',1,'admin'),
('BFGF25YUPP',1,'admin'),
('BFGF25WLGV',1,'admin'),
('BFGF25GDS8',1,'admin'),
('BFGF25SUJ9',1,'admin'),
('BFGF25WPWW',1,'admin'),
('BFGF25E8G1',1,'admin'),
('BFGF25JW6O',1,'admin'),
('BFGF252GE5',1,'admin');







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

 ALTER TABLE  Ts_Berikan_Voucher
 ADD Status_print CHAR(1) DEFAULT 'N',
    User_print VARCHAR(100) NULL,
    Date_print DATETIME NULL ;

    
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