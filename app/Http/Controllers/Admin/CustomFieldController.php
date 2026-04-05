<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomField;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CustomFieldController extends Controller
{
    public function index()
    {
        $fields = CustomField::orderBy('sort_order')->orderByDesc('id')->paginate(25);

        return view('admin.custom_field.index', compact('fields'));
    }

    public function create()
    {
        return view('admin.custom_field.add', [
            'fieldTypes' => CustomField::fieldTypeLabels(),
            'contextLabels' => CustomField::contextLabels(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? Str::slug($data['label']));
        CustomField::create($data);
        flash(__('Custom field created.'))->success();

        return redirect()->route('list.custom.fields');
    }

    public function edit($id)
    {
        $field = CustomField::findOrFail($id);

        return view('admin.custom_field.edit', [
            'field' => $field,
            'fieldTypes' => CustomField::fieldTypeLabels(),
            'contextLabels' => CustomField::contextLabels(),
            'optionsText' => $this->optionsToText($field->options),
        ]);
    }

    public function update(Request $request, $id)
    {
        $field = CustomField::findOrFail($id);
        $data = $this->validated($request, $field->id);
        if (empty($data['slug'])) {
            $data['slug'] = $this->uniqueSlug(Str::slug($data['label']), $field->id);
        } else {
            $data['slug'] = $this->uniqueSlug($data['slug'], $field->id);
        }
        $field->update($data);
        flash(__('Custom field updated.'))->success();

        return redirect()->route('list.custom.fields');
    }

    public function destroy($id)
    {
        $field = CustomField::findOrFail($id);
        $field->delete();
        flash(__('Custom field deleted.'))->success();

        return redirect()->route('list.custom.fields');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        $types = array_keys(CustomField::fieldTypeLabels());

        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'slug' => ['nullable', 'string', 'max:191', Rule::unique('custom_fields', 'slug')->ignore($ignoreId)],
            'icon_url' => 'nullable|string|max:2048',
            'field_type' => ['required', Rule::in($types)],
            'options_text' => 'nullable|string|max:20000',
            'contexts' => 'required|array|min:1',
            'contexts.*' => Rule::in(array_keys(CustomField::contextLabels())),
            'is_required' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
            'sort_order' => 'nullable|integer|min:0|max:999999',
        ]);

        $type = $validated['field_type'];
        $optionsRaw = $request->input('options_text');
        $options = $this->parseOptionsText(is_string($optionsRaw) ? $optionsRaw : null);

        if (in_array($type, CustomField::typesRequiringOptions(), true)) {
            if ($options === [] || $options === null) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'options_text' => [__('Add at least one option (one per line).')],
                ]);
            }
        } else {
            $options = null;
        }

        $contexts = array_values(array_unique($validated['contexts']));

        return [
            'label' => $validated['label'],
            'slug' => isset($validated['slug']) && $validated['slug'] !== ''
                ? Str::slug($validated['slug'])
                : null,
            'icon_url' => $validated['icon_url'] ?? null,
            'field_type' => $type,
            'options' => $options,
            'contexts' => $contexts,
            'is_required' => $request->boolean('is_required'),
            'is_active' => $request->boolean('is_active'),
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
        ];
    }

    /**
     * Each line: "Label" or "value|Label". Stored as list of ["value" => ..., "label" => ...].
     *
     * @return array<int, array{value: string, label: string}>|null
     */
    private function parseOptionsText(?string $raw): ?array
    {
        $raw = trim((string) ($raw ?? ''));
        if ($raw === '') {
            return null;
        }

        $lines = preg_split('/\r\n|\r|\n/', $raw) ?: [];
        $out = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }
            if (str_contains($line, '|')) {
                [$value, $label] = array_map('trim', explode('|', $line, 2));
                $value = $value !== '' ? $value : Str::slug($label);
            } else {
                $label = $line;
                $value = Str::slug($line);
            }
            $out[] = ['value' => $value, 'label' => $label];
        }

        return $out === [] ? null : $out;
    }

    private function optionsToText(?array $options): string
    {
        if ($options === null || $options === []) {
            return '';
        }
        $lines = [];
        foreach ($options as $row) {
            if (is_string($row)) {
                $lines[] = $row;
                continue;
            }
            $value = $row['value'] ?? '';
            $label = $row['label'] ?? $value;
            if ((string) $value !== '' && strcasecmp((string) $value, Str::slug((string) $label)) !== 0) {
                $lines[] = $value . '|' . $label;
            } else {
                $lines[] = $label;
            }
        }

        return implode("\n", $lines);
    }

    private function uniqueSlug(string $base, ?int $ignoreId = null): string
    {
        $base = Str::slug($base);
        if ($base === '') {
            $base = 'field';
        }
        $slug = $base;
        $n = 1;
        while (CustomField::where('slug', $slug)
            ->when($ignoreId !== null, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $base . '-' . $n++;
        }

        return $slug;
    }
}
