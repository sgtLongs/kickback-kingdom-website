<?php

declare(strict_types=1);

namespace Kickback\Views;
use \Kickback\Models\ForeignRecordId;

class vStore extends vRecordId
{

    public string $name;
    public ForeignRecordId $ownerId;

    function __construct(vRecordId $store, string $storeName, ForeignRecordId $owner)
    {
        parent::__construct( $store->ctime, $store->crand);
        $this->ownerId = $owner;
        $this->name = $storeName;
    }

}

?>