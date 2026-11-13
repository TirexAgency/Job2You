<?php

namespace App\Http\Requests\Concerns;

trait ComposesDateFields
{
    /**
     * Compose les champs de date à partir des sélecteurs jour / mois / année.
     *
     * @param  array<int, string>  $fields
     */
    protected function composeDateFields(array $fields): void
    {
        foreach ($fields as $field) {
            if ($this->filled($field)) {
                continue;
            }

            $day = $this->input($field.'_day');
            $month = $this->input($field.'_month');
            $year = $this->input($field.'_year');

            $this->merge([
                $field => ($day && $month && $year)
                    ? sprintf('%04d-%02d-%02d', (int) $year, (int) $month, (int) $day)
                    : null,
            ]);
        }
    }
}
