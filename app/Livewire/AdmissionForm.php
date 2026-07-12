<?php

namespace App\Livewire;

use App\Models\Admission;
use Livewire\Component;
use Illuminate\Support\Facades\Session;

class AdmissionForm extends Component
{
    public $name;
    public $email;
    public $phone;
    public $birth_date;
    public $address;
    public $previous_school;
    public $parent_name;
    public $parent_phone;

    public $isSubmitted = false;
    public $registrationNumber = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'nullable|email|max:255',
        'phone' => 'nullable|string|max:20',
        'birth_date' => 'nullable|date',
        'address' => 'nullable|string',
        'previous_school' => 'nullable|string|max:255',
        'parent_name' => 'nullable|string|max:255',
        'parent_phone' => 'nullable|string|max:20',
    ];

    public function submit()
    {
        $this->validate();

        $admission = Admission::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'birth_date' => $this->birth_date,
            'address' => $this->address,
            'previous_school' => $this->previous_school,
            'parent_name' => $this->parent_name,
            'parent_phone' => $this->parent_phone,
            'status' => 'pending',
        ]);

        $this->registrationNumber = $admission->registration_number;
        $this->isSubmitted = true;
    }

    public function render()
    {
        return view('livewire.admission-form');
    }
}
