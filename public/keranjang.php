<?php
// Definisikan judul halaman untuk digunakan di header

$page_title = "Keranjang Belanja";

// Panggil kerangka bagian atas (header)
include 'includes/header.php';


?>

<style>
    .cart-page {
        padding: 8rem 7% 4rem;
        /* Memberi ruang dari navbar dan footer */
        max-width: 960px;
        /* Agar tidak terlalu lebar di layar besar */
        margin: 0 auto;
    }

    .cart-page h2 {
        text-align: center;
        font-size: 2.6rem;
        margin-bottom: 2rem;
        color: var(--primary);
    }

    .cart-page h2 span {
        color: #333;
    }

    .cart-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 2rem;
        font-size: 1.4rem;
    }

    .cart-table th,
    .cart-table td {
        border: 1px solid #ddd;
        padding: 1rem 1.2rem;
        text-align: left;
        vertical-align: middle;
    }

    .cart-table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }

    .cart-table .text-end {
        text-align: right;
    }

    .cart-table .text-center {
        text-align: center;
    }

    .quantity-control {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .quantity-control button {
        background: var(--primary);
        color: white;
        border: none;
        cursor: pointer;
        padding: 0.4rem 0.8rem;
        font-size: 1.2rem;
        line-height: 1;
    }

    .quantity-control input {
        width: 50px;
        text-align: center;
        border: 1px solid #ccc;
        margin: 0 0.5rem;
        padding: 0.3rem;
        font-size: 1.4rem;
    }

    .remove-btn {
        color: #e74c3c;
        text-decoration: none;
        font-weight: bold;
    }

    .remove-btn:hover {
        color: #c0392b;
    }

    .cart-total {
        text-align: right;
        font-size: 1.8rem;
        font-weight: bold;
        margin-bottom: 2rem;
    }

    .checkout-form {
        border-top: 2px solid #eee;
        padding-top: 2rem;
    }

    .checkout-form h3 {
        font-size: 2rem;
        color: var(--primary);
        margin-bottom: 1.5rem;
    }

    /* ============================================== */
    /* == STYLE TAMBAHAN UNTUK FORM CHECKOUT == */
    /* ============================================== */

    .checkout-form {
        margin-top: 3rem;
        padding-top: 2rem;
        border-top: 1px solid #ddd;
    }

    .checkout-form h3 {
        text-align: center;
        margin-bottom: 2rem;
        font-size: 2rem;
    }

    /* Mengatur wadah input (ikon + kolom input) */
    .checkout-form .input-group {
        display: flex;
        align-items: center;
        margin-top: 1.5rem;
        background-color: #f9f9f9;
        border: 1px solid #ccc;
        padding-left: 1.5rem;
        border-radius: 0.5rem;
    }

    /* Mengatur input field di dalam .input-group */
    .checkout-form .input-group input {
        width: 100%;
        padding: 1.5rem;
        font-size: 1.4rem;
        background: none;
        color: #333;
    }

    /* Mengatur ikon feather */
    .checkout-form .input-group svg {
        color: #555;
    }

    /* Mengatur tombol submit "Buat Pesanan" */
    .checkout-form .btn {
        margin-top: 2rem;
        display: inline-block;
        padding: 1rem 3rem;
        font-size: 1.7rem;
        font-weight: 700;
        color: #fff;
        background-color: var(--primary);
        /* Warna utama tema */
        border-radius: 0.5rem;
        cursor: pointer;
        width: 100%;
        /* Tombol memenuhi lebar form */
        box-sizing: border-box;
        transition: background-color 0.3s ease;
    }

    .checkout-form .btn:hover {
        background-color: #c89a6f;
        /* Warna hover, sesuaikan jika perlu */
    }
</style>
<?php

?>

<section class="cart-page" id="cart">
    <h2>Keranjang <span>Belanja Anda</span></h2>

    <div class="row">
        <div class="cart-container">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Harga Satuan</th>
                        <th class="text-center">Jumlah</th>
                        <th class="text-end">Subtotal</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="cart-items-container">
                </tbody>
            </table>
        </div>

        <div class="cart-total" id="cart-total-price">
            Total: Rp 0
        </div>

        <div class="checkout-form">
            <h3>Lengkapi Data untuk Pemesanan</h3>

            <form id="checkout-form" action="proses_pesanan.php" method="POST">
                <div class="input-group">
                    <i data-feather="user"></i>
                    <input type="text" name="nama_pemesan" placeholder="Nama Lengkap Anda" required>
                </div>
                <div class="input-group">
                    <i data-feather="phone"></i>
                    <input type="tel" name="no_telepon" placeholder="Nomor Telepon / WA Aktif" required>
                </div>

                <input type="hidden" name="cart_data" id="cart-data-input">

                <button type="submit" class="btn">Buat Pesanan & Lanjutkan Pembayaran</button>
            </form>
        </div>
    </div>
</section>

<?php
// Panggil kerangka bagian bawah (footer)
include 'includes/footer.php';


?>