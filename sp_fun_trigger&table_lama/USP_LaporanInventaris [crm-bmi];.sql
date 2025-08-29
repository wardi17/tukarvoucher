USE [crm-bmi];
GO

SET ANSI_NULLS ON;
GO
SET QUOTED_IDENTIFIER ON;
GO

-- =============================================
-- Author      : <Author Name>
-- Create date : <Create Date>
-- Description : Laporan Pengambilan Barang Inventaris
-- =============================================
CREATE PROCEDURE USP_LaporanInventaris 
    @date_from DATETIME,
    @date_to   DATETIME
AS
BEGIN
    SET NOCOUNT ON;

    -------------------------------------------------------
    -- 1. Data Detail Transaksi
    -------------------------------------------------------
    SELECT 
        ts.TransaksiID,
        ts.InventarisID,
        ts.UserID,
        u1.nama AS NamaUser,               
        ts.Jumlah,
        ts.TanggalPengambilan,
        ts.UserID_Posting,
        u2.nama AS NamaUserPosting,        
        ts.dateposting,
        ms.NamaBarang,
        ms.Stok,
        ms.document_files,
        mk.NamaKategori
    FROM dbo.ts_Pengambilan_Inventaris AS ts
    LEFT JOIN [um_db].[dbo].a_user AS u1 
        ON u1.id_user = ts.UserID
    LEFT JOIN [um_db].[dbo].a_user AS u2 
        ON u2.id_user = ts.UserID_Posting
    LEFT JOIN dbo.ms_Inventaris AS ms  
        ON ms.InventarisID = ts.InventarisID
    LEFT JOIN dbo.ms_KategoriInvenaris AS mk  
        ON mk.KategoriID = ms.KategoriID 
    WHERE ts.statusposting = 'Y'
      AND ts.TanggalPengambilan BETWEEN @date_from AND @date_to
    ORDER BY ts.TanggalPengambilan ASC;

    -------------------------------------------------------
    -- 2. Ringkasan Total Transaksi
    -------------------------------------------------------
    SELECT 
        COUNT(ts.TransaksiID) AS TotalTransaksi,
        SUM(ts.Jumlah) AS TotalBarangDiambil
    FROM dbo.ts_Pengambilan_Inventaris ts
    WHERE ts.statusposting = 'Y'
      AND ts.TanggalPengambilan BETWEEN @date_from AND @date_to;

    -------------------------------------------------------
    -- 3. Ringkasan per Kategori
    -------------------------------------------------------
    SELECT 
        mk.NamaKategori,
        SUM(ts.Jumlah) AS TotalBarangPerKategori
    FROM dbo.ts_Pengambilan_Inventaris ts
    LEFT JOIN dbo.ms_Inventaris ms  
        ON ms.InventarisID = ts.InventarisID
    LEFT JOIN dbo.ms_KategoriInvenaris mk  
        ON mk.KategoriID = ms.KategoriID 
    WHERE ts.statusposting = 'Y'
      AND ts.TanggalPengambilan BETWEEN @date_from AND @date_to
    GROUP BY mk.NamaKategori
    ORDER BY mk.NamaKategori ASC;

END;
GO

-- Contoh eksekusi
EXEC USP_LaporanInventaris 
    @date_from = '2025-08-01', 
    @date_to   = '2025-08-19 23:59:59';
