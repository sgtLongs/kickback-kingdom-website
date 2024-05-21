<?php

declare(strict_types=1);

namespace Kickback\Models;

class CartProductLink extends RecordId
{
    public ForeignRecordId $cartId;
    public ForeignRecordId $productId;

    public function __construct(ForeignRecordId $product, ForeignRecordId $cart)
    {
        parent::__construct();

        $this->cartId = $cart;
        $this->productId = $product;
    }
}

?>