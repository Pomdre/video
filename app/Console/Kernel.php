<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->call(function () {
             $basename = [];
             $extension = [];
             $filename = [];
             $path = public_path('storage/video');
             $files = \File::allFiles($path);
         
             foreach($files as $file) {
                // dd(pathinfo($file));
                array_push($basename, pathinfo($file)['basename']);
                // array_push($extension, pathinfo($file)['extension']);
                array_push($filename, pathinfo($file)['filename']);
             }

             $arrayLength = count($filename);
             $i = 0;
             while ($i < $arrayLength)
             {
                // error_log($fileNames[$i]);
                // Save contents to DB;
                \DB::table('files')->updateorinsert([
                    'basename' => $basename[$i],
                   // 'extension' => $extension[$i],
                    'filename' => $filename[$i],
                    'gif' => $filename[$i] . '.gif',
                ]);
                 $i++;
             }
        })->everyMinute();


    //     $schedule->call(function () {
    //         $result = \DB::table('files')->get();
    //         foreach($result as $r){
    //             $video = \DB::table('files')->where('basename', $r->basename)->get();
    //             if ($video[0]->static == null) {
    //                 \FFMpeg::fromDisk('video')
    //                 ->open($video[0]->basename)
    //                 ->getFrameFromSeconds(10)
    //                 ->resize(1080, 608)
    //                 ->export()
    //                 ->onProgress(function ($percentage) {
    //                     error_log("{$percentage}% transcoded");
    //                 })
    //                 ->toDisk('static')
    //                 ->save($video[0]->filename . '.png');
    //                 \DB::table('files')->where('basename', $r->basename)->update(['static' => $video[0]->filename . '.png', 'gif' => $video[0]->filename . '.gif']);
    //             }
    //             else {
    //             error_log($video[0]->basename . ' <--Eksisterer');
    //             }
    //         }
    //    })->everyMinute();
        
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
