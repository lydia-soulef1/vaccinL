<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParentUserSeeder extends Seeder
{
    public function run()
    {
        $parents = DB::table('parents')->get();
        foreach ($parents as $parent) {
            // نفترض أنك تريد الربط بالبريد الإلكتروني مثلاً:
            $user = DB::table('users')->where('email', $parent->email)->first();

            if ($user) {
                DB::table('parents')
                    ->where('id', $parent->id)
                    ->update(['user_id' => $user->id]);
            }
        }
    }
}

