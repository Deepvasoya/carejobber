<?php

namespace App\Services;

use App\Models\CustomField;
use Illuminate\Http\Request;
use Illuminate\Validation\Validator;

class CustomFieldValueService
{
    /**
     * Add validation errors for admin-defined fields for the given context.
     */
    public function validateContext(Request $request, string $context, Validator $validator): void
    {
        $fields = CustomField::query()->forContext($context)->get();
        $input = $request->input('custom_fields', []);
        if (! is_array($input)) {
            $input = [];
        }

        foreach ($fields as $field) {
            $slug = $field->slug;
            $raw = $input[$slug] ?? null;
            if ($this->isEmpty($field, $raw)) {
                if ($field->is_required) {
                    $validator->errors()->add(
                        'custom_fields.'.$slug,
                        __('The :label field is required.', ['label' => $field->label])
                    );
                }

                continue;
            }

            $msg = $this->validateValue($field, $raw);
            if ($msg !== null) {
                $validator->errors()->add('custom_fields.'.$slug, $msg);
            }
        }
    }

    /**
     * Normalized slug => value for JSON storage (null = clear key).
     *
     * @return array<string, mixed>
     */
    public function normalizeForContext(Request $request, string $context): array
    {
        $fields = CustomField::query()->forContext($context)->get();
        $input = $request->input('custom_fields', []);
        if (! is_array($input)) {
            $input = [];
        }

        $out = [];
        foreach ($fields as $field) {
            $slug = $field->slug;
            $raw = $input[$slug] ?? null;
            if ($this->isEmpty($field, $raw)) {
                $out[$slug] = null;

                continue;
            }
            $out[$slug] = $this->normalizeValue($field, $raw);
        }

        return $out;
    }

    /**
     * Merge normalized values into an existing JSON map (by slug).
     *
     * @param  array<string, mixed>|null  $existing
     * @param  array<string, mixed>  $normalized
     * @return array<string, mixed>
     */
    public function mergeStored(?array $existing, array $normalized): array
    {
        $existing = $existing ?? [];
        foreach ($normalized as $slug => $value) {
            if ($value === null) {
                unset($existing[$slug]);
            } else {
                $existing[$slug] = $value;
            }
        }

        return $existing;
    }

    private function isEmpty(CustomField $field, $raw): bool
    {
        if ($raw === null || $raw === '') {
            return true;
        }
        if (is_array($raw)) {
            return count(array_filter($raw, fn ($x) => $x !== null && $x !== '')) === 0;
        }

        return false;
    }

    /**
     * @param  mixed  $raw
     */
    private function normalizeValue(CustomField $field, $raw)
    {
        switch ($field->field_type) {
            case CustomField::TYPE_MULTISELECT:
            case CustomField::TYPE_CHECKBOXES:
                if (! is_array($raw)) {
                    return [];
                }

                return array_values(array_unique(array_map('strval', array_filter($raw, fn ($x) => $x !== null && $x !== ''))));
            case CustomField::TYPE_NUMBER:
                return is_numeric($raw) ? 0 + $raw : null;
            default:
                return is_string($raw) ? trim($raw) : $raw;
        }
    }

    /**
     * @param  mixed  $raw
     */
    private function validateValue(CustomField $field, $raw): ?string
    {
        if (! in_array($field->field_type, CustomField::typesRequiringOptions(), true)) {
            return null;
        }

        $opts = $field->options ?? [];
        $allowed = [];
        foreach ($opts as $o) {
            $allowed[] = is_array($o) ? (string) ($o['value'] ?? '') : (string) $o;
        }

        switch ($field->field_type) {
            case CustomField::TYPE_RADIO:
            case CustomField::TYPE_SELECT:
                if (! in_array((string) $raw, $allowed, true)) {
                    return __('Invalid option selected.');
                }

                return null;
            case CustomField::TYPE_MULTISELECT:
            case CustomField::TYPE_CHECKBOXES:
                if (! is_array($raw)) {
                    return __('Invalid value.');
                }
                foreach ($raw as $v) {
                    if ($v === null || $v === '') {
                        continue;
                    }
                    if (! in_array((string) $v, $allowed, true)) {
                        return __('Invalid option selected.');
                    }
                }

                return null;
            default:
                return null;
        }
    }

    /**
     * Whether the stored value should be shown on public profile (non-empty).
     *
     * @param  mixed  $stored
     */
    public function hasDisplayableValue(CustomField $field, $stored): bool
    {
        if ($stored === 0 || $stored === '0') {
            return true;
        }

        return ! $this->isEmpty($field, $stored);
    }

    /**
     * Plain-text display for public profile / company page (escape in Blade with {{ }} except textarea uses nl2br).
     *
     * @param  mixed  $stored
     */
    public function formatDisplayValue(CustomField $field, $stored): string
    {
        if (! $this->hasDisplayableValue($field, $stored)) {
            return '';
        }

        switch ($field->field_type) {
            case CustomField::TYPE_MULTISELECT:
            case CustomField::TYPE_CHECKBOXES:
                if (! is_array($stored)) {
                    $stored = [$stored];
                }
                $parts = [];
                foreach ($stored as $v) {
                    if ($v === null || $v === '') {
                        continue;
                    }
                    $parts[] = $this->optionLabelForValue($field, (string) $v) ?? (string) $v;
                }

                return implode(', ', $parts);

            case CustomField::TYPE_RADIO:
            case CustomField::TYPE_SELECT:

                return $this->optionLabelForValue($field, (string) $stored) ?? (string) $stored;

            case CustomField::TYPE_DATE:
                try {
                    return \Carbon\Carbon::parse($stored)->format('Y-m-d');
                } catch (\Throwable $e) {
                    return (string) $stored;
                }

            case CustomField::TYPE_DATETIME:
                try {
                    return \Carbon\Carbon::parse($stored)->format('Y-m-d H:i');
                } catch (\Throwable $e) {
                    return (string) $stored;
                }

            case CustomField::TYPE_NUMBER:
                return (string) $stored;

            case CustomField::TYPE_TEXTAREA:
                return (string) $stored;

            default:
                return (string) $stored;
        }
    }

    private function optionLabelForValue(CustomField $field, string $value): ?string
    {
        foreach ($field->options ?? [] as $opt) {
            $ov = is_array($opt) ? (string) ($opt['value'] ?? '') : (string) $opt;
            $ol = is_array($opt) ? (string) ($opt['label'] ?? $ov) : (string) $opt;
            if ($ov === $value) {
                return $ol;
            }
        }

        return null;
    }
}
