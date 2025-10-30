<?php 
 
namespace App\Http\Controllers;

use Illuminate\Http\Request; 
use App\Models\DosenModel;
use App\Models\UserModel;
use App\Models\MahasiswaModel;
use App\Models\ProdiModel;
use App\Models\LevelModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller 
{ 
    public function login() 
    { 
        if(Auth::check()){ // jika sudah login, maka redirect ke halaman home 
            return redirect('/'); 
        } 
        return view('auth.login'); 
    } 
 
    public function postlogin(Request $request) 
    { 
        if ($request->ajax() || $request->wantsJson()) { 
            $credentials = $request->only('username', 'password'); 

            if (Auth::attempt($credentials)) { 
                $user = Auth::user();

                // Redirect berdasarkan level
                switch ($user->level->level_name) {
                    case 'admin':
                        $redirectUrl = url('/dashboard-admin');
                        break;
                    case 'dosen':
                        $redirectUrl = url('/dashboard-dosen');
                        break;
                    case 'mahasiswa':
                        $redirectUrl = url('/dashboard-mahasiswa');
                        break;
                    default:
                        $redirectUrl = url('/dashboard');
                }

                return response()->json([ 
                    'status' => true, 
                    'message' => 'Login Berhasil', 
                    'redirect' => $redirectUrl 
                ]); 
            }

            return response()->json([ 
                'status' => false, 
                'message' => 'Login Gagal' 
            ]); 
        }

        return redirect('login'); 
    }
 
 
    public function logout(Request $request) 
    { 
        Auth::logout(); 
 
        $request->session()->invalidate(); 
        $request->session()->regenerateToken();     
        return redirect('login'); 
    } 

    public function registerMahasiswa()
    {
        $prodis = ProdiModel::all(); // ambil semua data program studi
        return view('register.register_mhs', compact('prodis'));
    }

    public function storeMahasiswa(Request $request)
    {
        // Ubah koma ke titik agar bisa divalidasi sebagai angka desimal
        //$request->merge([
            //'//ipk' => str_replace(',', '.', $request->ipk)
        //]);

        // Gabungkan semua validasi dalam satu langkah
        $validated = $request->validate([
            'username' => 'required|unique:m_users,username',
            'password' => 'required',
            'mhs_nim' => 'required|unique:m_mahasiswa,mhs_nim',
            'full_name' => 'required',
            'alamat' => 'nullable',
            'telp' => 'nullable',
            'prodi_id' => 'required',
            'angkatan' => 'nullable|integer',
            'jenis_kelamin' => 'nullable|in:L,P',
            'ipk' => 'nullable|numeric|min:0|max:4.0',
            'status_magang' => 'required',
        ]);

        // Setelah semua validasi berhasil, baru simpan ke database

        $user = UserModel::create([
            'username' => $validated['username'],
            'password' => bcrypt($validated['password']),
            'level_id' => 3,
        ]);

        MahasiswaModel::create([
            'user_id' => $user->user_id,
            'mhs_nim' => $validated['mhs_nim'],
            'full_name' => $validated['full_name'],
            'alamat' => $validated['alamat'] ?? null,
            'telp' => $validated['telp'] ?? null,
            'prodi_id' => $validated['prodi_id'],
            'angkatan' => $validated['angkatan'] ?? null,
            'jenis_kelamin' => $validated['jenis_kelamin'] ?? null,
            'ipk' => $validated['ipk'] ?? null,
            'status_magang' => $validated['status_magang'],
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Registrasi Anda Berhasil'
        ]);
    }


    public function registerDosen()
    {
        return view('register.register_dsn');
    }

    public function storeDosen(Request $request)
    {
        // Gabungkan semua validasi di awal
        $validated = $request->validate([
            'username' => 'required|unique:m_users,username',
            'password' => 'required',
            'nama' => 'required',
            'email' => 'required|email|unique:m_dosen,email',
            'telp' => 'nullable',
        ]);

        // Setelah validasi berhasil, baru simpan data ke tabel
        $user = UserModel::create([
            'username' => $validated['username'],
            'password' => bcrypt($validated['password']),
            'level_id' => 2, // level untuk dosen
        ]);

        DosenModel::create([
            'user_id' => $user->user_id,
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'telp' => $validated['telp'] ?? null,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Registrasi Anda Berhasil'
        ]);
    }

} 