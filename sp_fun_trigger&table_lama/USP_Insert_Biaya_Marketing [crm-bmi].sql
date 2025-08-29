USE [crm-bmi]
GO

-- Stored Procedure to insert biaya marketing otomatis
CREATE PROCEDURE sp_Insert_Biaya_Marketing
    @TransaksiID INT
AS
BEGIN
    SET NOCOUNT ON;

    DECLARE @InventarisID INT, @Jumlah INT, @HargaPokok DECIMAL(18,2), @TotalBiaya DECIMAL(18,2);

    SELECT InventarisID, Jumlah INTO #tmp FROM ts_Pengambilan_Inventaris WHERE TransaksiID = @TransaksiID;

    SELECT @InventarisID = InventarisID, @Jumlah = Jumlah FROM #tmp;

    SELECT @HargaPokok = HargaPokok FROM ms_Inventaris WHERE InventarisID = @InventarisID;

    SET @TotalBiaya = @HargaPokok * @Jumlah;

    INSERT INTO ts_Biaya_Marketing(TransaksiID, Biaya, TanggalTransaksi)
    VALUES (@TransaksiID, @TotalBiaya, GETDATE());
END;

-- Use: EXEC sp_Insert_Biaya_Marketing @TransaksiID = <id_transaksi>;


