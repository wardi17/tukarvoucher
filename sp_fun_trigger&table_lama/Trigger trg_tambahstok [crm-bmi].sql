USE [crm-bmi]
GO
CREATE TRIGGER trg_tambahstok 
ON ms_InventarisDetail
AFTER INSERT
AS
BEGIN
    UPDATE ms_Inventaris
    SET Stok = Stok + t.QtyChange
    FROM ms_Inventaris i
    JOIN (
        SELECT InventarisID,
               SUM(CASE WHEN FlagUpdateStock = '+' THEN Qty
                        WHEN FlagUpdateStock = '-' THEN -Qty ELSE 0 END) AS QtyChange
        FROM inserted
        GROUP BY InventarisID
    ) t ON i.InventarisID = t.InventarisID;
END;
