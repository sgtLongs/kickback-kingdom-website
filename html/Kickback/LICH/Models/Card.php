<?php

namespace Kickback\LICH;

use Kickback\Models\RecordId;

class Card extends RecordId
{
    string $name;
    string $description;
    int $cost;
    string $type;
    string $team;
    int $cardImageId;

    public function __construct($name, $description, $cost, $type, $team, $cardImageId)
    {
        parent::__construct();

        $this->name = $name;
        $this->description = $description;
        $this->cost = $cost;
        $this->type = $type;
        $this->cardImageId = $cardImageId;
    }


}


?>