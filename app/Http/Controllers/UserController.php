<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;
use Illuminate\Routing\Redirector;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use \RouterOS\Client;
use \RouterOS\Query;

class UserController extends Controller
{

    public function index(): Factory|View {
        $client = new Client(config: [
            'host' => '114.30.81.4',
            'user' => 'admin',
            'pass' => 'masuk.aja'
        ]);

        $data = $client->query(endpoint: '/ip/hotspot/user/print')->read();
        $user = collect(value: $data)->except(keys: ['0'])->toArray();
        $aktif = $client->query(endpoint: '/ip/hotspot/active/print')->read();

        return view('user', compact('user', 'aktif'));

    }

    public function add(): Factory|View {
        $client = new Client(config: [
            'host' => '114.30.81.4',
            'user' => 'admin',
            'pass' => 'masuk.aja'
        ]);
        $profile = $client->query(endpoint: '/ip/hotspot/user/profile/print')->read();
        // dd($profle);
        return view(view: 'addUser', data: compact(var_name: 'profile'));
    }

    public function store(Request $request): Redirector|RedirectResponse {
        $client = new Client(config: [
            'host' => '114.30.81.4',
            'user' => 'admin',
            'pass' => 'masuk.aja'
        ]);

        // Simpan ke database Laravel (lengkap sesuai struktur tabel)
        \App\Models\User::create([
            'name' => $request->name ?? $request->username ?? 'user',
            'username' => $request->username ?? ('user'.Str::random(5)),
            'email' => $request->email ?? (($request->username ?? 'user').Str::random(5).'@local'),
            'email_verified_at' => now(),
            'password' => bcrypt($request->password),
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'remember_token' => Str::random(10),
        ]);

        // Tambahkan juga ke Mikrotik
        $client->query(endpoint: [
            '/ip/hotspot/user/add',
            '=name='.$request->username,
            '=password='.$request->password
        ])->read();

        return redirect(to: '/user');
    }

    public function quick(): Redirector|RedirectResponse {
        $client = new Client(config: [
            'host' => '114.30.81.4',
            'user' => 'admin',
            'pass' => 'masuk.aja'
        ]);

        for($i = 0; $i <= 200; $i++) {
            $password = Str::random(length: 5);
            $username = Str::random(length: 2) . "-lugaru";
            $client->query(endpoint: ['/ip/hotspot/user/add', '=name='.$username, '=password='.$password])->read();
        }

        return redirect(to: '/user');
    }

    public function destroy($id): Redirector|RedirectResponse {
        $client = new Client(config: [
            'host' => '114.30.81.4',
            'user' => 'admin',
            'pass' => 'masuk.aja'
        ]);
        $client->query(endpoint: ['/ip/hotspot/user/remove', '=.id='.$id])->read();

        return redirect(to: '/user');
    }

    public function deleteAll(Request $request): Redirector|RedirectResponse {
        $client = new Client(config: [
            'host' => '114.30.81.4',
            'user' => 'admin',
            'pass' => 'masuk.aja'
        ]);
        // Hapus semua user Mikrotik
        $users = $client->query(endpoint: '/ip/hotspot/user/print')->read();
        foreach ($users as $user) {
            if (isset($user['.id'])) {
                $client->query(endpoint: ['/ip/hotspot/user/remove', '=.id='.$user['.id']])->read();
            }
        }
        // Hapus semua user di database Laravel
        \App\Models\User::truncate();
        return redirect('/user')->with('success', 'Semua user berhasil dihapus!');
    }

    public function mikrotikUserList()
    {
        // Ambil data user dari Mikrotik API
        // Contoh: $users = $mikrotik->comm('/user/print');
        // Untuk demo, gunakan array dummy:
        $users = [
            ['name' => 'admin', 'group' => 'full', 'address' => '192.168.88.1', 'last-logged-in' => '2025-07-05 12:00:00', 'disabled' => 'false'],
            ['name' => 'operator', 'group' => 'read', 'address' => '192.168.88.2', 'last-logged-in' => '2025-07-04 09:00:00', 'disabled' => 'true'],
        ];
        return view('user_list', compact('users'));
    }
}

?>
