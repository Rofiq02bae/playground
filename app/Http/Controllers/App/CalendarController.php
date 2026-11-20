<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Support\CalendarData;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $month = CalendarData::parseMonth($request->query('bulan'));

        return view('app.calendar.index', CalendarData::forMonth($request->user(), $month));
    }
}
