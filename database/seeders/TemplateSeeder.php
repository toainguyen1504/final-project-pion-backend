<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Template;

class TemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templateList = [
            ['name' => 'Giao diện Cổ điển', 'slug' => 'classic', 'css_class' => 'template-classic', 'view_path' => 'templates.classic'],
            ['name' => 'Giao diện Tối giản', 'slug' => 'minimal', 'css_class' => 'template-minimal', 'view_path' => 'templates.minimal'],
            ['name' => 'Giao diện Landing', 'slug' => 'landing', 'css_class' => 'template-landing', 'view_path' => 'templates.landing'],
            ['name' => 'Giao diện Card', 'slug' => 'carded', 'css_class' => 'template-carded', 'view_path' => 'templates.carded'],
            ['name' => 'Giao diện Nổi bật', 'slug' => 'highlight', 'css_class' => 'template-highlight', 'view_path' => 'templates.highlight'],
            ['name' => 'Giao diện Blog chuyên sâu', 'slug' => 'longread', 'css_class' => 'template-longread', 'view_path' => 'templates.longread'],
        ];

        foreach ($templateList as $data) {
            Template::firstOrCreate(
                ['slug' => $data['slug']],
                array_merge($data, ['is_active' => true])
            );
        }
    }
}
