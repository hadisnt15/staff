<?php

use Livewire\Component;

new class extends Component
{
    
};
?>

<div>
    <div x-data="{ showRule: true }">
        <div class="flex justify-end mb-1">
            <button @click="showRule = true" class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-700 hover:text-emerald-900 transition">
                <i class="ri-information-2-fill"></i> Panduan Penggunaan
            </button>
        </div>
            <div x-show="showRule" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" style="display: none;">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] flex flex-col">
                <div class="flex items-center justify-between border-b px-6 py-4">
                    <h2 class="text-lg font-semibold">Panduan Penggunaan</h2>
                    <button @click="showRule = false" class="text-gray-500 hover:text-black text-xl">✕</button>
                </div>
                <div class="p-4 overflow-y-auto text-sm space-y-5">
                    <div>
                        <h3 class="font-semibold mb-1">Prasyarat</h3>
                        <p class="px-1">
                            Sebelum menggunakan sistem absensi, karyawan wajib melakukan registrasi wajah terlebih dahulu. Absensi hanya dapat dilakukan setelah registrasi wajah disetujui oleh admin. <a href="{{ route('face-registration') }}" class="text-emerald-600 font-semibold">Registrasikan Wajah</a>
                        </p>
                        <h3 class="font-semibold mb-1 mt-4">Pengenalan Fitur</h3>
                        <p class="px-1">
                            Sistem absensi menyediakan 4 buah fitur tombol, yaitu Absen Masuk, Absen Keluar, Absen Luar Kota, dan Absen Tidak Hadir.
                        </p>
                        <ol class="list-decimal ml-5 space-y-1">
                            <li>
                                <i>Absen Masuk</i> digunakan untuk mencatat absen <i>datang</i>, <i>mulai istirahat</i>, dan <i>mulai izin keluar</i>.
                            </li>
                            <li>
                                <i>Absen Keluar</i> digunakan untuk mencatat absen <i>pulang</i>, <i>selesai istirahat</i>, dan <i>selesai izin keluar</i>.
                            </li>
                            <li>
                                <i>Absen Luar Kota</i> digunakan untuk mencatat absensi karyawan yang bertugas ke luar kota.
                            </li>
                            <li>
                                <i>Absen Tidak Hadir</i> digunakan untuk mencatat absensi karyawan yang berhalangan hadir bekerja.
                            </li>
                        </ol>
                        <h3 class="font-semibold mb-1 mt-4">Aturan Absensi</h3>
                        <ol class="list-decimal ml-5 space-y-1">
                            <li>Absen datang wajib dilakukan sebelum jam kerja dimulai, yaitu pada jam {{ auth()->user()->branch->work_start_time->format('H.i') }}. Absen setelah jam kerja akan tercatat sebagai terlambat.</li>
                            <li>Absen istirahat hanya dapat dilakukan pada jam istirahat yang telah ditentukan, yaitu pada jam {{ auth()->user()->branch->break_start_time->format('H.i') }} s/d {{ auth()->user()->branch->break_end_time->format('H.i') }}. Absen di luar jam tersebut akan tercatat sebagai istirahat tidak tepat waktu.</li>
                            <li>Khusus hari Jumat, karyawan yang melaksanakan salat Jumat diperbolehkan melakukan absen istirahat di luar jam istirahat dengan durasi maksimal 60 menit. Lebih dari durasi tersebut maka juga akan tercatat sebagai istirahat tidak tepat waktu.</li>
                            <li>Absen izin keluar wajib disertai dengan keterangan.</li>
                            <li>Absen istirahat dan absen izin keluar masing-masing hanya dapat dilakukan satu kali dalam satu hari.</li>
                            <li>Absen pulang wajib dilakukan setelah selesai bekerja.</li>
                            <li>Absen luar kota hanya diperuntukkan bagi karyawan yang menjalankan tugas luar kota lebih dari satu hari.</li>
                            <li>Pada hari keberangkatan, karyawan wajib melakukan absen datang, kemudian absen luar kota sebelum berangkat, serta mengisi keterangan luar kota.</li>
                            <li>Absen tidak hadir wajib dilakukan sesegera mungkin pada pagi hari dan disertai dengan keterangan alasan ketidakhadiran.</li>
                        </ol>
                        <h3 class="font-semibold mb-1 mt-4">Rencana Cuti</h3>
                        <p class="px-1">
                            Disarankan mengajukan rencana cuti jauh hari sebelum tanggal pelaksanaan untuk memudahkan proses perencanaan dan persetujuan. <a href="{{ route('leave-plan') }}" class="text-emerald-600 font-semibold">Ajukan Rencana Cuti</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>