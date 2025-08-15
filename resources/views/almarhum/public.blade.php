<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $almarhum->nama }} - Almarhum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }
        .card {
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .profile-img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #007bff;
            margin: -60px auto 20px;
            display: block;
            background: #ddd;
        }
        .card-body {
            padding: 1.5rem;
        }
        h3 {
            color: #333;
            font-weight: 600;
        }
        .footer {
            text-align: center;
            padding: 1rem 0;
            color: #666;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    @if($almarhum->foto)
                        <img src="{{ asset('storage/' . $almarhum->foto) }}" class="profile-img" alt="Foto {{ $almarhum->nama }}">
                    @else
                        <div class="profile-img bg-secondary d-flex align-items-center justify-content-center text-white">
                            <i class="fas fa-user fa-3x"></i>
                        </div>
                    @endif

                    <div class="card-body text-center">
                        <h3>{{ $almarhum->nama }}</h3>
                        <p class="text-muted">
                            Lahir: {{ $almarhum->tanggal_lahir?->format('d M Y') }} — 
                            Wafat: {{ $almarhum->tanggal_wafat?->format('d M Y') }}
                        </p>
                        <p><strong>Lokasi:</strong> Blok {{ $almarhum->blok_makam }}, No {{ $almarhum->nomor_makam }}</p>

                        <div class="mt-4">
                            <h5><i class="fas fa-users"></i> Keluarga yang Ditinggalkan</h5>
                            @if($almarhum->keluargas->isNotEmpty())
                                <ul class="list-group">
                                    @foreach($almarhum->keluargas as $kel)
                                        <li class="list-group-item">
                                            <strong>{{ $kel->nama }}</strong> 
                                            ({{ ucfirst(str_replace('_', ' ', $kel->hubungan)) }})
                                            @if($kel->telepon) | {{ $kel->telepon }} @endif
                                            @if($kel->alamat) | {{ $kel->alamat }} @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-muted">Belum ada data keluarga.</p>
                            @endif
                        </div>

                        @if($almarhum->riwayat)
                            <div class="alert alert-light text-start">
                                "{{ $almarhum->riwayat }}"
                            </div>
                        @endif

                        <a href="{{ url('/') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>

                <div class="footer">
                    Dusun Jetis | Ziarah Digital
                </div>
            </div>
        </div>
    </div>
</body>
</html>