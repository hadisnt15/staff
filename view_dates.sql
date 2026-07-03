CREATE OR REPLACE VIEW vdates AS

WITH RECURSIVE dates AS (
    SELECT DATE('2026-07-01') AS tanggal

    UNION ALL

    SELECT tanggal + INTERVAL 1 DAY
    FROM dates
    WHERE tanggal < CURDATE()
)

SELECT
    tanggal,
    DAYNAME(tanggal) AS hari
FROM dates;