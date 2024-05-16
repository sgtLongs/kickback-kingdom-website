<?php

declare(strict_types=1);

namespace Kickback\Views;
use \Kickback\Models\ForeignRecordId;

class vStore extends vRecordId
{

    public string $name;
    public ForeignRecordId $ownerId;

    function __construct(int $crand, string $ctime, string $storeName, ForeignRecordId $owner)
    {
        parent::__construct($crand, $ctime);
        $this->ownerId = $owner;
        $this->name = $storeName;
    }

}

?>