<?php

declare(strict_types=1);

namespace Kickback\Views;
use Kickback\Models\ForeignRecordId;

class vCart extends vRecordId
{
    public ForeignRecordId $storeId;
    public ForeignRecordId $accountId;

    function __construct(string $ctime, int $crand, string $storeCtime, int $storeCrand, string $accountCtime, int $accountCrand)
    {
        parent::__construct($ctime, $crand);

        $storeId = new ForeignRecordId($storeCtime, $storeCrand);
        $accountId = new ForeignRecordId($accountCtime, $accountCrand);
    }
}

?>