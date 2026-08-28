# Rencana Eksekusi — KeySaku

Cara pakai: user memicu dengan "kerjakan Task N sesuai
docs/EXECUTION-PLAN.md". Aturan kerja ada di CLAUDE.md.

Prinsip: backend dan alur dibereskan sampai solid, frontend dikerjakan
terakhir sekali jalan.

Status: BELUM / JALAN / SELESAI

| # | Task | Status | Branch |
|---|------|--------|--------|
| 1 | Money formatter backend | SELESAI | refactor/money-formatting |
| 2 | Salvage parser + normalizeAmount | SELESAI | feat/voice-income-support |
| 2.5 | Word-boundary category match + fuzzy threshold | SELESAI | fix/fuzzy-category-threshold |
| 3 | Observer kategori + test otorisasi | SELESAI | fix/category-seeding |
| 4 | Trait scoping + Policy | SELESAI | refactor/tenant-scoping |
| 5 | Saldo awal + semantik earmarking | SELESAI | feat/initial-balance |
| 6 | Household / family sharing | SELESAI | feat/household |
| 7 | Export PDF/CSV | SELESAI | feat/export |
| 8 | CategorySuggester v1 | SELESAI | feat/category-suggester |
| 9 | Frontend: redesign + migrasi format uang | SELESAI | feat/frontend-rework |

---

## Task 1 — Money formatter backend  [SELESAI]

Lapisan server-side formatter uang: config/currencies.php,
app/Support/Money.php, helper money() global, resources/js/money.js
sebagai referensi paritas. Migrasi call site sengaja ditunda ke Task 9.

---

## Task 2 — Salvage parser + normalizeAmount

Dependensi: Task 1 merged.

Tujuan: menyelamatkan perbaikan VoiceTransactionParser dari branch arsip
ui/bento-dark-dashboard, beserta test-nya. Di main, input suara selalu
dianggap pengeluaran, cuma paham "ribu", dan tanggal selalu hari ini.

DIKERJAKAN:
- `git checkout ui/bento-dark-dashboard -- <file>` untuk 4 file:
  app/Services/VoiceTransactionParser.php
  app/Livewire/Transactions/VoiceTransaction.php
  tests/Unit/VoiceTransactionParserTest.php
  tests/Feature/Transactions/VoiceTransactionTest.php
- normalizeAmount() dari CreateTransaction.php — KETIK MANUAL, jangan
  checkout file itu
- Test baru untuk normalizeAmount: "50.000" → 50000, "1.500.000" → 1500000

TIDAK DIKERJAKAN:
- app/Http/Controllers/TransactionController.php dan route POST /transaksi
- seedDefaultsIfEmpty() (jadi Task 3)
- perubahan save() jadi return redirect
- perubahan view apa pun

Wajib:
- Diff kedua file test harus MURNI PENAMBAHAN case. Satu saja case lama
  berubah/dihapus → BERHENTI.
- Konfirmasi kontrak view utuh: transcript, parsedDraft, canSave, parse,
  save, clearParsedDraft masih ada di komponen baru.
- Konfirmasi gate hardcoded Category::TYPE_EXPENSE sekarang baca type
  dari parsedDraft.

Commit:
1. feat(voice): support income transactions and richer amount parsing
2. fix(transactions): accept thousand-separated amount input

Definition of done:
- 122+ test hijau, nol test lama merah
- Verifikasi manual: ucapkan kalimat uji untuk income, nominal juta,
  dan "kemarin" — tuliskan kalimatnya untuk user

Risiko diterima: type bisa flip ke income; Levenshtein ≤2 bisa
false-positive; tanggal bisa jadi kemarin.

---

## Task 2.5 — Word-boundary category match + fuzzy threshold  [SELESAI]

Substring match sebelumnya menerima kecocokan di tengah kata, sehingga
"gajih" cocok ke "Gaji" — user melihat kategori salah di preview dan
gate save menolak dengan pesan ambigu. Substring sekarang wajib cocok di
batas kata (\b), dan fuzzy Levenshtein dimatikan untuk kata < 5 huruf
(ambang floor(len/4), maks 2 untuk kata lebih panjang).

Presedensi tipe dari commit 500e171 tidak disentuh.

---

## Task 3 — Observer kategori + test otorisasi

Dependensi: Task 2 merged.

Tujuan: user baru di main tidak punya kategori. 13 default baru lahir
sebagai efek samping membuka /kategori — halaman yang tidak ada di
navigasi utama. Akibatnya onboarding memutar 4 langkah, DAN user yang
sengaja menghapus semua kategorinya melihat 13 default bangkit lagi.

DIKERJAKAN:
- app/Observers/UserObserver.php — seed 13 kategori default pada
  User::created. Daftar kategori diambil dari CategoryManager yang ada.
- Daftar kategori default dipindah ke satu sumber (mis. config atau
  Action class), jangan diduplikasi.
- HAPUS pemanggilan seedDefaultsIfEmpty() dari CategoryManager::mount()
- Artisan command backfill untuk user lama yang belum punya kategori
- Test: user baru registrasi → punya 13 kategori
- Test: user hapus semua kategori → buka /kategori → TETAP kosong
- Test otorisasi: loop semua route ber-parameter, login sebagai user A,
  akses milik user B, pastikan 404
- Cek penanganan restrictOnDelete: hapus kategori yang punya transaksi →
  apakah user dapat pesan ramah atau layar error SQLSTATE?

TIDAK DIKERJAKAN:
- Perubahan view (empty state di halaman suara masuk Task 9)
- Policy dan global scope (Task 4)

Definition of done:
- Registrasi user baru langsung punya kategori, nol langkah tambahan
- Menghapus semua kategori bersifat permanen
- Test otorisasi menutup semua route ber-parameter
- Penanganan restrictOnDelete terkonfirmasi (perbaiki kalau kasar)

---

## Task 4 — Trait scoping + Policy

Dependensi: Task 3 merged.

Tujuan: murni refactor, NOL perubahan perilaku. Menyiapkan fondasi
untuk Task 6. Sekarang scoping manual `auth()->user()->relasi()` di 37
call site, nol Policy.

DIKERJAKAN:
- Trait BelongsToUser: global scope + auto-fill user_id saat create
- Pasang di Category, Transaction, Budget, SavingsGoal
- Tambah user_id ke savings_contributions (migration + backfill dari
  savings_goals) — ini juga menutup lubang otorisasi
- Policy untuk 5 model, ganti abort_unless inline dengan authorize()
- Tambah index: (user_id, type) dan (user_id, transaction_date) di
  transactions

TIDAK DIKERJAKAN:
- Konsep household (Task 6)
- Perubahan view

Definition of done:
- SELURUH test lama hijau TANPA dimodifikasi — ini ukuran keberhasilan
- Test otorisasi dari Task 3 tetap hijau
- Nol perubahan perilaku yang terlihat user

Penyimpangan terkonfirmasi dari rencana (opsi a-revisi, disetujui saat
eksekusi):
- Global scope query-level TIDAK diaktifkan di mana pun. Dibuktikan
  empiris dua kali: (1) dipasang di Budget saja langsung merusak
  AuthorizationTest (assertion `Budget::find()` pasca cross-user delete
  ikut ter-filter dan balik null padahal budget tidak terhapus); (2)
  dipasang di SavingsContribution merusak 3 test ManageSavingsGoalsTest
  karena fixture-nya create kontribusi sebelum actingAs, jadi user_id
  masih null saat scope aktif. Trait BelongsToUser yang dipakai hanya
  auto-fill user_id saat create, tanpa global scope.
- Policy dipakai HANYA di titik yang polanya memang "bandingkan
  user_id manual" (EditTransaction + PageController::editTransaction,
  4 call site) — via `abort_unless(auth()->user()->can(...), 404)`
  supaya status tetap 404, bukan 403 dari $this->authorize() yang
  defaultnya melempar 403 dan akan merusak AuthorizationTest. Pola
  whereKey()->first()+abort_if di CategoryManager/ManageBudgets/
  ManageSavingsGoals TIDAK disentuh — sudah benar dan sudah teruji.
- Index (user_id, type) dan (user_id, transaction_date) di transactions
  ternyata SUDAH ada sejak migration awal — tidak ada migration baru
  untuk ini.
- Policy untuk Category/Budget/SavingsGoal/SavingsContribution dibuat
  (siap dipakai Task 6) tapi belum di-wire ke component manapun — belum
  ada call site yang butuh (semua sudah aman lewat whereKey scoping).

---

## Task 5 — Saldo awal + semantik earmarking

Dependensi: Task 4 merged.

Tujuan: saldo = income − expense sepanjang waktu, tanpa saldo awal.
User baru yang punya uang 12 juta lalu catat kopi 25rb melihat saldo
−25.000. Selain itu kontribusi tabungan tidak menyentuh saldo, dan itu
tidak pernah dijelaskan — user berisiko mencatat ganda.

DIKERJAKAN:
- Kolom initial_balance, diisi saat registrasi atau lewat pengaturan
- netBalance = initial_balance + income − expense
- Model earmarking dipertahankan (kontribusi ≠ pengeluaran)
- Dashboard menampilkan "Saldo tersedia" = saldo − total earmarked
- Salinan eksplisit di form kontribusi: "tidak dicatat sebagai pengeluaran"

TIDAK DIKERJAKAN:
- Tipe transaksi transfer (model arus kas) — ditolak, terlalu invasif
- Halaman onboarding baru (Task 9)

Definition of done:
- Saldo masuk akal sejak transaksi pertama
- Tidak mungkin salah paham soal kontribusi vs pengeluaran

---

## Task 6 — Household / family sharing

Dependensi: Task 5 merged. INI YANG PALING BESAR.

Tujuan: aplikasi bernama "keuangan keluarga" tapi secara implementasi
single-user. Ini yang membedakan dari ratusan personal finance app.

DIKERJAKAN:
- Tabel households + household_members (dengan kolom role)
- Kolom household_id di 5 tabel bertenant
- Migration backfill: satu household per user existing
- JANGAN hapus user_id — maknanya berubah jadi "siapa yang mencatat",
  dipakai untuk atribusi ("dicatat oleh ...")
- Ganti isi global scope dari user_id ke household_id (satu file,
  berkat Task 4)
- Undang anggota, anggaran bersama, target tabungan kolaboratif

TIDAK DIKERJAKAN:
- Notifikasi (di luar scope produk)
- Perubahan visual (Task 9)

Definition of done:
- User existing tidak merasakan perubahan apa pun
- Dua user dalam satu household melihat data yang sama
- Test otorisasi diperluas: user dari household lain → 404

Catatan implementasi (lihat docs/TASK-6-HOUSEHOLD-DESIGN.md untuk draf
lengkap sebelum eksekusi):
- Premis "ganti global scope satu file" di plan tidak berlaku — Task 4
  tidak pernah mengaktifkan global scope (opsi a-revisi). Realisasinya:
  ~30 call site scoping manual diubah satu per satu di 9 file
  (Category/Budget/SavingsGoal/Transaction Livewire components +
  DashboardController), dari `auth()->user()->relasi()` jadi
  `auth()->user()->household->relasi()`.
- Bug ditemukan saat implementasi: `household_id` tidak ada di
  `$fillable` kelima model bertenant, jadi sempat diam-diam terbuang
  saat mass-assignment (kolom nullable, tidak ada error) — sudah
  diperbaiki di kelima model sekaligus.
- Kelima Policy (Task 4) diubah dari cek `user_id` jadi cek
  `household_id`, supaya konsisten dengan model baru — sebelumnya
  hanya TransactionPolicy yang benar-benar dipakai di satu 404 check.
- `categories:backfill` (Task 3) dihapus — skenario yang ditanganinya
  (user tanpa kategori) sudah tidak mungkin terjadi lagi berkat
  provisioning household otomatis, dan implementasinya sendiri sudah
  salah untuk skema baru (insert tanpa household_id).
- Atribusi kontribusi tabungan (`ManageSavingsGoals::addContribution`)
  diubah dari `$goal->user_id` (pemilik goal) jadi `auth()->id()`
  (siapa yang benar-benar mencatat) — dua hal itu selalu sama sebelum
  Task 6, sekarang bisa beda kalau anggota lain yang menambah dana.
- `User::household` adalah Attribute accessor (lewat
  `householdMembership`), bukan relasi Eloquent asli — tidak bisa
  di-eager-load lewat `->with('household')`.
- Halaman "Anggota Keluarga" ditambahkan di Pengaturan (lihat kode
  undangan, keluarkan anggota, keluar household). Owner belum bisa
  transfer kepemilikan sebelum keluar — dicatat sebagai utang
  perbaikan di bawah. **Update:** sudah dikerjakan, lihat commit
  branch `feat/household-ownership-transfer`.
- `docs/SCHEMA.md` sudah usang jauh sebelum task ini (nama tabel dan
  kolom sudah tidak cocok sejak beberapa task lalu) — tidak disegarkan
  di task ini, di luar cakupan.

---

## Task 7 — Export PDF/CSV

Dependensi: Task 6 merged.

DIKERJAKAN:
- Export transaksi dan laporan bulanan ke CSV/XLSX dan PDF
- Pakai helper money() dari Task 1
- Hormati scope household dari Task 6

Butuh dependency baru (maatwebsite/excel, barryvdh/laravel-dompdf) —
MINTA IZIN user dulu.

Definition of done:
- File terunduh berisi data yang benar dan ter-scope
- Format uang mengikuti preferred_currency

Catatan implementasi:
- Interpretasi "transaksi dan laporan bulanan ke CSV/XLSX dan PDF":
  transaksi → CSV + XLSX (riwayat penuh, tanpa PDF — daftar transaksi
  mentah tidak natural sebagai dokumen cetak); laporan bulanan →
  CSV + XLSX + PDF (PDF berisi ringkasan + breakdown kategori + daftar
  transaksi bulan itu, meniru tampilan halaman laporan).
- Ditemukan sebelum kode ditulis: `ReportController` masih pakai
  `$request->user()->transactions()`, lolos dari refactor scoping
  Task 6 karena idiomnya beda dari `auth()->user()->` yang di-grep
  waktu itu. Diperbaiki di commit terpisah sebelum fitur export
  dibangun di atasnya — kalau tidak, laporan bulanan (dan exportnya)
  tetap cuma tampilkan transaksi milik user yang login, bukan
  household.
- `composer require` memunculkan 33 advisory keamanan di 11 package
  (guzzlehttp/guzzle, laravel/framework, symfony/*, league/commonmark)
  — dikonfirmasi sudah ada sebelum install (composer.lock diff tidak
  menyentuh versi package-package itu), di luar cakupan task ini.

---

## Task 8 — CategorySuggester v1

Dependensi: Task 7 merged.

Tujuan: sekarang kategori hanya ketemu kalau user menyebut kata
"kategori". "beli bensin 50 ribu" jelas Transportasi tanpa disebut.

DIKERJAKAN:
- Interface CategorySuggester dengan
  suggest(string $text, string $type): ?CategorySuggestion
  (berisi category_id + confidence)
- v1: peta kata kunci global
- Inject di DUA tempat: VoiceTransactionParser (fallback saat regex
  "kategori" tidak ketemu) dan CreateTransaction (saran saat user
  mengetik catatan)
- Kalau confidence di bawah ambang: JANGAN pre-select apa pun

TIDAK DIKERJAKAN:
- v2 Naive Bayes (task terpisah nanti)
- OCR struk (ditolak, akurasi struk Indonesia buruk)

Catatan implementasi:
- Signature asli `suggest(string $text, string $type): ?CategorySuggestion`
  diperluas jadi `suggest(string $text, string $type, Collection $categories): ?CategorySuggestion`
  — konsisten dengan `VoiceTransactionParser::parse()` yang sudah lebih
  dulu terima `Collection $categories`, dan memang wajib supaya hasil
  `category_id` bisa di-scope ke household (Task 6), bukan cuma nama
  kategori global.
- Ambang confidence (`CategorySuggester::MINIMUM_CONFIDENCE = 0.5`)
  sengaja ditaruh di interface, bukan di implementasi v1 — supaya v2
  nanti bisa punya confidence yang benar-benar bervariasi tanpa
  caller (VoiceTransactionParser, CreateTransaction) perlu tahu
  implementasi mana yang sedang dipakai.
- `VoiceTransactionParser` terima `CategorySuggester` lewat constructor
  opsional (`= null`) supaya test unit lama (`new VoiceTransactionParser()`
  tanpa argumen) tetap jalan tanpa modifikasi.

---

## Task 9 — Frontend: redesign + migrasi format uang

Dependensi: Task 8 merged.

Dikerjakan SEKALI JALAN supaya view tidak disentuh dua kali.

DIKERJAKAN:
- Blade component <x-money :value="..." />
- Migrasi seluruh call site format uang (jalankan grep ULANG, angka 36
  sudah usang)
- Sambungkan window.formatMoney ke titik pakai JS
- Empty state halaman /transaksi/suara (utang dari Task 3)
- Pesan error halaman suara lebih spesifik (sekarang ambigu)
- Evaluasi ulang branch arsip ui/bento-dark-dashboard: pakai sebagai
  referensi visual, JANGAN merge kodenya — pendekatan inline
  style !important sebaiknya ditulis ulang bersih
- Onboarding: titik masuk initial_balance dari Task 5

Definition of done:
- Mengganti preferred_currency di database mengubah SELURUH tampilan
  angka uang
- Nol number_format/'Rp ' tersisa di view

Catatan implementasi:
- Cakupan "redesign" dikonfirmasi ke user sebelum mulai: hanya item
  DIKERJAKAN di atas, TANPA ganti tema visual. Tema terang/gradient
  indigo-purple yang ada dipertahankan. Branch arsip
  ui/bento-dark-dashboard tetap tidak di-merge (sesuai instruksi) dan
  tidak dipakai — adopsi tema gelap penuh akan jadi task terpisah
  kalau memang diinginkan.
- Selain <x-money>, ditambahkan `Money::number()` (format tanpa
  simbol) karena dibutuhkan di dua tempat: `<x-money :symbol="false">`
  dan input saldo awal yang menampilkan angka polos di sebelah prefix
  simbol terpisah.
- `<x-money>` punya prop `prefix` supaya label gabungan seperti
  "Lewat Rp 25.000" tetap satu text node — kalau dipecah jadi
  `Lewat <span>Rp 25.000</span>`, assertion test lama yang mencocokkan
  frasa utuh akan pecah padahal tampilannya identik.
- Attribute value dan ekspresi PHP (mis. `<x-dashboard-card value="..."/>`,
  ternary pembentuk string) tidak bisa memakai komponen Blade, jadi
  di titik itu dipakai `\App\Support\Money::format()` langsung. Hasil
  akhirnya tetap nol `number_format`/`'Rp '` di seluruh view.
- Bug ditemukan saat migrasi: baris "Jenis" di preview halaman suara
  hardcoded "Pengeluaran", jadi draf pemasukan yang sudah diparse
  benar tetap tampil sebagai pengeluaran sampai detik disimpan.
  Diperbaiki di commit terpisah.
- `window.formatMoney` + `window.appCurrency` ternyata SUDAH tersambung
  sejak Task 1 (`resources/js/app.js`, `layouts/app.blade.php`) — item
  "Sambungkan window.formatMoney" tidak butuh pekerjaan baru,
  diverifikasi bukan diasumsikan.

---

## Utang perbaikan (belum dijadwalkan)

- Timezone Asia/Jakarta fixed di config, tidak per-user
- Tidak ada audit log untuk perubahan budget/goal
- SoftDeletes hanya di Transaction
- docs/SCHEMA.md sudah usang, tidak mencerminkan skema sebenarnya
  sejak beberapa task lalu (household_id, initial_balance, source,
  raw_voice_text semua belum tercatat di sana)
- 33 advisory keamanan composer di 11 package (guzzlehttp/guzzle,
  laravel/framework, symfony/*, league/commonmark) — sudah ada sebelum
  Task 7, belum pernah ditangani. Cek `composer audit` untuk detail
- Adopsi tema gelap (referensi: branch arsip ui/bento-dark-dashboard)
  — Task 9 sengaja dibatasi ke item konkretnya saja setelah
  dikonfirmasi ke user, tema visual belum diubah
