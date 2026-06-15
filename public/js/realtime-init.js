// resources/js/realtime-init.js
// atau embed langsung di blade layout (app.blade.php / dashboard.blade.php)

(function () {
    // Ambil fingerprint dari cookie
    function getFingerprint() {
        const match = document.cookie.match(/device_fp=([^;]+)/);
        return match ? match[1] : null;
    }

    // Ambil email user yang sedang login (di-pass dari blade)
    const currentUserEmail = document.querySelector('meta[name="user-email"]')
        ? document.querySelector('meta[name="user-email"]').content
        : null;

    const currentFp = getFingerprint();

    if (!currentUserEmail || !currentFp) {
        console.warn('[RealtimeMonitor] Email atau fingerprint tidak ditemukan, monitoring dilewati.');
        return;
    }

    // ============================================================
    // Notifikasi toast sebelum logout
    // ============================================================
    function showLogoutToast(message) {
        // Cek apakah sudah ada toast
        if (document.getElementById('logout-toast')) return;

        const toast = document.createElement('div');
        toast.id = 'logout-toast';
        toast.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 99999;
            background: #ef4444;
            color: white;
            padding: 16px 20px;
            border-radius: 8px;
            font-size: 14px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            max-width: 320px;
            line-height: 1.5;
        `;
        toast.innerHTML = `
            <strong>⚠️ Sesi Diakhiri</strong><br>
            ${message}
        `;
        document.body.appendChild(toast);
    }

    // ============================================================
    // Fungsi logout otomatis
    // ============================================================
    function autoLogout(reason) {
        showLogoutToast(reason || 'Akun ini login di perangkat lain. Anda akan dikeluarkan.');

        // Tunggu 2 detik agar user sempat membaca notifikasi
        setTimeout(function () {
            // POST ke route logout Laravel Breeze
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/logout';

            const csrfInput = document.createElement('input');
            csrfInput.type  = 'hidden';
            csrfInput.name  = '_token';
            csrfInput.value = document.querySelector('meta[name="csrf-token"]')
                ? document.querySelector('meta[name="csrf-token"]').content
                : '';

            form.appendChild(csrfInput);
            document.body.appendChild(form);
            form.submit();
        }, 2000);
    }

    // ============================================================
    // Start RealtimeMonitor
    // ============================================================
    const monitor = new RealtimeMonitor({
        baseUrl: window.location.origin + '/',
        user: currentUserEmail,

        onConnected: function () {
            console.log('[RealtimeMonitor] Terhubung ke server SSE.');
        },

        onDisconnected: function () {
            console.warn('[RealtimeMonitor] Koneksi SSE terputus, mencoba reconnect...');
        },

        // ← INI YANG PENTING: dipanggil saat fingerprint di DB berubah
        onUpdateAttendance: function (attendance) {
            console.log('[RealtimeMonitor] Perubahan terdeteksi:', attendance);

            // Pastikan ini untuk user yang sedang login
            if (attendance.email === currentUserEmail) {
                // Fingerprint di DB sudah diganti → ada login baru di device lain
                autoLogout('Akun Anda baru saja login di perangkat lain. Sesi ini diakhiri untuk keamanan.');
            }
        },

        onNewAttendance: function (attendance) {
            console.log('[RealtimeMonitor] Attendance baru:', attendance);
        },

        onError: function (error) {
            console.error('[RealtimeMonitor] Error:', error);
        },
    });

    monitor.start();

    console.log('[RealtimeMonitor] Monitoring dimulai untuk:', currentUserEmail);
})();