<?php
require_once __DIR__ . '/../config.php';
class Stok extends Database
{
    public function getAllStok()
    {
        $data = $this->runQuery(
            "SELECT stok.id, stok.id_product, product.nama, product.harga, stok.batch, stok.jumlah, stok.tgl_masuk, stok.tgl_exp
            FROM stok
            INNER JOIN product ON product.id = stok.id_product
            ORDER BY stok.id DESC",
            "Gagal mengambil data stok"
        );
        if (!$data) {
            return [];
        }
        $result = mysqli_fetch_all($data, MYSQLI_ASSOC);
        return $result;
    }
    public function addStok($idProduct, $kodeBatch, $jumlah, $tglExp)
    {
        $idProduct = (int) $idProduct;
        $jumlah = (int) $jumlah;
        $kodeBatch = trim($kodeBatch);
        $tglExp = trim($tglExp);

        $this->runQuery("
            INSERT INTO stok (id_product, batch, jumlah, tgl_exp)
            VALUES ($idProduct, '$kodeBatch', $jumlah, '$tglExp')
        ", "Gagal menambah stok");
    }
    public function deleteStok($id)
    {
        $id = (int) $id;
        $this->runQuery("DELETE FROM stok WHERE id = $id", "Gagal menghapus data stok");
    }
    public function increaseStok($id)
    {
        $id = (int) $id;
        $this->runQuery("
            UPDATE stok SET jumlah = jumlah + 1
            WHERE id = $id
        ", "Gagal menambah jumlah stok");
    }
    public function decreaseStok($id)
    {
        $id = (int) $id;
        $this->runQuery("
            UPDATE stok SET jumlah = jumlah - 1
            WHERE id = $id AND jumlah > 0
        ", "Gagal mengurangi jumlah stok");
    }
}

$stok = new Stok();
?>