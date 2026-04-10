# Laporan Eksekusi User Story 2.2: Input Validasi Suhu Pemanasan RT-PCR

## Route Definition

### HTTP Methods & URL Endpoints
- **GET** `/thermal/create` - Menampilkan form input suhu pemanasan
  - Route name: `thermal.create`
  - Controller: `ThermalController@create`
  - Return: View Blade (thermal.create)

- **POST** `/thermal/store` - Menyimpan data suhu ke database
  - Route name: `thermal.store`
  - Controller: `ThermalController@store`
  - Middleware: Validasi via `StoreThermalLogRequest`
  - Return: JSON Response (API) atau Redirect (Web)

### Route Implementation
```php
Route::prefix('thermal')->group(function () {
    Route::get('/create', [ThermalController::class, 'create'])->name('thermal.create');
    Route::post('/store', [ThermalController::class, 'store'])->name('thermal.store');
});
```

---

## Testing Scenarios

### Test Case 1: Success - Valid Temperature (85.5°C)
- **Deskripsi**: Memasukkan suhu valid dalam range aman (≤90°C)
- **Input**: sample_id = 1, temperature_celsius = 85.5
- **Expected Output**: 
  - Redirect ke `thermal.create` dengan success message
  - Data tersimpan di tabel `thermal_logs`
- **Assertion**: `assertRedirect()`, `assertSessionHas('success')`, `assertDatabaseHas()`

### Test Case 2: Success - Boundary Temperature (95.0°C)
- **Deskripsi**: Memasukkan suhu tepat di batas maksimal
- **Input**: sample_id = 1, temperature_celsius = 95.0
- **Expected Output**: Data tersimpan, namun tanpa warning tambahan
- **Assertion**: `assertRedirect()`, `assertDatabaseHas()`

### Test Case 3: Fail - Exceed Temperature (95.1°C)
- **Deskripsi**: Suhu melebihi 95.0°C akan mencegah penyimpanan
- **Input**: sample_id = 1, temperature_celsius = 95.1
- **Expected Output**: 
  - Session error dengan pesan: "Suhu pemanasan tidak boleh melebihi 95.0°C untuk mencegah kerusakan RNA."
  - Data TIDAK tersimpan
- **Assertion**: `assertSessionHasErrors('temperature_celsius')`, `assertDatabaseMissing()`

### Test Case 4: Fail - Empty Temperature
- **Deskripsi**: Field suhu kosong/tidak diisi
- **Input**: sample_id = 1, temperature_celsius = ""
- **Expected Output**: Validasi error "Suhu pemanasan wajib diisi."
- **Assertion**: `assertSessionHasErrors()`, pesan custom dalam Bahasa Indonesia

### Test Case 5: Fail - Empty Sample ID
- **Deskripsi**: Field sample_id kosong
- **Input**: sample_id = "", temperature_celsius = 85.0
- **Expected Output**: Validasi error "ID sampel wajib diisi."
- **Assertion**: `assertSessionHasErrors('sample_id')`

### Test Case 6: Fail - Non-numeric Temperature
- **Deskripsi**: Input berupa teks, bukan angka
- **Input**: sample_id = 1, temperature_celsius = "abc"
- **Expected Output**: Validasi error "Suhu pemanasan harus berupa angka."
- **Assertion**: `assertSessionHasErrors()`

### Test Case 7: Fail - Invalid Sample ID
- **Deskripsi**: Sample ID tidak ada di database
- **Input**: sample_id = 9999, temperature_celsius = 85.0
- **Expected Output**: Validasi error "ID sampel tidak ditemukan dalam database."
- **Assertion**: `assertSessionHasErrors('sample_id')`

### Test Case 8: API - JSON Response Success
- **Deskripsi**: POST dengan Accept: application/json mengembalikan JSON
- **Input**: Accept header = application/json
- **Expected Output**: 
  - HTTP 201 Created
  - JSON dengan struktur: { message, data: { id, sample_id, temperature_celsius, created_at, updated_at } }
- **Assertion**: `assertStatus(201)`, `assertJsonStructure()`, `assertJsonPath()`

### Test Case 9: API - JSON Validation Error
- **Deskripsi**: POST JSON dengan data invalid mengembalikan 422
- **Input**: temperature_celsius = 95.1 via JSON
- **Expected Output**: HTTP 422 Unprocessable Entity dengan validation errors
- **Assertion**: `assertStatus(422)`, `assertJsonValidationErrors()`

---

## UI Components

### 1. Form Container
- **Elemen**: Card dengan shadow, rounded corners
- **Styling**: `bg-white rounded-lg shadow-lg p-6 sm:p-8`
- **Layout**: Center align dengan max-width 448px untuk responsive design

### 2. Header Section
- **Judul**: "Input Suhu Pemanasan" (h1, font-bold, text-2xl)
- **Subtitle**: "Extraction-Free RT-PCR" (gray text, smaller)
- **Fungsi**: Memberikan konteks kepada user

### 3. Success Message Alert
- **Trigger**: Jika `session('success')` ada
- **Warna**: Green (bg-green-50, border-green-200, text-green-700)
- **Icon**: SVG checkmark
- **Durasi**: Persistent hingga refresh page

### 4. Sample ID Input (Dropdown/Select)
- **Label**: "ID Sampel" dengan tanda merah "*" (required indicator)
- **Type**: Dropdown (select element)
- **Opsi**: Dinamis dari database (sample_code + patient_name)
- **Validasi: 
  - Required
  - Highlight merah jika error
  - Tampilkan error message di bawah field

### 5. Temperature Input (Number)
- **Label**: "Suhu Pemanasan (°C)" dengan tanda "*"
- **Type**: Number input dengan step="0.1"
- **Placeholder**: "Masukkan suhu (contoh: 85.5)"
- **Min/Max**: min="0" max="100"
- **Real-time Validation**: Alpine.js untuk monitoring nilai input

### 6. Temperature Status Indicator (Real-time)
**Status Icons** (di sebelah kanan input field):
- **Safe (≤90°C)**: Icon info (gray) - tidak ada warning
- **Warning (90°C < temp ≤ 95°C)**: Icon warning (yellow) dengan pulse animation
- **Danger (>95°C)**: Icon prohibition (red) dengan pulse animation

**Status Messages** (di bawah input field):
- **Safe (≤90°C)**: 
  - Text: "Suhu aman untuk RNA" 
  - Color: Green (#059669)
  - Icon: Checkmark

- **Warning (90°C < temp ≤ 95°C)**:
  - Text: "Peringatan: Suhu mendekati batas maksimal (>90°C)"
  - Color: Yellow (#ca8a04)
  - Icon: Warning triangle

- **Danger (>95°C)**:
  - Text: "BERBAHAYA: Suhu melebihi 95.0°C! RNA akan rusak!"
  - Color: Red (#dc2626)
  - Font: Bold (font-semibold)
  - Icon: Prohibition

### 7. Server-side Error Display
- **Tampil jika**: Validasi gagal saat POST
- **Warna**: Red (text-red-600)
- **Icon**: Error icon
- **Pesan**: Dari Laravel validation messages (Bahasa Indonesia)

### 8. Information Box
- **Styling**: Blue border-left, blue background, blue icon
- **Konten**: 
  - Text: "Perhatian: Suhu maksimal untuk mencegah kerusakan RNA adalah **95.0°C**."
  - Bold: Nomor 95.0°C
- **Fungsi**: Educational hint untuk user

### 9. Submit Button
- **Level**: Full width (w-full)
- **Text**: "Simpan Data Suhu"
- **Color**: Blue (bg-blue-600, hover:bg-blue-700)
- **Feedback**: 
  - Hover effect (darker blue)
  - Focus ring (focus:ring-2 focus:ring-blue-500)
  - Transition smooth (duration-200)

### 10. Footer Help Text
- **Text**: "Sistem validasi real-time untuk mencegah kerusakan sampel biologis akibat suhu berlebih."
- **Styling**: Dimmed (text-xs text-gray-600), center align
- **Fungsi**: Additional context

---

## UX Logic

### Real-time Feedback Flow

1. **User mulai mengetik angka di Temperature Input**
   - Alpine.js mendengarkan event `@input`
   - Format: temperature = $el.querySelector(...).value

2. **Sistem menghitung status**
   ```javascript
   isWarning = temperature > 90 && temperature <= 95;
   isExceeded = temperature > 95;
   ```

3. **Update Visual Indicators**
   
   | Kondisi | Icon | Message | Color | Animation |
   |---------|------|---------|-------|-----------|
   | temp ≤ 90 | ℹ️ Info | "Suhu aman untuk RNA" | Green | None |
   | 90 < temp ≤ 95 | ⚠️ Warning | "Peringatan: mendekati batas" | Yellow | Pulse |
   | temp > 95 | 🚫 Block | "BERBAHAYA: RNA akan rusak!" | Red | Pulse |

4. **Client-side Prevention**
   - Input range="0-100" mencegah user mengetik nilai negatif atau > 100
   - Type="number" dengan step="0.1" untuk presisi termal

5. **Server-side Validation**
   - Laravel Form Request (`StoreThermalLogRequest`) memvalidasi ulang
   - Jika temperature_celsius > 95.0 → reject dengan error message
   - Pesan error dalam Bahasa Indonesia menjelaskan mengapa ditolak

6. **Success Feedback**
   - Jika validation pass → redirect ke form + success message
   - Success message hilang saat page refresh
   - Data berhasil tersimpan di tabel `thermal_logs`

### Error Handling Strategy

**Client-side:**
- Alpine.js updates visual indicators in real-time
- No form submission if user aware of danger (namun tetap bisa submit)

**Server-side:**
- Validasi ulang pada FormRequest level
- Jika ada error → session()->withErrors() + redirect back
- Error messages ditampilkan elegantly di view

**User Experience:**
- Tidak ada frustasi karena warning visible sebelum submit
- Pesan error jelas dan actionable
- Bahasa Indonesia untuk kemudahan pemahaman

---

## DatabaseStructure

### Tabel: samples
```
id (bigint, primary key)
sample_code (string, unique)
patient_name (string)
created_at (timestamp)
updated_at (timestamp)
```

### Tabel: thermal_logs
```
id (bigint, primary key)
sample_id (bigint, foreign key -> samples.id)
temperature_celsius (decimal 5,2)
created_at (timestamp)
updated_at (timestamp)
```

---

## Commit Message

**Format Conventional Commits:**
```
feat(thermal): add RT-PCR heating temperature validation

- Add thermal temperature input form with Blade template
- Implement real-time validation with Alpine.js
- Add warning indicators for temperatures >90°C
- Prevent RNA damage by enforcing 95.0°C limit
- Add comprehensive feature tests (9 test cases)
- Add responsive Tailwind CSS styling

Closes #2.2
```

**Versi ringkas:**
```
feat(thermal): add RT-PCR temperature validation with real-time feedback
```

---

## Summary

✅ **Implementasi Lengkap User Story 2.2:**
- Routes: GET create form + POST store with validation
- Form Validation: StoreThermalLogRequest dengan custom messages (Indonesian)
- UI/UX: Responsive Blade template dengan Tailwind CSS
- Real-time Feedback: Alpine.js untuk warning indicators
- Testing: 11 feature tests mencakup success & failure scenarios
- Database: Migration + Model dengan relationships

**Key Features:**
- Suhu maksimal: 95.0°C (mencegah kerusakan RNA)
- Real-time indicators: Safe (green) → Warning (yellow) → Danger (red)
- Server-side validation: Mencegah bypass client-side validation
- API support: JSON response untuk programmatic access
- Indonesian messages: User-friendly error descriptions
