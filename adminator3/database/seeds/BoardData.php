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

        // generate "fresh" posts
        for ($i = 0; $i < 4; $i++) {
            $data[] = [
                'author'        => $faker->userName,
                'email'         => $faker->email,
                'subject'       => $faker->words(5, true),
                'body'          => $faker->text(60),
                'from_date'     => date('Y-m-d', strtotime('yesterday')),
                'to_date'       => date('Y-m-d', strtotime('+'. $i .' weeks')),
            ];
        }

        // generate "old" posts
        // TODO: add second loop with dates in past
        
        // This is a cool short-hand method
        $this->insert('board', $data);
    }
}
