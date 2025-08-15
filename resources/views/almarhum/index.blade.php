<!DOCTYPE html>
<html>
<head>
    <title>Data Almarhum</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tippy.js Animations -->
    <link rel="stylesheet" href="https://unpkg.com/tippy.js@6/animations/scale.css" />

    <style>
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            border-radius: 10px 10px 0 0 !important;
        }
        .table-responsive {
            overflow-x: auto;
        }
        .img-thumbnail {
            max-width: 80px;
            height: auto;
        }
        .riwayat-cell {
            max-width: 300px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Custom theme for Tippy */
        .tippy-box[data-theme~='custom'] {
            background-color: #4a148c;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
        .tippy-box[data-theme~='custom'] .tippy-content {
            font-size: 0.9em;
            color: white;
            text-align: left;
            max-width: 250px;
            padding: 0.8rem;
            font-family: sans-serif;
        }
        .tippy-box[data-theme~='custom'] strong {
            color: #ffca28;
        }
        .tippy-box[data-theme~='custom'] hr {
            border-color: #666;
            margin: 0.5em 0;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="mb-0"><i class="fas fa-book me-2"></i> Sistem Pencatat Makam Dusun Jetis</h3>
                    <a href="{{ route('almarhum.create') }}" class="btn btn-light">
                        <i class="fas fa-plus me-1"></i> Tambah Data
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Foto</th>
                                <th>Nama</th>
                                <th>Tanggal Lahir</th>
                                <th>Tanggal Wafat</th>
                                <th>Usia</th>
                                <th>Lokasi Makam</th>
                                <th>Riwayat Singkat</th>
                                <th>Keluarga</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($almarhums as $almarhum)
                            <tr>
                                <td>
                                    @if($almarhum->foto)
                                        <img src="{{ asset('storage/' . $almarhum->foto) }}" 
                                             class="img-thumbnail rounded" 
                                             alt="Foto Makam">
                                    @else
                                        <span class="text-muted">Tidak ada foto</span>
                                    @endif
                                </td>
                                <td>{{ $almarhum->nama }}</td>
                                <td>{{ $almarhum->tanggal_lahir?->format('d/m/Y') }}</td>
                                <td>{{ $almarhum->tanggal_wafat?->format('d/m/Y') }}</td>
                                <td>{{ $almarhum->usiaLengkap }}</td>
                                <td>Blok {{ $almarhum->blok_makam }}, No. {{ $almarhum->nomor_makam }}</td>
                                <td class="riwayat-cell" title="{{ $almarhum->riwayat }}" data-bs-toggle="tooltip">
                                    {{ $almarhum->riwayat ?? '-' }}
                                </td>
                                <td>
                                    @if($almarhum->keluargas->isNotEmpty())
                                        <span class="badge bg-info text-white"
                                            data-bs-toggle="tooltip"
                                            data-bs-placement="left"
                                            title="
                                                @foreach($almarhum->keluargas as $kel)
                                                    {{ $kel->nama }} ({{ ucfirst($kel->hubungan) }})
                                                    @unless($loop->last)
                                                        • 
                                                    @endunless
                                                @endforeach
                                            "
                                            style="cursor: help;">
                                            {{ $almarhum->keluargas->count() }} Keluarga
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('almarhum.edit', $almarhum) }}" 
                                           class="btn btn-sm btn-warning" 
                                           aria-label="Edit data {{ $almarhum->nama }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('almarhum.destroy', $almarhum) }}" 
                                              method="POST" 
                                              class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" aria-label="Hapus data {{ $almarhum->nama }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        <a href="{{ route('almarhum.qr', $almarhum) }}" 
                                           class="btn btn-sm btn-success" 
                                           title="Download QR Code untuk {{ $almarhum->nama }}">
                                            <i class="fas fa-qrcode"></i> QR
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    <i class="fas fa-database fa-2x mb-3"></i><br>
                                    Belum ada data almarhum
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Konfirmasi hapus
        document.querySelectorAll('.delete-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const nama = this.closest('tr').querySelector('td:nth-child(2)').textContent.trim();
                
                Swal.fire({
                    title: 'Yakin Hapus Data?',
                    html: `Data <b>${nama}</b> akan dihapus permanen!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        });

        // Tooltip untuk riwayat (Bootstrap)
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (element) {
            
        });
    </script>

    <!-- Tippy.js (Tooltip Keren untuk Keluarga) -->
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://unpkg.com/tippy.js@6"></script>
    <script>
        // Inisialisasi Tippy.js
        document.addEventListener('DOMContentLoaded', function () {
            tippy('[data-tippy-html]', {
                allowHTML: true,
                interactive: true,
                placement: 'left',
                animation: 'scale',
                theme: 'custom'
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Aktifkan semua tooltip
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl, {
            trigger: 'hover'  // hanya muncul saat hover
        });
    });
    </script>
</body>
</html>