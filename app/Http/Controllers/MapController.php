<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class MapController extends Controller
{
    public function index()
    {
        $devices = Device::all();
        return view('map', compact('devices'));
    }

    public function getStatus()
    {
        $data = Device::all()->map(function ($d) {
            $status = exec("ping -c 1 -W 1 " . $d->ip_address, $out, $code) === false ? false : ($code === 0);
            return [
                'id' => $d->id,
                'status' => $status ? 'Hidup' : 'Mati'
            ];
        });

        return Response::json($data);
    }

    public function getGlobalTraffic()
    {
        require_once base_path('vendor/routeros_api/routeros_api.class.php');

        $totalRx = 0;
        $totalTx = 0;

        foreach (Device::all() as $device) {
            $API = new \RouterosAPI();
            if ($API->connect($device->ip_address, $device->username, $device->password, $device->port)) {
                $interfaces = $API->comm("/interface/monitor-traffic", ["interface" => "ether1", "once" => ""]);
                $rx = $interfaces[0]['rx-bits-per-second'] ?? 0;
                $tx = $interfaces[0]['tx-bits-per-second'] ?? 0;
                $totalRx += $rx;
                $totalTx += $tx;
                $API->disconnect();
            }
        }

        return response()->json([
            'rx' => round($totalRx / 1024, 2), // KBps
            'tx' => round($totalTx / 1024, 2)
        ]);
    }

    public function getTraffic($id)
    {
        $device = Device::findOrFail($id);
        require_once base_path('vendor/routeros_api/routeros_api.class.php');

        $API = new \RouterosAPI();
        $API->debug = false;

        if ($API->connect($device->ip_address, $device->username, $device->password, $device->port)) {
            $interfaces = $API->comm("/interface/monitor-traffic", [
                "interface" => "ether1",
                "once" => "",
            ]);

            $API->disconnect();

            $rx = $interfaces[0]['rx-bits-per-second'] ?? 0;
            $tx = $interfaces[0]['tx-bits-per-second'] ?? 0;

            return response()->json([
                'rx' => round($rx / 1024, 2), // KBps
                'tx' => round($tx / 1024, 2)
            ]);
        }

        return response()->json(['rx' => 0, 'tx' => 0]);
    }
}
