<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>{{ $letter->subject }}</title>
  <style>
    body {
      font-family: 'Arial', sans-serif;
      font-size: 14px;
      line-height: 1.6;
      margin: 40px;
    }
    .kop-surat {
      text-align: center;
      margin-bottom: 10px;
    }
    .kop-surat img {
      max-height: 80px;
      margin-bottom: 10px;
    }
    .kop-surat h1 {
      margin: 0;
      font-size: 18px;
    }
    .kop-surat p {
      margin: 0;
      font-size: 13px;
    }
    hr {
      border: none;
      border-top: 2px solid #000;
      margin: 20px 0;
    }
    .content {
      text-align: justify;
    }
    .footer {
      margin-top: 60px;
      width: 100%;
      display: flex;
      justify-content: flex-end;
    }
    .signature {
      text-align: right;
    }
  </style>
</head>
<body>

  {{-- KOP SURAT --}}
  <div class="kop-surat">
    <img src="{{ public_path('images/logo.png') }}" alt="Logo Perusahaan">
    <h1>PT Nayanika Kiara</h1>
    <p>Jl. Contoh Alamat No. 123, Kota Contoh, Indonesia</p>
    <p>Telp: (021) 123456 | Email: info@nayanika.co.id</p>
  </div>

  <hr>

  {{-- JUDUL SURAT --}}
  <h2 style="text-align: center; text-decoration: underline;">
    {{ strtoupper($letter->format->name ?? 'SURAT') }}
  </h2>

  <div class="content">
    {!! $letter->body !!}
  </div>

  {{-- TANGGAL DAN TANDA TANGAN --}}
  <div class="footer">
    <div class="signature">
      <p>{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
      <br><br><br>
      <p><strong>{{ $letter->user->email ?? 'Admin' }}</strong></p>
    </div>
  </div>

</body>
</html>
