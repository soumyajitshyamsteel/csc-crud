<?php
namespace Database\Seeders;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Http\Request;
use Illuminate\Database\Seeder;
class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Request $request)
    {
        $faker = Faker::create();
        $user = new User();

        $user->name = $faker->name();
        $user->email = "tester@gmail.com";
        $user->email_verified_at = $faker->dateTime();
        $user->password = bcrypt('secret');
        $user->save();
    }
}
