<?php

declare(strict_types=1);

namespace Kickback\Views;
use Kickback\Models\ForeignRecordId;


class vProduct extends vRecordId
{
    public string $name;
    public string $locator;
    public vPrice $price;
    public string $description;
    public string $storeLocator;
    public ForeignRecordId $storeId;
    public vMedia $media;

    function __construct(string $ctime, int $crand, string $name, string $locator, vPrice $price, string $description, string $storeLocator, string $storeCtime, int $storeCrand, string $mediaCtime, int $mediaCrand, string $mediaPath)
    {
        parent::__construct($ctime, $crand);

        $this->name = $name;
        $this->locator = $locator;
        $this->price = $price;
        $this->description = $description;
        $this->storeLocator = $storeLocator;
        $this->storeId = new ForeignRecordId($storeCtime, $storeCrand);

        $this->media = new vMedia($mediaCtime, $mediaCrand);
        $this->media->setMediaPath($mediaPath);
    }
}


?>