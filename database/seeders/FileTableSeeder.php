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
        $file->path = "storage/media/videos/1907632_AppDemo.mp4";
        $file->type = 'video';
        $file->url = "http://127.0.0.1:8080/media/videos/1907632_AppDemo.mp4";
        $file->title = "1907632_AppDemo.mp4";
        $file->original_title = "1907632_AppDemo.mp4";
        $file->user_id = 1;
        $file->save();
    }
}
