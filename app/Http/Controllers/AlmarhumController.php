<?php

namespace App\Http\Controllers;

use App\Models\Almarhum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon; // [TAMBAHKAN INI]
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;


class AlmarhumController extends Controller
{
    public function index()
    {
        $almarhums = Almarhum::with('keluargas')->latest()->get();
        $bloks = Almarhum::pluck('blok_makam')->unique();

        return view('almarhum.index', compact('almarhums', 'bloks'));
    }

    public function create()
    {
        return view('almarhum.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|max:100',
            'tanggal_lahir' => 'required|date|before:tanggal_wafat',
            'tanggal_wafat' => 'required|date|after:tanggal_lahir',
            'blok_makam' => 'required|max:20',
            'nomor_makam' => 'required|max:10',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'riwayat' => 'nullable|max:500',
            'keluargas.*.nama' => 'required|max:100',
            'keluargas.*.hubungan' => 'required',
        ], [
            'tanggal_lahir.before' => 'Tanggal lahir harus sebelum tanggal wafat!',
            'tanggal_wafat.after' => 'Tanggal wafat harus setelah tanggal lahir!',
            'foto.max' => 'Ukuran foto maksimal 2MB!'
        ]);

        try {
            if ($request->hasFile('foto')) {
                $validated['foto'] = $request->file('foto')->store('almarhum', 'public');
            }

            $almarhum = Almarhum::create($validated);

            // Simpan data keluarga
            if ($request->has('keluargas')) {
                foreach ($request->keluargas as $keluarga) {
                    $almarhum->keluargas()->create($keluarga);
                }
            }

            return redirect()->route('almarhum.index')
                        ->with('success', 'Data berhasil disimpan!');
        } catch (\Exception $e) {
            return redirect()->back()
                        ->with('error', 'Gagal menyimpan data: ' . $e->getMessage())
                        ->withInput();
        }
    }

    public function show(Almarhum $almarhum)
    {
        return view('almarhum.show', compact('almarhum'));
    }

    public function edit(Almarhum $almarhum)
    {
        return view('almarhum.edit', compact('almarhum'));
    }

    public function update(Request $request, Almarhum $almarhum)
    {
        $validated = $request->validate([
            'nama' => 'required|max:100',
            'tanggal_lahir' => 'required|date|before:tanggal_wafat',
            'tanggal_wafat' => 'required|date|after:tanggal_lahir',
            'blok_makam' => 'required|max:20',
            'nomor_makam' => 'required|max:10',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'riwayat' => 'nullable|max:500',
            'keluargas.*.nama' => 'required|max:100',
            'keluargas.*.hubungan' => 'required',
        ], [
            'tanggal_lahir.before' => 'Tanggal lahir harus sebelum tanggal wafat!',
            'tanggal_wafat.after' => 'Tanggal wafat harus setelah tanggal lahir!'
        ]);

        try {
            if ($request->hasFile('foto')) {
                if ($almarhum->foto) {
                    Storage::disk('public')->delete($almarhum->foto);
                }
                $validated['foto'] = $request->file('foto')->store('almarhum', 'public');
            }

            $almarhum->update($validated);

            // Hapus & simpan ulang keluarga
            $almarhum->keluargas()->delete();

            if ($request->has('keluargas')) {
                foreach ($request->keluargas as $keluarga) {
                    $almarhum->keluargas()->create($keluarga);
                }
            }

            return redirect()->route('almarhum.index')
                        ->with('success', 'Data berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()
                        ->with('error', 'Gagal memperbarui data: ' . $e->getMessage())
                        ->withInput();
        }
    }

    public function destroy(Almarhum $almarhum)
    {
        try {
            if ($almarhum->foto) {
                Storage::disk('public')->delete($almarhum->foto);
            }

            $almarhum->delete();

            return redirect()->route('almarhum.index')
                           ->with('success', 'Data berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    public function generateQr($id)
    {
        $almarhum = Almarhum::findOrFail($id);
        $nama = $almarhum->nama;
        $blok = $almarhum->blok_makam;
        $nomor = $almarhum->nomor_makam;

        // URL halaman publik
        $url = route('almarhum.public.show', $almarhum->id);

        // Konfigurasi QR code
        $options = new QROptions([
            'outputType'     => QRCode::OUTPUT_IMAGE_PNG, // Pakai ini
            'eccLevel'       => QRCode::ECC_L,
            'scale'          => 10,
            'imageTransparent' => false,
        ]);

        // Hapus buffer jika ada
        if (ob_get_contents()) ob_clean();

        try {
            $qrData = (new QRCode($options))->render($url);
        } catch (\Exception $e) {
            abort(500, 'Gagal generate QR: ' . $e->getMessage());
        }

        // Cek apakah hasilnya base64
        if (strpos($qrData, 'data:image/png;base64') === 0) {
            // Ekstrak base64 dan decode
            $base64Data = substr($qrData, 22); // hapus 'data:image/png;base64,'
            $qrPngData = base64_decode($base64Data);
        } else {
            // Anggap ini binary PNG
            $qrPngData = $qrData;
        }

        // Validasi apakah ini benar-benar PNG
        if (substr($qrPngData, 0, 4) !== "\x89PNG") {
            abort(500, 'Hasil QR bukan format PNG yang valid.');
        }

        // Buat gambar dari string PNG
        $qr = imagecreatefromstring($qrPngData);
        if (!$qr) {
            abort(500, 'Gagal membuat gambar dari QR code.');
        }

        $qrWidth = imagesx($qr);
        $qrHeight = imagesy($qr);

        // Canvas utama
        $width = 300;
        $height = 380;
        $image = imagecreatetruecolor($width, $height);

        $white = imagecolorallocate($image, 255, 255, 255);
        $black = imagecolorallocate($image, 0, 0, 0);
        $gray = imagecolorallocate($image, 100, 100, 100);

        imagefilledrectangle($image, 0, 0, $width, $height, $white);

        // Tempel QR
        $qrX = (300 - $qrWidth) / 2;
        $qrY = 10;
        imagecopy($image, $qr, $qrX, $qrY, 0, 0, $qrWidth, $qrHeight);

        // Teks: Nama
        $textNama = "Nama: " . $nama;
        $textWidthNama = imagefontwidth(5) * strlen($textNama);
        $xNama = (300 - $textWidthNama) / 2;
        imagestring($image, 5, $xNama, $qrY + $qrHeight + 20, $textNama, $black);

        // Teks: Lokasi
        $textLokasi = "Blok $blok, No $nomor";
        $textWidthLokasi = imagefontwidth(5) * strlen($textLokasi);
        $xLokasi = (300 - $textWidthLokasi) / 2;
        imagestring($image, 5, $xLokasi, $qrY + $qrHeight + 50, $textLokasi, $gray);

        // Footer
        $textFooter = "Dusun Jetis";
        $textWidthFooter = imagefontwidth(2) * strlen($textFooter);
        $xFooter = (300 - $textWidthFooter) / 2;
        imagestring($image, 2, $xFooter, $qrY + $qrHeight + 90, $textFooter, $gray);

        // Output sebagai JPG
        ob_start();
        imagejpeg($image, null, 90);
        $jpgData = ob_get_contents();
        ob_end_clean();

        // Hapus dari memory
        imagedestroy($image);
        imagedestroy($qr);

        // Nama file
        $safeName = preg_replace('/[^a-zA-Z0-9]/', '_', $nama);
        $fileName = "QR_{$safeName}_Blok{$blok}_No{$nomor}.jpg";

        return response($jpgData, 200)
            ->header('Content-Type', 'image/jpeg')
            ->header('Content-Disposition', "attachment; filename=\"{$fileName}\"");
    }

    public function showPublic($id)
    {
        $almarhum = Almarhum::findOrFail($id);
        return view('almarhum.public', compact('almarhum'));
    }
}