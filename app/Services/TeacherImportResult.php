<?php

namespace App\Services;

class TeacherImportResult
{
    /**
     * @param  list<array{row: int, action: string, attributes: array<string, mixed>}>  $imported  Rows that were saved.
     * @param  list<array{row: int, reason: string, attributes: array<string, mixed>}>  $failures  Rows that could not be saved.
     */
    public function __construct(
        public int $created = 0,
        public int $updated = 0,
        public array $imported = [],
        public array $failures = [],
    ) {}

    public function processed(): int
    {
        return $this->created + $this->updated;
    }

    public function failed(): int
    {
        return count($this->failures);
    }

    public function total(): int
    {
        return $this->processed() + $this->failed();
    }

    public function hasErrors(): bool
    {
        return $this->failures !== [];
    }

    /**
     * Row-scoped failure messages, one per failed row.
     *
     * @return list<string>
     */
    public function errorMessages(): array
    {
        return array_map(
            fn (array $failure): string => "Baris {$failure['row']}: {$failure['reason']}",
            $this->failures,
        );
    }
}
