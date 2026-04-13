<?php

namespace Database\Factories;

use App\Models\Complaint;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttachmentFactory extends Factory
{
    private static array $imageTypes = [
        ['mime' => 'image/jpeg', 'ext' => 'jpg'],
        ['mime' => 'image/png',  'ext' => 'png'],
        ['mime' => 'image/webp', 'ext' => 'webp'],
    ];

    private static array $docTypes = [
        ['mime' => 'application/pdf', 'ext' => 'pdf'],
        ['mime' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'ext' => 'docx'],
    ];

    public function definition(): array
    {
        $fileType = fake()->randomElement([...self::$imageTypes, ...self::$imageTypes, ...self::$docTypes]);
        $fileName = fake()->uuid() . '.' . $fileType['ext'];

        return [
            'complaint_id' => Complaint::factory(),
            'file_path'    => 'attachments/' . date('Y/m') . '/' . $fileName,
            'file_name'    => 'bukti_' . fake()->numberBetween(1, 999) . '.' . $fileType['ext'],
            'mime_type'    => $fileType['mime'],
            'file_size'    => fake()->numberBetween(50_000, 5_000_000),
        ];
    }

    public function image(): static
    {
        return $this->state(function () {
            $fileType = fake()->randomElement(self::$imageTypes);
            $fileName = fake()->uuid() . '.' . $fileType['ext'];

            return [
                'file_path' => 'attachments/' . date('Y/m') . '/' . $fileName,
                'file_name' => 'gambar_' . fake()->numberBetween(1, 999) . '.' . $fileType['ext'],
                'mime_type' => $fileType['mime'],
                'file_size' => fake()->numberBetween(100_000, 3_000_000),
            ];
        });
    }
}
