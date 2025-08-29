USE [crm-bmi];
GO

SET ANSI_NULLS ON;
GO
SET QUOTED_IDENTIFIER ON;
GO

-- =============================================
-- Author      : <Author Name>
-- Create date : <Create Date>
-- Description : Laporan Penggunaan Tools
-- =============================================
CREATE PROCEDURE USP_LaporanTools 
    @tanggal DATETIME
AS
BEGIN
    SET NOCOUNT ON;

    IF EXISTS (
        SELECT [TABLE_NAME] 
        FROM tempdb.information_schema.tables 
        WHERE [TABLE_NAME] LIKE '#temptess%'
    )
    BEGIN
        DROP TABLE #temptess;
    END;

    IF EXISTS (
        SELECT [TABLE_NAME] 
        FROM tempdb.information_schema.tables 
        WHERE [TABLE_NAME] LIKE '#temptess2%'
    )
    BEGIN
        DROP TABLE #temptess2;
    END;

    CREATE TABLE #temptess
    (
        InventarisID       VARCHAR(100),
        NamaBarang         NVARCHAR(250),
        KategoriID         VARCHAR(100),
        NamaKategori       NVARCHAR(100),
        StokMaksimum       INT,
        StokMinimum        INT,
        document_files     VARCHAR(2000),
        TotalQtyMasuk      INT,
        TotalQtyKeluar     INT
    );

    CREATE TABLE #temptess2
    (
        InventarisID       VARCHAR(100),
        NamaBarang         NVARCHAR(250),
        KategoriID         VARCHAR(100),
        NamaKategori       NVARCHAR(100),
        StokMaksimum       INT,
        StokMinimum        INT,
        document_files     VARCHAR(2000),
        TotalQtyMasuk      INT,
        TotalQtyKeluar     INT,
        SisaStok           INT,
        SisaTerhadapMax    INT,
        PersentaseTerhadapMax FLOAT,
        SisaTerhadapMin    INT,
        PersentaseTerhadapMin FLOAT,
        StatusStok         NVARCHAR(50)
    );

    INSERT INTO #temptess
    SELECT 
        MS.InventarisID,
        MS.NamaBarang,
        MS.KategoriID,
        KT.NamaKategori,
        MS.StokMaksimum,
        MS.StokMinimum,
        MS.document_files,
        [dbo].Fun_gettotal(MS.InventarisID,@tanggal,'+') AS TotalQtyMasuk,
        [dbo].Fun_gettotal(MS.InventarisID,@tanggal,'-') AS TotalQtyKeluar
    FROM ms_Inventaris AS MS 
    LEFT JOIN ms_KategoriInvenaris AS KT
        ON KT.KategoriID = MS.KategoriID
    WHERE MS.Status = 1;

    INSERT INTO #temptess2
SELECT *,
       (TotalQtyMasuk - TotalQtyKeluar) AS SisaStok,
       (StokMaksimum - (TotalQtyMasuk - TotalQtyKeluar)) AS SisaTerhadapMax,
       CASE 
           WHEN StokMaksimum > 0 
           THEN ((TotalQtyMasuk - TotalQtyKeluar) * 100.0 / NULLIF(StokMaksimum,0))
           ELSE 0 
       END AS PersentaseTerhadapMax,
       ((TotalQtyMasuk - TotalQtyKeluar) - StokMinimum) AS SisaTerhadapMin,
       CASE 
           WHEN StokMinimum > 0 
           THEN ((TotalQtyMasuk - TotalQtyKeluar) * 100.0 / NULLIF(StokMinimum,0))
           ELSE 0 
       END AS PersentaseTerhadapMin,
       CASE 
           WHEN (TotalQtyMasuk - TotalQtyKeluar) < StokMinimum THEN 'Di Bawah Minimum'
           ELSE 'Aman'
       END AS StatusStok
FROM #temptess;

    SELECT * FROM #temptess2
    ORDER BY InventarisID;
END;
GO

-- Contoh eksekusi
EXEC USP_LaporanTools @Tanggal = '2025-08-21 23:59:59';
