<?php

namespace App\Console\Commands;

use App\Models\Teacher;
use Illuminate\Console\Command;

class teacherSalary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'teacher_salary';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'salary for teacher';

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
     * @return int
     */
    public function handle()
    {
        // $teacher = Teacher::firt();
        // $teacher->balance = 1000;
        // $teacher->save();

        Teacher::whereHas('Available_classes', function($query){
            $query->where('teacher_mony', 0)->where('status', 3);
        })
        ->chunk(30, function($data){
            dispatch(new teacherSalary($data));
        });
    }
}
