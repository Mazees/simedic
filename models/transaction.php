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
                'subtotal' => $harga
            ];
        }
    }

    public function reduceCart($id, $nama, $harga)
    {
        $id = (int) $id;
        $harga = (int) $harga;

        $found = false;
        foreach ($_SESSION['carts'] as $key => $cart) {
            if ($cart['id'] == $id) {
                if ($cart['jumlah'] <= 1) {
                    unset($_SESSION['carts'][$key]);

                    // Me-reset ulang indeks array agar tetap berurutan
                    $_SESSION['carts'] = array_values($_SESSION['carts']);
                } else {
                    $_SESSION['carts'][$key]['jumlah']--;
                    $_SESSION['carts'][$key]['subtotal'] -= $harga;
                    $found = true;
                    break;
                }
            }
        }
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