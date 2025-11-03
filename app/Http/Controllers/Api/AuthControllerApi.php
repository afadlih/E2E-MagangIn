<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DosenModel;
use App\Models\UserModel;
use App\Models\MahasiswaModel;
use App\Models\ProdiModel;
use App\Models\LevelModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthControllerApi extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $user->load('level'); // Load relasi level

            // Buat token Sanctum (jika menggunakan Laravel Sanctum)
            $token = $user->createToken('auth_token')->plainTextToken;

            // Tentukan redirect URL berdasarkan level user
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
                'token' => $token,
                'user' => $user,
                'redirect' => $redirectUrl
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Username atau password salah'
        ], 401);
    }

    public function logout(Request $request)
    {
        // Hapus token Sanctum (jika menggunakan Laravel Sanctum)
        if ($request->user() && $request->user()->currentAccessToken()) {
            $request->user()->currentAccessToken()->delete();
        }

        return response()->json([
            'status' => true,
            'message' => 'Logout berhasil'
        ]);
    }

    public function registerMahasiswa(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:m_users,username',
            'password' => 'required',
            'mhs_nim' => 'required|unique:m_mahasiswa,mhs_nim',
            'full_name' => 'required',
            'alamat' => 'nullable',
            'telp' => 'nullable',
            'prodi_id' => 'required|exists:m_program_studi,prodi_id',
            'angkatan' => 'nullable|integer',
            'jenis_kelamin' => 'nullable|in:L,P',
            'ipk' => 'nullable|numeric|min:0|max:4.0',
            'status_magang' => 'required|in:Belum Magang,Sedang Magang, Selesai Magang',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = UserModel::create([
                'username' => $request->username,
                'password' => bcrypt($request->password),
                'level_id' => 3,
            ]);

            $mahasiswa = MahasiswaModel::create([
                'user_id' => $user->user_id,
                'mhs_nim' => $request->mhs_nim,
                'full_name' => $request->full_name,
                'alamat' => $request->alamat,
                'telp' => $request->telp,
                'prodi_id' => $request->prodi_id,
                'angkatan' => $request->angkatan,
                'jenis_kelamin' => $request->jenis_kelamin,
                'ipk' => $request->ipk,
                'status_magang' => $request->status_magang,
            ]);

            // Auto login setelah registrasi (opsional)
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'Registrasi mahasiswa berhasil',
                'token' => $token,
                'user' => $user,
                'mahasiswa' => $mahasiswa
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Registrasi gagal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function registerDosen(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:m_users,username',
            'password' => 'required|min:6',
            'nama' => 'required',
            'email' => 'required|email|unique:m_dosen,email',
            'telp' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = UserModel::create([
                'username' => $request->username,
                'password' => bcrypt($request->password),
                'level_id' => 2,
            ]);

            $dosen = DosenModel::create([
                'user_id' => $user->user_id,
                'nama' => $request->nama,
                'email' => $request->email,
                'telp' => $request->telp,
            ]);

            // Auto login setelah registrasi (opsional)
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'Registrasi dosen berhasil',
                'token' => $token,
                'user' => $user,
                'dosen' => $dosen
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Registrasi gagal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getUser(Request $request)
    {
        $user = $request->user();
        $user->load('level');

        // Load data tambahan berdasarkan level
        if ($user->level->level_name == 'mahasiswa') {
            $user->load('mahasiswa.prodi');
        } elseif ($user->level->level_name == 'dosen') {
            $user->load('dosen');
        }

        return response()->json([
            'status' => true,
            'user' => $user
        ]);
    }
}
