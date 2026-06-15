(function (global) {
    class RealtimeMonitor {
        constructor(options = {}) {
            this.baseUrl = options.baseUrl || window.location.origin + "/";
            this.onNewAttendance = options.onNewAttendance || null;
            this.onUpdateAttendance = options.onUpdateAttendance || null;
            this.onError = options.onError || null;
            this.onConnected = options.onConnected || null;
            this.onDisconnected = options.onDisconnected || null;
            this.user = options.user || null;
            this.eventSource = null;
            this.lastCheck = Math.floor(Date.now() / 1000);
            this.reconnectDelay = 3000; // 3 detik
            this.maxReconnectDelay = 30000; // 30 detik
            this.isConnected = false;
        }

        /**
         * Mulai monitoring
         */
        start() {
            this.connect();
        }

        /**
         * Koneksi ke SSE endpoint
         */
        connect() {
            const url = `${this.baseUrl}api/realtime/attendance-stream?user=${encodeURIComponent(this.user)}`;

            this.eventSource = new EventSource(url);

            // Event: Koneksi terbuka
            this.eventSource.onopen = () => {
                this.isConnected = true;
                this.reconnectDelay = 3000; // Reset delay
                console.log("[SSE] Connected to server");

                if (this.onConnected) {
                    this.onConnected();
                }
            };

            // Event: Menerima data
            this.eventSource.onmessage = (e) => {
                if (e.data.trim() === "") return; // Skip heartbeat

                try {
                    const data = JSON.parse(e.data);
                    this.lastCheck = data.timestamp;

                    // Handle perubahan
                    this.handleChanges(data.changes);
                } catch (error) {
                    console.error("[SSE] Parse error:", error);
                }
            };

            // Event: Error atau koneksi terputus
            this.eventSource.onerror = (error) => {
                console.error("[SSE] Connection error:", error);
                this.isConnected = false;

                if (this.onDisconnected) {
                    this.onDisconnected();
                }

                // Tutup koneksi dan reconnect
                this.eventSource.close();
                this.reconnect();

                if (this.onError) {
                    this.onError(error);
                }
            };

            // Event: Server minta reconnect
            this.eventSource.addEventListener("close", () => {
                console.log("[SSE] Server requested reconnect");
                this.eventSource.close();
                this.reconnect();
            });
        }

        /**
         * Reconnect dengan exponential backoff
         */
        reconnect() {
            console.log(`[SSE] Reconnecting in ${this.reconnectDelay}ms...`);

            setTimeout(() => {
                this.connect();
            }, this.reconnectDelay);

            // Tingkatkan delay untuk next reconnect
            this.reconnectDelay = Math.min(
                this.reconnectDelay * 1.5,
                this.maxReconnectDelay,
            );
        }

        /**
         * Handle perubahan dari server
         */
        handleChanges(changes) {
            if (!changes) return;

            // Handle attendance baru
            if (changes.new_attendances && this.onNewAttendance) {
                changes.new_attendances.forEach((attendance) => {
                    this.onNewAttendance(attendance);
                });
            }

            // Handle update attendance (ini yang memicu auto logout)
            if (changes.updated_attendances && this.onUpdateAttendance) {
                changes.updated_attendances.forEach((attendance) => {
                    this.onUpdateAttendance(attendance);
                });
            }
        }

        /**
         * Stop monitoring
         */
        stop() {
            if (this.eventSource) {
                this.eventSource.close();
                this.eventSource = null;
                this.isConnected = false;
                console.log("[SSE] Connection closed");
            }
        }

        /**
         * Cek status koneksi
         */
        isConnectedToServer() {
            return this.isConnected;
        }
    }

    global.RealtimeMonitor = RealtimeMonitor;
})(window);