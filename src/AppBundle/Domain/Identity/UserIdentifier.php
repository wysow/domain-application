<?php

namespace AppBundle\Domain\Identity;

use RayRutjes\DomainFoundation\Domain\AggregateRoot\AggregateRootIdentifier;
use RayRutjes\DomainFoundation\ValueObject\Identity\Uuid;

final class UserIdentifier extends Uuid implements AggregateRootIdentifier
{
}
