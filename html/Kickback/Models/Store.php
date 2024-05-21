<?php
declare(strict_types=1);

namespace Kickback\Models;
use Kickback\Views\vAccount;
use Kickback\Views;
use \Datetime;

class Store extends RecordId
{
    public string $name;
    public ForeignRecordId $ownerId;

    function __construct(string $storeName, ForeignRecordId $owner)
    {
        parent::__construct();
        $this->ownerId = $owner;
        $this->name = $storeName;
    }

}

?>