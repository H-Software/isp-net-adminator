<?php


use Phinx\Seed\AbstractSeed;

class UsersAndPersistenceData extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     */
    public function run()
    {
        $faker = Faker\Factory::create();
        $data = [];

        $data[] = [
            'username'      => "administrator",
            'email'         => "admin@admin",
            'password'      => '$2y$10$uZ7ZBCl/Shp9sW.QRy10CuFxkO/Vg7Yr1kbVJzhSACNQoohChkxaW',
            'level'         => 101,
            'last_login'    => $faker->unixTime(),
            'created_at'	=> $faker->unixTime($faker->dateTimeInInterval('-1 week', '+3 days')),
            'updated_at'	=> $faker->unixTime($faker->dateTimeInInterval('-1 week', '+3 days')),
        ];

        // This is a cool short-hand method
        $this->insert('users', $data);
    }
}
