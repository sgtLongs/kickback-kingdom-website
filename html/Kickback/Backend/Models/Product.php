<?php

declare(strict_types=1);

namespace Kickback\Models;
use Kickback\Views\vRecordId;

class Product extends RecordId
{
    public string $name;
    public ForeignRecordId $storeId;

    function __construct(string $productName, string $storeCtime, int $storeCrand)
    {
        parent::__construct();

        $this->name = $productName;
        $this->storeId = new ForeignRecordId($storeCtime, $storeCrand);

    }
}

?>