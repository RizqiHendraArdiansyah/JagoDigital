<?php

namespace App\Controllers;

use App\Models\MemberModels;

class Login extends BaseController
{
    public function index()
    {
        // Pengecekan jika pengguna sudah login
        if (session()->get('logged_in')) {
            // Mendapatkan role pengguna
            $role = session()->get('role');

            // Mengarahkan berdasarkan role pengguna
            if ($role === 'admin') {
                return redirect()->to(base_url('admin/dashboard'));
            } elseif ($role === 'user') {
                return redirect()->to(base_url('/pengumuman')); // Atur URL untuk halaman user
            } else {
                // Jika role tidak dikenali, arahkan ke halaman login atau error
                return redirect()->to(base_url('/login'));
            }
        }

        // Tampilkan halaman login jika pengguna belum login
        return view('admin/login/index');
    }

    public function process()
    {
        $users = new MemberModels();
        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');
        $dataUser = $users->where('username', $username)->first();

        if ($dataUser) {
            // Periksa kata sandi
            if ($password === $dataUser->password) {
                // Kata sandi cocok, set session dan role
                session()->set([
                    'username' => $dataUser->username,
                    'id_member' => $dataUser->id_member,
                    'logged_in' => true,
                    'role' => $dataUser->role  // Anda perlu memiliki kolom 'role' pada tabel pengguna
                ]);

                // Redirect sesuai dengan peran (role)
                if ($dataUser->role == 'admin') {
                    return redirect()->to(base_url('admin'));
                } elseif ($dataUser->role == 'user') {
                    return redirect()->to(base_url('/pengumuman')); // Sesuaikan dengan URL halaman user
                }
            } else {
                session()->setFlashdata('error', 'Username & Password Salah');
                return redirect()->to(base_url('login'));
            }
        } else {
            session()->setFlashdata('error', 'Username & Password Salah');
            return redirect()->to(base_url('login'));
        }
    }


    public function logout()
    {
        // Hapus semua data sesi dan arahkan ke halaman login
        session()->destroy();
        return redirect()->to('/login');
    }
}
