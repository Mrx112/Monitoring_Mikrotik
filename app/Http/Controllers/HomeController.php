<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use RouterOS\Client;
use RouterOS\Query;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;
use App\Models\User;
use Symfony\Component\HttpFoundation\StreamedResponse;

class HomeController extends Controller
{
    public function index(): Factory|View
    {
        try {
            $client = new Client([
                'host' => '192.168.112.1',
                'user' => 'admin',
                'pass' => ''
            ]);

            $user = $client->query('/ip/hotspot/user/print')->read();
            $aktif = $client->query('/ip/hotspot/active/print')->read();
            $resource = $client->query('/system/resource/print')->read();
            $trafic = $client->query(['/interface/monitor-traffic', '=interface=ether1', '=once='])->read();
            $log = $client->query('/log/print')->read();
            $totalUser = is_array($user) ? count($user) : 0;
            $totalAktif = is_array($aktif) ? count($aktif) : 0;
        } catch (\Exception $e) {
            // Jika gagal koneksi Mikrotik, tampilkan dashboard kosong dengan pesan error
            $user = $aktif = $resource = $trafic = $log = [];
            $totalUser = $totalAktif = 0;
            session()->flash('error', 'Tidak dapat terhubung ke Mikrotik: ' . $e->getMessage());
        }

        // Contoh: Ambil data queue dari Mikrotik atau database
        // $queues = ...;
        // $trafic = ...;

        // Contoh dummy jika belum ada data asli:
        $queues = [
            [
                'name' => 'Host',
                'target' => 'ether1',
                'max-limit' => 'Unlimited',
                'comment' => 'Sample',
            ],
            [
                'name' => 'User1',
                'target' => 'ether2',
                'max-limit' => 'Unlimited',
                'comment' => 'Sample 2',
            ],
            [
                'name' => 'User2',
                'target' => 'ether3',
                'max-limit' => 'Unlimited',
                'comment' => 'Sample 3',
            ],
        ];
        $trafic = [
            'ether1' => [[
                'rx-bits-per-second' => 1000000,
                'tx-bits-per-second' => 500000,
                'status' => 'up'
            ]],
            'ether2' => [[
                'rx-bits-per-second' => 2000000,
                'tx-bits-per-second' => 1000000,
                'status' => 'down'
            ]],
            'ether3' => [[
                'rx-bits-per-second' => 2000000,
                'tx-bits-per-second' => 1000000,
                'status' => 'down'
            ]],
        ];

        // Kirim ke view
        return view('home', compact('totalUser', 'totalAktif', 'resource', 'trafic', 'log', 'queues'));
    }

    public function userList(Request $request): View
    {
        $query = User::query();
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
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
        $callback = function() use ($users) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Name', 'Username', 'Email', 'Created At']);
            foreach ($users as $user) {
                fputcsv($handle, [$user->id, $user->name, $user->username, $user->email, $user->created_at]);
            }
            fclose($handle);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function queueList(): View
    {
        try {
            $client = new Client([
                'host' => '192.168.112.1',
                'user' => 'admin',
                'pass' => ''
            ]);
            $queues = $client->query('/queue/simple/print')->read();
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
        return view('queue_list', compact('queues', 'trafic'));
    }

    public function apiTraffic(Request $request)
    {
        $interface = $request->query('interface', 'ether1');
        try {
            $client = new Client([
                'host' => '192.168.112.1',
                'user' => 'admin',
                'pass' => ''
            ]);
            $trafic = $client->query([
                '/interface/monitor-traffic',
                '=interface=' . $interface,
                '=once='
            ])->read();
            $rx = isset($trafic[0]['rx-bits-per-second']) ? $trafic[0]['rx-bits-per-second'] : 0;
            $tx = isset($trafic[0]['tx-bits-per-second']) ? $trafic[0]['tx-bits-per-second'] : 0;
        } catch (\Exception $e) {
            $rx = 0;
            $tx = 0;
        }
        return response()->json([
            'rx' => $rx,
            'tx' => $tx
        ]);
    }

    // API untuk status queue (sidebar)
    public function apiQueueStatus()
    {
        try {
            $client = new Client([
                'host' => '192.168.112.1',
                'user' => 'admin',
                'pass' => ''
            ]);
            $queues = $client->query('/queue/simple/print')->read();
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
}