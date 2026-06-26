<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Portal Aduan Penyelenggaraan</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.8.0/dist/tabler-icons.min.css">
  <style>
    /* ─── RESET & BASE ─── */
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
      --green:     #1D9E75;
      --green-dk:  #0F6E56;
      --green-lt:  #E1F5EE;
      --red:       #E24B4A;
      --red-lt:    #FCEBEB;
      --amber:     #EF9F27;
      --amber-lt:  #FAEEDA;
      --blue:      #185FA5;
      --blue-lt:   #E6F1FB;
      --bg:        #F5F6F8;
      --surface:   #FFFFFF;
      --border:    #E2E5EA;
      --text:      #1A1D23;
      --muted:     #6B7280;
      --faint:     #9CA3AF;
      --radius-sm: 8px;
      --radius:    12px;
      --radius-lg: 16px;
      --shadow:    0 1px 4px rgba(0,0,0,.08), 0 4px 16px rgba(0,0,0,.04);
    }
    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
      background: var(--bg);
      color: var(--text);
      font-size: 15px;
      line-height: 1.5;
    }

    /* ─── LAYOUT ─── */
    .wrap { max-width: 520px; margin: 0 auto; padding: 20px 16px 40px; min-height: 100vh; }

    /* ─── HEADER ─── */
    .hdr { text-align: center; padding: 32px 0 28px; }
    .hdr-icon {
      width: 60px; height: 60px; border-radius: 18px;
      background: var(--green-lt); display: flex;
      align-items: center; justify-content: center;
      margin: 0 auto 14px; font-size: 28px; color: var(--green);
    }
    .hdr h1 { font-size: 20px; font-weight: 600; margin-bottom: 6px; }
    .hdr p  { font-size: 14px; color: var(--muted); }

    /* ─── TABS ─── */
    .tabs {
      display: flex; background: var(--border); border-radius: var(--radius-sm);
      padding: 3px; margin-bottom: 24px; gap: 2px;
    }
    .tab {
      flex: 1; padding: 9px 12px; text-align: center; font-size: 13px;
      border-radius: calc(var(--radius-sm) - 2px); border: none;
      background: none; cursor: pointer; color: var(--muted);
      font-weight: 500; transition: all .18s;
    }
    .tab.active {
      background: var(--surface); color: var(--text);
      box-shadow: 0 1px 4px rgba(0,0,0,.10);
    }

    /* ─── PROGRESS ─── */
    .prog-wrap { margin-bottom: 24px; }
    .prog-labels { display: flex; justify-content: space-between; margin-bottom: 6px; }
    .prog-labels span { font-size: 12px; color: var(--muted); }
    .prog-labels strong { font-size: 12px; color: var(--green); }
    .prog-bar { background: var(--border); border-radius: 99px; height: 5px; }
    .prog-fill { height: 5px; border-radius: 99px; background: var(--green); transition: width .3s ease; }

    /* ─── CARDS ─── */
    .card {
      background: var(--surface); border-radius: var(--radius);
      border: 1px solid var(--border); padding: 18px;
      margin-bottom: 14px; box-shadow: var(--shadow);
    }
    .card-title {
      font-size: 13px; font-weight: 600; color: var(--muted);
      margin-bottom: 16px; display: flex; align-items: center; gap: 7px;
      text-transform: uppercase; letter-spacing: .4px;
    }
    .card-title i { font-size: 16px; color: var(--green); }

    /* ─── FORM ─── */
    .fg { display: flex; flex-direction: column; gap: 5px; margin-bottom: 14px; }
    .fg:last-child { margin-bottom: 0; }
    .fg label { font-size: 13px; font-weight: 500; }
    .fg .hint { font-size: 11px; color: var(--faint); }
    .fg input, .fg select, .fg textarea {
      border: 1.5px solid var(--border); border-radius: var(--radius-sm);
      padding: 10px 13px; font-size: 14px; background: var(--surface);
      color: var(--text); width: 100%; transition: border-color .15s;
      font-family: inherit;
    }
    .fg input:focus, .fg select:focus, .fg textarea:focus {
      outline: none; border-color: var(--green);
      box-shadow: 0 0 0 3px rgba(29,158,117,.12);
    }
    .fg textarea { resize: vertical; min-height: 90px; }
    .fg input.err, .fg select.err, .fg textarea.err { border-color: var(--red); }
    .err-msg { font-size: 11px; color: var(--red); margin-top: 2px; }
    .fg2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    @media (max-width: 400px) { .fg2 { grid-template-columns: 1fr; } }

    /* ─── PRIORITY GRID ─── */
    .pri-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 8px; }
    .pri-opt {
      border: 1.5px solid var(--border); border-radius: var(--radius-sm);
      padding: 10px 4px; text-align: center; cursor: pointer;
      transition: all .15s; background: var(--surface); user-select: none;
    }
    .pri-opt .pi { font-size: 22px; display: block; margin-bottom: 4px; }
    .pri-opt .pl { font-size: 10px; color: var(--muted); font-weight: 500; }
    .pri-opt.sel-rendah   { border-color: #639922; background: #EAF3DE; }
    .pri-opt.sel-rendah .pl { color: #27500A; }
    .pri-opt.sel-sederhana { border-color: var(--amber); background: var(--amber-lt); }
    .pri-opt.sel-sederhana .pl { color: #633806; }
    .pri-opt.sel-tinggi   { border-color: var(--red); background: var(--red-lt); }
    .pri-opt.sel-tinggi .pl { color: #791F1F; }
    .pri-opt.sel-kritikal { border-color: #A32D2D; background: #F9DADA; }
    .pri-opt.sel-kritikal .pl { color: #501313; }

    /* ─── PHOTO UPLOAD ─── */
    .photo-box {
      border: 2px dashed var(--border); border-radius: var(--radius-sm);
      padding: 22px; text-align: center; cursor: pointer;
      transition: all .15s; background: var(--bg);
    }
    .photo-box:hover { border-color: var(--green); background: var(--green-lt); }
    .photo-box i { font-size: 30px; color: var(--faint); display: block; margin-bottom: 8px; }
    .photo-box p { font-size: 13px; color: var(--muted); }
    .photo-box span { font-size: 11px; color: var(--faint); }
    .previews { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 12px; }
    .thumb {
      width: 72px; height: 72px; border-radius: var(--radius-sm);
      border: 1px solid var(--border); background-size: cover;
      background-position: center; position: relative;
    }
    .thumb button {
      position: absolute; top: -6px; right: -6px;
      width: 20px; height: 20px; border-radius: 50%;
      background: var(--red); color: #fff; border: none;
      cursor: pointer; font-size: 12px; display: flex;
      align-items: center; justify-content: center;
      box-shadow: 0 1px 4px rgba(0,0,0,.2);
    }

    /* ─── SUMMARY TABLE ─── */
    .summ-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .summ-table tr td { padding: 5px 0; vertical-align: top; }
    .summ-table tr td:first-child { color: var(--muted); width: 38%; padding-right: 8px; }
    .summ-table tr td:last-child { font-weight: 500; word-break: break-word; }

    /* ─── BUTTONS ─── */
    .btn {
      padding: 11px 20px; border-radius: var(--radius-sm);
      border: 1.5px solid var(--border); background: var(--surface);
      font-size: 14px; cursor: pointer; color: var(--text);
      display: inline-flex; align-items: center; gap: 7px;
      font-weight: 500; transition: all .15s; font-family: inherit;
    }
    .btn:hover { background: var(--bg); }
    .btn.pri {
      background: var(--green); border-color: var(--green);
      color: #fff; width: 100%; justify-content: center;
    }
    .btn.pri:hover { background: var(--green-dk); }
    .btn.pri:disabled { background: #9FE1CB; border-color: #9FE1CB; cursor: not-allowed; }
    .btn.sec { width: 100%; justify-content: center; }
    .btn-row { display: flex; gap: 10px; }
    .btn-row .btn.sec { flex: 1; }
    .btn-row .btn.pri { flex: 2; }

    /* ─── SEMAK STATUS ─── */
    .status-result { display: none; margin-top: 16px; }
    .status-badge-box {
      text-align: center; padding: 24px 16px;
      background: var(--surface); border: 1px solid var(--border);
      border-radius: var(--radius); margin-bottom: 14px;
      box-shadow: var(--shadow);
    }
    .status-badge-box .tid { font-size: 20px; font-weight: 600; font-family: monospace; color: var(--green); margin-bottom: 8px; }
    .badge {
      display: inline-flex; align-items: center; gap: 5px;
      padding: 5px 14px; border-radius: 99px; font-size: 13px; font-weight: 600;
    }
    .badge.baru   { background: var(--amber-lt); color: #633806; }
    .badge.prog   { background: var(--blue-lt); color: #0C447C; }
    .badge.selesai { background: var(--green-lt); color: #085041; }

    /* ─── TRACKER ─── */
    .tracker { display: flex; flex-direction: column; }
    .ts { display: flex; gap: 14px; }
    .ts-left { display: flex; flex-direction: column; align-items: center; }
    .ts-dot {
      width: 30px; height: 30px; border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      font-size: 15px; flex-shrink: 0; border: 1.5px solid var(--border);
      background: var(--bg); color: var(--faint);
    }
    .ts-dot.done  { background: var(--green-lt); border-color: var(--green); color: var(--green); }
    .ts-dot.active { background: var(--blue-lt); border-color: var(--blue); color: var(--blue); }
    .ts-line { width: 2px; flex: 1; background: var(--border); min-height: 18px; margin: 3px 0; }
    .ts-line.done { background: var(--green); }
    .ts-body { flex: 1; padding-bottom: 18px; }
    .ts-body h4 { font-size: 13px; font-weight: 600; margin-bottom: 2px; }
    .ts-body p  { font-size: 12px; color: var(--muted); }

    /* ─── SUCCESS ─── */
    .success-screen { text-align: center; padding: 32px 0; }
    .success-icon {
      width: 72px; height: 72px; border-radius: 50%;
      background: var(--green-lt); display: flex; align-items: center;
      justify-content: center; margin: 0 auto 18px; font-size: 36px; color: var(--green);
    }
    .success-screen h2 { font-size: 20px; font-weight: 600; margin-bottom: 8px; }
    .success-screen p  { font-size: 14px; color: var(--muted); }
    .ticket-box {
      background: var(--green-lt); border: 1.5px solid var(--green);
      border-radius: var(--radius); padding: 18px; margin: 20px 0; text-align: left;
    }
    .ticket-box .tid { font-size: 26px; font-weight: 700; font-family: monospace; color: var(--green); }
    .ticket-box p { font-size: 12px; color: var(--green-dk); margin-top: 4px; }

    /* ─── VIEWS ─── */
    .view { display: none; }
    .view.active { display: block; }
  </style>
</head>
<body>
<div class="wrap">

  <!-- ═══ VIEW: BORANG ═══ -->
  <div class="view active" id="v-home">
    <header class="hdr">
      <div class="hdr-icon"><i class="ti ti-tool"></i></div>
      <h1>Portal Aduan Penyelenggaraan</h1>
      <p>Laporkan kerosakan dengan mudah — tanpa log masuk</p>
    </header>

    <div class="tabs" role="tablist">
      <button class="tab active" role="tab" onclick="showTab('buat', this)">Buat Aduan</button>
      <button class="tab" role="tab" onclick="showTab('semak', this)">Semak Status</button>
    </div>

    <!-- TAB: BUAT ADUAN -->
    <div id="tab-buat">
      <div class="prog-wrap">
        <div class="prog-labels">
          <span id="prog-lbl">Langkah 1 / 3 — Maklumat Pelapor</span>
          <strong id="prog-pct">33%</strong>
        </div>
        <div class="prog-bar"><div class="prog-fill" id="prog-fill" style="width:33%"></div></div>
      </div>

      <!-- STEP 1 -->
      <div id="s1">
        <div class="card">
          <div class="card-title"><i class="ti ti-user"></i> Maklumat pelapor</div>
          <div class="fg">
            <label>Nama penuh <span style="color:var(--red)">*</span></label>
            <input type="text" id="f-nama" placeholder="cth: Ahmad bin Ali" autocomplete="name">
          </div>
          <div class="fg2">
            <div class="fg" style="margin-bottom:0">
              <label>Bahagian / Jabatan <span style="color:var(--red)">*</span></label>
              <select id="f-jab">
                <option value="">— Pilih —</option>
                <option>Further Processing (FP)</option>
                <option>Chilling Plant (CP)</option>
                <option>Dirty Side (D/S)</option>
                <option>Blast Freezer</option>
                <option>Water Treatment (W/T)</option>
                <option>Loading Bay</option>
                <option>HQ / Pejabat</option>
                <option>Stor</option>
                <option>Lain-lain</option>
              </select>
            </div>
            <div class="fg" style="margin-bottom:0">
              <label>No. telefon</label>
              <input type="tel" id="f-tel" placeholder="01X-XXXXXXX" autocomplete="tel">
            </div>
          </div>
        </div>
        <button class="btn pri" onclick="next1()"><i class="ti ti-arrow-right"></i> Seterusnya</button>
      </div>

      <!-- STEP 2 -->
      <div id="s2" style="display:none">
        <div class="card">
          <div class="card-title"><i class="ti ti-clipboard-list"></i> Butiran kerosakan</div>
          <div class="fg">
            <label>Nama peralatan / item yang rosak <span style="color:var(--red)">*</span></label>
            <input type="text" id="f-item" placeholder="cth: Air conditioner, lampu, paip, forklift...">
          </div>
          <div class="fg">
            <label>Lokasi tepat kerosakan <span style="color:var(--red)">*</span></label>
            <input type="text" id="f-lok" placeholder="cth: Bilik penyimpanan sejuk, lantai 2, bilik server...">
          </div>
          <div class="fg">
            <label>Penerangan kerosakan <span style="color:var(--red)">*</span></label>
            <textarea id="f-perihal" placeholder="Huraikan kerosakan dengan terperinci. Bila mula berlaku? Ada bunyi luar biasa? Air bocor? Terperinci membantu juruteknik bertindak lebih cepat."></textarea>
          </div>
          <div class="fg">
            <label>Tarikh mula dikesan</label>
            <input type="date" id="f-tarikh">
          </div>
        </div>

        <div class="card">
          <div class="card-title"><i class="ti ti-alert-triangle"></i> Tahap kecemasan</div>
          <div class="pri-grid">
            <div class="pri-opt" onclick="selPri(this,'rendah')">
              <span class="pi">🟢</span><span class="pl">Rendah</span>
            </div>
            <div class="pri-opt sel-sederhana" id="pri-def" onclick="selPri(this,'sederhana')">
              <span class="pi">🟡</span><span class="pl">Sederhana</span>
            </div>
            <div class="pri-opt" onclick="selPri(this,'tinggi')">
              <span class="pi">🔴</span><span class="pl">Tinggi</span>
            </div>
            <div class="pri-opt" onclick="selPri(this,'kritikal')">
              <span class="pi">🚨</span><span class="pl">Kritikal</span>
            </div>
          </div>
          <p style="font-size:11px;color:var(--faint);margin-top:10px;line-height:1.6">
            <strong>Kritikal</strong> — bahaya keselamatan / henti operasi sepenuhnya.<br>
            <strong>Tinggi</strong> — jejas pengeluaran, perlu tindak segera.<br>
            <strong>Sederhana / Rendah</strong> — boleh dijadualkan.
          </p>
        </div>
        <div class="btn-row">
          <button class="btn sec" onclick="goStep(1,33,'Langkah 1 / 3 — Maklumat Pelapor')"><i class="ti ti-arrow-left"></i> Balik</button>
          <button class="btn pri" onclick="next2()"><i class="ti ti-arrow-right"></i> Seterusnya</button>
        </div>
      </div>

      <!-- STEP 3 -->
      <div id="s3" style="display:none">
        <div class="card">
          <div class="card-title"><i class="ti ti-camera"></i> Gambar kerosakan (pilihan)</div>
          <div class="photo-box" onclick="document.getElementById('f-img').click()">
            <i class="ti ti-photo-plus"></i>
            <p>Ketik untuk ambil gambar atau pilih dari galeri</p>
            <span>JPG, PNG — maksimum 5 gambar</span>
          </div>
          <input type="file" id="f-img" multiple accept="image/*" style="display:none" onchange="previewImgs(this)">
          <div class="previews" id="prev-box"></div>
        </div>

        <div class="card">
          <div class="card-title"><i class="ti ti-file-check"></i> Semak sebelum hantar</div>
          <table class="summ-table" id="summary"></table>
        </div>

        <div class="btn-row">
          <button class="btn sec" onclick="goStep(2,66,'Langkah 2 / 3 — Butiran Kerosakan')"><i class="ti ti-arrow-left"></i> Balik</button>
          <button class="btn pri" id="btn-hantar" onclick="hantar()"><i class="ti ti-send"></i> Hantar Aduan</button>
        </div>
      </div>
    </div>

    <!-- TAB: SEMAK STATUS -->
    <div id="tab-semak" style="display:none">
      <div class="card">
        <div class="card-title"><i class="ti ti-search"></i> Semak status aduan</div>
        <div class="fg">
          <label>Nombor tiket aduan</label>
          <input type="text" id="f-tiket" placeholder="cth: ADU-2026-0042" style="font-family:monospace;letter-spacing:.5px">
          <span class="hint">Nombor tiket diberikan selepas anda menghantar aduan</span>
        </div>
        <button class="btn pri" style="margin-top:4px" onclick="semakStatus()"><i class="ti ti-search"></i> Semak Status</button>
      </div>

      <div class="status-result" id="status-result">
        <div class="status-badge-box">
          <p style="font-size:11px;color:var(--faint);margin-bottom:4px">Nombor Tiket</p>
          <div class="tid" id="semak-tid"></div>
          <div style="margin:10px 0"><span class="badge prog" id="semak-badge"><i class="ti ti-loader"></i> Dalam proses</span></div>
          <p style="font-size:12px;color:var(--muted)" id="semak-info"></p>
        </div>
        <div class="card">
          <div class="card-title"><i class="ti ti-map-pin"></i> Perkembangan aduan</div>
          <div class="tracker" id="tracker"></div>
        </div>
      </div>
    </div>
  </div>

  <!-- ═══ VIEW: BERJAYA ═══ -->
  <div class="view" id="v-success">
    <div class="success-screen">
      <div class="success-icon"><i class="ti ti-circle-check"></i></div>
      <h2>Aduan berjaya dihantar!</h2>
      <p>Penyelia penyelenggaraan akan menyemak aduan anda dalam masa 1 hari bekerja.</p>
    </div>
    <div class="ticket-box">
      <div class="tid" id="ticket-no">—</div>
      <p>📌 Simpan nombor tiket ini untuk menyemak status aduan anda kemudian.</p>
    </div>
    <div class="card">
      <div class="card-title"><i class="ti ti-info-circle"></i> Apa berlaku seterusnya?</div>
      <div class="tracker">
        <div class="ts">
          <div class="ts-left"><div class="ts-dot done"><i class="ti ti-check"></i></div><div class="ts-line done"></div></div>
          <div class="ts-body"><h4>Aduan diterima</h4><p>Baru sahaja</p></div>
        </div>
        <div class="ts">
          <div class="ts-left"><div class="ts-dot active"><i class="ti ti-eye"></i></div><div class="ts-line"></div></div>
          <div class="ts-body"><h4>Penyelia akan semak & sahkan</h4><p>Dalam masa 1 hari bekerja</p></div>
        </div>
        <div class="ts">
          <div class="ts-left"><div class="ts-dot"><i class="ti ti-tool"></i></div><div class="ts-line"></div></div>
          <div class="ts-body"><h4>Juruteknik akan ditugaskan</h4><p>Mengikut tahap keutamaan</p></div>
        </div>
        <div class="ts">
          <div class="ts-left"><div class="ts-dot"><i class="ti ti-flag"></i></div></div>
          <div class="ts-body"><h4>Kerja selesai — anda dimaklumkan</h4><p>—</p></div>
        </div>
      </div>
    </div>
    <button class="btn pri" onclick="resetForm()"><i class="ti ti-plus"></i> Hantar Aduan Lain</button>
    <button class="btn sec" style="margin-top:10px;width:100%;justify-content:center" onclick="semakSelepasHantar()"><i class="ti ti-search"></i> Semak Status Aduan</button>
  </div>

</div>

<script>
// ─── KONFIG ──────────────────────────────────────────────────
const CSRF      = document.querySelector('meta[name="csrf-token"]').content;
const URL_HANTAR = @json(route('aduan.hantar'));
const URL_SEMAK  = @json(url('/aduan/status')); // + '/' + tiket

// ─── STATE ───────────────────────────────────────────────────
let priVal = 'sederhana';
let imgs   = [];
let lastTicket = '';
const priMap = { rendah:'Rendah', sederhana:'Sederhana', tinggi:'Tinggi', kritikal:'Kritikal' };

// ─── NAVIGATION ──────────────────────────────────────────────
function showView(id) {
  document.querySelectorAll('.view').forEach(v => v.classList.remove('active'));
  document.getElementById(id).classList.add('active');
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

function showTab(t, el) {
  document.querySelectorAll('.tab').forEach(b => b.classList.remove('active'));
  el.classList.add('active');
  document.getElementById('tab-buat').style.display  = t === 'buat'  ? 'block' : 'none';
  document.getElementById('tab-semak').style.display = t === 'semak' ? 'block' : 'none';
}

function goStep(n, pct, lbl) {
  ['s1','s2','s3'].forEach((id, i) =>
    document.getElementById(id).style.display = (i + 1 === n) ? 'block' : 'none'
  );
  document.getElementById('prog-fill').style.width = pct + '%';
  document.getElementById('prog-lbl').textContent  = lbl;
  document.getElementById('prog-pct').textContent  = pct + '%';
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ─── VALIDATION ───────────────────────────────────────────────
function clrErr(id) {
  const el = document.getElementById(id);
  if (!el) return;
  el.classList.remove('err');
  const em = el.parentElement.querySelector('.err-msg');
  if (em) em.remove();
}
function addErr(id, msg) {
  const el = document.getElementById(id);
  if (!el) return;
  el.classList.add('err');
  if (!el.parentElement.querySelector('.err-msg')) {
    const em = document.createElement('span');
    em.className   = 'err-msg';
    em.textContent = msg;
    el.parentElement.appendChild(em);
  }
}

function next1() {
  let ok = true;
  ['f-nama', 'f-jab'].forEach(id => {
    clrErr(id);
    if (!document.getElementById(id).value.trim()) { addErr(id, 'Wajib diisi'); ok = false; }
  });
  if (!ok) return;
  goStep(2, 66, 'Langkah 2 / 3 — Butiran Kerosakan');
}

function next2() {
  let ok = true;
  ['f-item', 'f-lok', 'f-perihal'].forEach(id => {
    clrErr(id);
    if (!document.getElementById(id).value.trim()) { addErr(id, 'Wajib diisi'); ok = false; }
  });
  if (!ok) return;
  buildSummary();
  goStep(3, 100, 'Langkah 3 / 3 — Semak & Hantar');
}

// ─── PRIORITY ─────────────────────────────────────────────────
function selPri(el, val) {
  document.querySelectorAll('.pri-opt').forEach(o => o.className = 'pri-opt');
  el.classList.add('sel-' + val);
  priVal = val;
}

// ─── PHOTO PREVIEW ────────────────────────────────────────────
function previewImgs(input) {
  const files = [...input.files].slice(0, 5);
  imgs        = files;
  const box   = document.getElementById('prev-box');
  box.innerHTML = '';
  files.forEach((f, i) => {
    const url = URL.createObjectURL(f);
    const div = document.createElement('div');
    div.className = 'thumb';
    div.style.backgroundImage = `url(${url})`;
    const rm = document.createElement('button');
    rm.textContent = '×';
    rm.setAttribute('aria-label', 'Buang gambar');
    rm.onclick = () => { imgs.splice(i, 1); div.remove(); };
    div.appendChild(rm);
    box.appendChild(div);
  });
}

// ─── SUMMARY ──────────────────────────────────────────────────
const priLabel = { rendah:'🟢 Rendah', sederhana:'🟡 Sederhana', tinggi:'🔴 Tinggi', kritikal:'🚨 Kritikal' };

function buildSummary() {
  const rows = [
    ['Nama',        document.getElementById('f-nama').value],
    ['Bahagian',    document.getElementById('f-jab').value],
    ['Tel',         document.getElementById('f-tel').value || '—'],
    ['Peralatan',   document.getElementById('f-item').value],
    ['Lokasi',      document.getElementById('f-lok').value],
    ['Tarikh',      document.getElementById('f-tarikh').value || '—'],
    ['Keutamaan',   priLabel[priVal]],
    ['Penerangan',  document.getElementById('f-perihal').value],
    ['Gambar',      imgs.length ? imgs.length + ' fail' : 'Tiada'],
  ];
  document.getElementById('summary').innerHTML = rows
    .map(([k, v]) => `<tr><td>${k}</td><td>${escapeHtml(String(v))}</td></tr>`)
    .join('');
}

function escapeHtml(s) {
  return s.replace(/[&<>"']/g, c => ({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' }[c]));
}

// ─── HANTAR ───────────────────────────────────────────────────
async function hantar() {
  const btn = document.getElementById('btn-hantar');
  btn.disabled = true;
  btn.innerHTML = '<i class="ti ti-loader"></i> Menghantar...';

  const fd = new FormData();
  fd.append('nama_pelapor',       document.getElementById('f-nama').value.trim());
  fd.append('bahagian_pelapor',   document.getElementById('f-jab').value.trim());
  fd.append('no_telefon_pelapor', document.getElementById('f-tel').value.trim());
  fd.append('nama_peralatan',     document.getElementById('f-item').value.trim());
  fd.append('lokasi',             document.getElementById('f-lok').value.trim());
  fd.append('perihal_kerosakan',  document.getElementById('f-perihal').value.trim());
  fd.append('tarikh_rosak',       document.getElementById('f-tarikh').value);
  fd.append('keutamaan',          priMap[priVal]);
  imgs.forEach(f => fd.append('gambar[]', f));

  try {
    const res = await fetch(URL_HANTAR, {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
      body: fd,
    });

    if (res.status === 422) {
      const data = await res.json();
      const first = Object.values(data.errors || {})[0];
      alert(first ? first[0] : 'Sila lengkapkan semua medan wajib.');
      return;
    }
    if (!res.ok) throw new Error('Ralat pelayan');

    const data = await res.json();
    lastTicket = data.no_tiket;
    document.getElementById('ticket-no').textContent = lastTicket;
    showView('v-success');
  } catch (e) {
    alert('Maaf, aduan gagal dihantar. Sila cuba lagi.');
  } finally {
    btn.disabled = false;
    btn.innerHTML = '<i class="ti ti-send"></i> Hantar Aduan';
  }
}

// ─── SEMAK STATUS ─────────────────────────────────────────────
async function semakStatus() {
  const tid = document.getElementById('f-tiket').value.trim();
  if (!tid) { addErr('f-tiket', 'Sila masukkan nombor tiket'); return; }
  clrErr('f-tiket');

  try {
    const res = await fetch(URL_SEMAK + '/' + encodeURIComponent(tid), {
      headers: { 'Accept': 'application/json' },
    });

    if (res.status === 404) {
      addErr('f-tiket', 'Nombor tiket tidak dijumpai');
      document.getElementById('status-result').style.display = 'none';
      return;
    }
    if (!res.ok) throw new Error('Ralat pelayan');

    const data = await res.json();
    const badgeIcon = { baru:'ti-clock', prog:'ti-loader', selesai:'ti-circle-check' }[data.badge] || 'ti-loader';

    document.getElementById('semak-tid').textContent  = data.no_tiket;
    document.getElementById('semak-badge').className  = 'badge ' + data.badge;
    document.getElementById('semak-badge').innerHTML  = `<i class="ti ${badgeIcon}"></i> ${escapeHtml(data.status)}`;
    document.getElementById('semak-info').textContent = data.info;

    const t = document.getElementById('tracker');
    t.innerHTML = data.steps.map((s, i) => `
      <div class="ts">
        <div class="ts-left">
          <div class="ts-dot ${s.state}">
            <i class="ti ${s.state==='done' ? 'ti-check' : s.state==='active' ? 'ti-tool' : 'ti-circle'}"></i>
          </div>
          ${i < data.steps.length - 1 ? `<div class="ts-line ${s.state==='done' ? 'done' : ''}"></div>` : ''}
        </div>
        <div class="ts-body"><h4>${escapeHtml(s.label)}</h4><p>${escapeHtml(s.sub)}</p></div>
      </div>`).join('');

    document.getElementById('status-result').style.display = 'block';
    document.getElementById('status-result').scrollIntoView({ behavior: 'smooth' });
  } catch (e) {
    alert('Maaf, tidak dapat menyemak status. Sila cuba lagi.');
  }
}

function semakSelepasHantar() {
  showView('v-home');
  document.querySelectorAll('.tab')[1].click();
  document.getElementById('f-tiket').value = lastTicket;
  semakStatus();
}

// ─── RESET ────────────────────────────────────────────────────
function resetForm() {
  ['f-nama','f-jab','f-tel','f-item','f-lok','f-perihal','f-tiket']
    .forEach(id => { const el = document.getElementById(id); if (el) el.value = ''; });
  document.querySelectorAll('.pri-opt').forEach(o => o.className = 'pri-opt');
  document.getElementById('pri-def').classList.add('sel-sederhana');
  priVal = 'sederhana'; imgs = [];
  document.getElementById('prev-box').innerHTML = '';
  document.getElementById('f-img').value = '';
  document.getElementById('status-result').style.display = 'none';
  document.getElementById('f-tarikh').value = new Date().toISOString().split('T')[0];
  goStep(1, 33, 'Langkah 1 / 3 — Maklumat Pelapor');
  showView('v-home');
}

// ─── INIT ─────────────────────────────────────────────────────
document.getElementById('f-tarikh').value = new Date().toISOString().split('T')[0];
</script>
</body>
</html>
