<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Services\UsernameService;
use App\Models\User;
use App\Models\Learner;
use App\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $roles = Role::pluck('id', 'name'); // ['admin' => 1, 'staff' => 2, ...]

        // ===== Staffs =====
        $staff1 = User::firstOrCreate(['email' => 'staff1@pion.vn'], [
            'display_name' => 'Admin Pion',
            'password'     => Hash::make('Staff@6868'),
            'role_id'      => $roles['staff'] ?? null,
        ]);
        if (!$staff1->username) {
            $staff1->username = UsernameService::generate($staff1);
            $staff1->save();
        }

        $staff2 = User::firstOrCreate(['email' => 'staff2@pion.vn'], [
            'display_name' => 'Staff Two',
            'password'     => Hash::make('Staff@6868'),
            'role_id'      => $roles['staff'] ?? null,
        ]);
        if (!$staff2->username) {
            $staff2->username = UsernameService::generate($staff2);
            $staff2->save();
        }

        // Staff ADS
        $staffAds = User::firstOrCreate(['email' => 'adminads@pion.vn'], [
            'display_name' => 'Staff ADS',
            'password'     => Hash::make('Admin@Pion6868'),
            'role_id'      => $roles['staffads'] ?? null,
        ]);
        if (!$staffAds->username) {
            $staffAds->username = UsernameService::generate($staffAds);
            $staffAds->save();
        }

        // ===== Teachers =====
        $teacher1 = User::firstOrCreate(['email' => 'teacher1@pion.vn'], [
            'display_name' => 'Cô Rita',
            'password'     => Hash::make('Teacher@6868'),
            'role_id'      => $roles['teacher'] ?? null,
        ]);
        if (!$teacher1->username) {
            $teacher1->username = UsernameService::generate($teacher1);
            $teacher1->save();
        }

        $teacher2 = User::firstOrCreate(['email' => 'teacher2@pion.vn'], [
            'display_name' => 'Thầy Minh',
            'password'     => Hash::make('Teacher@6868'),
            'role_id'      => $roles['teacher'] ?? null,
        ]);
        if (!$teacher2->username) {
            $teacher2->username = UsernameService::generate($teacher2);
            $teacher2->save();
        }

        // ===== Learners =====
        $learner1 = User::firstOrCreate(['email' => 'learner1@pion.vn'], [
            'display_name' => 'Bạn An',
            'password'     => Hash::make('Learner@6868'),
            'role_id'      => $roles['learner'] ?? null,
        ]);

        Learner::firstOrCreate(['user_id' => $learner1->id], [
            'first_name' => 'An',
            'last_name'  => 'Nguyen',
            'dob'        => '2010-05-12',
            'grade'      => '10',
            'class'      => '10A1',
        ]);

        // reload quan hệ learner để chắc chắn có dữ liệu
        $learner1->load('learner');
        if (!$learner1->username) {
            $learner1->username = UsernameService::generate($learner1);
            $learner1->save();
        }

        $learner2 = User::firstOrCreate(['email' => 'learner2@pion.vn'], [
            'display_name' => 'Bạn Bình',
            'password'     => Hash::make('Learner@6868'),
            'role_id'      => $roles['learner'] ?? null,
        ]);

        Learner::firstOrCreate(['user_id' => $learner2->id], [
            'first_name' => 'Binh',
            'last_name'  => 'Tran',
            'dob'        => '2009-08-20',
            'grade'      => '11',
            'class'      => '11B2',
        ]);

        $learner2->load('learner');
        if (!$learner2->username) {
            $learner2->username = UsernameService::generate($learner2);
            $learner2->save();
        }
    }
}
