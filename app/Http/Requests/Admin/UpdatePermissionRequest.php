<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePermissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->hasPermission('permissions.update');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $permissionId = $this->route('permission')->id;
        
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:permissions,name,' . $permissionId,
                'regex:/^[a-z_.]+$/'
            ],
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'module' => 'required|string|max:100|alpha_dash',
            'action' => 'required|string|max:100|alpha_dash'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Permission name is required.',
            'name.unique' => 'This permission name already exists.',
            'name.regex' => 'Permission name must only contain lowercase letters, dots, and underscores.',
            'display_name.required' => 'Display name is required.',
            'module.required' => 'Module is required.',
            'module.alpha_dash' => 'Module must only contain letters, numbers, dashes, and underscores.',
            'action.required' => 'Action is required.',
            'action.alpha_dash' => 'Action must only contain letters, numbers, dashes, and underscores.',
            'description.max' => 'Description cannot exceed 500 characters.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'permission name',
            'display_name' => 'display name',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Clean up permission name
        if ($this->name) {
            $this->merge([
                'name' => strtolower(trim($this->name))
            ]);
        }

        // Clean up module and action
        if ($this->module) {
            $this->merge([
                'module' => strtolower(trim($this->module))
            ]);
        }

        if ($this->action) {
            $this->merge([
                'action' => strtolower(trim($this->action))
            ]);
        }
    }
}
