<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DeleteEmptyStorageDirectories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storageDirectories:empty';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete empty directories contained in the \'storage\' folder';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $directories = Storage::allDirectories();

        foreach ($directories as $directory) {

            if (empty($directory)) {
                Storage::deleteDirectory($directory);
            }
        }
    }
}
