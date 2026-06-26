<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Log Masuk — SysPenyelenggaraan</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.8.0/dist/tabler-icons.min.css">
  <style>
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
    body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;background:#F5F6F8;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:16px;}
    .card{background:#fff;border-radius:16px;padding:32px;width:100%;max-width:380px;box-shadow:0 4px 24px rgba(0,0,0,.08);}
    .logo{display:flex;align-items:center;gap:12px;margin-bottom:28px;}
    .logo-icon{width:40px;height:40px;border-radius:10px;background:#E1F5EE;display:flex;align-items:center;justify-content:center;color:#1D9E75;font-size:22px;}
    .logo h1{font-size:15px;font-weight:700;}
    .logo p{font-size:11px;color:#6B7280;}
    h2{font-size:20px;font-weight:700;margin-bottom:4px;}
    .sub{font-size:13px;color:#6B7280;margin-bottom:24px;}
    .fg{display:flex;flex-direction:column;gap:5px;margin-bottom:16px;}
    .fg label{font-size:12px;font-weight:500;color:#6B7280;}
    .fg input{border:1px solid #E2E5EA;border-radius:8px;padding:10px 12px;font-size:14px;color:#1A1D23;width:100%;font-family:inherit;}
    .fg input:focus{outline:none;border-color:#1D9E75;}
    .fg .err{font-size:11px;color:#E24B4A;}
    .remember{display:flex;align-items:center;gap:8px;font-size:13px;color:#6B7280;margin-bottom:20px;}
    .remember input{width:14px;height:14px;accent-color:#1D9E75;}
    .btn-login{width:100%;padding:11px;border-radius:8px;border:none;background:#1D9E75;color:#fff;font-size:14px;font-weight:600;cursor:pointer;font-family:inherit;transition:background .15s;}
    .btn-login:hover{background:#0F6E56;}
    .hint{margin-top:20px;padding:12px;background:#F5F6F8;border-radius:8px;font-size:11px;color:#6B7280;line-height:1.6;}
  </style>
</head>
<body>
  <div class="card">
    <div class="logo">
      <div class="logo-icon"><i class="ti ti-tool"></i></div>
      <div><h1>SysPenyelenggaraan</h1><p>DEPC & HQ</p></div>
    </div>
    <h2>Log Masuk</h2>
    <p class="sub">Panel Pengurusan Penyelenggaraan</p>

    @if ($errors->any())
    <div style="background:#FCEBEB;border:1px solid #E24B4A;border-radius:8px;padding:10px 12px;margin-bottom:16px;font-size:12px;color:#9B1C1C;">
      {{ $errors->first() }}
    </div>
    @endif

    <form method="POST" action="{{ route('admin.login.post') }}">
      @csrf
      <div class="fg">
        <label>E-mel</label>
        <input type="email" name="email" value="{{ old('email') }}" placeholder="nama@depc.test" autofocus>
      </div>
      <div class="fg">
        <label>Kata Laluan</label>
        <input type="password" name="password" placeholder="••••••••">
      </div>
      <div class="remember">
        <input type="checkbox" name="ingat" id="ingat">
        <label for="ingat">Ingat saya</label>
      </div>
      <button type="submit" class="btn-login">Log Masuk</button>
    </form>

    <div class="hint">
      <strong>Akaun ujian:</strong><br>
      admin@depc.test · penyelia@depc.test<br>
      juruteknik1@depc.test · pengurus@depc.test<br>
      Semua kata laluan: <code>password</code>
    </div>
  </div>
</body>
</html>
