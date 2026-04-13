<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name'        => 'Jalan Raya & Lebuh Raya',
                'description' => 'Aduan berkaitan jalan berlubang, kerosakkan permukaan jalan, papan tanda rosak dan sebarang isu jalan raya.',
                'is_active'   => true,
            ],
            [
                'name'        => 'Kebersihan & Pengurusan Sisa',
                'description' => 'Aduan berkaitan pengutipan sampah, pelupusan sisa haram, kawasan kotor dan kebersihan persekitaran.',
                'is_active'   => true,
            ],
            [
                'name'        => 'Infrastruktur & Pembetungan',
                'description' => 'Aduan berkaitan longkang tersumbat, parit rosak, sistem pembetungan dan isu infrastruktur awam.',
                'is_active'   => true,
            ],
            [
                'name'        => 'Taman & Landskap',
                'description' => 'Aduan berkaitan kemudahan taman awam, pokok tumbang, rumput tidak dipotong dan landskap persekitaran.',
                'is_active'   => true,
            ],
            [
                'name'        => 'Bangunan & Premis',
                'description' => 'Aduan berkaitan bangunan terbiar, pembinaan haram, premis perniagaan yang melanggar undang-undang.',
                'is_active'   => true,
            ],
            [
                'name'        => 'Alam Sekitar & Pencemaran',
                'description' => 'Aduan berkaitan pencemaran air, udara, bunyi bising dan sebarang isu alam sekitar.',
                'is_active'   => true,
            ],
            [
                'name'        => 'Lampu Jalan & Utiliti',
                'description' => 'Aduan berkaitan lampu jalan rosak, bekalan elektrik awam, kemudahan utiliti yang tidak berfungsi.',
                'is_active'   => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
