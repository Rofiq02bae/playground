<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Beranda webapp pencatatan (mobile view).
     * Menu lengkap (profil sawah, pencatatan, kalender, laporan, surat)
     * dibangun bertahap; fondasi role & aksesnya mulai dari sini.
     */
    public function index()
    {
        return view('app.home', [
            'user' => Auth::user(),
        ]);
    }
}
