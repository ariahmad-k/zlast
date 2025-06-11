<?php
$page_title = "Lacak Pesanan";
include 'includes/header.php'; // Pastikan koneksi ada di dalam header.php

$hasil_lacak = null;
$id_pesanan_lacak = '';

// Logika untuk menangani pencarian pesanan
if (isset($_GET['id_pesanan']) && !empty(trim($_GET['id_pesanan']))) {
    $id_pesanan_lacak = trim($_GET['id_pesanan']);

    // Menggunakan prepared statement untuk keamanan
    $stmt = mysqli_prepare($koneksi, "SELECT nama_pemesan, status_pesanan, tgl_pesanan FROM pesanan WHERE id_pesanan = ?");
    mysqli_stmt_bind_param($stmt, "s", $id_pesanan_lacak);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $hasil_lacak = mysqli_fetch_assoc($result);
}

// Logika untuk mengambil data antrean publik
$sql_antrean = "SELECT id_pesanan, status_pesanan FROM pesanan WHERE status_pesanan IN ('pending', 'diproses') ORDER BY FIELD(status_pesanan, 'diproses', 'pending'), tgl_pesanan ASC LIMIT 10";
$result_antrean = mysqli_query($koneksi, $sql_antrean);
$antrean_publik = mysqli_fetch_all($result_antrean, MYSQLI_ASSOC);
?>

<style>
    /* CSS Anda sudah bagus, tidak perlu diubah. Saya salin kembali di sini. */
    .page-section {
        padding: 8rem 7% 4rem;
    }
    .tracking-form-container, .tracking-result-container, .queue-board-container {
        max-width: 800px;
        margin: 0 auto 2rem auto;
        padding: 2rem;
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.05);
    }
    .tracking-form-container h2, .tracking-result-container h3, .queue-board-container h3 {
        text-align: center;
        color: var(--primary);
        margin-bottom: 1.5rem;
    }
    .progress-bar-container {
        display: flex;
        justify-content: space-between;
        margin-top: 2rem;
        position: relative;
    }
    .progress-step {
        text-align: center;
        flex: 1;
        position: relative;
    }
    .progress-step .step-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #e0e0e0;
        color: white;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        border: 3px solid #e0e0e0;
        transition: all 0.3s ease;
    }
    .progress-step .step-label {
        margin-top: 0.5rem;
        font-size: 1.2rem;
        color: #999;
    }
    .progress-step.completed .step-icon, .progress-step.active .step-icon {
        background-color: #28a745;
        border-color: #28a745;
    }
    .progress-step.active .step-icon {
        background-color: #ffc107; /* Kuning untuk status aktif */
    }
    /* --- PERUBAHAN CSS UNTUK STATUS DIBATALKAN --- */
    .progress-step.cancelled .step-icon {
        background-color: #dc3545; /* Merah untuk batal */
        border-color: #dc3545;
    }
    .progress-step::after {
        content: '';
        position: absolute;
        top: 20px;
        left: 50%;
        width: 100%;
        height: 3px;
        background-color: #e0e0e0;
        z-index: -1;
    }
    .progress-step:last-child::after { display: none; }
    .progress-step.completed::after { background-color: #28a745; }
</style>

<section class="page-section">
    <div class="tracking-form-container">
        <h2>Lacak <span>Pesanan Anda</span></h2>
        <p style="text-align:center; margin-bottom: 2rem;">Masukkan Nomor Pesanan yang Anda dapatkan di halaman konfirmasi.</p>
        <form action="lacak.php" method="GET" class="input-group" style="border:none; background:none;">
            <input type="text" name="id_pesanan" class="form-control" placeholder="Contoh: ONLINE-1749367495" value="<?= htmlspecialchars($id_pesanan_lacak) ?>" required>
            <button type="submit" class="btn">Lacak</button>
        </form>
    </div>

    <?php if (isset($_GET['id_pesanan']) && !empty($_GET['id_pesanan'])): ?>
        <div class="tracking-result-container">
            <?php if ($hasil_lacak): ?>
                <h3>Status untuk Pesanan #<?= htmlspecialchars($id_pesanan_lacak) ?></h3>
                <p style="text-align:center;">Pemesan: <strong><?= htmlspecialchars($hasil_lacak['nama_pemesan']) ?></strong></p>
                <?php
                $status = $hasil_lacak['status_pesanan'];

                // --- PERBAIKAN UTAMA: PISAHKAN LOGIKA JIKA PESANAN DIBATALKAN ---
                if ($status === 'dibatalkan'):
                ?>
                    <div class="alert alert-danger text-center mt-4">
                        <i class="fas fa-times-circle fa-3x mb-3"></i>
                        <h4>Pesanan Dibatalkan</h4>
                        <p>Pesanan ini telah dibatalkan.</p>
                    </div>
                <?php
                // --- LOGIKA UNTUK ALUR NORMAL (TIDAK DIBATALKAN) ---
                else:
                    // Definisikan langkah-langkah normal TANPA status 'dibatalkan'
                    $steps = [
                        'menunggu_pembayaran' => ['label' => 'Pesanan Dibuat', 'icon' => 'receipt'],
                        'menunggu_konfirmasi' => ['label' => 'Konfirmasi Bayar', 'icon' => 'hourglass-half'],
                        'pending'             => ['label' => 'Dalam Antrean', 'icon' => 'clock'],
                        'diproses'            => ['label' => 'Sedang Dibuat', 'icon' => 'blender-phone'],
                        'siap_diambil'        => ['label' => 'Siap Diambil', 'icon' => 'shopping-bag'],
                        'selesai'             => ['label' => 'Selesai', 'icon' => 'check-circle']
                    ];
                    $status_keys = array_keys($steps);
                    $current_step_index = array_search($status, $status_keys);
                ?>
                    <div class="progress-bar-container">
                        <?php foreach ($steps as $key => $step): ?>
                            <?php
                            $step_index = array_search($key, $status_keys);
                            $class = '';
                            // Logika ini sekarang aman karena 'dibatalkan' tidak ada di array $steps
                            if ($step_index < $current_step_index || $status === 'selesai') {
                                $class = 'completed';
                            } elseif ($step_index === $current_step_index) {
                                $class = 'active';
                            }
                            ?>
                            <div class="progress-step <?= $class ?>">
                                <div class="step-icon"><i class="fas fa-<?= $step['icon'] ?>"></i></div>
                                <div class="step-label"><?= $step['label'] ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; // Akhir dari if status dibatalkan ?>

            <?php else: // Jika pesanan tidak ditemukan ?>
                <div class="alert alert-warning text-center">Nomor Pesanan '<?= htmlspecialchars($id_pesanan_lacak) ?>' tidak ditemukan. Pastikan Anda memasukkan nomor yang benar.</div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="queue-board-container">
        <h3><i class="fas fa-users"></i> Antrean Dapur Saat Ini</h3>
        <?php if (!empty($antrean_publik)): ?>
            <ul class="list-group">
                <?php foreach ($antrean_publik as $antrean): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Pesanan #...<?= substr(htmlspecialchars($antrean['id_pesanan']), -6) ?>
                        <?php if ($antrean['status_pesanan'] == 'diproses'): ?>
                            <span class="badge bg-primary rounded-pill">Sedang Dibuat</span>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark rounded-pill">Menunggu</span>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="text-center text-muted">Antrean dapur sedang kosong.</p>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>