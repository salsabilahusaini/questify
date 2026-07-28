<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Mission;
use Illuminate\View\View;

class MissionController extends Controller
{
    public function __invoke(): View
    {
        $missions = Mission::query()
            ->where('is_published', true)
            ->orderBy('sequence')
            ->get();

        return view('student.missions.index', [
            'missions' => $missions,
        ]);
    }
}
