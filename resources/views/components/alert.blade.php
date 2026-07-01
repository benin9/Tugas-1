@if (session('message') || session('obat_error'))
    @php
        $type = session('type', 'success');
        $message = session('message') ?? session('obat_error');

        // Normalisasi tipe ke format daisyUI alert
        $alertClass = 'alert-success';
        $iconClass = 'fa-circle-check';

        if ($type === 'error' || $type === 'danger' || session()->has('obat_error')) {
            $alertClass = 'alert-error';
            $iconClass = 'fa-circle-xmark';
        } elseif ($type === 'warning') {
            $alertClass = 'alert-warning';
            $iconClass = 'fa-triangle-exclamation';
        } elseif ($type === 'info') {
            $alertClass = 'alert-info';
            $iconClass = 'fa-circle-info';
        }
    @endphp

    <div class="alert {{ $alertClass }} alert-dismissible mb-4 rounded-xl shadow-sm transition-opacity duration-500 flex items-center justify-between" role="alert" id="global-alert">
        <div class="flex items-center gap-3">
            <i class="fas {{ $iconClass }}"></i>
            <span>{{ $message }}</span>
        </div>
        <button type="button" class="btn btn-ghost btn-sm btn-circle" onclick="document.getElementById('global-alert').style.display='none'">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <script>
        setTimeout(() => {
            const alertEl = document.getElementById('global-alert');
            if (alertEl) {
                alertEl.style.opacity = '0';
                setTimeout(() => alertEl.remove(), 500);
            }
        }, 3000);
    </script>
@endif
