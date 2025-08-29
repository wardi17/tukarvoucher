USE [crm-bmi];
GO

SET ANSI_NULLS ON;
GO
SET QUOTED_IDENTIFIER ON;
GO

-- =============================================
-- Author      : <Author Name>
-- Create date : <Create Date>
-- Description : Laporan Pengambilan Barang Inventaris keluar masuk
-- =============================================
ALTER PROCEDURE USP_LaporanInventarisInOut 
    @date_from DATETIME,
    @date_to   DATETIME,
    @type VARCHAR(1)
AS
BEGIN
    SET NOCOUNT ON;

    -------------------------------------------------------
    -- 1. Data Detail Transaksi
    -------------------------------------------------------
    SELECT 
        ts.TransaksiIDDetail,
        ts.InventarisID,
        ts.UserID,
        u1.nama AS NamaUser,               
        ts.Qty,
        ts.TanggalMasuk,
        ms.NamaBarang,
        ms.Stok,
        ms.document_files,
        mk.NamaKategori
    FROM dbo.ms_InventarisDetail AS ts
    LEFT JOIN [um_db].[dbo].a_user AS u1 
        ON u1.id_user = ts.UserID
    LEFT JOIN dbo.ms_Inventaris AS ms  
        ON ms.InventarisID = ts.InventarisID
    LEFT JOIN dbo.ms_KategoriInvenaris AS mk  
        ON mk.KategoriID = ms.KategoriID 
    WHERE ts.FlagUpdateStock = @type
      AND ts.TanggalMasuk BETWEEN @date_from AND @date_to
    ORDER BY ts.TanggalMasuk ASC;

   
END;
GO

-- Contoh eksekusi
EXEC USP_LaporanInventarisInOut  
    @date_from = '2025-08-01', 
    @date_to   = '2025-08-20 23:59:59',
    @type      ='-';

