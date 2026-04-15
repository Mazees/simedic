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

    public function pushTransaction()
    {
        $totalHarga = $this->getTotalHarga();
        if ($totalHarga > 0) {
            $insertTransaksi = $this->runQuery("
            INSERT INTO transaksi (total_harga)
            VALUES ($totalHarga)
            ", "Gagal menambah stok");

            if ($insertTransaksi) {
                $id = (int) $this->connection->insert_id;
                $dataCarts = array_map(function ($cart) use ($id) {
                    $nama = $this->connection->real_escape_string($cart['nama']);
                    return "($id, {$cart['id']}, '$nama', {$cart['harga']}, {$cart['jumlah']})";
                }, $_SESSION['carts']);

                $value = implode(", ", $dataCarts);
                $insertDetailTransaksi = $this->runQuery("
                    INSERT INTO detail_transaksi(id_transaksi, id_product, nama_product, harga_product, qty) VALUES $value
                " . "

                ", "Gagal menambah stok");
                if ($insertDetailTransaksi) {
                    return $id;
                }

            } else {
                return false;
            }
        } else {
            return false;
        }
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