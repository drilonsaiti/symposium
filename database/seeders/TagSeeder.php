<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            'Laravel',
            'PHP',
            'JavaScript',
            'DevOps',
            'Cloud',
            'APIs',
            'Security',
            'Testing',
            'Databases',
            'AI & Machine Learning',
        ];

        foreach ($tags as $tag) {
            Tag::firstOrCreate([
                'name' => $tag,
            ]);
        }
    }
}
