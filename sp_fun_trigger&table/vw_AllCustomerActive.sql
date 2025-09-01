USE [crm-bmi];
GO

CREATE VIEW [dbo].[vw_AllCustomerActive]
AS
SELECT CustomerID, CustName, 'bambi04' AS SourceDB
FROM [bambi04].[dbo].[customer]
WHERE custStatus = 1

UNION ALL

SELECT CustomerID, CustName, 'bambi-mg2' AS SourceDB
FROM [bambi-mg2].[dbo].[customer]
WHERE custStatus = 1

UNION ALL

SELECT CustomerID, CustName, 'bambi-bmi' AS SourceDB
FROM [bambi-bmi].[dbo].[customer]
WHERE custStatus = 1
GO

-- Untuk menampilkan data:
SELECT * FROM [dbo].[vw_AllCustomerActive];
