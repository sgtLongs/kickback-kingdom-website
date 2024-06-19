<?php

declare(strict_types=1);

namespace Kickback\Views;
use \Kickback\Models\ForeignRecordId;

class vStore extends vRecordId
{

    public string $name;
    public string $locator;
    public ForeignRecordId $ownerId;

    function __construct(string $ctime, int $crand, string $storeName, string $locator, string $ref_account_ctime, int $ref_account_crand)
    {
        parent::__construct($ctime, $crand);
        $this->ownerId = new ForeignRecordId($ref_account_ctime, $ref_account_crand);
        $this->name = $storeName;
        $this->locator = $locator;
    }

}

?>