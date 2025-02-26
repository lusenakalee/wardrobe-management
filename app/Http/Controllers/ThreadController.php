<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ThreadController extends Controller
{
    public function index()
    {
        $threads = Thread::get();
        return Inertia::render('Thread/Index' , [
            'data'=>$threads
        ]);
    }
}