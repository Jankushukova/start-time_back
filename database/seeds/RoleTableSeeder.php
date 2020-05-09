<?php

use App\Role;
use Illuminate\Database\Seeder;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = new Role();
        $admin->name = 'Админ';
        $admin->save();

        $admin = new Role();
        $admin->name = 'Директор';
        $admin->save();

        $teacher = new Role();
        $teacher->name = 'Менеджер';
        $teacher->save();

        $student = new Role();
        $student->name = 'Авторизованный';
        $student->save();

        $student = new Role();
        $student->name = 'Неавторизованный';
        $student->save();
    }
}
