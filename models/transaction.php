<?php
require_once __DIR__ . '/../config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class Transaction extends Database
{

    public function __construct()
    {
        parent::__construct();
        if (!isset($_SESSION['carts'])) {
            $_SESSION['carts'] = [];
        }
    }

    public function addCart($id, $nama, $harga)
    {
        $id = (int) $id;
        $harga = (int) $harga;

        $found = false;
        foreach ($_SESSION['carts'] as $key => $cart) {
            if ($cart['id'] == $id) {
                $_SESSION['carts'][$key]['jumlah']++;
                $_SESSION['carts'][$key]['subtotal'] += $harga;
                $found = true;
                break;
            }
        }
        if (!$found) {
            $_SESSION['carts'][] = [
                'id' => $id,
                'nama' => $nama,
                'jumlah' => 1,
                'subtotal' => $harga,
                'harga' => $harga
            ];
        }
    }

    public function reduceCart($id, $harga)
    {
        $id = (int) $id;
        $harga = (int) $harga;
        foreach ($_SESSION['carts'] as $key => $cart) {
            if ($cart['id'] == $id) {
                if ($cart['jumlah'] <= 1) {
                    unset($_SESSION['carts'][$key]);
                    $_SESSION['carts'] = array_values($_SESSION['carts']);
                    break;
                } else {
                    $_SESSION['carts'][$key]['jumlah']--;
                    $_SESSION['carts'][$key]['subtotal'] -= $harga;
                    break;
                }
            }
        }
    }

    public function getTotalHarga()
    {
        $total = 0;
        foreach ($_SESSION['carts'] as $cart) {
            $total += (int) ($cart['subtotal'] ?? 0);
        }
        return $total;
    }

    public function checkStockAvailability($id_product, $requested_qty)
    {
        $id_product = (int) $id_product;
        $requested_qty = (int) $requested_qty;
        $result = $this->runQuery("
        SELECT SUM(jumlah) as total_stok 
        FROM stok 
        WHERE id_product = $id_product
    ", "Gagal cek stok");

        if (!$result) {
            return false;
        }

        $row = mysqli_fetch_assoc($result);
        $totalStok = (int) ($row['total_stok'] ?? 0);
        return ($totalStok >= $requested_qty);
    }

    public function pushTransaction()
    {
        if (count($_SESSION['carts']) <= 0) {
            return "Keranjang masih kosong.";
        }

        foreach ($_SESSION['carts'] as $cart) {
            if (!$this->checkStockAvailability($cart['id'], $cart['jumlah'])) {
                // Jika ada satu saja barang yang stoknya kurang, batalkan semua
                return "Stok untuk produk " . $cart['nama'] . " tidak mencukupi!";
            }
        }

        // 2. Jika lolos validasi, baru jalankan insert transaksi dan update stok
        $totalHarga = $this->getTotalHarga();
        if ($totalHarga <= 0) {
            return "Keranjang masih kosong.";
        }

        $this->connection->begin_transaction();

        $insertTransaksi = $this->runQuery("
            INSERT INTO transaksi (total_harga)
            VALUES ($totalHarga)
        ", "Gagal menambah transaksi");

        if (!$insertTransaksi) {
            $this->connection->rollback();
            return "Gagal menambah transaksi.";
        }

        $id = (int) $this->connection->insert_id;
        $dataCarts = array_map(function ($cart) use ($id) {
            $nama = $this->connection->real_escape_string((string) ($cart['nama'] ?? ''));
            $idProduct = (int) ($cart['id'] ?? 0);
            $harga = (int) ($cart['harga'] ?? 0);
            $jumlah = (int) ($cart['jumlah'] ?? 0);
            return "($id, $idProduct, '$nama', $harga, $jumlah)";
        }, $_SESSION['carts']);

        if (count($dataCarts) <= 0) {
            $this->connection->rollback();
            return "Keranjang masih kosong.";
        }

        $value = implode(", ", $dataCarts);
        $insertDetailTransaksi = $this->runQuery("
            INSERT INTO detail_transaksi(id_transaksi, id_product, nama_product, harga_product, qty) VALUES $value
        ", "Gagal menambah detail transaksi");

        if (!$insertDetailTransaksi) {
            $this->connection->rollback();
            return "Gagal menambah detail transaksi.";
        }

        foreach ($_SESSION['carts'] as $cart) {
            $idProduct = (int) ($cart['id'] ?? 0);
            $qtyYangDibeli = (int) ($cart['jumlah'] ?? 0);

            for ($i = 0; $i < $qtyYangDibeli; $i++) {
                $update = $this->runQuery("
                    UPDATE stok
                    SET jumlah = jumlah - 1
                    WHERE id_product = $idProduct
                    AND jumlah > 0
                    ORDER BY (tgl_exp IS NULL), tgl_exp ASC, tgl_masuk ASC, id ASC
                    LIMIT 1
                ", "Gagal mengurangi stok");

                if (!$update || $this->connection->affected_rows <= 0) {
                    $this->connection->rollback();
                    return "Stok untuk produk " . ($cart['nama'] ?? 'ini') . " tidak mencukupi!";
                }
            }
        }

        $this->connection->commit();
        return $id;
    }

    public function getTransactions()
    {
        $data = $this->runQuery("SELECT * FROM transaksi ORDER BY id DESC", "Gagal mengambil data transaksi");
        if (!$data) {
            return [];
        }
        $result = mysqli_fetch_all($data, MYSQLI_ASSOC);
        return $result;
    }

    public function getTodayStatistic()
    {
        $today = date('Y-m-d');
        $data = $this->runQuery("SELECT COUNT(*) as total, SUM(total_harga) as omzet, AVG(total_harga) as rata_rata FROM transaksi WHERE DATE(tgl_pembelian) = '$today' ORDER BY id DESC", "Gagal mengambil data transaksi hari ini");
        if (!$data) {
            return 0;
        }
        $result = mysqli_fetch_assoc($data);
        return $result;
    }

    public function getTransactionDetails($idTransaksi)
    {
        $idTransaksi = (int) $idTransaksi;
        $data = $this->runQuery("SELECT * FROM detail_transaksi WHERE id_transaksi = $idTransaksi", "Gagal mengambil data detail transaksi");
        if (!$data) {
            return [];
        }
        $result = mysqli_fetch_all($data, MYSQLI_ASSOC);
        return $result;
    }

    public function getCarts()
    {
        return $_SESSION['carts'] ?? [];
    }

    public function clearCarts()
    {
        $_SESSION['carts'] = [];
    }
}

$trs = new Transaction();

?>