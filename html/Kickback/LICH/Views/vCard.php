<?php
declare(strict_types=1);

namespace Kickback\LICH\Views;

use \Kickback\Views\vRecordId;

class vCard extends vRecordId
{
    public string $name;
    public string $locator;
    public string $description;
    public int $cost;
    public string $type;
    public string $team;
    public string $mediaPath;
    public int $imageId;

    public function __construct($ctime, $crand, $name, $locator, $description, $cost, $type, $team, $mediaPath, $imageId)
    {
        parent::__construct($ctime, $crand);
        
        $this->name = $name;
        $this->locator = $locator;
        $this->description = $description;
        $this->cost = $cost;
        $this->type = $type;
        $this->team = $team;
        $this->mediaPath = $mediaPath;
        $this->imageId = $imageId;
    
    }
}

?>