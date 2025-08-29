use [crm-bmi]
GO
CREATE PROCEDURE USP_GetInventarisWithStok
    @InventarisID VARCHAR(50)
AS
BEGIN
    SET NOCOUNT ON;

    SELECT 
        i.NamaBarang,
        i.KategoriID,
        CASE 
            WHEN d.TotalQty IS NOT NULL THEN d.TotalQty
            ELSE i.Stok
        END AS Stok,
        i.StokMinimum,
        i.StokMaksimum,
        i.HargaPokok,
        i.document_files
    FROM [crm-bmi].[dbo].ms_Inventaris i
    LEFT JOIN (
        SELECT 
            InventarisID,
            COALESCE(SUM(CASE WHEN FlagUpdateStock = '+' THEN Qty ELSE 0 END), 0)
            - COALESCE(SUM(CASE WHEN FlagUpdateStock = '-' THEN Qty ELSE 0 END), 0)
            AS TotalQty
        FROM [crm-bmi].[dbo].ms_InventarisDetail
        GROUP BY InventarisID
    ) d ON i.InventarisID = d.InventarisID
    WHERE i.InventarisID = @InventarisID;
END;
GO

EXEC USP_GetInventarisWithStok @InventarisID = 'TRX-17556760';