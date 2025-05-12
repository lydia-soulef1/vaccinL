<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Child;
use App\Models\ParentModel; // تأكد أنك تستخدم النموذج الصحيح
use App\Models\User;

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
    DB::table('pediatricians')
        ->where('id', $id)
        ->update(['accepted' => true]);

    return response()->json(['message' => 'Pédiatre accepté avec succès.']);
}

}
