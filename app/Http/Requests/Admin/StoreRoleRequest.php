<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->hasPermission('roles.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z_]+$/',
                function ($attribute, $value, $fail) {
                    $companyId = session('selected_company_id') ?? auth()->user()->company_id;
                    $exists = \App\Models\Role::where('name', $value)
                                            ->where('company_id', $companyId)
                                            ->exists();
                    if ($exists) {
                        $fail('The role name has already been taken within your company.');
                    }
                }
            ],
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Role name is required.',
            'name.regex' => 'Role name must only contain lowercase letters and underscores.',
            'display_name.required' => 'Display name is required.',
            'description.max' => 'Description cannot exceed 500 characters.',
            'permissions.array' => 'Permissions must be an array.',
            'permissions.*.exists' => 'One or more selected permissions are invalid.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'role name',
            'display_name' => 'display name',
            'is_active' => 'status',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Clean up role name
        if ($this->name) {
            $this->merge([
                'name' => strtolower(trim($this->name))
            ]);
        }

        // Ensure is_active has a default value
        if (!$this->has('is_active')) {
            $this->merge(['is_active' => true]);
        }
    }
}
