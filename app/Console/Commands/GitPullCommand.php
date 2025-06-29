<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class GitPullCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'git:pull';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perform a Git pull from the specified branch';

    /**
     * Execute the console command.
     */
    public function handle()
    {
      /*  $branch = $this->ask('Enter the branch to pull from (default: main)', 'main');

        $this->info("Pulling changes from the '$branch' branch...");*/
        $branch='main';
        // Run the Git pull command
        $process = new Process(['git', 'pull', 'origin', $branch]);
        $process->setWorkingDirectory(base_path()); // Set the Laravel project root as the working directory

        try {
            $process->mustRun();

            $this->info($process->getOutput());
        } catch (ProcessFailedException $exception) {
            $this->error('Git pull failed:');
            $this->error($exception->getMessage());
        }
    }
}
