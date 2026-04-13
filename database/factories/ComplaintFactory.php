<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ComplaintFactory extends Factory
{
    private static int $sequence = 0;

    private static array $titles = [
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
        'Sisa pepejal dibuang merata-rata',
        'Kemudahan awam yang rosak di taman permainan',
        'Pencemaran sungai berhampiran kilang',
        'Sistem saliran yang tidak mencukupi',
        'Tandas awam dalam keadaan kotor',
        'Perparitan tersumbat di kawasan perniagaan',
        'Kerosakan pada kemudahan rekreasi awam',
    ];

    private static array $locations = [
        'Jalan Dato\' Keramat, Kuala Lumpur',
        'Taman Sri Muda, Shah Alam, Selangor',
        'Lorong Perak, Ipoh, Perak',
        'Persiaran Bestari, Putrajaya',
        'Jalan Putra, Kuala Lumpur',
        'Taman Maju, Johor Bahru, Johor',
        'Jalan Besar, Alor Setar, Kedah',
        'Kampung Baru, Kuala Lumpur',
        'Taman Desa, Kuala Lumpur',
        'Jalan Pahang, Kuala Lumpur',
        'Taman Bukit Indah, Ampang, Selangor',
        'Jalan Chow Kit, Kuala Lumpur',
        'Kawasan Perindustrian Prai, Pulau Pinang',
        'Taman Universiti, Skudai, Johor',
        'Jalan Tuanku Abdul Halim, Alor Setar',
    ];

    public function definition(): array
    {
        self::$sequence++;

        return [
            'aduan_no'    => sprintf('ADU-%d-%05d', 2024, self::$sequence),
            'user_id'     => User::factory()->complainant(),
            'category_id' => Category::inRandomOrder()->first()?->id ?? Category::factory(),
            'officer_id'  => null,
            'title'       => fake()->randomElement(self::$titles),
            'description' => fake()->paragraphs(2, true),
            'location'    => fake()->randomElement(self::$locations),
            'status'      => 'pending',
            'priority'    => fake()->randomElement(['low', 'medium', 'medium', 'high', 'urgent']),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => 'pending', 'officer_id' => null]);
    }

    public function inReview(): static
    {
        return $this->state(fn () => [
            'status'     => 'in_review',
            'officer_id' => User::where('role', 'officer')->inRandomOrder()->first()?->id,
        ]);
    }

    public function inProgress(): static
    {
        return $this->state(fn () => [
            'status'     => 'in_progress',
            'officer_id' => User::where('role', 'officer')->inRandomOrder()->first()?->id,
        ]);
    }

    public function resolved(): static
    {
        return $this->state(fn () => [
            'status'     => 'resolved',
            'officer_id' => User::where('role', 'officer')->inRandomOrder()->first()?->id,
        ]);
    }

    public function closed(): static
    {
        return $this->state(fn () => [
            'status'     => 'closed',
            'officer_id' => User::where('role', 'officer')->inRandomOrder()->first()?->id,
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn () => [
            'status'     => 'rejected',
            'officer_id' => User::where('role', 'officer')->inRandomOrder()->first()?->id,
        ]);
    }
}
