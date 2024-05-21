<?php

declare(strict_types=1);

namespace Kickback\Views;
use Kickback\Views\vRecordId;
use Kickback\Models\ForeignRecordId;

class vCartProductLink extends vRecordId
{
    public ForeignRecordId $cartId;
    public ForeignRecordId $productId;

    public function __construct(vRecordId $cartProductLink, ForeignRecordId $product, ForeignRecordId $cart)
    {
        parent::__construct($cartProductLink->ctime, $cartProductLink->crand);

        $this->cartId = $cart;
        $this->productId = $product;
    }
}

?>