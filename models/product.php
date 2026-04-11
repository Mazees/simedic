<?php
require_once __DIR__ . '/../config.php';
class Product extends Database
{
    public function getAllProduct()
    {
        $data = $this->connection->query("SELECT * FROM product");
        $result = mysqli_fetch_all($data, MYSQLI_ASSOC);
        return $result;
    }
    public function addProduct($nama, $harga)
    {
        $this->connection->query("
            INSERT INTO product (nama, harga)
            VALUES ('$nama', '$harga')
        ");
    }
    public function updateProduct($nama, $harga, $id)
    {
        $this->connection->query("
            UPDATE product SET nama = '$nama', harga = $harga
            WHERE id = $id
        ");
    }
    public function deleteProduct($id)
    {
        $id = (int) $id;
        $this->connection->query("
            DELETE FROM product WHERE id = $id
        ");
    }
    public function getJumlahProduct()
    {
        $data = $this->connection->query("SELECT COUNT(*) AS total FROM product");
        $result = mysqli_fetch_assoc($data);
        return (int) $result['total'];
    }
    public function getRataRataHargaProduct()
    {
        $data = $this->connection->query("SELECT AVG(harga) AS avg FROM product");
        $result = mysqli_fetch_assoc($data);
        return (int) $result['avg'];
    }
}

$product = new Product();
?>