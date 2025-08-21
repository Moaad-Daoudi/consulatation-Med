<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use App\Models\User;

class DoctorBaseController extends Controller
{
    /**
     * Constructor for the DoctorBaseController.
     * This will automatically run for any controller that extends it.
     */
    public function __construct()
    {
        $this->shareModalData();
    }

    /**
     * A helper method to fetch data needed by global modals and share it with all views.
     */
    private function shareModalData(): void
    {
        $patientsForModal = User::whereHas('role', function ($query) {
            $query->where('role', 'patient');
        })->orderBy('name')->get(['id', 'name']);

        View::share('patientsForModal', $patientsForModal);
    }
}