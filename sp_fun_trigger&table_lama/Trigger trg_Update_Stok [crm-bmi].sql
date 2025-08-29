USE [crm-bmi]
GO

CREATE TRIGGER trg_Update_Stok
ON ts_Pengambilan_Inventaris
AFTER UPDATE, DELETE
AS
BEGIN
    SET NOCOUNT ON;

    -- 1. DELETE → kembalikan stok lama
    UPDATE ms_Inventaris
    SET ms_Inventaris.Stok = ms_Inventaris.Stok + d.Jumlah,
        ms_Inventaris.UpdatedAt = GETDATE()
    FROM ms_Inventaris
    INNER JOIN deleted d ON ms_Inventaris.InventarisID = d.InventarisID;

    -- 2. INSERT → kurangi stok baru
    UPDATE ms_Inventaris
    SET ms_Inventaris.Stok = ms_Inventaris.Stok - i.Jumlah,
        ms_Inventaris.UpdatedAt = GETDATE()
    FROM ms_Inventaris
    INNER JOIN inserted i ON ms_Inventaris.InventarisID = i.InventarisID;

    -- 3. VALIDASI stok tidak boleh minus
    IF EXISTS (
        SELECT 1 FROM ms_Inventaris WHERE Stok < 0
    )
    BEGIN
        RAISERROR('Stok tidak boleh minus!', 16, 1);
        ROLLBACK TRANSACTION; -- batalkan INSERT/UPDATE/DELETE
    END
END;
