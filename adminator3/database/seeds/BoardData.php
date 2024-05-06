<?php


use Phinx\Seed\AbstractSeed;

class BoardData extends AbstractSeed
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
        for ($i = 0; $i < 4; $i++) {
            $data[] = [
                'author'        => $faker->userName,
                'email'         => $faker->email,
                'subject'       => $faker->words(5, true),
                'body'          => $faker->text(120),
                'from_date'     => $faker->date('2024-04-08'),
                'to_date'       => $faker->date('2024-04-10'),
            ];
        }

        // This is a cool short-hand method
        $this->insert('board', $data);
        // $board = $this->table('board');
        // $board->insert($data)
        //       ->saveData();
    }
}
