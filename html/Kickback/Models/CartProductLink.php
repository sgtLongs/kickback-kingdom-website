<?php

declare(strict_types=1);

namespace Kickback\Models;

use \Kickback\Views\vRecordId;

class CartProductLink extends RecordId
{
    public ForeignRecordId $cartId;
    public ForeignRecordId $productId;

    public function __construct(vRecordId $product, vRecordId $cart)
    {
        parent::__construct();

        $this->cartId = new ForeignRecordId($cart->ctime, $cart->crand);
        $this->productId = new ForeignRecordId($product->ctime, $product->crand);
    }
}

?>