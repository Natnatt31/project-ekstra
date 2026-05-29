// ==========================================
// Globaliti Esport — Admin Panel JS
// ==========================================

document.addEventListener('DOMContentLoaded', function() {
    // Sidebar toggle (mobile)
    const hamburgerBtn = document.getElementById('hamburgerBtn');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    if (hamburgerBtn) {
        hamburgerBtn.addEventListener('click', () => {
            sidebar.classList.toggle('active');
            sidebarOverlay.classList.toggle('active');
        });
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', () => {
            sidebar.classList.remove('active');
            sidebarOverlay.classList.remove('active');
        });
    }

    // Modal handlers
    document.querySelectorAll('[data-modal]').forEach(btn => {
        btn.addEventListener('click', () => {
            const target = document.getElementById(btn.dataset.modal);
            if (target) {
                target.classList.add('active');
                setTimeout(() => target.querySelector('.modal')?.classList.add('show'), 10);
            }
        });
    });

    document.querySelectorAll('.modal-close, .modal-cancel').forEach(btn => {
        btn.addEventListener('click', () => {
            const overlay = btn.closest('.modal-overlay');
            if (overlay) overlay.classList.remove('active');
        });
    });

    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) overlay.classList.remove('active');
        });
    });

    // Auto-dismiss alerts
    document.querySelectorAll('.alert').forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(() => alert.remove(), 300);
        }, 4000);
    });

    // Search filter for tables
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            const rows = document.querySelectorAll('table tbody tr');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        });
    }

    // Absensi status filter and live summary updates
    const absensiTable = document.getElementById('absensiTable');
    if (absensiTable) {
        const summaryHadir = document.getElementById('summaryHadir');
        const summaryIzin = document.getElementById('summaryIzin');
        const summarySakit = document.getElementById('summarySakit');
        const summaryAlpha = document.getElementById('summaryAlpha');
        const statusFilters = document.querySelectorAll('.status-filter-checkbox');

        function updateAbsensiSummary() {
            const counts = { Hadir: 0, Izin: 0, Sakit: 0, Alpha: 0 };
            absensiTable.querySelectorAll('tbody tr').forEach(row => {
                const checked = row.querySelector('input[type="radio"]:checked');
                const status = checked ? checked.value : row.dataset.status || '';
                row.dataset.status = status;
                if (counts.hasOwnProperty(status)) {
                    counts[status]++;
                }
            });
            if (summaryHadir) summaryHadir.textContent = counts.Hadir;
            if (summaryIzin) summaryIzin.textContent = counts.Izin;
            if (summarySakit) summarySakit.textContent = counts.Sakit;
            if (summaryAlpha) summaryAlpha.textContent = counts.Alpha;
        }

        function applyAbsensiFilter() {
            const activeStatuses = Array.from(statusFilters)
                .filter(input => input.checked)
                .map(input => input.value);

            absensiTable.querySelectorAll('tbody tr').forEach(row => {
                const status = row.dataset.status || '';
                if (activeStatuses.length === 0 || activeStatuses.includes(status)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        absensiTable.querySelectorAll('.absen-radio').forEach(radio => {
            radio.addEventListener('change', () => {
                const row = radio.closest('tr');
                if (row) {
                    row.dataset.status = radio.value;
                }
                updateAbsensiSummary();
                applyAbsensiFilter();
            });
        });

        statusFilters.forEach(input => {
            input.addEventListener('change', applyAbsensiFilter);
        });

        updateAbsensiSummary();
        applyAbsensiFilter();
    }

    // Delete confirmation
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (!confirm('Apakah Anda yakin ingin menghapus data ini?')) {
                e.preventDefault();
            }
        });
    });
});

// Open edit modal with data
function openEditModal(modalId, data) {
    const modal = document.getElementById(modalId);
    if (!modal) return;

    Object.keys(data).forEach(key => {
        const field = modal.querySelector(`[name="${key}"]`);
        if (field) field.value = data[key];
    });

    modal.classList.add('active');
}
