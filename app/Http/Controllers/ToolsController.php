<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class ToolsController extends Controller
{
    // Show ping form
    public function pingForm()
    {
        return view('tools.ping');
    }

    // Handle ping request
    public function ping(Request $request)
    {
        $request->validate([
            'host' => 'required|string',
        ]);
        $host = escapeshellarg($request->input('host'));
        $output = shell_exec("ping -c 4 $host");
        return view('tools.ping', [
            'output' => $output,
            'host' => $request->input('host'),
        ]);
    }

    // Show access link monitoring (dummy for now)
    public function accessLink()
    {
        // Dummy data, replace with real monitoring logic
        $links = [
            ['name' => 'Link A', 'status' => 'up', 'latency' => '2ms'],
            ['name' => 'Link B', 'status' => 'down', 'latency' => '-'],
        ];
        return view('tools.access_link', compact('links'));
    }
}
