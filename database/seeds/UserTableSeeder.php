<?php

use App\User;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *ll
     * @return void
     */
    public function run()
    {
        $admin = new User();
        $admin->fill([
            'password' => '11223344',
            'firstname' => 'admin',
            'lastname' => 'admin',
            'phone_number' => '87777777',
            'email' => 'admin@mail.kz',
            'email_verified_at'=> \Carbon\Carbon::today(),
            'role_id' => 1
        ]);
        $admin->password = bcrypt($admin->password);
        $admin->save();


    }
}
