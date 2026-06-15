<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class FingerprintController extends Controller
{
    public function check(Request $request)
    {
        $request->validate([
            'username'  => 'required|string',
            'pass'      => 'required|string',
            'action'    => 'nullable|string',
            'device_fp' => 'nullable|string',
        ]);

        $email  = $request->input('username');
        $pass   = $request->input('pass');
        $action = $request->input('action');
        $fp     = $request->input('device_fp') ?? $request->cookie('device_fp');

        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json([
                'status'  => 'invalid',
                'message' => 'Email tidak ditemukan.',
            ], 401);
        }

        if (!Hash::check($pass, $user->password)) {
            return response()->json([
                'status'  => 'invalid',
                'message' => 'Email atau password salah.',
            ], 401);
        }

        // Kasus 1: fingerprint sama → langsung boleh login
        if ($fp !== null && $fp === $user->fingerprint_device) {
            return response()->json([
                'status' => 'ok',
                'valid'  => true,
            ], 200);
        }

        // Kasus 2: belum ada fingerprint di DB → simpan, langsung boleh login
        if ($user->fingerprint_device === null) {
            $user->update([
                'fingerprint_device' => $fp,
                'action'             => 'first_login',
            ]);

            return response()->json([
                'status' => 'ok',
                'valid'  => true,
            ], 200);
        }

        // Kasus 3: fp beda, user pilih "Lanjutkan login"
        // → hapus session lama di DB, update fp baru
        // → device lama akan ter-logout saat request berikutnya (session tidak valid)
        if ($action === 'keep') {
            // Ambil session_id lama sebelum di-overwrite
            $oldSessionId = $user->session_id;

            // Hapus session lama dari tabel sessions Laravel
            if ($oldSessionId) {
                DB::table('sessions')->where('id', $oldSessionId)->delete();
            }

            // Update fp dan kosongkan session_id lama
            $user->update([
                'fingerprint_device' => $fp,
                'action'             => 'keep',
                'session_id'         => null,
            ]);

            return response()->json([
                'status' => 'ok',
                'valid'  => true,
            ], 200);
        }

        // Kasus 4: fp beda, belum ada keputusan → tampilkan modal konflik
        return response()->json([
            'status' => 'conflict',
            'valid'  => false,
        ], 200);
    }
}