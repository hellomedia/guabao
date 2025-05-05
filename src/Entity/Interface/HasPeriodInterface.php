<?php

namespace App\Entity\Interface;

interface HasPeriodInterface
{
    public function getPeriod(): ?string;
}