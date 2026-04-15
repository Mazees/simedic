<?php
require_once __DIR__ . '/../config.php';
class Product extends Database
{
    public function getAllProduct()
    {
        $data = $this->runQuery("SELECT * FROM product", "Gagal mengambil data produk");
        if (!$data) {
            return [];
        }
        $result = mysqli_fetch_all($data, MYSQLI_ASSOC);
        return $result;
    }
    public function getProductWithStock()
    {
        $data = $this->runQuery("
            SELECT product.id, product.nama, product.harga, COALESCE(SUM(stok.jumlah), 0) AS total_stok
            FROM product
            JOIN stok ON product.id = stok.id_product
            GROUP BY product.id, product.nama, product.harga
            ORDER BY product.nama ASC
        ", "Gagal mengambil data produk dengan stok");
        if (!$data) {
            return [];
        }
        $result = mysqli_fetch_all($data, MYSQLI_ASSOC);
        return $result;
    }
    public function addProduct($nama, $harga)
    {
        $nama = trim((string) $nama);
        $harga = (int) $harga;
        $this->runQuery("
            INSERT INTO product (nama, harga)
            VALUES ('$nama', '$harga')
        ", "Gagal menambah produk");
    }
    public function updateProduct($nama, $harga, $id)
    {
        $nama = trim((string) $nama);
        $harga = (int) $harga;
        $id = (int) $id;
        $this->runQuery("
            UPDATE product SET nama = '$nama', harga = $harga
            WHERE id = $id
        ", "Gagal mengubah produk");
    }
    public function deleteProduct($id)
    {
        $id = (int) $id;
        $this->runQuery("
            DELETE FROM product WHERE id = $id
        ", "Produk tidak bisa dihapus karena masih dipakai");
    }
    public function getJumlahProduct()
    {
        $data = $this->runQuery("SELECT COUNT(*) AS total FROM product", "Gagal menghitung jumlah produk");
        if (!$data) {
            return 0;
        }
        $result = mysqli_fetch_assoc($data);
        return (int) $result['total'];
    }
    public function getRataRataHargaProduct()
    {
        $data = $this->runQuery("SELECT AVG(harga) AS avg FROM product", "Gagal menghitung rata-rata harga");
        if (!$data) {
            return 0;
        }
        $result = mysqli_fetch_assoc($data);
        return (int) $result['avg'];
    }
    public function searchProduct($query)
    {
        $data = $this->runQuery("SELECT * FROM product WHERE nama LIKE '$query%'", "Gagal mencari $query");
        if (!$data) {
            return [];
        }
        $result = mysqli_fetch_all($data, MYSQLI_ASSOC);
        return $result;
    }
}

$product = new Product();
?>