<?php

namespace Database\Factories;

use App\Models\Complaint;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    private static array $publicComments = [
        'Terima kasih atas laporan anda. Kami sedang menyiasat perkara ini.',
        'Aduan anda telah diterima dan akan diproses dalam masa terdekat.',
        'Pasukan kami telah dihantar ke lokasi untuk pemeriksaan.',
        'Kerja-kerja baik pulih sedang dijalankan. Mohon bersabar.',
        'Masalah ini telah berjaya diselesaikan. Terima kasih atas kerjasama anda.',
        'Kami memohon maaf atas kesulitan yang dialami.',
        'Aduan ini sedang dalam proses siasatan lanjut.',
        'Pihak kami telah menghubungi kontraktor untuk kerja pembaikan.',
        'Pemeriksaan telah dilakukan dan tindakan segera akan diambil.',
        'Kerja-kerja pembersihan telah dijadualkan untuk minggu ini.',
    ];

    private static array $internalComments = [
        'Sila hubungi kontraktor Zone 3 untuk tindakan segera.',
        'Kes ini perlu dirujuk kepada Jabatan Kejuruteraan.',
        'Peruntukan bajet untuk kerja ini perlu diluluskan terlebih dahulu.',
        'Pegawai lapangan telah mengesahkan keterukan masalah ini.',
        'Kerja ini memerlukan kelulusan dari Pengarah Bahagian.',
        'Sila pastikan gambar sebelum dan selepas diambil untuk rekod.',
        'Kontraktor melaporkan kerja akan siap dalam 3 hari bekerja.',
        'Kes ini berkait rapat dengan aduan ADU-2024-00012, sila gabungkan.',
    ];

    public function definition(): array
    {
        $isInternal = fake()->boolean(30);

        return [
            'complaint_id' => Complaint::factory(),
            'user_id'      => User::factory(),
            'content'      => $isInternal
                ? fake()->randomElement(self::$internalComments)
                : fake()->randomElement(self::$publicComments),
            'is_internal'  => $isInternal,
        ];
    }

    public function public(): static
    {
        return $this->state(fn () => [
            'is_internal' => false,
            'content'     => fake()->randomElement(self::$publicComments),
        ]);
    }

    public function internal(): static
    {
        return $this->state(fn () => [
            'is_internal' => true,
            'content'     => fake()->randomElement(self::$internalComments),
        ]);
    }
}
