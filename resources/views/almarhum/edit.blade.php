<!DOCTYPE html>
<html>
<head>
    <title>Edit Data Almarhum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h3>Edit Data Almarhum</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('almarhum.update', $almarhum->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" value="{{ old('nama', $almarhum->nama) }}" required>
                    </div>
                    <div class="row mb-3">
                    <div class="mb-3">
                        <label for="tanggal_lahir">Tanggal Lahir</label>
                        <input type="date" 
                            name="tanggal_lahir" 
                            value="{{ old('tanggal_lahir', $almarhum->tanggal_lahir ? \Carbon\Carbon::parse($almarhum->tanggal_lahir)->format('Y-m-d') : '') }}"
                            class="form-control"
                            id="tanggal_lahir">
                    </div>

                    <div class="mb-3">
                        <label for="tanggal_wafat">Tanggal Wafat</label>
                        <input type="date" 
                            name="tanggal_wafat" 
                            value="{{ old('tanggal_wafat', $almarhum->tanggal_wafat ? \Carbon\Carbon::parse($almarhum->tanggal_wafat)->format('Y-m-d') : '') }}"
                            class="form-control"
                            id="tanggal_wafat">
                    </div>

                        <div class="col-md-3">
                            <label class="form-label">Nomor Makam</label>
                            <input type="text" name="nomor_makam" class="form-control" 
                                   value="{{ old('nomor_makam', $almarhum->nomor_makam) }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Foto Makam (Kosongkan jika tidak diubah)</label>
                        <input type="file" name="foto" class="form-control">
                        @if($almarhum->foto)
                            <div class="mt-2">
                                <img src="{{ asset('storage/'.$almarhum->foto) }}" width="100" class="img-thumbnail">
                                <p class="text-muted mt-1">Foto saat ini</p>
                            </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Riwayat Singkat</label>
                        <textarea name="riwayat" class="form-control" rows="3">{{ old('riwayat', $almarhum->riwayat) }}</textarea>
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

                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('almarhum.index') }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>

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