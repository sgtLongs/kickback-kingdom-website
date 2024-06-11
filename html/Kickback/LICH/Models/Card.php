<?php
declare(strict_types=1);

namespace Kickback\LICH\Models;

use \Kickback\Models\RecordId;

class Card extends RecordId
{
    public string $name;
    public string $locator;
    public string $description;
    public int $cost;
    public string $type;
    public string $team;
    public int $cardImageId;

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