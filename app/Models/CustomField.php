<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class CustomField extends Model
{
    public const TYPE_TEXT = 'text';

    public const TYPE_TEXTAREA = 'textarea';

    public const TYPE_RADIO = 'radio';

    public const TYPE_SELECT = 'select';

    public const TYPE_MULTISELECT = 'multiselect';

    public const TYPE_CHECKBOXES = 'checkboxes';

    public const TYPE_DATE = 'date';

    public const TYPE_DATETIME = 'datetime';

    public const TYPE_NUMBER = 'number';

    public const CONTEXT_PROFILE = 'profile';

    public const CONTEXT_JOB_LISTING = 'job_listing';

    public const CONTEXT_RESUME_BUILDER = 'resume_builder';

    protected $fillable = [
        'label',
        'slug',
        'icon_url',
        'field_type',
        'options',
        'contexts',
        'is_required',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'options' => 'array',
        'contexts' => 'array',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public static function fieldTypeLabels(): array
    {
        return [
            self::TYPE_TEXT => __('Text field'),
            self::TYPE_TEXTAREA => __('Textarea'),
            self::TYPE_RADIO => __('Radio buttons'),
            self::TYPE_SELECT => __('Select box'),
            self::TYPE_MULTISELECT => __('Multiple select'),
            self::TYPE_CHECKBOXES => __('Checkboxes'),
            self::TYPE_DATE => __('Date picker'),
            self::TYPE_DATETIME => __('Date & time'),
            self::TYPE_NUMBER => __('Number'),
        ];
    }

    public static function contextLabels(): array
    {
        return [
            self::CONTEXT_PROFILE => __('Job seeker profile form'),
            self::CONTEXT_JOB_LISTING => __('Job listing (employer)'),
            self::CONTEXT_RESUME_BUILDER => __('Resume builder'),
        ];
    }

    public static function typesRequiringOptions(): array
    {
        return [
            self::TYPE_RADIO,
            self::TYPE_SELECT,
            self::TYPE_MULTISELECT,
            self::TYPE_CHECKBOXES,
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeForContext(Builder $query, string $context): Builder
    {
        return $query->where('is_active', true)
            ->whereJsonContains('contexts', $context)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function needsOptions(): bool
    {
        return in_array($this->field_type, self::typesRequiringOptions(), true);
    }
}
