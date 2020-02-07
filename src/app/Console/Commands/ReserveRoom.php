<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Mail\ActivateMail;
use App\User;
use Carbon\Carbon;
use App\Operations;

class ReserveRoom extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:reserverooms';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $operations = Operations::where('operations_rooms_id',null)->get();
        

        \Mail::to($user)->send(new ActivateMail($user,'asdsakdjaslkjdsakljdlsakjds'));
    }
}
