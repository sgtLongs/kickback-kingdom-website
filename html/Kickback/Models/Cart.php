<?php

declare(strict_types=1);

namespace Kickback\Models;


class Cart extends RecordId
{
    public ForeignRecordId $storeId;
    public ForeignRecordId $accountId;


    public function __construct(ForeignRecordId $account, ForeignRecordId $store)
    {
        parent::__construct();

        $this->storeId = $store;
        $this->accountId = $account;
    }
}

?>