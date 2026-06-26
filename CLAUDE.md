# CLAUDE.md — Sistem Pengurusan Penyelenggaraan (Filament v5 + Laravel)

Ini adalah panduan lengkap untuk Claude Code CLI membina sistem pengurusan penyelenggaraan peralatan bagi DEPC & HQ menggunakan **Laravel + Filament v5**.

---

## Gambaran Sistem

Sistem ini mempunyai DUA bahagian:

| Bahagian | URL | Teknologi | Pengguna |
|---|---|---|---|
| Portal Aduan Staff | `/aduan` | Blade biasa (tanpa login) | Semua staff |
| Panel Dalaman | `/admin` | Filament v5 | Penyelia, Juruteknik, Pengurus, Admin |

---

## Stack

```
Laravel 11+
Filament v5
Spatie Laravel Permission (roles & permissions)
MySQL / PostgreSQL
Tailwind CSS (via Filament)
Livewire 4 (via Filament)
```

---

## LANGKAH 1 — Pasang Laravel & Filament

```bash
# Buat projek Laravel baru
composer create-project laravel/laravel sistem-penyelenggaraan
cd sistem-penyelenggaraan

# Pasang Filament v5
composer require filament/filament

# Pasang Spatie Permission
composer require spatie/laravel-permission

# Publish & migrate
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan filament:install --panels
php artisan migrate

# Buat akaun admin pertama
php artisan make:filament-user
```

---

## LANGKAH 2 — Database Schema

Bina migrations berikut mengikut urutan:

### 2a. Kemaskini `users` table (tambah kolom)
```bash
php artisan make:migration add_fields_to_users_table
```
```php
// Tambah dalam migration:
$table->string('bahagian')->nullable();       // FP, CP, D/S, HQ dll
$table->string('no_telefon')->nullable();
$table->string('jawatan')->nullable();         // Juruteknik, Penyelia dll
$table->boolean('is_active')->default(true);
```

### 2b. Buat table `aduan`
```bash
php artisan make:migration create_aduan_table
```
```php
Schema::create('aduan', function (Blueprint $table) {
    $table->id();
    $table->string('no_tiket')->unique();          // ADU-2026-0001
    $table->string('nama_pelapor');
    $table->string('bahagian_pelapor');
    $table->string('no_telefon_pelapor')->nullable();
    $table->string('nama_peralatan');
    $table->string('lokasi');
    $table->text('perihal_kerosakan');
    $table->date('tarikh_rosak');
    $table->enum('keutamaan', ['Rendah','Sederhana','Tinggi','Kritikal'])->default('Sederhana');
    $table->enum('status', ['Baru','Dalam Proses','Selesai','Ditutup'])->default('Baru');
    $table->enum('kategori', ['Elektrikal','Mekanikal','Paip','Penyejukan','Struktur','Lain-lain'])->nullable();
    $table->foreignId('juruteknik_id')->nullable()->constrained('users')->nullOnDelete();
    $table->foreignId('disahkan_oleh')->nullable()->constrained('users')->nullOnDelete();
    $table->foreignId('diluluskan_oleh')->nullable()->constrained('users')->nullOnDelete();
    $table->date('tarikh_sasaran_siap')->nullable();
    $table->date('tarikh_siap')->nullable();
    $table->text('catatan_penyelia')->nullable();
    $table->text('tindakan_juruteknik')->nullable();
    $table->timestamps();
    $table->softDeletes();
});
```

### 2c. Buat table `gambar_aduan`
```bash
php artisan make:migration create_gambar_aduan_table
```
```php
Schema::create('gambar_aduan', function (Blueprint $table) {
    $table->id();
    $table->foreignId('aduan_id')->constrained()->cascadeOnDelete();
    $table->string('path');
    $table->string('nama_asal')->nullable();
    $table->timestamps();
});
```

### 2d. Buat table `nota_aduan` (log aktiviti)
```bash
php artisan make:migration create_nota_aduan_table
```
```php
Schema::create('nota_aduan', function (Blueprint $table) {
    $table->id();
    $table->foreignId('aduan_id')->constrained()->cascadeOnDelete();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->string('jenis');   // 'tugasan', 'kemaskini', 'selesai', 'nota'
    $table->text('kandungan');
    $table->timestamps();
});
```

---

## LANGKAH 3 — Models

### 3a. Model Aduan
```bash
php artisan make:model Aduan
```
```php
// app/Models/Aduan.php
class Aduan extends Model
{
    use SoftDeletes;

    protected $table = 'aduan';

    protected $fillable = [
        'no_tiket', 'nama_pelapor', 'bahagian_pelapor', 'no_telefon_pelapor',
        'nama_peralatan', 'lokasi', 'perihal_kerosakan', 'tarikh_rosak',
        'keutamaan', 'status', 'kategori', 'juruteknik_id',
        'disahkan_oleh', 'diluluskan_oleh', 'tarikh_sasaran_siap',
        'tarikh_siap', 'catatan_penyelia', 'tindakan_juruteknik',
    ];

    protected $casts = [
        'tarikh_rosak' => 'date',
        'tarikh_sasaran_siap' => 'date',
        'tarikh_siap' => 'date',
    ];

    public function juruteknik() { return $this->belongsTo(User::class, 'juruteknik_id'); }
    public function pengesah()   { return $this->belongsTo(User::class, 'disahkan_oleh'); }
    public function pelulus()    { return $this->belongsTo(User::class, 'diluluskan_oleh'); }
    public function gambar()     { return $this->hasMany(GambarAduan::class); }
    public function nota()       { return $this->hasMany(NotaAduan::class)->latest(); }

    // Auto-jana no tiket
    protected static function booted(): void
    {
        static::creating(function (Aduan $aduan) {
            $tahun = now()->year;
            $bil   = static::whereYear('created_at', $tahun)->count() + 1;
            $aduan->no_tiket = 'ADU-' . $tahun . '-' . str_pad($bil, 4, '0', STR_PAD_LEFT);
        });
    }
}
```

### 3b. Model GambarAduan
```bash
php artisan make:model GambarAduan
```
```php
class GambarAduan extends Model
{
    protected $table = 'gambar_aduan';
    protected $fillable = ['aduan_id', 'path', 'nama_asal'];

    public function aduan() { return $this->belongsTo(Aduan::class); }
    public function getUrlAttribute() { return Storage::url($this->path); }
}
```

### 3c. Model NotaAduan
```bash
php artisan make:model NotaAduan
```
```php
class NotaAduan extends Model
{
    protected $table = 'nota_aduan';
    protected $fillable = ['aduan_id', 'user_id', 'jenis', 'kandungan'];

    public function aduan() { return $this->belongsTo(Aduan::class); }
    public function user()  { return $this->belongsTo(User::class); }
}
```

---

## LANGKAH 4 — Roles & Permissions

```bash
php artisan make:seeder RolePermissionSeeder
```
```php
// database/seeders/RolePermissionSeeder.php
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

public function run(): void
{
    // Permissions
    $permissions = [
        'aduan.view_any', 'aduan.view', 'aduan.create', 'aduan.update', 'aduan.delete',
        'aduan.assign',     // tugaskan juruteknik
        'aduan.verify',     // sahkan selesai (penyelia)
        'aduan.approve',    // lulus akhir (pengurus)
        'user.manage',      // urus pengguna
        'laporan.view',     // lihat laporan
    ];

    foreach ($permissions as $p) {
        Permission::firstOrCreate(['name' => $p]);
    }

    // Roles
    $admin = Role::firstOrCreate(['name' => 'Pentadbir']);
    $admin->syncPermissions(Permission::all());

    $pengurus = Role::firstOrCreate(['name' => 'Pengurus Operasi']);
    $pengurus->syncPermissions([
        'aduan.view_any','aduan.view','aduan.approve','laporan.view'
    ]);

    $penyelia = Role::firstOrCreate(['name' => 'Penyelia Penyelenggaraan']);
    $penyelia->syncPermissions([
        'aduan.view_any','aduan.view','aduan.create','aduan.update',
        'aduan.assign','aduan.verify','laporan.view'
    ]);

    $juruteknik = Role::firstOrCreate(['name' => 'Juruteknik']);
    $juruteknik->syncPermissions([
        'aduan.view','aduan.update'  // hanya aduan sendiri
    ]);
}
```
```bash
php artisan db:seed --class=RolePermissionSeeder
```

---

## LANGKAH 5 — Filament Panel Setup

```bash
php artisan make:filament-panel admin
```

```php
// app/Providers/Filament/AdminPanelProvider.php
public function panel(Panel $panel): Panel
{
    return $panel
        ->default()
        ->id('admin')
        ->path('admin')
        ->login()
        ->colors(['primary' => Color::Teal])
        ->brandName('SysPenyelenggaraan')
        ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
        ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
        ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
        ->middleware([...])
        ->authMiddleware([Authenticate::class]);
}
```

---

## LANGKAH 6 — Filament Resources

### 6a. Resource Aduan (UTAMA)
```bash
php artisan make:filament-resource Aduan --generate
```

Selepas generate, kemaskini `AduanResource.php` dengan:

```php
// Table columns
Tables\Columns\TextColumn::make('no_tiket')->searchable()->sortable()->copyable(),
Tables\Columns\TextColumn::make('nama_peralatan')->searchable()->limit(30),
Tables\Columns\TextColumn::make('lokasi')->searchable(),
Tables\Columns\TextColumn::make('bahagian_pelapor'),
Tables\Columns\BadgeColumn::make('keutamaan')
    ->colors([
        'success' => 'Rendah',
        'warning' => 'Sederhana',
        'danger'  => ['Tinggi', 'Kritikal'],
    ]),
Tables\Columns\BadgeColumn::make('status')
    ->colors([
        'danger'   => 'Baru',
        'warning'  => 'Dalam Proses',
        'success'  => 'Selesai',
        'gray'     => 'Ditutup',
    ]),
Tables\Columns\TextColumn::make('juruteknik.name')->label('Juruteknik'),
Tables\Columns\TextColumn::make('tarikh_rosak')->date('d/m/Y')->sortable(),

// Table filters
Tables\Filters\SelectFilter::make('status')
    ->options(['Baru'=>'Baru','Dalam Proses'=>'Dalam Proses','Selesai'=>'Selesai']),
Tables\Filters\SelectFilter::make('keutamaan')
    ->options(['Rendah'=>'Rendah','Sederhana'=>'Sederhana','Tinggi'=>'Tinggi','Kritikal'=>'Kritikal']),
Tables\Filters\SelectFilter::make('juruteknik_id')
    ->relationship('juruteknik', 'name')->label('Juruteknik'),

// Table actions
Tables\Actions\Action::make('tugaskan')
    ->label('Tugaskan')
    ->icon('heroicon-o-user-plus')
    ->color('primary')
    ->visible(fn($record) => $record->status === 'Baru' && auth()->user()->can('aduan.assign'))
    ->form([
        Forms\Components\Select::make('juruteknik_id')
            ->label('Juruteknik')
            ->options(User::role('Juruteknik')->pluck('name','id'))
            ->required(),
        Forms\Components\DatePicker::make('tarikh_sasaran_siap')->label('Sasaran siap'),
        Forms\Components\Textarea::make('catatan_penyelia')->label('Catatan'),
    ])
    ->action(function ($record, array $data) {
        $record->update([
            ...$data,
            'status' => 'Dalam Proses',
        ]);
        NotaAduan::create([
            'aduan_id'   => $record->id,
            'user_id'    => auth()->id(),
            'jenis'      => 'tugasan',
            'kandungan'  => 'Ditugaskan kepada ' . User::find($data['juruteknik_id'])->name,
        ]);
        Notification::make()->title('Juruteknik berjaya ditugaskan')->success()->send();
    }),

Tables\Actions\Action::make('selesai')
    ->label('Tandakan Selesai')
    ->icon('heroicon-o-check-circle')
    ->color('success')
    ->visible(fn($record) => $record->status === 'Dalam Proses' && auth()->user()->can('aduan.verify'))
    ->requiresConfirmation()
    ->action(function ($record) {
        $record->update(['status' => 'Selesai', 'tarikh_siap' => now()]);
        Notification::make()->title('Aduan ditanda selesai')->success()->send();
    }),
```

### 6b. Resource User
```bash
php artisan make:filament-resource User --generate
```
```php
// Tambah field role dalam form
Forms\Components\Select::make('roles')
    ->relationship('roles', 'name')
    ->multiple()
    ->preload()
    ->label('Peranan'),
Forms\Components\Select::make('bahagian')
    ->options([
        'FP' => 'Further Processing (FP)',
        'CP' => 'Chilling Plant (CP)',
        'D/S' => 'Dirty Side (D/S)',
        'Blast' => 'Blast Freezer',
        'HQ' => 'HQ / Pejabat',
    ]),
```

---

## LANGKAH 7 — Dashboard Widgets

```bash
php artisan make:filament-widget AduanStatsWidget --stats-overview
php artisan make:filament-widget AduanKritikal
php artisan make:filament-widget AduanChart
```

```php
// AduanStatsWidget.php
protected function getStats(): array
{
    return [
        Stat::make('Belum Ditindak', Aduan::where('status','Baru')->count())
            ->color('danger')->icon('heroicon-o-exclamation-circle'),
        Stat::make('Dalam Proses', Aduan::where('status','Dalam Proses')->count())
            ->color('warning')->icon('heroicon-o-arrow-path'),
        Stat::make('Selesai Bulan Ini', Aduan::where('status','Selesai')
            ->whereMonth('tarikh_siap', now()->month)->count())
            ->color('success')->icon('heroicon-o-check-circle'),
        Stat::make('Jumlah Bulan Ini', Aduan::whereMonth('created_at', now()->month)->count())
            ->icon('heroicon-o-clipboard-list'),
    ];
}
```

---

## LANGKAH 8 — Portal Aduan Staff (Blade — Tanpa Login)

```bash
php artisan make:controller AduanStaffController
```

```php
// routes/web.php
Route::get('/aduan', [AduanStaffController::class, 'borang'])->name('aduan.borang');
Route::post('/aduan', [AduanStaffController::class, 'hantar'])->name('aduan.hantar');
Route::get('/aduan/status', [AduanStaffController::class, 'status'])->name('aduan.status');
Route::get('/aduan/status/{tiket}', [AduanStaffController::class, 'semak'])->name('aduan.semak');
```

```php
// AduanStaffController.php
public function hantar(Request $request)
{
    $validated = $request->validate([
        'nama_pelapor'       => 'required|string|max:100',
        'bahagian_pelapor'   => 'required|string',
        'no_telefon_pelapor' => 'nullable|string|max:20',
        'nama_peralatan'     => 'required|string|max:200',
        'lokasi'             => 'required|string|max:200',
        'perihal_kerosakan'  => 'required|string',
        'tarikh_rosak'       => 'required|date',
        'keutamaan'          => 'required|in:Rendah,Sederhana,Tinggi,Kritikal',
        'gambar.*'           => 'nullable|image|max:5120',
    ]);

    $aduan = Aduan::create($validated);

    // Simpan gambar
    if ($request->hasFile('gambar')) {
        foreach ($request->file('gambar') as $file) {
            $path = $file->store('aduan/' . $aduan->id, 'public');
            GambarAduan::create([
                'aduan_id'  => $aduan->id,
                'path'      => $path,
                'nama_asal' => $file->getClientOriginalName(),
            ]);
        }
    }

    // TODO: Hantar notifikasi kepada penyelia
    // Notification::send($penyelia, new AduanBaruNotification($aduan));

    return redirect()->route('aduan.semak', $aduan->no_tiket)
        ->with('success', 'Aduan berjaya dihantar! Sila simpan nombor tiket anda.');
}
```

Salin fail HTML portal dari `public/portal-aduan.html` ke `resources/views/staff/aduan.blade.php` dan sambung ke controller di atas.

---

## LANGKAH 9 — Notifikasi (Pilihan)

### Email
```bash
php artisan make:notification AduanBaruNotification
php artisan make:notification AduanSelesaiNotification
```

### Telegram (percuma, lebih mudah)
```bash
composer require irazasyed/telegram-bot-sdk
```
```env
TELEGRAM_BOT_TOKEN=your_bot_token
TELEGRAM_CHAT_ID=your_group_chat_id
```

---

## LANGKAH 10 — Juruteknik View (Panel berasingan)

Juruteknik hanya nampak aduan yang ditugaskan kepada mereka:

```php
// Dalam AduanResource.php — overide query untuk juruteknik
public static function getEloquentQuery(): Builder
{
    $query = parent::getEloquentQuery();

    if (auth()->user()->hasRole('Juruteknik')) {
        $query->where('juruteknik_id', auth()->id());
    }

    return $query;
}
```

---

## Senarai Arahan Claude Code CLI

Guna arahan ini satu persatu dalam terminal Claude Code:

```
"Bina semua migrations untuk sistem penyelenggaraan mengikut schema dalam CLAUDE.md"

"Bina Model Aduan dengan semua relationships dan auto-generate no tiket"

"Jalankan RolePermissionSeeder dan buat seeder untuk data sample"

"Bina AduanResource Filament dengan table columns, filters, dan actions tugaskan juruteknik"

"Bina AduanStatsWidget dengan 4 stat cards mengikut CLAUDE.md"

"Bina AduanStaffController dan sambung portal-aduan.html sebagai Blade template"

"Bina Filament custom page untuk Laporan Bulanan dengan export PDF"

"Setup Spatie Permission dan pastikan setiap role ada akses yang betul"

"Bina notifikasi Telegram bila ada aduan kritikal masuk"

"Bina artisan command untuk hantar ringkasan harian kepada penyelia"
```

---

## Fail Rujukan

| Fail | Tujuan |
|---|---|
| `public/portal-aduan.html` | Reka bentuk portal staff — tukar ke Blade |
| `public/dashboard-sv.html` | Rujukan UI dashboard — Filament akan ganti ini |

---

## Warna Sistem

```php
// Dalam AdminPanelProvider.php
->colors(['primary' => Color::Teal])

// Keutamaan badge colors
'Rendah'   => 'success'   // hijau
'Sederhana' => 'warning'  // oren
'Tinggi'   => 'danger'    // merah
'Kritikal' => 'danger'    // merah

// Status badge colors
'Baru'        => 'danger'   // merah
'Dalam Proses' => 'warning' // oren
'Selesai'     => 'success'  // hijau
'Ditutup'     => 'gray'
```

---

## Nota Penting

- **Portal aduan (`/aduan`) mesti boleh akses tanpa login** — jangan letak auth middleware
- **Semua label dalam Bahasa Melayu** — guna `->label('...')` dalam setiap Filament field
- **Mobile-first** — Filament sudah responsive, tapi pastikan portal Blade juga responsive
- **Soft delete** untuk aduan — jangan hard delete, simpan untuk audit
- **No tiket auto-jana** dalam model `booted()` — jangan bagi user input manual

---

## LANGKAH 11 — Deploy ke VPS via GitHub Actions

### Setup GitHub Secrets (buat sekali sahaja)

Pergi ke **GitHub repo → Settings → Secrets and variables → Actions**, tambah:

| Secret | Nilai |
|---|---|
| `VPS_HOST` | IP VPS anda (contoh: `103.x.x.x`) |
| `VPS_USER` | Username SSH (contoh: `ubuntu` atau `root`) |
| `VPS_SSH_KEY` | Private key SSH (hasil dari `setup-vps.sh`) |
| `VPS_PORT` | `22` (atau port SSH anda) |

### Aliran kerja selepas setup

```
Anda edit kod di local / Claude Code
    ↓
git push origin main
    ↓
GitHub Actions trigger auto
    ↓
SSH masuk VPS → git pull → migrate → cache → restart
    ↓
Sistem live dalam masa < 2 minit
```

### Struktur folder dalam VPS

```
/var/www/sistem-penyelenggaraan/    ← root projek
├── public/                          ← Nginx document root
├── storage/
│   └── app/public/aduan/            ← Gambar aduan staff
├── .env                             ← Konfigurasi (JANGAN commit ke GitHub)
└── .github/workflows/deploy.yml    ← CI/CD workflow
```

### URL sistem

```
Portal Staff (tanpa login) : https://yourdomain.com/aduan
Panel Dalaman (Filament)   : https://yourdomain.com/admin
```
