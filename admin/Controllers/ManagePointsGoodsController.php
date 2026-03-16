<?php

namespace Admin\Controllers;

use Admin\Models\Point;
use Admin\Models\Product;

class ManagePointsGoodsController
{
    private Point $point;
    private Product $product;

    public function __construct($pdo)
    {
        $this->point = new Point($pdo);
        $this->product = new Product($pdo);
    }

    public function handle(): void
    {
        $message = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['add_point'])) {
                $message = $this->handleAddPoint();
            }

            if (isset($_POST['add_produit'])) {
                $message = $this->handleAddProduct();
            }
        }

        $points = $this->point->getAll();
        $products = $this->product->getAll();

        require __DIR__ . '/../Views/points_goods/manage.php';
    }

    private function handleAddPoint(): string
    {
        $name = trim($_POST['point_name'] ?? '');
        $type = $_POST['point_type'] ?? '';

        if ($name === '' || !in_array($type, ['origin', 'destination', 'both'], true)) {
            return 'البيانات غير صالحة لإنشاء نقطة';
        }

        $success = $this->point->create($name, $type);
        return $success ? 'Point ajouté avec succès.' : 'Erreur lors de l\'ajout du point.';
    }

    private function handleAddProduct(): string
    {
        $name = trim($_POST['produit_name'] ?? '');
        $code = trim($_POST['produit_code'] ?? '');
        $description = trim($_POST['produit_description'] ?? '');

        if ($name === '') {
            return 'البيانات غير صالحة لإنشاء المنتج';
        }

        if ($this->product->existsByName($name)) {
            return 'Produit déjà existant.';
        }

        $success = $this->product->create($name, $code, $description);
        return $success ? 'Produit ajouté avec succès.' : 'Erreur lors de l\'ajout du produit.';
    }
}
