<?php

namespace App\Http\Controllers;

use App\Models\Bus;
use App\Models\Student;
use Illuminate\Http\Request;

class SchoolDashboardController extends Controller {
    public function index() {
        // جلب الإحصائيات الفورية لإرسالها للوحة القيادة
        $activeBusesCount = Bus::where('status', 'active')->count();
        $totalStudents = Student::count();
        $delayedBusesCount = Bus::where('status', 'delayed')->count();

        return view('dashboard', compact('activeBusesCount', 'totalStudents', 'delayedBusesCount'));
    }
}