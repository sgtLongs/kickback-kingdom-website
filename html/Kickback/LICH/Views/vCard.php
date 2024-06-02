<?php

namespace Kickback\LICH;

use Kickback\Views\vRecordId;

class vCard extends vRecordId
{
    string $name;
    string $description;
    int $cost;
    string $type;
    string $team;
    string $mediaPath;

    public function __construct($ctime, $crand, $name, $description, $cost, $type, $team, $mediaPath)
    {
        parent::__construct($ctime, $crand);
        
        $this->name = $name;
        $this->description = $description;
        $this->cost = $cost;
        $this->type = $type;
        $this->team = $team;
        $this->mediaPath = $mediaPath;
    
    }
}

?>