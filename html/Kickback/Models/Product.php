<?php

declare(strict_types=1);

namespace Kickback\Models;
use Kickback\Views\vRecordId;

class Product extends RecordId
{
    public string $name;
    public ForeignRecordId $storeId;

    function __construct(string $productName, ForeignRecordId $store)
    {
        parent::__construct();

        $this->name = $productName;
        $this->storeId = $store;

    }
}

?>