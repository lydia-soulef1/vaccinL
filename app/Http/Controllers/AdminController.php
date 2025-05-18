<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Child;
use App\Models\ParentModel; // تأكد أنك تستخدم النموذج الصحيح
use App\Models\User;
use App\Models\Pediatrician;
use Illuminate\Support\Facades\Mail;
use App\Mail\PediatricianAccepted;

class AdminController extends Controller
{
    // لا تحتاج إلى parent_id هنا
    public function statistics()
    {
        $pediatricians = DB::table('pediatricians')
            ->where('accepted', false)
            ->select('id', 'name', 'prenom', 'email', 'hospital', 'rpps_number', 'accepted')
            ->get();


        $parents = DB::table('parents')->select('id', 'name', 'email', 'user_id')->get();

        $childrenCount = DB::table('children')->count();
        $pediatriciansCount = DB::table('pediatricians')->count();
        $parentsCount = DB::table('parents')->count();

        return view('statistics', compact(
            'pediatricians',
            'parents',
            'childrenCount',
            'pediatriciansCount',
            'parentsCount'
        ));
    }



    public function getChildren($parentId)
    {
        // جلب user_id من جدول parents بناءً على parentId
        $parent = DB::table('parents')->where('user_id', $parentId)->first();

        if (!$parent) {
            return response()->json(['message' => 'Parent not found.']);
        }

        // جلب الأطفال باستخدام user_id
        $children = DB::table('children')
            ->where('parent_id', $parent->user_id)
            ->get();

        if ($children->isEmpty()) {
            return response()->json(['children' => []]); // لا توجد أطفال
        }

        return response()->json(['children' => $children]);
    }



    public function acceptPediatrician($id)
    {
        $pediatrician = Pediatrician::findOrFail($id);

        // تحديث الحقل accepted
        $pediatrician->accepted = true;
        $pediatrician->save();

        // إرسال بريد للطبيب
        Mail::to($pediatrician->email)->send(new PediatricianAccepted($pediatrician));

        return response()->json(['success' => true]);
    }
}
