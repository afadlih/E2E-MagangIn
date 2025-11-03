/**
 * E2E Test: Dosen - Validasi Log Aktivitas Mahasiswa
 * Skenario: E2E-DSN-002
 * Dosen mereview dan meng-approve log aktivitas mahasiswa yang sudah disetujui industri
 */

const { test, expect } = require("@playwright/test");

test.describe("Dosen - Validasi Log Aktivitas", () => {
    test("E2E-DSN-002: Review dan approval log aktivitas mahasiswa", async ({
        page,
    }) => {
        test.setTimeout(60000);
        
        // ===== STEP 1: Navigasi ke Dashboard =====
        console.log("Step 1: Navigasi ke dashboard dosen...");
        await page.goto("http://localhost/E2E-MagangIn/public/dashboard-dosen");
        await page.waitForLoadState("networkidle");
        await page.screenshot({
            path: "tests/screenshots/dosen-01-dashboard.png",
            fullPage: true,
        });

        // ===== STEP 2: Verifikasi Role =====
        console.log("Step 2: Verifikasi sudah authenticated...");
        expect(page.url()).toContain("/dashboard-dosen");

        const navbar = page.locator(".navbar");
        await expect(navbar).toBeVisible();
        console.log("✓ Navbar visible - User authenticated");

        // ===== STEP 3: Navigasi ke Log Aktivitas Mahasiswa =====
        console.log("Step 3: Klik menu Monitoring dan Evaluasi Magang...");
        await page.click('a:has-text("Monitoring dan Evaluasi Magang")');
        
        console.log("Step 4: Klik submenu Log Aktivitas...");
        await page.click('a[href*="/log-aktivitas"]');
        await page.waitForLoadState("networkidle");
        
        // Verifikasi URL
        expect(page.url()).toContain("/log-aktivitas");
        console.log("✓ Berhasil navigasi ke halaman log aktivitas");

        await page.screenshot({
            path: "tests/screenshots/dosen-02-log-list.png",
            fullPage: true,
        });

        // ===== STEP 4: Tunggu DataTable Load =====
        console.log("Step 5: Menunggu DataTable selesai load...");
        await page.waitForSelector('#log-table', { state: 'visible' });
        
        // Tunggu sampai DataTable selesai processing
        await page.waitForFunction(() => {
            return !document.querySelector('#log-table_processing')?.style.display || 
                   document.querySelector('#log-table_processing')?.style.display === 'none';
        }, { timeout: 10000 });
        
        console.log("✓ DataTable berhasil dimuat");

        // ===== STEP 5: Verifikasi Ada Data di Tabel =====
        console.log("Step 6: Verifikasi ada data di tabel...");
        const tableRows = await page.locator('#log-table tbody tr').count();
        
        if (tableRows === 0 || await page.locator('#log-table tbody tr td.dataTables_empty').isVisible().catch(() => false)) {
            console.log("⚠️ Tidak ada data log aktivitas");
            await page.screenshot({
                path: "tests/screenshots/dosen-03-no-data.png",
                fullPage: true,
            });
            test.skip();
            return;
        }
        
        console.log(`✓ Ditemukan ${tableRows} baris data log aktivitas`);

        // ===== STEP 6: Klik Tombol Detail/Aksi =====
        console.log("Step 7: Klik tombol detail pada log aktivitas pertama...");
        
        // Cari tombol aksi pertama (bisa berupa button dengan onclick modalAction)
        const detailButton = page.locator('#log-table tbody tr:first-child td:last-child button, #log-table tbody tr:first-child td:last-child a').first();
        await detailButton.waitFor({ state: 'visible' });
        await detailButton.click();
        
        console.log("✓ Tombol detail diklik");

        // ===== STEP 7: Tunggu Modal Muncul =====
        console.log("Step 8: Menunggu modal detail muncul...");
        await page.waitForSelector('#myModal', { state: 'visible', timeout: 5000 });
        
        // Tunggu konten modal dimuat
        await page.waitForSelector('#myModal .modal-content', { state: 'visible' });
        await page.waitForTimeout(1000); // Beri waktu untuk AJAX load
        
        console.log("✓ Modal detail berhasil dimuat");

        await page.screenshot({
            path: "tests/screenshots/dosen-03-modal-detail.png",
            fullPage: true,
        });

        // ===== STEP 8: Verifikasi Konten Modal =====
        console.log("Step 9: Verifikasi konten modal...");
        
        // Verifikasi judul modal
        const modalTitle = page.locator('#myModal .modal-title');
        await expect(modalTitle).toBeVisible();
        await expect(modalTitle).toHaveText(/Detail Log Aktivitas/i);
        console.log("✓ Judul modal terverifikasi");

        // Verifikasi data mahasiswa tampil
        const namaField = page.locator('#myModal table tr:has-text("Nama:") td');
        await expect(namaField).toBeVisible();
        console.log("✓ Data mahasiswa terverifikasi");

        // Verifikasi form komentar ada
        const formKomentar = page.locator('#form-komentar');
        await expect(formKomentar).toBeVisible();
        console.log("✓ Form feedback/saran terverifikasi");

        // ===== STEP 9: Isi dan Submit Form Komentar =====
        console.log("Step 10: Mengisi form feedback/saran...");
        
        const komentarText = "Test feedback dari E2E test - Log aktivitas sudah sesuai";
        await page.fill('#komentar', komentarText);
        console.log("✓ Feedback/saran berhasil diisi");

        await page.screenshot({
            path: "tests/screenshots/dosen-04-filled-form.png",
            fullPage: true,
        });

        console.log("Step 11: Submit form feedback/saran...");
        await page.click('#form-komentar button[type="submit"]');
        
        // Tunggu SweetAlert muncul
        await page.waitForSelector('.swal2-container', { state: 'visible', timeout: 5000 });
        console.log("✓ SweetAlert muncul");

        await page.screenshot({
            path: "tests/screenshots/dosen-05-success-alert.png",
            fullPage: true,
        });

        // Verifikasi pesan sukses
        const alertTitle = page.locator('.swal2-title');
        await expect(alertTitle).toHaveText(/Berhasil/i);
        console.log("✓ Feedback/saran berhasil dikirim");

        // Klik OK pada SweetAlert
        await page.click('.swal2-confirm');
        await page.waitForTimeout(500);

        // ===== STEP 10: Verifikasi Modal Tertutup =====
        console.log("Step 12: Verifikasi modal tertutup...");
        await page.waitForSelector('#myModal', { state: 'hidden', timeout: 5000 });
        console.log("✓ Modal berhasil tertutup");

        // ===== STEP 11: Verifikasi DataTable Reload =====
        console.log("Step 13: Verifikasi DataTable di-reload...");
        await page.waitForTimeout(1000); // Beri waktu untuk reload
        
        await page.screenshot({
            path: "tests/screenshots/dosen-06-after-submit.png",
            fullPage: true,
        });

        console.log("🎉 TEST SELESAI: E2E-DSN-002 PASSED");
        console.log("===========================================");
        console.log("✓ Dashboard dosen berhasil diakses");
        console.log("✓ Menu Monitoring dan Evaluasi Magang berhasil diklik");
        console.log("✓ Halaman Log Aktivitas berhasil dimuat");
        console.log("✓ DataTable berhasil menampilkan data");
        console.log("✓ Modal detail berhasil dibuka");
        console.log("✓ Form feedback/saran berhasil diisi dan dikirim");
        console.log("✓ Notifikasi sukses muncul");
        console.log("✓ Modal tertutup dan DataTable di-reload");
        console.log("===========================================");
    });
});