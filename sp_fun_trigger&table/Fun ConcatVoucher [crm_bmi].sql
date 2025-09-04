USE [crm-bmi]
GO
CREATE FUNCTION dbo.ConcatVoucher(@KodeTukar varchar(50))
RETURNS varchar(8000)
AS
BEGIN
    DECLARE @Result varchar(8000)
    SET @Result = ''

    SELECT @Result = @Result + LTRIM(RTRIM(Kode_voucher)) + ','
    FROM [crm-bmi].[dbo].Ts_Tukar_VoucherDetail
    WHERE Kode_Tukar = @KodeTukar
    ORDER BY Kode_voucher

    -- Hapus koma terakhir
    IF LEN(@Result) > 0
        SET @Result = LEFT(@Result, LEN(@Result)-1)

    RETURN @Result
END
GO
