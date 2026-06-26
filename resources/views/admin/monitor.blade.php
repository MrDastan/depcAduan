@extends('admin.layout')
@section('title', 'Monitor Masa Nyata')
@section('page-title', 'Monitor Masa Nyata')

@section('content')

  {{-- QR Code Panel --}}
  <div style="background:#fff;border:1px solid #E2E5EA;border-radius:12px;padding:20px;margin-bottom:20px;display:flex;align-items:center;gap:24px;flex-wrap:wrap">
    <div id="qrcode"></div>
    <div style="flex:1;min-width:200px">
      <p style="font-size:13px;font-weight:600;margin-bottom:6px">QR Code Portal Aduan Staff</p>
      <p style="font-size:12px;color:#6B7280;margin-bottom:12px;line-height:1.6">
        Tampal QR code ini di papan notis kilang. Staff boleh imbas untuk hantar aduan tanpa perlu ingat URL atau log masuk.
      </p>
      <div style="display:flex;gap:8px;flex-wrap:wrap">
        <span style="font-size:12px;background:#F5F6F8;border:1px solid #E2E5EA;padding:6px 12px;border-radius:8px;font-family:monospace;color:#1A1D23" id="portal-url"></span>
        <button class="btn" onclick="copyUrl()"><i class="ti ti-copy"></i> Salin URL</button>
        <button class="btn pri" onclick="printQR()"><i class="ti ti-printer"></i> Cetak QR</button>
      </div>
    </div>
  </div>

  {{-- Live Monitor --}}
  @livewire('admin.live-monitor')

@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
  const portalUrl = window.location.origin + '/aduan';
  document.getElementById('portal-url').textContent = portalUrl;

  new QRCode(document.getElementById('qrcode'), {
    text: portalUrl,
    width: 140,
    height: 140,
    colorDark: '#1A1D23',
    colorLight: '#ffffff',
    correctLevel: QRCode.CorrectLevel.H
  });

  function copyUrl() {
    navigator.clipboard.writeText(portalUrl);
    showToast('✅ URL disalin: ' + portalUrl);
  }

  function printQR() {
    const url = portalUrl;
    const win = window.open('', '_blank');
    win.document.write(`
      <!DOCTYPE html><html><head><title>QR Portal Aduan</title>
      <style>body{display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:100vh;font-family:sans-serif;padding:40px}
      h2{font-size:20px;font-weight:700;margin-bottom:8px}
      p{font-size:13px;color:#6B7280;margin-bottom:24px;text-align:center}
      #qr{margin-bottom:20px}
      .url{font-family:monospace;font-size:13px;background:#F5F6F8;padding:8px 16px;border-radius:8px;border:1px solid #E2E5EA}
      .foot{margin-top:32px;font-size:11px;color:#9CA3AF}</style>
      </head><body>
      <h2>Portal Aduan Kerosakan</h2>
      <p>Imbas QR code di bawah untuk hantar aduan kerosakan<br>tanpa perlu log masuk.</p>
      <div id="qr"></div>
      <div class="url">${url}</div>
      <div class="foot">SysPenyelenggaraan DEPC & HQ</div>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"><\/script>
      <script>new QRCode(document.getElementById('qr'),{text:'${url}',width:200,height:200,colorDark:'#1A1D23',colorLight:'#ffffff',correctLevel:QRCode.CorrectLevel.H});
      setTimeout(()=>window.print(),800);<\/script>
      </body></html>`);
    win.document.close();
  }
</script>
@endsection
