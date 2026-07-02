<?php

namespace App\Http\Controllers;

use App\Models\LogApi;
use App\Models\LogWebhook;

class LogApiController extends Controller
{
    public function index()
    {
        $logApis = LogApi::latest()->paginate(20);
        return view('log.api', compact('logApis'));
    }

    public function webhook()
    {
        $logWebhooks = LogWebhook::latest()->paginate(20);
        return view('log.webhook', compact('logWebhooks'));
    }
}
