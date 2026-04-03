<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Music',            'icon' => 'music',      'color' => '#3B82F6'],
            ['name' => 'Business',         'icon' => 'briefcase',  'color' => '#1D4ED8'],
            ['name' => 'Technology',       'icon' => 'cpu',        'color' => '#2563EB'],
            ['name' => 'Arts & Culture',   'icon' => 'palette',    'color' => '#8B5CF6'],
            ['name' => 'Sports',           'icon' => 'trophy',     'color' => '#10B981'],
            ['name' => 'Food & Drink',     'icon' => 'utensils',   'color' => '#F59E0B'],
            ['name' => 'Fashion',          'icon' => 'shirt',      'color' => '#EC4899'],
            ['name' => 'Health & Wellness','icon' => 'heart',      'color' => '#EF4444'],
            ['name' => 'Education',        'icon' => 'book-open',  'color' => '#6366F1'],
            ['name' => 'Networking',       'icon' => 'users',      'color' => '#14B8A6'],
            ['name' => 'Comedy',           'icon' => 'laugh',      'color' => '#F97316'],
            ['name' => 'Religion',         'icon' => 'church',     'color' => '#A855F7'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(
                ['slug' => Str::slug($cat['name'])],
                array_merge($cat, ['slug' => Str::slug($cat['name']), 'is_active' => true])
            );
        }
    }
}
