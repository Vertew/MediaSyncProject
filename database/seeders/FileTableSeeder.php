<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\File;

class FileTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $file = new File;
        $file->path = "storage/media/videos/LuxeaRec2023-01-01_12-58-08.mp4";
        $file->type = 'video';
        $file->url = "http://127.0.0.1:8080/media/videos/LuxeaRec2023-01-01_12-58-08.mp4";
        $file->title = "LuxeaRec2023-01-01_12-58-08.mp4";
        $file->user_id = 1;
        $file->save();
    }
}
