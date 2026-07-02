<?php

require_once __DIR__ . '/Model.php';

class ProductImg extends Model {


    public function findById(int $id): ?array {
        return $this->fetchOne(
            'SELECT * FROM Product_img WHERE id = ?',
            [$id]
        );
    }

    public function findByVariantProduct(int $productId): array {
        return $this->fetchAll(
            'SELECT DISTINCT pi.*
             FROM Product_img pi
             JOIN Product_variants pv ON pv.product_img_id = pi.id
             WHERE pv.product_id = ?',
            [$productId]
        );
    }


    public function create(
        string  $thumbnail,
        ?string $topView    = null,
        ?string $bottomView = null,
        ?string $sideView   = null,
        ?string $pairView   = null
    ): int {
        return $this->insert(
            'INSERT INTO Product_img (thumbnail, top_view, bottom_view, side_view, pair_view)
             VALUES (?, ?, ?, ?, ?)',
            [$thumbnail, $topView, $bottomView, $sideView, $pairView]
        );
    }


    public function update(
        int     $id,
        string  $thumbnail,
        ?string $topView    = null,
        ?string $bottomView = null,
        ?string $sideView   = null,
        ?string $pairView   = null
    ): int {
        return $this->execute(
            'UPDATE Product_img
             SET thumbnail = ?, top_view = ?, bottom_view = ?, side_view = ?, pair_view = ?
             WHERE id = ?',
            [$thumbnail, $topView, $bottomView, $sideView, $pairView, $id]
        );
    }


    public function delete(int $id): int {
        return $this->execute('DELETE FROM Product_img WHERE id = ?', [$id]);
    }
}