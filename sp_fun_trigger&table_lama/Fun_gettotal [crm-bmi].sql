USE [crm-bmi]
GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

-- =============================================
-- Author      : <Your Name>
-- Create date : <Date>
-- Description : Fungsi untuk menghitung total Qty
-- =============================================
IF OBJECT_ID('dbo.Fun_gettotal') IS NOT NULL
    DROP FUNCTION dbo.Fun_gettotal
GO

CREATE FUNCTION dbo.Fun_gettotal
(
    @InventarisID  VARCHAR(100),
    @tanggal       DATETIME,
    @FlagStock     VARCHAR(1)
)
RETURNS FLOAT
AS
BEGIN
    DECLARE @total FLOAT

    SELECT @total = COALESCE(SUM(Qty), 0)
    FROM ms_InventarisDetail
    WHERE InventarisID = @InventarisID
      AND FlagUpdateStock = @FlagStock
      AND TanggalMasuk <= @tanggal

    RETURN @total
END
GO

-- Cara memanggil fungsi:
SELECT dbo.Fun_gettotal('TRX-17556760', '2025-08-21 23:59:59', '+') AS TotalQty
GO
