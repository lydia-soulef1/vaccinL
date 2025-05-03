<?php

namespace App\Http\Controllers;

use App\Models\Child; // Assuming you have a Child model for children
use App\Models\Notification; // Assuming you have a Notification model
use App\Models\Vaccination;
use App\Models\Vaccine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{

    public function pediatricianDashboard()
    {

        $parent = Auth::user();
        // Get the authenticated pediatrician
        $pediatrician = Auth::user();
        $totalVaccines = Vaccine::count();

        // Get children assigned to the pediatrician
        $children = Child::with(['parent', 'pediatrician']) // تحميل علاقة الطبيب أيضًا
            ->withCount(['vaccinations as vaccines_done'])
            ->get();



        // Get all vaccines
        $vaccines = Vaccine::all();

        // Add age to each child
        foreach ($children as $child) {
            $child->vaccines_total = $totalVaccines;
            $child->age = \Carbon\Carbon::parse($child->dob)->age;

            // Get the latest vaccination for the child
            $lastVaccination = $child->vaccinations()->latest()->first();

            $child->pediatrician_name = $lastVaccination && $lastVaccination->pediatrician
                ? $lastVaccination->pediatrician->name
                : 'Non défini';

            if ($lastVaccination) {
                // If a vaccination exists, get the next rendezvous date
                $nextRendezvous = $lastVaccination->next_rendez_vous;
            } else {
                // If no vaccination exists, set the next rendezvous to null or some default
                $nextRendezvous = null;
            }

            // Attach next rendezvous date to the child model
            $child->next_rendez_vous = $nextRendezvous;

            // Get the next vaccine after the last vaccine
            if ($lastVaccination) {
                $lastVaccineId = $lastVaccination->vaccine_id;
                $nextVaccine = Vaccine::where('id', '>', $lastVaccineId)->orderBy('id')->first();
            } else {
                // If no vaccinations, get the first vaccine
                $nextVaccine = Vaccine::orderBy('id')->first();
            }

            // Store the next vaccine name for the child
            $child->next_vaccine_name = $nextVaccine ? $nextVaccine->name : null;
            $child->parent_name = $child->parent ? $child->parent->name : 'Non disponible';
        }

        // Get notifications related to these children
        $childIds = $children->pluck('id');
        $notifications = Notification::whereIn('child_id', $childIds)->get();

        // Return the dashboard view with the necessary data
        return view('meddash', compact('pediatrician', 'children', 'vaccines', 'notifications', 'lastVaccination', 'parent'));
    }





    /**
     * Show the parent dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function parentDashboard()
    {
        // Get the authenticated parent
        $parent = Auth::user();

        // Get the parent's children
        $children = Child::with('parent')  // Load the parent relationship
            ->withCount(['vaccinations as vaccines_done'])
            ->get();

        $childIds = $children->pluck('id');

        $totalVaccines = Vaccine::count();
        $vaccines = Vaccine::all();

        foreach ($children as $child) {
            $child->vaccines_total = $totalVaccines;
            $child->age = \Carbon\Carbon::parse($child->dob)->age;

            // Get the latest vaccination for the child
            $lastVaccination = $child->vaccinations()->latest()->first();
            $child->pediatrician_name = $lastVaccination && $lastVaccination->pediatrician
                ? $lastVaccination->pediatrician->name
                : 'Non défini';

            $child->last_vaccine_name = $lastVaccination && $lastVaccination->vaccine ? $lastVaccination->vaccine->name : 'غير متوفر';

            if ($lastVaccination) {
                // If a vaccination exists, get the next rendezvous date
                $nextRendezvous = $lastVaccination->next_rendez_vous;
            } else {
                // If no vaccination exists, set the next rendezvous to null or some default
                $nextRendezvous = null;
            }

            // Attach next rendezvous date to the child model
            $child->next_rendez_vous = $nextRendezvous;

            // Get the next vaccine after the last vaccine
            if ($lastVaccination) {
                $lastVaccineId = $lastVaccination->vaccine_id;
                $nextVaccine = Vaccine::where('id', '>', $lastVaccineId)->orderBy('id')->first();
            } else {
                // If no vaccinations, get the first vaccine
                $nextVaccine = Vaccine::orderBy('id')->first();
            }

            // Store the next vaccine name for the child
            $child->next_vaccine_name = $nextVaccine ? $nextVaccine->name : null;
            $child->parent_name = $child->parent ? $child->parent->name : 'Non disponible';
        }

        // Get the parent's notifications
        $notifications = Notification::whereIn('child_id', $childIds)->get();

        // Return the dashboard view with the parent data
        return view('pardash', compact('parent', 'children', 'notifications', 'vaccines', 'lastVaccination'));
    }


    // التعديل داخل الدالة updateVaccines
    public function updateVaccines(Request $request)
    {
        $childId = $request->child_id;
        $vaccineIds = $request->vaccine_id ?? []; // list of selected vaccines
        $pediatricianId = Auth::id();

        $vaccinationSchedule = [
            1 => 0,     // BCG
            2 => 0,     // HBV-1
            3 => 0,     // Polio 1
            4 => 30,    // HBV-2
            5 => 90,    // DTC-1
            6 => 90,    // HIB-1
            7 => 90,    // Polio 2
            8 => 120,   // DTC-2
            9 => 120,   // HIB-2
            10 => 120,  // Polio 3
            11 => 150,  // DTC-3
            12 => 150,  // HIB-3
            13 => 150,  // HBV-3
            14 => 150,  // Polio 4
            15 => 180,  // Vit D3
            16 => 270,  // Anti-rougeole
            17 => 540,  // DTC-4
            18 => 540,  // HIB-4
            19 => 540,  // Polio 5
            20 => 2190, // DT enfant
            21 => 2190, // Anti-rougeole 2
            22 => 2190, // Polio 6
            23 => 4015, // DT adulte
            24 => 4015, // Polio 7
            25 => 5840, // DT adulte (16 ans)
            26 => 5840, // Polio 8
            27 => 5840, // DT adulte rappel
        ];

        foreach ($vaccineIds as $vaccineId) {
            $today = now();
            $nextOffset = null;

            // ابحث عن رقم اللقاح التالي (مثلاً إذا اللقاح الحالي هو 6، التالي هو 7)
            $vaccineKeys = array_keys($vaccinationSchedule);
            $currentIndex = array_search($vaccineId, $vaccineKeys);

            if ($currentIndex !== false && isset($vaccineKeys[$currentIndex + 1])) {
                $nextVaccineId = $vaccineKeys[$currentIndex + 1];
                $nextDays = $vaccinationSchedule[$nextVaccineId] - $vaccinationSchedule[$vaccineId];
                $nextDate = $today->copy()->addDays($nextDays);
            } else {
                // لا يوجد لقاح بعده
                $nextDate = null;
            }

            // حفظ أو تحديث
            $vaccine = Vaccination::firstOrNew(['child_id' => $childId, 'vaccine_id' => $vaccineId]);
            $vaccine->date_administered = now();
            $vaccine->pediatrician_id = $pediatricianId;
            $vaccine->next_rendez_vous = $nextDate ? $nextDate->toDateString() : null; // تأكد من تخزين التاريخ فقط
            $vaccine->save();
        }

        return redirect()->back()->with('success', 'تم تحديث اللقاحات بنجاح.');
    }
}
