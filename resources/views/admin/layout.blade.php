<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Dashboard') — SysPenyelenggaraan</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.8.0/dist/tabler-icons.min.css">
  @livewireStyles
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
      --green:     #1D9E75; --green-dk:  #0F6E56; --green-lt:  #E1F5EE;
      --red:       #E24B4A; --red-lt:    #FCEBEB;
      --amber:     #EF9F27; --amber-lt:  #FAEEDA;
      --blue:      #185FA5; --blue-lt:   #E6F1FB;
      --purple:    #3C3489; --purple-lt: #EEEDFE;
      --bg:        #F5F6F8; --surface:   #FFFFFF;
      --border:    #E2E5EA; --text:      #1A1D23;
      --muted:     #6B7280; --faint:     #9CA3AF;
      --sidebar-w: 220px;   --topbar-h:  54px; --bottom-h: 56px;
    }
    body { font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif; background:var(--bg); color:var(--text); }
    .shell { display:flex; height:100vh; overflow:hidden; }
    /* SIDEBAR */
    .sidebar { width:var(--sidebar-w); background:var(--surface); border-right:1px solid var(--border); display:flex; flex-direction:column; flex-shrink:0; z-index:20; transition:transform .25s; }
    .sb-logo { padding:16px; border-bottom:1px solid var(--border); display:flex; align-items:center; gap:10px; }
    .sb-logo-icon { width:32px; height:32px; border-radius:8px; background:var(--green-lt); display:flex; align-items:center; justify-content:center; color:var(--green); font-size:18px; }
    .sb-logo h1 { font-size:13px; font-weight:600; } .sb-logo p { font-size:10px; color:var(--muted); }
    .sb-user { padding:12px 14px; display:flex; align-items:center; gap:10px; border-bottom:1px solid var(--border); }
    .avatar { width:32px; height:32px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:600; flex-shrink:0; }
    .av-sv { background:var(--green-lt); color:var(--green-dk); }
    .av-tc { background:var(--blue-lt);  color:var(--blue); }
    .av-mg { background:var(--amber-lt); color:#633806; }
    .av-ad { background:var(--purple-lt);color:var(--purple); }
    .sb-user-info p { font-size:12px; font-weight:500; } .sb-user-info span { font-size:10px; color:var(--muted); }
    .sb-nav { flex:1; overflow-y:auto; padding:8px 0; }
    .sb-sec { padding:10px 14px 4px; font-size:10px; color:var(--faint); text-transform:uppercase; letter-spacing:.5px; }
    .sb-item { display:flex; align-items:center; gap:9px; padding:9px 14px; font-size:13px; color:var(--muted); cursor:pointer; border:none; background:none; width:100%; text-align:left; border-radius:0; transition:background .15s; font-family:inherit; text-decoration:none; }
    .sb-item:hover { background:var(--bg); color:var(--text); }
    .sb-item.active { background:var(--bg); color:var(--text); font-weight:600; }
    .sb-item i { font-size:17px; flex-shrink:0; }
    .sb-item .cnt { margin-left:auto; background:var(--red); color:#fff; font-size:10px; padding:1px 6px; border-radius:99px; }
    .sb-foot { padding:12px 14px; border-top:1px solid var(--border); }
    .sb-foot p { font-size:11px; color:var(--muted); margin-bottom:4px; }
    .sb-foot span { font-size:12px; font-weight:500; }
    /* MAIN */
    .main { flex:1; display:flex; flex-direction:column; overflow:hidden; min-width:0; }
    .topbar { height:var(--topbar-h); padding:0 20px; display:flex; align-items:center; gap:10px; background:var(--surface); border-bottom:1px solid var(--border); flex-shrink:0; }
    .topbar-menu { display:none; background:none; border:none; font-size:20px; cursor:pointer; padding:6px; color:var(--text); }
    .topbar h2 { font-size:16px; font-weight:600; flex:1; }
    .topbar-actions { display:flex; gap:8px; }
    .content { flex:1; overflow-y:auto; padding:20px; }
    /* BOTTOM NAV */
    .bottom-nav { display:none; position:fixed; bottom:0; left:0; right:0; height:var(--bottom-h); background:var(--surface); border-top:1px solid var(--border); z-index:30; justify-content:space-around; align-items:center; }
    .bn-item { display:flex; flex-direction:column; align-items:center; gap:2px; padding:6px 10px; border:none; background:none; cursor:pointer; color:var(--faint); font-size:9px; font-weight:500; position:relative; text-decoration:none; font-family:inherit; }
    .bn-item i { font-size:22px; }
    .bn-item.active { color:var(--green); }
    .bn-item .dot { position:absolute; top:4px; right:8px; width:8px; height:8px; background:var(--red); border-radius:50%; border:2px solid var(--surface); }
    /* OVERLAY */
    .overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.4); z-index:15; }
    .overlay.show { display:block; }
    /* STATS */
    .stat-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:12px; margin-bottom:20px; }
    .sc { background:var(--surface); border:1px solid var(--border); border-radius:12px; padding:14px 16px; }
    .sc .n { font-size:24px; font-weight:600; margin-bottom:2px; }
    .sc .l { font-size:11px; color:var(--muted); }
    .sc.red .n { color:var(--red); } .sc.amber .n { color:var(--amber); } .sc.green .n { color:var(--green); }
    /* FILTERS */
    .filters { display:flex; gap:8px; margin-bottom:16px; flex-wrap:wrap; align-items:center; }
    .filters select { font-size:13px; padding:7px 12px; border-radius:8px; border:1px solid var(--border); background:var(--surface); color:var(--text); }
    /* ADUAN CARDS */
    .aduan-list { display:flex; flex-direction:column; gap:10px; }
    .acard { background:var(--surface); border:1px solid var(--border); border-radius:12px; padding:16px 16px 16px 20px; position:relative; overflow:hidden; transition:box-shadow .2s; }
    .acard:hover { box-shadow:0 4px 16px rgba(0,0,0,.08); }
    .acard-strip { position:absolute; left:0; top:0; bottom:0; width:4px; border-radius:12px 0 0 12px; }
    .strip-k { background:var(--red); } .strip-t { background:var(--amber); } .strip-s { background:#639922; } .strip-r { background:var(--blue); }
    .acard-top { display:flex; align-items:flex-start; justify-content:space-between; gap:8px; margin-bottom:8px; }
    .acard-top h4 { font-size:14px; font-weight:600; flex:1; }
    .badge { display:inline-flex; align-items:center; gap:4px; padding:4px 10px; border-radius:99px; font-size:11px; font-weight:600; white-space:nowrap; }
    .badge.baru    { background:var(--amber-lt); color:#633806; }
    .badge.prog    { background:var(--blue-lt);  color:#0C447C; }
    .badge.selesai { background:var(--green-lt); color:#085041; }
    .meta { display:flex; gap:6px; flex-wrap:wrap; margin-bottom:10px; }
    .tag { display:inline-flex; align-items:center; gap:4px; padding:3px 9px; border-radius:99px; font-size:11px; background:var(--bg); color:var(--muted); }
    .acard-desc { font-size:13px; color:var(--muted); margin-bottom:10px; line-height:1.5; }
    .acard-footer { display:flex; align-items:center; justify-content:space-between; gap:8px; flex-wrap:wrap; }
    .acard-footer-info { font-size:11px; color:var(--faint); }
    .acard-actions { display:flex; gap:6px; flex-wrap:wrap; }
    /* BUTTONS */
    .btn { padding:7px 14px; border-radius:8px; border:1px solid var(--border); background:var(--surface); font-size:12px; cursor:pointer; color:var(--text); display:inline-flex; align-items:center; gap:5px; font-weight:500; transition:all .15s; font-family:inherit; }
    .btn:hover { background:var(--bg); }
    .btn.pri { background:var(--green); border-color:var(--green); color:#fff; }
    .btn.pri:hover { background:var(--green-dk); }
    .btn.warn { background:var(--amber-lt); border-color:var(--amber); color:#633806; }
    .btn.full { width:100%; justify-content:center; padding:10px 16px; font-size:13px; }
    .btn:disabled { opacity:.5; cursor:not-allowed; }
    /* MODAL */
    .modal-bg { display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:50; align-items:center; justify-content:center; padding:16px; }
    .modal-bg.show { display:flex; }
    .modal { background:var(--surface); border-radius:16px; padding:22px; width:100%; max-width:400px; }
    .modal h3 { font-size:15px; font-weight:600; margin-bottom:16px; display:flex; align-items:center; gap:8px; }
    .fg { display:flex; flex-direction:column; gap:5px; margin-bottom:14px; }
    .fg label { font-size:12px; font-weight:500; color:var(--muted); }
    .fg select,.fg input,.fg textarea { border:1px solid var(--border); border-radius:8px; padding:9px 12px; font-size:13px; background:var(--surface); color:var(--text); width:100%; font-family:inherit; }
    .fg select:focus,.fg input:focus,.fg textarea:focus { outline:none; border-color:var(--green); }
    .modal-actions { display:flex; gap:8px; justify-content:flex-end; margin-top:6px; }
    /* TOAST */
    .toast { display:none; position:fixed; bottom:24px; right:20px; background:var(--text); color:#fff; padding:12px 18px; border-radius:10px; font-size:13px; max-width:320px; z-index:99; box-shadow:0 4px 20px rgba(0,0,0,.25); }
    /* EMPTY */
    .empty { text-align:center; padding:60px 20px; color:var(--muted); }
    .empty i { font-size:44px; display:block; margin-bottom:12px; opacity:.4; }
    /* TABLE */
    .tbl { width:100%; border-collapse:collapse; font-size:13px; }
    .tbl th { background:var(--bg); padding:10px 14px; text-align:left; font-size:11px; color:var(--muted); text-transform:uppercase; }
    .tbl td { padding:10px 14px; border-top:1px solid var(--border); }
    /* RESPONSIVE */
    @media(max-width:640px){
      .sidebar{position:fixed;top:0;bottom:0;left:0;transform:translateX(-100%);}
      .sidebar.open{transform:translateX(0);}
      .topbar-menu{display:flex!important;}
      .bottom-nav{display:flex;}
      .content{padding:14px 14px calc(var(--bottom-h) + 14px);}
      .stat-grid{grid-template-columns:repeat(2,1fr);}
    }
  </style>
</head>
<body>

<div class="overlay" id="overlay" onclick="closeSB()"></div>

<div class="shell">
  <!-- SIDEBAR -->
  <aside class="sidebar" id="sidebar">
    <div class="sb-logo">
      <div class="sb-logo-icon"><i class="ti ti-tool"></i></div>
      <div><h1>SysPenyelenggaraan</h1><p>Paparan Dalaman</p></div>
    </div>
    <div class="sb-user">
      @php
        $roleKey = auth()->user()->getRoleNames()->first() ?? '';
        $avCls = match($roleKey) {
          'Penyelia Penyelenggaraan' => 'av-sv',
          'Juruteknik'               => 'av-tc',
          'Pengurus Operasi'         => 'av-mg',
          default                    => 'av-ad',
        };
        $initials = collect(explode(' ', auth()->user()->name))->take(2)->map(fn($w) => strtoupper($w[0]))->implode('');
      @endphp
      <div class="avatar {{ $avCls }}">{{ $initials }}</div>
      <div class="sb-user-info">
        <p>{{ auth()->user()->name }}</p>
        <span>{{ $roleKey ?: 'Pentadbir' }}</span>
      </div>
    </div>
    <nav class="sb-nav">
      <div class="sb-sec">Utama</div>
      <a href="{{ route('admin.dashboard') }}" class="sb-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="ti ti-layout-dashboard"></i>Dashboard</a>
      <a href="{{ route('admin.aduan') }}" class="sb-item {{ request()->routeIs('admin.aduan') ? 'active' : '' }}">
        <i class="ti ti-clipboard-list"></i>Senarai Aduan
        @php $baru = \App\Models\Aduan::where('status','Baru')->count(); @endphp
        @if($baru) <span class="cnt">{{ $baru }}</span> @endif
      </a>
      <a href="{{ route('admin.aliran') }}" class="sb-item {{ request()->routeIs('admin.aliran') ? 'active' : '' }}"><i class="ti ti-arrows-right"></i>Aliran Kerja</a>
      <a href="{{ route('admin.monitor') }}" class="sb-item {{ request()->routeIs('admin.monitor') ? 'active' : '' }}"><i class="ti ti-live-view"></i>Monitor Langsung</a>

      <div class="sb-sec">Laporan</div>
      <a href="{{ route('admin.notifikasi') }}" class="sb-item {{ request()->routeIs('admin.notifikasi') ? 'active' : '' }}">
        <i class="ti ti-bell"></i>Notifikasi
        @php $alert = \App\Models\Aduan::whereIn('keutamaan',['Kritikal','Tinggi'])->where('status','Baru')->count(); @endphp
        @if($alert) <span class="cnt">{{ $alert }}</span> @endif
      </a>
      @if(auth()->user()->can('laporan.view'))
      <a href="{{ route('admin.laporan') }}" class="sb-item {{ request()->routeIs('admin.laporan') ? 'active' : '' }}"><i class="ti ti-chart-bar"></i>Laporan Bulanan</a>
      @endif

      @if(auth()->user()->can('user.manage'))
      <div class="sb-sec">Pentadbiran</div>
      <a href="{{ route('admin.pengguna') }}" class="sb-item {{ request()->routeIs('admin.pengguna') ? 'active' : '' }}"><i class="ti ti-users"></i>Pengurusan Pengguna</a>
      @endif
    </nav>
    <div class="sb-foot">
      <p>Log masuk sebagai</p>
      <span>{{ $roleKey ?: 'Pentadbir' }}</span>
    </div>
  </aside>

  <!-- MAIN -->
  <div class="main">
    <div class="topbar">
      <button class="topbar-menu" onclick="toggleSB()" aria-label="Menu"><i class="ti ti-menu-2"></i></button>
      <h2>@yield('page-title', 'Dashboard')</h2>
      <div class="topbar-actions">
        <a href="{{ route('aduan.borang') }}" target="_blank" class="btn pri"><i class="ti ti-external-link"></i><span>Portal Staff</span></a>
        <form method="POST" action="{{ route('admin.logout') }}" style="display:inline">
          @csrf
          <button type="submit" class="btn"><i class="ti ti-logout"></i><span>Keluar</span></button>
        </form>
      </div>
    </div>
    <div class="content">
      @yield('content')
    </div>
  </div>
</div>

<!-- BOTTOM NAV -->
@php $baruCount = \App\Models\Aduan::where('status','Baru')->count(); @endphp
<nav class="bottom-nav">
  <a href="{{ route('admin.dashboard') }}" class="bn-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="ti ti-layout-dashboard"></i>Utama</a>
  <a href="{{ route('admin.aduan') }}" class="bn-item {{ request()->routeIs('admin.aduan') ? 'active' : '' }}"><i class="ti ti-clipboard-list"></i>Aduan{!! $baruCount ? '<span class="dot"></span>' : '' !!}</a>
  <a href="{{ route('admin.aliran') }}" class="bn-item {{ request()->routeIs('admin.aliran') ? 'active' : '' }}"><i class="ti ti-arrows-right"></i>Aliran</a>
  <a href="{{ route('admin.notifikasi') }}" class="bn-item {{ request()->routeIs('admin.notifikasi') ? 'active' : '' }}"><i class="ti ti-bell"></i>Notif</a>
  <a href="{{ route('admin.pengguna') }}" class="bn-item {{ request()->routeIs('admin.pengguna') ? 'active' : '' }}"><i class="ti ti-user"></i>Profil</a>
</nav>

<!-- TOAST -->
<div class="toast" id="toast"></div>

@livewireScripts
<script>
  function toggleSB(){ document.getElementById('sidebar').classList.toggle('open'); document.getElementById('overlay').classList.toggle('show'); }
  function closeSB(){ document.getElementById('sidebar').classList.remove('open'); document.getElementById('overlay').classList.remove('show'); }
  let toastTimer;
  function showToast(msg){ const el=document.getElementById('toast'); el.textContent=msg; el.style.display='block'; clearTimeout(toastTimer); toastTimer=setTimeout(()=>el.style.display='none',3500); }

  document.addEventListener('livewire:init', () => {
    Livewire.on('toast', (msg) => showToast(Array.isArray(msg) ? msg[0] : msg));
    Livewire.on('open-modal',  (id) => { const m=document.getElementById(Array.isArray(id)?id[0]:id); if(m) m.classList.add('show'); });
    Livewire.on('close-modal', (id) => { const m=document.getElementById(Array.isArray(id)?id[0]:id); if(m) m.classList.remove('show'); });
  });
</script>
@yield('scripts')
</body>
</html>
