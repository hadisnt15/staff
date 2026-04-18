CREATE VIEW vdates AS 

WITH RECURSIVE dates AS (
    SELECT DATE('2026-01-01') AS tanggal
    UNION ALL
    SELECT tanggal + INTERVAL 1 DAY
    FROM dates
    WHERE tanggal < '2026-03-31'
)

SELECT
tanggal,
DAYNAME(tanggal) AS hari
FROM dates;