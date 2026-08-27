<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Attribute as MongoDB;

#[MongoDB\Document(collection: "StatsCommandes")]
class StatsCommandes
{
    #[MongoDB\Id]
    private ?string $id = null;

    #[MongoDB\Field(type: "int")]
    private int $orderId;

    #[MongoDB\Field(type: "int")]
    private int $menuId;

    #[MongoDB\Field(type: "string")]
    private string $menuTitle;

    #[MongoDB\Field(type: "float")]
    private float $price;

    #[MongoDB\Field(type: "date_immutable")]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    // --- GETTERS & SETTERS ---

    public function getId(): ?string { return $this->id; }

    public function getOrderId(): int { return $this->orderId; }
    public function setOrderId(int $orderId): self { $this->orderId = $orderId; return $this; }

    public function getMenuId(): int { return $this->menuId; }
    public function setMenuId(int $menuId): self { $this->menuId = $menuId; return $this; }

    public function getMenuTitle(): string { return $this->menuTitle; }
    public function setMenuTitle(string $menuTitle): self { $this->menuTitle = $menuTitle; return $this; }

    public function getPrice(): float { return $this->price; }
    public function setPrice(float $price): self { $this->price = $price; return $this; }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): self { $this->createdAt = $createdAt; return $this; }
}