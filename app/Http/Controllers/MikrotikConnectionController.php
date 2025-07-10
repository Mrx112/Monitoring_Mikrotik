<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use RouterOS\Client;
use RouterOS\Query;

class MikrotikConnectionController extends Controller
{
    /** Tampilkan form & (jika ada) hasil koneksi */
    public function index()
    {
        // data hasil print dititipkan di session agar tidak hilang saat refresh
        $connections = session('mk_connections', []);
        return view('connections.index', compact('connections'));
    }

    /** Proses login & ambil /ip/firewall/connection */
    public function login(Request $request)
    {
        $creds = $request->validate([
            'host'     => 'required|ip',
            'user'     => 'required|string',
            'password' => 'required|string',
        ]);

        try {
            $client = new Client($creds + ['timeout' => 5]);
            $query  = new Query('/ip/firewall/connection/print');
            $connections = $client->query($query)->read();

            // Simpan di session supaya bisa dipakai saat remove
            session([
                'mk_host'        => $creds['host'],
                'mk_user'        => $creds['user'],
                'mk_password'    => $creds['password'],
                'mk_connections' => $connections,
            ]);

            return redirect()->route('connections.index');
        } catch (\Throwable $e) {
            return back()->withErrors(['login' => 'Login gagal: '.$e->getMessage()]);
        }
    }

    /** Hapus koneksi berdasarkan .id Mikrotik */
    public function destroy($id)
    {
        // Ambil data koneksi dari session
        if (!session()->has('mk_host')) {
            return redirect()->route('connections.index')
                             ->withErrors('Silakan login dahulu.');
        }

        try {
            $client = new Client([
                'host'     => session('mk_host'),
                'user'     => session('mk_user'),
                'pass'     => session('mk_password'),
                'timeout'  => 5,
            ]);

            $remove = new Query('/ip/firewall/connection/remove');
            $remove->equal('.id', $id);
            $client->query($remove)->read();

            // Segarkan daftar
            $query  = new Query('/ip/firewall/connection/print');
            $connections = $client->query($query)->read();
            session(['mk_connections' => $connections]);

            return back()->with('success', 'Koneksi berhasil di‑remove.');
        } catch (\Throwable $e) {
            return back()->withErrors('Gagal remove: '.$e->getMessage());
        }
    }
}
?>