<?php

namespace App\Services;

use InvalidArgumentException;

class Builder
{
    protected function getRelationships(int $id = null): array
    {
        return [];
    }

    protected function getRequiredRelationships(array $relationshipNames, int $id = null)
    {
        $relationships = $this->getRelationships($id);
        $loads = [];

        foreach ($relationshipNames as $relationship) {
            if (key_exists($relationship, $relationships)) {
                $loads[$relationship] = $relationships[$relationship];
            } else {
                throw new InvalidArgumentException("Relationship not exists: {$relationship}", 1);
            }
        }

        return $loads;
    }
}
