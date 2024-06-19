<?php

declare(strict_types=1);

namespace Kickback\Models;


class Cart extends RecordId
{
    public ForeignRecordId $storeId;
    public ForeignRecordId $accountId;


    public function __construct(string $accountCtime, int $accountCrand, string $storeCtime, int $storeCrand)
    {
        parent::__construct();

        $this->storeId = new ForeignRecordId($storeCtime, $storeCrand);
        $this->accountId = new ForeignRecordId($accountCtime, $accountCrand);
    }
}

?>