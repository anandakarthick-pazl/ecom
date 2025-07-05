<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->hasPermission('users.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                function ($attribute, $value, $fail) {
                    $companyId = session('selected_company_id') ?? auth()->user()->company_id;
                    $exists = \App\Models\User::where('email', $value)
                                           ->where('company_id', $companyId)
                                           ->exists();
                    if ($exists) {
                        $fail('The email has already been taken within your company.');
                    }
                }
            ],
            'password' => ['required', 'confirmed', Password::defaults()],
            'employee_id' => [
                'nullable',
                'string',
                'max:50',
                function ($attribute, $value, $fail) {
                    if ($value) {
                        $companyId = session('selected_company_id') ?? auth()->user()->company_id;
                        $exists = \App\Models\User::where('employee_id', $value)
                                               ->where('company_id', $companyId)
                                               ->exists();
                        if ($exists) {
                            $fail('The employee ID has already been taken within your company.');
                        }
                    }
                }
            ],
            'phone' => 'nullable|string|max:20',
            'role_id' => 'required|exists:roles,id',
            'branch_id' => 'nullable|exists:branches,id',
            'department' => 'nullable|string|max:100',
            'designation' => 'nullable|string|max:100',
            'hire_date' => 'nullable|date|before_or_equal:today',
            'salary' => 'nullable|numeric|min:0|max:999999.99',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive',
            'individual_permissions' => 'nullable|array',
            'individual_permissions.*' => 'string|exists:permissions,name'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Employee name is required.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'password.required' => 'Password is required.',
            'password.confirmed' => 'Password confirmation does not match.',
            'role_id.required' => 'Please select a role for the employee.',
            'role_id.exists' => 'The selected role is invalid.',
            'hire_date.before_or_equal' => 'Hire date cannot be in the future.',
            'salary.numeric' => 'Salary must be a valid number.',
            'salary.min' => 'Salary cannot be negative.',
            'avatar.image' => 'Avatar must be an image file.',
            'avatar.mimes' => 'Avatar must be a JPEG, PNG, JPG, or GIF file.',
            'avatar.max' => 'Avatar file size cannot exceed 2MB.',
            'individual_permissions.array' => 'Individual permissions must be an array.',
            'individual_permissions.*.exists' => 'One or more selected permissions are invalid.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'employee name',
            'email' => 'email address',
            'employee_id' => 'employee ID',
            'role_id' => 'role',
            'branch_id' => 'branch',
            'hire_date' => 'hire date',
            'individual_permissions' => 'individual permissions',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Clean up employee ID
        if ($this->employee_id) {
            $this->merge([
                'employee_id' => strtoupper(trim($this->employee_id))
            ]);
        }

        // Clean up phone number
        if ($this->phone) {
            $this->merge([
                'phone' => preg_replace('/[^0-9+\-\s()]/', '', $this->phone)
            ]);
        }

        // Ensure status has a default value
        if (!$this->status) {
            $this->merge(['status' => 'active']);
        }
    }
}
