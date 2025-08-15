<!DOCTYPE html>
<html>
<head>
    <title>Tambah Data Almarhum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <style>
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            border-radius: 10px 10px 0 0 !important;
        }
        .form-control:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Tambah Data Almarhum</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('almarhum.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label fw-bold">Nama Lengkap</label>
                                <input type="text" name="nama" class="form-control" placeholder="Contoh: Ahmad Dahlan" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" class="form-control" 
                                    value="{{ old('tanggal_lahir', $almarhum->tanggal_lahir ?? '') }}">
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Tanggal Wafat</label>
                                    <input type="date" name="tanggal_wafat" class="form-control" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Blok Makam</label>
                                    <input type="text" name="blok_makam" class="form-control" placeholder="Contoh: A1" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Nomor Makam</label>
                                    <input type="text" name="nomor_makam" class="form-control" placeholder="Contoh: 012" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Foto Makam</label>
                                <input type="file" name="foto" class="form-control">
                                <small class="text-muted">Format: JPG/PNG (Maks. 2MB)</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Riwayat Singkat</label>
                                <textarea name="riwayat" class="form-control" rows="3" placeholder="Contoh: Tokoh masyarakat setempat..."></textarea>
                            </div>

                            <div class="mt-4 p-3 bg-light rounded">
                                <h5><i class="fas fa-users"></i> Data Keluarga yang Ditinggalkan</h5>
                                
                                <div id="keluarga-container">
                                    <!-- Field akan ditambahkan via JS -->
                                </div>

                                <button type="button" class="btn btn-sm btn-outline-primary" id="add-keluarga">
                                    <i class="fas fa-plus"></i> Tambah Keluarga
                                </button>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="{{ route('almarhum.index') }}" class="btn btn-secondary me-md-2">
                                    <i class="fas fa-arrow-left me-1"></i> Kembali
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Simpan Data
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script untuk Toastr (notifikasi) -->
    @if(session('success'))
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script>
        toastr.success('{{ session('success') }}', 'Sukses!');
    </script>
    @endif

    <!-- JS Toastr -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

    <script>
    @if(session('success'))
        toastr.success('{{ session('success') }}', 'SUKSES!', {
            timeOut: 3000,
            progressBar: true,
            closeButton: true
        });
    @endif

    @if($errors->any())
        @foreach($errors->all() as $error)
            toastr.error('{{ $error }}', 'ERROR!', {
                timeOut: 5000
            });
        @endforeach
    @endif
</script>


<script>
document.getElementById('add-keluarga').addEventListener('click', function () {
    const container = document.getElementById('keluarga-container');
    const index = container.children.length;

    const div = document.createElement('div');
    div.className = 'row mb-2';
    div.innerHTML = `
        <div class="col-md-3">
            <input type="text" name="keluargas[${index}][nama]" class="form-control" placeholder="Nama" required>
        </div>
        <div class="col-md-3">
            <select name="keluargas[${index}][hubungan]" class="form-control" required>
                <option value="">Pilih Hubungan</option>
                <option value="suami">Suami</option>
                <option value="istri">Istri</option>
                <option value="anak">Anak</option>
                <option value="cucu">Cucu</option>
                <option value="saudara">Saudara</option>
                <option value="orang_tua">Orang Tua</option>
                <option value="lainnya">Lainnya</option>
            </select>
        </div>
        <div class="col-md-3">
            <input type="text" name="keluargas[${index}][telepon]" class="form-control" placeholder="Telepon">
        </div>
        <div class="col-md-2">
            <input type="text" name="keluargas[${index}][alamat]" class="form-control" placeholder="Alamat">
        </div>
        <div class="col-md-1 d-flex align-items-center">
            <button type="button" class="btn btn-sm btn-danger remove-keluarga">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    container.appendChild(div);
});

// Hapus field
document.addEventListener('click', function (e) {
    if (e.target && e.target.matches('.remove-keluarga')) {
        e.target.closest('.row').remove();
    }
});
</script>


</body>
</html>