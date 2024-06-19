<?php

declare(strict_types=1);

namespace Kickback\Views;
use Kickback\Views\vRecordId;
use Kickback\Models\ForeignRecordId;

class vCartProductLink extends vRecordId
{
    public string $productName;
    public string $username;
    public string $description;
    public vPrice $price;
    public vMedia $media;
    public vRecordId $cartId;
    public vRecordId $productId;
    public vRecordId $accountId;

    public function __construct(string $ctime, int $crand, string $productName, string $username, string $description, string $price, string $mediaPath, vRecordId $mediaId, vRecordId $cartId, vRecordId $productId, vRecordId $accountId)
    {
        parent::__construct($ctime, $crand);

        $this->productName = $productName;
        $this->username = $username;
        $this->cartId = $cartId;
        $this->productId = $productId;
        $this->accountId = $accountId;
        $this->description = $description;
        $this->price = new vPrice($price);

        $this->media = new vMedia($mediaId->ctime, $mediaId->crand);
        $this->media->setMediaPath($mediaPath);

    }
}

?>