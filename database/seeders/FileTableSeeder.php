<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\File;

// use FFMpeg\Coordinate\TimeCode;
// use FFMpeg\FFProbe;
// use FFMpeg\FFMpeg;


class FileTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $ffprobe = FFProbe::create();
        // $ffmpeg = FFMpeg::create();

        $thumbnail = 'storage/app/public/media/videos/1907632_AppDemo.jpg';
                
        // $duration = $ffprobe->format('storage/app/public/media/videos/1907632_AppDemo.mp4')->get('duration');

        // $video = $ffmpeg->open('storage/app/public/media/videos/1907632_AppDemo.mp4');
        // $video->frame(TimeCode::fromSeconds(floor($duration/2)))->save($thumbnail);


        $file = new File;
        $file->path = "storage/media/videos/1907632_AppDemo.mp4";
        $file->type = 'video';
        $file->url = "http://127.0.0.1:8080/media/videos/1907632_AppDemo.mp4";
        $file->title = "1907632_AppDemo.mp4";
        $file->original_title = "1907632_AppDemo.mp4";
        $file->thumbnail = url($thumbnail);
        $file->user_id = 1;
        $file->save();
    }
}
