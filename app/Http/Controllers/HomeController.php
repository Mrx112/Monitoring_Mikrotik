<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use RouterOS\Client;
use RouterOS\Query;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;
use App\Models\User;
use App\Models\Device;
use Symfony\Component\HttpFoundation\StreamedResponse;

class HomeController extends Controller
{
    public function index(): Factory|View
    {
        $devices = Device::all();
        $deviceStatus = [];
        $primaryClient = null;

        foreach ($devices as $device) {
            try {
                $client = new Client([
                    'host' => $device->ip_address,
                    'user' => $device->username,
                    'pass' => $device->password,
                    'port' => $device->port ?? 8728,
                    'timeout' => 3
                ]);

                $client->query('/system/resource/print')->read();
                $device->status = 'online';

                if (!$primaryClient) {
                    $primaryClient = $client;
                }
            } catch (\Exception $e) {
                $device->status = 'offline';
            }

            $deviceStatus[] = $device;
        }

        if (!$primaryClient) {
            session()->flash('error', 'Tidak ada perangkat Mikrotik yang online.');
            return view('home', [
                'user' => [],
                'aktif' => [],
                'resource' => [],
                'trafic' => [],
                'log' => [],
                'totalUser' => 0,
                'totalAktif' => 0,
                'devices' => collect($deviceStatus),
                'deadDevices' => collect($deviceStatus)->filter(fn($d) => $d->status === 'offline')
            ]);
        }

        try {
            $user = $primaryClient->query('/ip/hotspot/user/print')->read();
            $aktif = $primaryClient->query('/ip/hotspot/active/print')->read();
            $resource = $primaryClient->query('/system/resource/print')->read();
            $trafic = $primaryClient->query([
                '/interface/monitor-traffic',
                '=interface=ether1',
                '=once='
            ])->read();
            $log = $primaryClient->query('/log/print')->read();
        } catch (\Exception $e) {
            $user = $aktif = $resource = $trafic = $log = [];
            session()->flash('error', 'Tidak dapat membaca data Mikrotik: ' . $e->getMessage());
        }

        $totalUser = is_array($user) ? count($user) : 0;
        $totalAktif = is_array($aktif) ? count($aktif) : 0;

        return view('home', [
            'user' => $user,
            'aktif' => $aktif,
            'resource' => $resource,
            'trafic' => $trafic,
            'log' => $log,
            'totalUser' => $totalUser,
            'totalAktif' => $totalAktif,
            'devices' => collect($deviceStatus),
            'deadDevices' => collect($deviceStatus)->filter(fn($d) => $d->status === 'offline')
        ]);
    }

    public function userList(Request $request): View
    {
        $query = User::query();
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('username', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            });
        }
        $users = $query->get();
        return view('user_list', compact('users'));
    }

    public function exportUserCsv(): StreamedResponse
    {
        $users = User::all();
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="users.csv"',
        ];
        $callback = function () use ($users) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Name', 'Username', 'Email', 'Created At']);
            foreach ($users as $user) {
                fputcsv($handle, [$user->id, $user->name, $user->username, $user->email, $user->created_at]);
            }
            fclose($handle);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function queueTree(): View
    {
        try {
            $client = new Client([
                'host' => '114.30.81.4',
                'user' => 'admin',
                'pass' => 'masuk.aja'
            ]);
            $queues = $client->query('/queue/tree/print')->read();
            $trafic = [];
            foreach ($queues as $queue) {
                if (!empty($queue['target'])) {
                    $trafic[$queue['target']] = $client->query([
                        '/interface/monitor-traffic',
                        '=interface=' . $queue['target'],
                        '=once='
                    ])->read();
                }
            }
        } catch (\Exception $e) {
            $queues = [];
            $trafic = [];
            session()->flash('error', 'Tidak dapat terhubung ke Mikrotik: ' . $e->getMessage());
        }

        return view('queue_tree', compact('queues', 'trafic'));
    }

    public function apiTraffic(Request $request)
    {
        $interface = $request->query('interface', 'ether1');

        $API = new \RouterOS\RouterosAPI();
        if ($API->connect(env('MIKROTIK_HOST'), env('MIKROTIK_USER'), env('MIKROTIK_PASS'))) {
            $response = $API->comm("/interface/monitor-traffic", [
                "interface" => $interface,
                "once" => ""
            ]);
            $API->disconnect();

            $rx = $response[0]['rx-bits-per-second'] ?? 0;
            $tx = $response[0]['tx-bits-per-second'] ?? 0;

            return response()->json([
                'rx' => (int)$rx,
                'tx' => (int)$tx
            ]);
        } else {
            return response()->json(['error' => 'Could not connect to Mikrotik'], 500);
        }
    }

    public function apiQueueStatus()
    {
        try {
            $client = new Client([
                'host' => '114.30.81.4',
                'user' => 'admin',
                'pass' => 'masuk.aja'
            ]);
            $queues = $client->query('/queue/tree/print')->read();
            $status = 'down';
            foreach ($queues as $queue) {
                if (isset($queue['disabled']) && $queue['disabled'] === 'false') {
                    $status = 'up';
                    break;
                }
            }
        } catch (\Exception $e) {
            $status = 'down';
        }
        return response()->json(['status' => $status]);
    }

    public function trafficChart(): View
    {
        return view('traffic_chart');
    }

    public function all(): \Illuminate\Http\JsonResponse
    {
        $client = new Client([
            'host' => '114.30.81.4',
            'user' => 'admin',
            'pass' => 'masuk.aja'
        ]);

        $interfaces = ['ether1', 'ether2', 'ether3', 'ether4', 'ether5', 'ether6', 'ether7', 'ether8', 'ether9', 'ether10'];
        $responses = [];

        foreach ($interfaces as $iface) {
            try {
                $query = new Query('/interface/monitor-traffic');
                $query->equal('interface', $iface)->equal('once', '');
                $result = $client->query($query)->read();

                if (isset($result[0])) {
                    $responses[$iface] = [
                        'rx' => (int) $result[0]['rx-bits-per-second'],
                        'tx' => (int) $result[0]['tx-bits-per-second'],
                    ];
                } else {
                    $responses[$iface] = ['rx' => 0, 'tx' => 0];
                }
            } catch (\Exception $e) {
                $responses[$iface] = ['rx' => 0, 'tx' => 0];
            }
        }

        return response()->json($responses);
    }
}
