@extends('layouts.user') {{-- sesuaikan nama layout user kamu --}}

@section('title', 'Buat Setoran')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>

<style>
  :root{
    --bg:#f3fff7;
    --card:#ffffff;
    --text:#0f172a;
    --muted:#64748b;
    --line:rgba(10,64,44,.14);
    --g1:#063a2a; --g2:#0b6b4d; --g3:#22c55e;

    --shadow: 0 18px 50px rgba(2,44,24,.10);
    --radius: 18px;
  }

  .page{
    max-width:980px;
    margin:0 auto;
    padding:18px;
    border-radius:18px;
    background:
      radial-gradient(900px 380px at 15% -10%, rgba(34,197,94,.18), transparent 60%),
      radial-gradient(820px 360px at 95% 10%, rgba(56,189,248,.10), transparent 55%),
      linear-gradient(180deg, #fff, var(--bg));
    color:var(--text);
    border:1px solid rgba(10,64,44,.10);
  }

  h2{margin:0 0 14px}
  .box{
    border:1px solid var(--line);
    padding:16px;
    border-radius:var(--radius);
    background:rgba(255,255,255,.90);
    box-shadow: var(--shadow);
  }
  .row{margin-bottom:12px}
  .flex{display:flex;gap:10px;align-items:center;flex-wrap:wrap}
  .between{display:flex;gap:10px;align-items:center;justify-content:space-between;flex-wrap:wrap}
  label{font-weight:900;display:block;margin-bottom:6px}
  input, select, textarea{
    padding:10px 12px;border-radius:14px;border:1px solid var(--line);
    background:#fff;outline:none;
  }
  input:focus, select:focus, textarea:focus{
    border-color: rgba(34,197,94,.35);
    box-shadow: 0 0 0 3px rgba(34,197,94,.10);
  }
  textarea{width:100%}
  table{border-collapse:collapse;width:100%;overflow:hidden;border-radius:14px}
  th,td{border:1px solid rgba(2,44,24,.12);padding:10px;vertical-align:top}
  th{background:rgba(34,197,94,.08);text-align:left}

  .btn{
    padding:10px 14px;border:1px solid rgba(2,44,24,.25);
    border-radius:14px;text-decoration:none;background:#fff;cursor:pointer;
    font-weight:900;
    display:inline-flex;align-items:center;gap:8px;
    transition: transform .15s ease, box-shadow .15s ease, filter .15s ease;
  }
  .btn:hover{transform: translateY(-1px); box-shadow: 0 12px 28px rgba(2,44,24,.10)}
  .btn-primary{
    background: linear-gradient(135deg, rgba(34,197,94,.98), rgba(8,68,51,.98));
    color:#fff;border-color:transparent;
  }
  .btn-soft{
    background: rgba(34,197,94,.10);
    border-color: rgba(34,197,94,.22);
    color: var(--g1);
  }
  .btn-danger{
    border-color:rgba(176,0,32,.5);
    color:#b00020;
    background:rgba(176,0,32,.06)
  }

  .muted{color:var(--muted);font-size:13px;font-weight:700}
  .err{
    background:#ffe7e7;border:1px solid #b00020;
    padding:10px;margin-bottom:15px;border-radius:14px
  }
  hr{border:none;border-top:1px solid rgba(2,44,24,.10);margin:16px 0}

  #map{height:340px;border-radius:16px;border:1px solid rgba(2,44,24,.12); overflow:hidden}

  .kpi{display:grid; grid-template-columns:1fr 1fr; gap:10px}
  @media (max-width:680px){ .kpi{grid-template-columns:1fr} }

  .pill{
    display:flex; align-items:center; justify-content:space-between; gap:10px;
    padding:10px 12px;border-radius:16px;
    border:1px solid rgba(34,197,94,.18);
    background:rgba(34,197,94,.10);
    font-weight:900;color:var(--g1);
  }
  .pill .label{font-weight:1000}
  .pill input{
    width:100%;
    border:1px solid rgba(2,44,24,.12);
    background:#fff;
    border-radius:12px;
    padding:8px 10px;
    font-weight:900;
    color:var(--g1);
  }

  .hint-box{
    border:1px dashed rgba(2,44,24,.18);
    background: rgba(255,255,255,.70);
    padding:10px 12px;
    border-radius: 16px;
  }

  .map-head{
    display:flex;align-items:center;justify-content:space-between;
    gap:10px;flex-wrap:wrap;margin-bottom:10px;
  }
  .map-actions{display:flex; gap:10px; flex-wrap:wrap}

  .status-good{color:var(--g2)}
  .status-bad{color:#b00020}
</style>
@endpush

@section('content')
<div class="page">
  <h2>🌿 Buat Setoran Sampah (Bisa Banyak Jenis)</h2>

  @if ($errors->any())
    <div class="err">
      <ul style="margin:0;padding-left:18px">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('user.setoran.store') }}">
    @csrf

    <div class="box">
      <div class="row flex">
        <div style="min-width:220px">
          <label>Metode *</label>
          <select name="metode" id="metodeSelect" required>
            <option value="antar" {{ old('metode','antar')=='antar'?'selected':'' }}>Antar sendiri</option>
            <option value="jemput" {{ old('metode')=='jemput'?'selected':'' }}>Jemput</option>
          </select>
        </div>
        <div class="muted">
          Jika <b>Jemput</b>: klik peta / geser marker. Koordinat & alamat akan terisi otomatis (alamat tetap bisa diedit).
        </div>
      </div>

      <div id="jemputFields" style="display:none;">
        <div class="row">
          <label>Alamat (untuk jemput) *</label>
          <input type="text" name="alamat" id="alamatInput" style="width:100%"
                 value="{{ old('alamat') }}" placeholder="Alamat lengkap...">
          <div class="muted" style="margin-top:6px">
            Alamat auto dari lokasi. Kalau kamu edit manual, tetap boleh.
          </div>
        </div>

        <div class="row flex">
          <button class="btn" type="button" id="btnGps">📍 Pakai Lokasi Saya (GPS)</button>
          <button class="btn btn-soft" type="button" id="btnAutoFill" title="Isi alamat dari titik saat ini">
            ✨ Gunakan Alamat Otomatis
          </button>
          <div class="muted" id="locStatus">Klik peta untuk memilih titik jemput.</div>
        </div>

        <div class="row kpi">
          <div class="pill">
            <div class="label">Latitude</div>
            <input type="text" id="latView" readonly placeholder="—">
          </div>
          <div class="pill">
            <div class="label">Longitude</div>
            <input type="text" id="lngView" readonly placeholder="—">
          </div>
        </div>

        <input type="hidden" name="latitude" id="latInput" value="{{ old('latitude') }}">
        <input type="hidden" name="longitude" id="lngInput" value="{{ old('longitude') }}">

        <div class="row">
          <div class="map-head">
            <div class="muted"><b>📌 Peta Titik Jemput</b></div>
            <div class="map-actions">
              <a class="btn btn-soft" id="gmapsLink" href="#" target="_blank" rel="noopener">🗺️ Buka Google Maps</a>
              <a class="btn btn-primary" id="gmapsDirLink" href="#" target="_blank" rel="noopener">🚗 Mulai Navigasi</a>
            </div>
          </div>

          <div id="map"></div>

          <div class="hint-box muted" style="margin-top:10px">
            <b>Tips:</b> klik peta untuk pasang marker, lalu geser marker untuk posisi yang tepat.
            Jika GPS diblokir browser, tetap bisa pakai klik peta.
          </div>
        </div>

        <div class="row" style="margin-top:10px;">
          <label>Jadwal Jemput (opsional)</label>
          <input type="datetime-local" name="jadwal_jemput" value="{{ old('jadwal_jemput') }}">
        </div>

        <hr>
      </div>

      <h3 style="margin:0 0 10px;">🧾 Item Sampah</h3>

      <table>
        <thead>
          <tr>
            <th style="width:40%">Jenis Sampah</th>
            <th style="width:15%">Jumlah</th>
            <th style="width:15%">Satuan</th>
            <th style="width:15%">Harga</th>
            <th style="width:15%">Subtotal</th>
            <th style="width:1%"></th>
          </tr>
        </thead>
        <tbody id="itemsBody"></tbody>
      </table>

      <div class="row" style="margin-top:10px">
        <button class="btn" type="button" onclick="addRow()">➕ Tambah Jenis</button>
      </div>

      <div class="row">
        <b>Total Estimasi: Rp <span id="grandTotal">0</span></b>
      </div>

      <div class="row">
        <label>Catatan (opsional)</label>
        <textarea name="catatan" rows="3">{{ old('catatan') }}</textarea>
      </div>

      <div class="flex">
        <button class="btn btn-primary" type="submit">✅ Kirim Setoran</button>
        <a class="btn" href="{{ route('user.dashboard') }}">⬅️ Kembali</a>
      </div>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

<script>
  // =========================
  // DATA ITEM
  // =========================
  const kategoriData = @json($kategoriData);
  const selectedId = @json((string)($selectedId ?? ''));

  function rupiah(n){
    try { return new Intl.NumberFormat('id-ID').format(n); }
    catch(e){ return n; }
  }

  function buildSelect(name, selectedValue=''){
    let html = `<select name="${name}" onchange="recalc()" required style="width:100%">`;
    html += `<option value="">-- pilih --</option>`;
    kategoriData.forEach(k => {
      const label = `${k.nama} (${k.kategori ?? '-'})`;
      const sel = (String(k.id) === String(selectedValue)) ? 'selected' : '';
      html += `<option value="${k.id}" data-harga="${k.harga}" data-satuan="${k.satuan}" ${sel}>${label}</option>`;
    });
    html += `</select>`;
    return html;
  }

  let rowIndex = 0;

  function addRow(prefillKategoriId = ''){
    const tbody = document.getElementById('itemsBody');
    const tr = document.createElement('tr');

    tr.innerHTML = `
      <td>${buildSelect(`items[${rowIndex}][kategori_sampah_id]`, prefillKategoriId)}</td>
      <td><input type="number" name="items[${rowIndex}][jumlah]" step="0.01" min="0.01" value="1"
                 oninput="recalc()" required style="width:100%"></td>
      <td class="satuanCell">-</td>
      <td class="hargaCell">-</td>
      <td class="subtotalCell">0</td>
      <td><button type="button" class="btn btn-danger" onclick="removeRow(this)">✖</button></td>
    `;

    tbody.appendChild(tr);
    rowIndex++;
    recalc();
  }

  function removeRow(btn){
    btn.closest('tr').remove();
    recalc();
  }

  function recalc(){
    const rows = Array.from(document.querySelectorAll('#itemsBody tr'));
    let grand = 0;

    rows.forEach(tr => {
      const select = tr.querySelector('select');
      const jumlahInput = tr.querySelector('input[type="number"]');

      const opt = select.options[select.selectedIndex];
      const harga = parseInt(opt?.dataset?.harga || '0', 10);
      const satuan = opt?.dataset?.satuan || '';

      const jumlah = parseFloat(jumlahInput.value || '0');
      const subtotal = Math.round(jumlah * harga);

      tr.querySelector('.satuanCell').innerText = satuan || '-';
      tr.querySelector('.hargaCell').innerText = harga ? ('Rp ' + rupiah(harga)) : '-';
      tr.querySelector('.subtotalCell').innerText = rupiah(subtotal);

      grand += subtotal;
    });

    document.getElementById('grandTotal').innerText = rupiah(grand);
  }

  addRow(selectedId || '');

  // =========================
  // JEMPUT TOGGLE
  // =========================
  const metodeSelect = document.getElementById('metodeSelect');
  const jemputFields = document.getElementById('jemputFields');

  function toggleJemput(){
    const isJemput = metodeSelect.value === 'jemput';
    jemputFields.style.display = isJemput ? 'block' : 'none';
    if(isJemput) setTimeout(()=>{ if(map) map.invalidateSize(); }, 200);
  }
  metodeSelect.addEventListener('change', toggleJemput);
  toggleJemput();

  // =========================
  // MAP + AUTO ADDRESS
  // =========================
  let map, marker;
  let lastAutoAddress = '';

  const locStatus   = document.getElementById('locStatus');
  const latInput    = document.getElementById('latInput');
  const lngInput    = document.getElementById('lngInput');
  const latView     = document.getElementById('latView');
  const lngView     = document.getElementById('lngView');
  const alamatInput = document.getElementById('alamatInput');

  const gmapsLink    = document.getElementById('gmapsLink');
  const gmapsDirLink = document.getElementById('gmapsDirLink');

  const defaultLat = 0.5071;
  const defaultLng = 101.4478;

  function updateLinks(lat, lng){
    gmapsLink.href = `https://www.google.com/maps?q=${lat},${lng}`;
    gmapsDirLink.href = `https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}&travelmode=driving`;
  }

  async function reverseGeocode(lat, lng){
    try{
      locStatus.innerHTML = `<span class="status-good">Mengambil alamat otomatis...</span>`;
      const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`;
      const res = await fetch(url, { headers: { 'Accept':'application/json', 'Accept-Language':'id' } });
      const data = await res.json();

      if(data && data.display_name){
        lastAutoAddress = data.display_name;

        if(!alamatInput.value || alamatInput.value.trim().length < 3){
          alamatInput.value = lastAutoAddress;
        }

        locStatus.innerHTML = `<span class="status-good">Alamat terisi otomatis. Kamu bisa edit jika perlu.</span>`;
      }else{
        locStatus.innerHTML = `<span class="status-bad">Alamat tidak ditemukan, isi manual.</span>`;
      }
    }catch(e){
      locStatus.innerHTML = `<span class="status-bad">Gagal ambil alamat otomatis, isi manual.</span>`;
    }
  }

  function setCoordinate(lat, lng, message){
    const latFixed = Number(lat);
    const lngFixed = Number(lng);

    latInput.value = latFixed;
    lngInput.value = lngFixed;

    latView.value = latFixed.toFixed(6);
    lngView.value = lngFixed.toFixed(6);

    locStatus.innerText = message;
    updateLinks(latFixed, lngFixed);

    if(!marker){
      marker = L.marker([latFixed, lngFixed], { draggable:true }).addTo(map);
      marker.on('dragend', function(e){
        const p = e.target.getLatLng();
        setCoordinate(p.lat, p.lng, 'Marker digeser. Memperbarui alamat...');
        reverseGeocode(p.lat, p.lng);
      });
    }else{
      marker.setLatLng([latFixed, lngFixed]);
    }

    map.setView([latFixed, lngFixed], 17);
    reverseGeocode(latFixed, lngFixed);
  }

  function initMap(){
    const oldLat = latInput.value;
    const oldLng = lngInput.value;

    const startLat = oldLat ? Number(oldLat) : defaultLat;
    const startLng = oldLng ? Number(oldLng) : defaultLng;

    map = L.map('map').setView([startLat, startLng], 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom:19,
      attribution:'© OpenStreetMap'
    }).addTo(map);

    map.on('click', function(e){
      setCoordinate(e.latlng.lat, e.latlng.lng, 'Titik dipilih. Mengambil alamat...');
    });

    if(oldLat && oldLng){
      setCoordinate(Number(oldLat), Number(oldLng), 'Lokasi jemput sudah tersimpan. Memperbarui alamat...');
    }else{
      latView.value = '';
      lngView.value = '';
      updateLinks(startLat, startLng);
    }
  }
  initMap();

  // GPS
  document.getElementById('btnGps').addEventListener('click', useMyLocation);

  function useMyLocation(){
    if(!navigator.geolocation){
      locStatus.innerHTML = `<span class="status-bad">Browser tidak mendukung GPS.</span>`;
      return;
    }

    if(navigator.permissions && navigator.permissions.query){
      navigator.permissions.query({ name: 'geolocation' }).then(p => {
        if(p.state === 'denied'){
          locStatus.innerHTML =
            `<span class="status-bad">GPS diblokir browser. Klik ikon 🔒 → Site settings → Location → Allow. Atau pilih titik lewat peta.</span>`;
        }
      }).catch(()=>{});
    }

    locStatus.innerHTML = `<span class="status-good">Mengambil lokasi GPS...</span>`;

    navigator.geolocation.getCurrentPosition(
      (pos) => {
        setCoordinate(pos.coords.latitude, pos.coords.longitude, 'Lokasi GPS dipakai. Mengambil alamat...');
      },
      (err) => {
        locStatus.innerHTML =
          `<span class="status-bad">GPS gagal (${err.message}). Kamu bisa pilih titik lewat peta.</span>`;
      },
      { enableHighAccuracy:true, timeout:12000, maximumAge:0 }
    );
  }

  // Button: gunakan alamat auto
  document.getElementById('btnAutoFill').addEventListener('click', () => {
    if(!lastAutoAddress){
      const lat = latInput.value, lng = lngInput.value;
      if(lat && lng){
        reverseGeocode(lat, lng);
      }else{
        alert('Pilih titik jemput dulu (klik peta / GPS).');
      }
      return;
    }
    alamatInput.value = lastAutoAddress;
    locStatus.innerHTML = `<span class="status-good">Alamat otomatis dipakai. Kamu bisa edit jika perlu.</span>`;
  });

  // VALIDASI SUBMIT
  document.querySelector('form').addEventListener('submit', function(e){
    if(metodeSelect.value === 'jemput'){
      if(!latInput.value || !lngInput.value){
        e.preventDefault();
        alert('Silakan pilih titik jemput di peta (klik peta / geser marker).');
        return;
      }
      if(!alamatInput.value || alamatInput.value.trim().length < 3){
        e.preventDefault();
        alert('Alamat wajib diisi jika metode jemput.');
        return;
      }
    }
  });
</script>
@endpush
