<?php

namespace Database\Seeders;

use App\Models\Attachment;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Complaint;
use App\Models\Notification;
use App\Models\StatusLog;
use App\Models\User;
use Illuminate\Database\Seeder;

class ComplaintSeeder extends Seeder
{
    // Status transition chain for realistic progressions
    private array $statusFlow = [
        'pending'     => [],
        'in_review'   => [['old' => 'pending',   'new' => 'in_review',   'remarks' => 'Aduan telah diterima dan sedang disemak.']],
        'in_progress' => [['old' => 'pending',   'new' => 'in_review',   'remarks' => 'Aduan telah diterima dan sedang disemak.'],
                          ['old' => 'in_review',  'new' => 'in_progress', 'remarks' => 'Kerja-kerja pembaikan sedang dijalankan.']],
        'resolved'    => [['old' => 'pending',   'new' => 'in_review',   'remarks' => 'Aduan telah diterima dan sedang disemak.'],
                          ['old' => 'in_review',  'new' => 'in_progress', 'remarks' => 'Kerja-kerja pembaikan sedang dijalankan.'],
                          ['old' => 'in_progress','new' => 'resolved',    'remarks' => 'Kerja-kerja pembaikan telah selesai.']],
        'closed'      => [['old' => 'pending',   'new' => 'in_review',   'remarks' => 'Aduan telah diterima dan sedang disemak.'],
                          ['old' => 'in_review',  'new' => 'in_progress', 'remarks' => 'Kerja-kerja pembaikan sedang dijalankan.'],
                          ['old' => 'in_progress','new' => 'resolved',    'remarks' => 'Kerja-kerja pembaikan telah selesai.'],
                          ['old' => 'resolved',   'new' => 'closed',      'remarks' => 'Aduan ditutup setelah pengesahan pengadu.']],
        'rejected'    => [['old' => 'pending',   'new' => 'in_review',   'remarks' => 'Aduan telah diterima dan sedang disemak.'],
                          ['old' => 'in_review',  'new' => 'rejected',    'remarks' => 'Aduan ditolak kerana maklumat tidak mencukupi atau di luar bidang kuasa.']],
    ];

    private array $notificationMessages = [
        'pending'     => 'Aduan anda telah berjaya dihantar dan sedang menunggu semakan.',
        'in_review'   => 'Aduan anda sedang disemak oleh pegawai yang berkenaan.',
        'in_progress' => 'Kerja-kerja untuk menangani aduan anda sedang dijalankan.',
        'resolved'    => 'Aduan anda telah berjaya diselesaikan.',
        'closed'      => 'Aduan anda telah ditutup. Terima kasih atas kerjasama anda.',
        'rejected'    => 'Aduan anda telah ditolak. Sila hubungi pejabat untuk maklumat lanjut.',
    ];

    public function run(): void
    {
        $officers     = User::where('role', 'officer')->get();
        $complainants = User::where('role', 'complainant')->get();
        $categories   = Category::all();

        // Distribution: 8 pending, 5 in_review, 7 in_progress, 10 resolved, 5 closed, 5 rejected
        $distribution = [
            'pending'     => 8,
            'in_review'   => 5,
            'in_progress' => 7,
            'resolved'    => 10,
            'closed'      => 5,
            'rejected'    => 5,
        ];

        $counter = 1;

        foreach ($distribution as $status => $count) {
            for ($i = 0; $i < $count; $i++) {
                $complainant = $complainants->random();
                $officer     = in_array($status, ['pending']) ? null : $officers->random();

                $complaint = Complaint::create([
                    'aduan_no'    => sprintf('ADU-%d-%05d', 2024, $counter++),
                    'user_id'     => $complainant->id,
                    'category_id' => $categories->random()->id,
                    'officer_id'  => $officer?->id,
                    'title'       => $this->randomTitle(),
                    'description' => $this->randomDescription(),
                    'location'    => $this->randomLocation(),
                    'status'      => $status,
                    'priority'    => $this->randomPriority(),
                    'created_at'  => now()->subDays(rand(1, 60)),
                ]);

                // Status logs — replay the full transition chain
                foreach ($this->statusFlow[$status] as $transition) {
                    StatusLog::create([
                        'complaint_id' => $complaint->id,
                        'changed_by'   => ($officer ?? $officers->random())->id,
                        'old_status'   => $transition['old'],
                        'new_status'   => $transition['new'],
                        'remarks'      => $transition['remarks'],
                        'created_at'   => $complaint->created_at->addDays(rand(1, 3)),
                    ]);
                }

                // Notifications for complainant on each status transition
                $notifyStatuses = $status === 'pending'
                    ? ['pending']
                    : array_column($this->statusFlow[$status], 'new');

                foreach ($notifyStatuses as $notifyStatus) {
                    Notification::create([
                        'user_id'      => $complainant->id,
                        'complaint_id' => $complaint->id,
                        'type'         => 'status_updated',
                        'message'      => $this->notificationMessages[$notifyStatus]
                            ?? "Status aduan {$complaint->aduan_no} telah dikemaskini.",
                        'is_read'      => in_array($status, ['resolved', 'closed', 'rejected']),
                        'created_at'   => $complaint->created_at->addDays(rand(1, 5)),
                    ]);
                }

                // Comments (1–3 per complaint, skip pending with no officer)
                $commentCount = rand(1, 3);
                if ($status === 'pending') {
                    $commentCount = rand(0, 1);
                }

                for ($c = 0; $c < $commentCount; $c++) {
                    $isInternal = $officer && fake()->boolean(30);
                    Comment::create([
                        'complaint_id' => $complaint->id,
                        'user_id'      => $isInternal ? $officer->id : $complainant->id,
                        'content'      => $isInternal
                            ? fake()->randomElement($this->internalComments())
                            : fake()->randomElement($this->publicComments()),
                        'is_internal'  => $isInternal,
                        'created_at'   => $complaint->created_at->addDays(rand(1, 10)),
                    ]);
                }

                // Attachments — 60% of complaints get 1–2 attachments
                if (fake()->boolean(60)) {
                    $attachmentCount = rand(1, 2);
                    for ($a = 0; $a < $attachmentCount; $a++) {
                        $ext      = fake()->randomElement(['jpg', 'png', 'pdf']);
                        $mime     = match ($ext) {
                            'jpg'   => 'image/jpeg',
                            'png'   => 'image/png',
                            'pdf'   => 'application/pdf',
                        };
                        $fileName = fake()->uuid() . '.' . $ext;

                        Attachment::create([
                            'complaint_id' => $complaint->id,
                            'file_path'    => 'attachments/' . $complaint->created_at->format('Y/m') . '/' . $fileName,
                            'file_name'    => 'bukti_' . ($a + 1) . '.' . $ext,
                            'mime_type'    => $mime,
                            'file_size'    => rand(50_000, 5_000_000),
                            'created_at'   => $complaint->created_at,
                        ]);
                    }
                }
            }
        }
    }

    private function randomTitle(): string
    {
        $titles = [
            'Jalan berlubang merbahaya di kawasan perumahan',
            'Sampah tidak dikutip selama lebih 5 hari',
            'Longkang tersumbat menyebabkan banjir kilat',
            'Pokok tumbang menghalang laluan awam',
            'Lampu jalan tidak berfungsi sejak seminggu',
            'Air bertakung di tepi jalan selepas hujan',
            'Bau busuk dari longkang yang tersumbat',
            'Haiwan liar berkeliaran di kawasan perumahan',
            'Bangunan terbiar dalam keadaan berbahaya',
            'Gangguan bunyi bising daripada premis berdekatan',
            'Papan tanda jalan rosak dan tidak jelas',
            'Rumput panjang di tepi jalan tidak dipotong',
            'Parit yang rosak menyebabkan hakisan tanah',
            'Sisa pepejal dibuang merata-rata di kawasan awam',
            'Kemudahan awam rosak di taman permainan',
            'Pencemaran sungai berhampiran kilang',
            'Sistem saliran yang tidak mencukupi',
            'Tandas awam dalam keadaan sangat kotor',
            'Perparitan tersumbat di kawasan perniagaan',
            'Kerosakan pada gelanggang sukan awam',
        ];

        return fake()->randomElement($titles);
    }

    private function randomDescription(): string
    {
        $descriptions = [
            'Masalah ini telah berlaku sejak beberapa minggu yang lalu dan menyebabkan kesulitan kepada penghuni kawasan ini. Kami berharap pihak berkuasa dapat mengambil tindakan segera untuk mengatasi masalah ini sebelum ia menjadi lebih serius.',
            'Keadaan ini amat membimbangkan dan mengancam keselamatan orang ramai, terutamanya kanak-kanak dan warga emas. Mohon pihak bertanggungjawab segera mengambil tindakan.',
            'Saya telah cuba menghubungi pihak berkuasa tempatan beberapa kali namun tiada tindakan yang diambil. Masalah ini telah menjejaskan kualiti hidup kami di kawasan ini.',
            'Isu ini berlaku hampir setiap hari dan semakin teruk apabila musim hujan tiba. Penduduk kawasan ini sangat terjejas dan memerlukan penyelesaian segera.',
            'Keadaan ini telah dilaporkan oleh beberapa orang penduduk kawasan ini. Kami berharap pihak bertanggungjawab dapat menangani isu ini dengan segera demi keselamatan dan keselesaan semua pihak.',
        ];

        return fake()->randomElement($descriptions);
    }

    private function randomLocation(): string
    {
        $locations = [
            'Jalan Dato\' Keramat, Kuala Lumpur',
            'Taman Sri Muda, Shah Alam, Selangor',
            'Lorong Perak, Ipoh, Perak',
            'Persiaran Bestari, Putrajaya',
            'Jalan Putra, Kuala Lumpur',
            'Taman Maju, Johor Bahru, Johor',
            'Jalan Besar, Alor Setar, Kedah',
            'Kampung Baru, Kuala Lumpur',
            'Taman Desa, Kuala Lumpur',
            'Taman Bukit Indah, Ampang, Selangor',
            'Jalan Chow Kit, Kuala Lumpur',
            'Kawasan Perindustrian Prai, Pulau Pinang',
            'Taman Universiti, Skudai, Johor',
        ];

        return fake()->randomElement($locations);
    }

    private function randomPriority(): string
    {
        return fake()->randomElement(['low', 'medium', 'medium', 'medium', 'high', 'urgent']);
    }

    private function publicComments(): array
    {
        return [
            'Aduan anda telah diterima dan akan diproses dalam masa terdekat.',
            'Pasukan kami telah dihantar ke lokasi untuk pemeriksaan.',
            'Kerja-kerja baik pulih sedang dijalankan. Mohon bersabar.',
            'Masalah ini telah berjaya diselesaikan. Terima kasih.',
            'Kami memohon maaf atas kesulitan yang dialami.',
            'Aduan ini sedang dalam proses siasatan lanjut.',
            'Pihak kami telah menghubungi kontraktor untuk kerja pembaikan.',
            'Kerja-kerja pembersihan telah dijadualkan untuk minggu ini.',
            'Saya ingin menambah maklumat — keadaan semakin teruk selepas hujan semalam.',
            'Masalah masih belum diselesaikan. Harap dapat dipercepatkan.',
        ];
    }

    private function internalComments(): array
    {
        return [
            'Sila hubungi kontraktor Zone 3 untuk tindakan segera.',
            'Kes ini perlu dirujuk kepada Jabatan Kejuruteraan.',
            'Peruntukan bajet untuk kerja ini perlu diluluskan terlebih dahulu.',
            'Pegawai lapangan telah mengesahkan keterukan masalah ini.',
            'Kerja ini memerlukan kelulusan dari Pengarah Bahagian.',
            'Sila pastikan gambar sebelum dan selepas diambil untuk rekod.',
            'Kontraktor melaporkan kerja akan siap dalam 3 hari bekerja.',
        ];
    }
}
