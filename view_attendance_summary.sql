ALTER VIEW vattendance_summaries AS 

SELECT
	T0.id_pengguna, 
	T0.nama_pengguna, 
	T0.tanggal, 
	T0.hari,
	T0.tanggal_merah,
			
	T0.jam_datang,
	T0.durasi_telat_datang,
	T0.ket_datang,
	T0.poin_datang,
		
	T0.jam_pulang,
	T0.durasi_duluan_pulang,
	T0.ket_pulang,
	T0.poin_pulang,
			
	T0.jam_mulai_istirahat,
	T0.jam_selesai_istirahat,
	T0.durasi_istirahat,
	T0.ket_istirahat,
	T0.poin_istirahat,
	
	T0.jam_mulai_izin,
	T0.jam_selesai_izin,
	T0.durasi_izin,
	T0.ket_izin,
	T0.poin_izin,
	
	T0.ket_luar_kota,
	T0.ket_tidak_hadir,
	
	T0.poin_kehadiran,
	REPLACE(REPLACE(T0.ket_kehadiran, ' .', ''), '. ','') AS ket_kehadiran,
	T0.ket_kehadiran_rekap
FROM (
	SELECT
		T0.*,
		CASE WHEN T0.poin_kehadiran = 7 THEN 'Tidak hadir tanpa konfirmasi.'
			WHEN T0.poin_kehadiran=  6 THEN CONCAT('Tidak hadir dengan konfirmasi:', T0.ket_tidak_hadir,'.')
			WHEN T0.poin_kehadiran = 0 THEN 'Kehadiran terpenuhi'
			WHEN T0.poin_kehadiran = 4 THEN CONCAT(T0.ket_luar_kota, '. ', T0.ket_datang,'.')
			WHEN T0.poin_kehadiran = 5 THEN CONCAT(T0.ket_luar_kota,'.')
			WHEN T0.poin_kehadiran BETWEEN 0.5 AND 3 THEN CONCAT(T0.ket_datang, '. ', T0.ket_pulang, '. ', T0.ket_istirahat, '. ', T0.ket_izin)
			ELSE '' END AS ket_kehadiran,
		CASE WHEN T0.poin_kehadiran = 7 THEN 'Tidak hadir tanpa konfirmasi'
			WHEN T0.poin_kehadiran=  6 THEN 'Tidak hadir dengan konfirmasi'
			WHEN T0.poin_kehadiran = 0 THEN 'Tehadiran terpenuhi'
			WHEN T0.poin_kehadiran = 4 THEN 'Luar kota, tapi tidak lengkap'
			WHEN T0.poin_kehadiran = 5 THEN 'Luar kota'
			WHEN T0.poin_kehadiran BETWEEN 0.5 AND 3 THEN 'Hadir, tapi tidak lengkap'
			ELSE '' END AS ket_kehadiran_rekap
	FROM (
		SELECT
			T0.id_pengguna, 
			T0.nama_pengguna, 
			T0.tanggal, 
			T0.hari,
			T0.tanggal_merah,
			
			T0.jam_datang,
			T0.durasi_telat_datang,
			T0.ket_datang,
			T0.poin_datang,
		
			T0.jam_pulang,
			T0.durasi_duluan_pulang,
			T0.ket_pulang,
			T0.poin_pulang,
			
			T0.jam_mulai_istirahat,
			T0.jam_selesai_istirahat,
			T0.durasi_istirahat,
			T0.ket_istirahat,
			T0.poin_istirahat,
			
			T0.jam_mulai_izin,
			T0.jam_selesai_izin,
			T0.durasi_izin,
			T0.ket_izin,
			T0.poin_izin,
			
			T0.ket_luar_kota,
			T0.ket_tidak_hadir,
		
			CASE
		   	WHEN T0.ket_luar_kota IS NOT NULL AND T0.poin_datang = 0 THEN 5
		   	WHEN T0.ket_luar_kota IS NOT NULL AND T0.poin_datang BETWEEN 0.5 AND 1 THEN 4
				WHEN T0.ket_tidak_hadir IS NOT NULL THEN 6
				WHEN T0.jam_datang IS NULL AND T0.jam_pulang IS NULL THEN 7
				ELSE (T0.poin_datang + T0.poin_pulang + T0.poin_istirahat + T0.poin_izin)
				END AS poin_kehadiran
		FROM (
			SELECT
				T0.id_pengguna, 
				T0.nama_pengguna, 
				T0.tanggal, 
				T0.hari,
				T0.tanggal_merah,
				
				T1.jam_datang,
				T1.durasi_telat_datang,
				CASE WHEN T1.durasi_telat_datang > 0 THEN 1 WHEN T1.jam_datang IS NULL AND CURDATE() = T1.tanggal AND CURTIME() <= T1.aturan_mulai_kerja THEN 0
						WHEN T1.jam_datang IS NULL THEN 0.5 ELSE 0 END AS poin_datang,
				CASE WHEN T1.jam_datang IS NULL AND CURDATE() = T1.tanggal AND CURTIME() <= T1.aturan_mulai_kerja THEN ''
						WHEN T1.jam_datang IS NULL THEN 'Absen datang tidak ada.' ELSE 
					(CASE WHEN T1.durasi_telat_datang > 0 THEN CONCAT('Datang telat ', T1.durasi_telat_datang, ' menit.') ELSE '' END) END AS ket_datang,
				
				T1.jam_pulang,
				T1.durasi_duluan_pulang,
				CASE WHEN T1.durasi_duluan_pulang > 0 THEN 1 WHEN T1.jam_pulang IS NULL AND CURDATE() = T1.tanggal AND CURTIME() <= T1.aturan_selesai_kerja THEN 0 
						WHEN T1.jam_pulang IS NULL THEN 0.5 ELSE 0 END AS poin_pulang,
				CASE WHEN T1.jam_pulang IS NULL AND CURDATE() = T1.tanggal AND CURTIME() <= T1.aturan_selesai_kerja THEN ''
						WHEN T1.jam_pulang IS NULL THEN 'Absen pulang tidak ada.' ELSE 
					(CASE WHEN T1.durasi_duluan_pulang > 0 THEN CONCAT('Pulang duluan ', T1.durasi_duluan_pulang, ' menit.') ELSE '' END) END AS ket_pulang,
				
				T1.jam_mulai_istirahat,
				T1.jam_selesai_istirahat,
				T1.durasi_istirahat,
				CASE
                    WHEN T1.jam_mulai_istirahat IS NULL AND T1.jam_selesai_istirahat IS NOT NULL THEN 0.5
                    WHEN T1.jam_mulai_istirahat IS NOT NULL AND T1.jam_selesai_istirahat IS NULL THEN 0.5
                    WHEN DAYOFWEEK(T0.tanggal) = 6 AND T1.durasi_istirahat > 60 THEN 1
                    WHEN DAYOFWEEK(T0.tanggal) <> 6 AND (
                        T1.jam_mulai_istirahat < T1.aturan_mulai_istirahat OR T1.jam_selesai_istirahat > T1.aturan_selesai_istirahat
                    ) THEN 1
                    ELSE 0
                END AS poin_istirahat,
				CASE
                    WHEN T1.jam_mulai_istirahat IS NULL AND T1.jam_selesai_istirahat IS NOT NULL THEN 'Absen istirahat tidak lengkap.'
                    WHEN T1.jam_mulai_istirahat IS NOT NULL AND T1.jam_selesai_istirahat IS NULL THEN 'Absen istirahat tidak lengkap.'
                    WHEN DAYOFWEEK(T0.tanggal) = 6 AND T1.durasi_istirahat > 60 THEN 'Durasi istirahat melebihi 60 menit'
                    WHEN DAYOFWEEK(T0.tanggal) <> 6 AND (
                        T1.jam_mulai_istirahat < T1.aturan_mulai_istirahat OR T1.jam_selesai_istirahat > T1.aturan_selesai_istirahat
                    ) THEN 'Istirahat tidak tepat waktu.'
                    ELSE ''
                END AS ket_istirahat,
            T1.jam_mulai_izin,
				T1.jam_selesai_izin,
				T1.durasi_izin,
				CASE 
					WHEN T1.jam_mulai_izin IS NULL AND T1.jam_selesai_izin IS NOT NULL THEN 0.5
               WHEN T1.jam_mulai_izin IS NOT NULL AND T1.jam_selesai_izin IS NULL THEN 0.5
					WHEN T1.durasi_izin <= 60 THEN 0.5 WHEN T1.durasi_izin > 60 THEN 1 ELSE 0 END poin_izin,
				CASE 
					WHEN T1.jam_mulai_izin IS NULL AND T1.jam_selesai_izin IS NOT NULL THEN 'Absen izin tidak lengkap.'
               WHEN T1.jam_mulai_izin IS NOT NULL AND T1.jam_selesai_izin IS NULL THEN 'Absen izin tidak lengkap.'
					WHEN T1.durasi_izin <= 60 THEN 'Izin di bawah 1 jam.' WHEN T1.durasi_izin > 60 THEN 'Izin di atas 1 jam.' ELSE '' END ket_izin,
					
				T1.ket_luar_kota,
				T1.ket_tidak_hadir
			FROM (
				SELECT T0.*, T1.id AS id_pengguna, T1.`name` AS nama_pengguna, IFNULL(T2.holiday_note,'Hari Kerja') AS tanggal_merah
				FROM vdates T0
				CROSS JOIN users T1 
				LEFT JOIN holidays T2 ON T2.holiday_date = T0.tanggal
				ORDER BY T1.name, T1.id
			) T0
			LEFT JOIN (
				SELECT
					T0.id_pengguna, 
					T0.nama_pengguna, 
					T0.tanggal, 
					T0.hari, 
					T0.aturan_mulai_kerja,
					T0.aturan_selesai_kerja,
					T0.aturan_selesai_kerja_sabtu,
					T0.aturan_mulai_istirahat,
					T0.aturan_selesai_istirahat,
					 
					T0.jam_datang, 
					CASE WHEN T0.jam_datang > T0.aturan_mulai_kerja THEN TIMESTAMPDIFF(MINUTE, T0.aturan_mulai_kerja, T0.jam_datang) ELSE 0 END AS durasi_telat_datang,
					T0.jam_pulang,
					CASE WHEN T0.jam_pulang < (CASE WHEN DAYOFWEEK(T0.tanggal) = 7 THEN T0.aturan_selesai_kerja ELSE T0.aturan_selesai_kerja END) 
						THEN TIMESTAMPDIFF(MINUTE, T0.jam_pulang, (CASE WHEN DAYOFWEEK(T0.tanggal) = 7 THEN T0.aturan_selesai_kerja ELSE T0.aturan_selesai_kerja END))+1 
						ELSE 0 END AS durasi_duluan_pulang,
					T0.jam_mulai_istirahat,
					T0.jam_selesai_istirahat,
					TIMESTAMPDIFF(MINUTE, T0.jam_mulai_istirahat, T0.jam_selesai_istirahat) AS durasi_istirahat,
					T0.jam_mulai_izin,
					T0.jam_selesai_izin,
					TIMESTAMPDIFF(MINUTE, T0.jam_mulai_izin, T0.jam_selesai_izin) AS durasi_izin,
					T0.ket_luar_kota,
					T0.ket_tidak_hadir
				FROM (
					SELECT
						T0.user_id AS id_pengguna,
						T1.`name` AS nama_pengguna,
						DATE(T0.attendance_datetime) AS tanggal,
						DAYNAME(DATE(T0.attendance_datetime)) AS hari,
						T2.work_start_time AS aturan_mulai_kerja,
						T2.work_end_time AS aturan_selesai_kerja,
						T2.work_end_time_weekend AS aturan_selesai_kerja_sabtu,
						T2.break_start_time AS aturan_mulai_istirahat,
						T2.break_end_time AS aturan_selesai_istirahat,
			
						MIN(CASE WHEN T0.attendance_type = 'absen_masuk' AND T0.attendance_break = 0 AND T0.attendance_permission = 0 THEN TIME(T0.attendance_datetime) END) AS jam_datang,
						MAX(CASE WHEN T0.attendance_type = 'absen_keluar' AND T0.attendance_break = 0 AND T0.attendance_permission = 0 THEN TIME(T0.attendance_datetime) END) AS jam_pulang,
						MIN(CASE WHEN T0.attendance_type = 'absen_keluar' AND T0.attendance_break = 1 THEN TIME(T0.attendance_datetime) END) AS jam_mulai_istirahat,
						MAX(CASE WHEN T0.attendance_type = 'absen_masuk' AND T0.attendance_break = 1 THEN TIME(T0.attendance_datetime) END) AS jam_selesai_istirahat,
						MIN(CASE WHEN T0.attendance_type = 'absen_keluar' AND T0.attendance_permission = 1 THEN TIME(T0.attendance_datetime) END) AS jam_mulai_izin,
						MAX(CASE WHEN T0.attendance_type = 'absen_masuk' AND T0.attendance_permission = 1 THEN TIME(T0.attendance_datetime) END) AS jam_selesai_izin,
						MAX(CASE WHEN T0.attendance_type = 'luar_kota' THEN TIME(T0.attendance_datetime) END) AS luar_kota,
						MAX(CASE WHEN T0.attendance_type = 'luar_kota' THEN CONCAT('[',T0.attendance_note,']') END) AS ket_luar_kota,
						MAX(CASE WHEN T0.attendance_type = 'tidak_hadir' THEN TIME(T0.attendance_datetime) END) AS tidak_hadir,
						MAX(CASE WHEN T0.attendance_type = 'tidak_hadir' THEN CONCAT('[',T0.attendance_note,']') END) AS ket_tidak_hadir
					FROM attendances T0
					JOIN users T1 ON T1.id = T0.user_id
					JOIN branches T2 ON T2.id = T1.branch_id
					GROUP BY T0.user_id, T1.name, DATE(T0.attendance_datetime), DAYNAME(DATE(T0.attendance_datetime))
				) T0
			) T1 ON T1.tanggal = T0.tanggal AND T1.id_pengguna = T0.id_pengguna
		) T0
	) T0
) T0
-- WHERE T0.tanggal = '2026-03-18'