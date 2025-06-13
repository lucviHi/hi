<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room; // Import model Room

class LayoutController extends Controller
{
    public function index()
    {
        $rooms = Room::all(); // Lấy danh sách kênh từ database
        return view('layouts.app', compact('rooms'));
    }
}
