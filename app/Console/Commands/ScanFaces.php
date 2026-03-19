<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ScanFaces extends Command
{
    protected $signature = 'faces:scan
        {--tolerance=0.6 : Face match tolerance (lower = stricter)}
        {--frames=5 : Number of frames to extract per video}
        {--video-dir= : Custom video directory path}';

    protected $description = 'Scan videos for faces and group them by person';

    public function handle()
    {
        $scriptPath = base_path('scripts/scan_faces.py');
        $tolerance = $this->option('tolerance');
        $frames = $this->option('frames');
        $videoDir = $this->option('video-dir') ?: public_path('storage/video');

        $command = sprintf(
            'python "%s" --video-dir "%s" --tolerance %s --frames %s',
            $scriptPath,
            $videoDir,
            $tolerance,
            $frames
        );

        $this->info('Starting face scan...');
        $this->info("Running: {$command}");
        $this->newLine();

        $process = proc_open($command, [
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ], $pipes);

        if (is_resource($process)) {
            while ($line = fgets($pipes[1])) {
                $this->line(trim($line));
            }
            $errors = stream_get_contents($pipes[2]);
            if ($errors) {
                $this->error($errors);
            }
            fclose($pipes[1]);
            fclose($pipes[2]);
            $exitCode = proc_close($process);

            if ($exitCode === 0) {
                $this->newLine();
                $this->info('Face scan completed successfully!');
            } else {
                $this->error("Face scan failed with exit code: {$exitCode}");
            }

            return $exitCode;
        }

        $this->error('Failed to start the Python process.');
        return 1;
    }
}
