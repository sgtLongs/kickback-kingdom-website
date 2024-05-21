<?php

declare(strict_types=1);

namespace Kickback\Views;
use Kickback\Models\ForeignRecordId;

class vCart extends vRecordId
{
    public ForeignRecordId $storeId;
    public ForeignRecordId $accountId;

    function __construct(vRecordId $cart, ForeignRecordId $store, ForeignRecordId $account)
    {
        parent::__construct($cart->ctime, $cart->crand);

        $storeId = $store;
        $accountId = $account;
    }
}

?>