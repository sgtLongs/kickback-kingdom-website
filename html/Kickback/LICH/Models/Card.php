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
    public int $imageId;

    public function __construct($name, $locator, $description, $cost, $type, $team, $imageId)
    {
        parent::__construct();

        $this->name = $name;
        $this->locator = $locator;
        $this->description = $description;
        $this->cost = $cost;
        $this->type = $type;
        $this->team = $team;
        $this->imageId = $imageId;
    }


}


?>