<?php

namespace App\Actions\Occupation;

use App\Models\Authentication\Occupation;
use InvalidArgumentException;

class UpdateOccupation
{
    public function __construct(
        private CheckOccupationDuplication $checkOccupationDuplication
    ) {}

    public function execute(string $occupationId, string $occupationName): Occupation
    {
        if ($this->checkOccupationDuplication->execute($occupationName, $occupationId)) {
            throw new InvalidArgumentException('An occupation with this name already exists.');
        }

        $occupation = Occupation::where('occupation_id', $occupationId)->firstOrFail();

        $occupation->update(['occupation' => $occupationName]);

        return $occupation->fresh();
    }
}
