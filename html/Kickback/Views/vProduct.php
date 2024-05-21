<?php

declare(strict_types=1);

namespace Kickback\Views;
use Kickback\Models\ForeignRecordId;

class vProduct extends vRecordId
{
    public string $name;
    public ForeignRecordId $storeId;

    function __construct(int $crand, string $ctime, string $productName, ForeignRecordId $store)
    {
        parent::__construct($crand, $ctime);

        $this->name = $productName;
        $this->storeId = $store;

    }
}

?>