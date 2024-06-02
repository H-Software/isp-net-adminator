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
                'author'        => $this->sanitizeString(
                                        $faker->userName
                                    ),
                'email'         => $this->sanitizeString(
                                        $faker->email
                ),
                'subject'       => $this->sanitizeString(
                                        $faker->words(5, true)
                                    ),
                'body'          => $this->sanitizeString(
                                        $faker->text(60)
                                    ),
                'from_date'     => date('Y-m-d', strtotime('yesterday')),
                'to_date'       => date('Y-m-d', strtotime('+'. $i .' weeks')),
            ];
        }

        // generate "old" posts
        for ($i = 0; $i < 4; $i++) {
            $data[] = [
                'author'        => $this->sanitizeString(
                                        $faker->userName
                                    ),
                'email'         => $this->sanitizeString(
                                        $faker->email
                ),
                'subject'       => $this->sanitizeString(
                                        $faker->words(5, true)
                                    ),
                'body'          => $this->sanitizeString(
                                        $faker->text(60)
                                    ),
                'from_date'     => date('Y-m-d', strtotime('-'. $i .' weeks')),
                'to_date'       => date('Y-m-d', strtotime('-2 days')),
            ];
        }

        // This is a cool short-hand method
        $this->insert('board', $data);
    }

    private function sanitizeString(string $input): string
    {
        return preg_replace(
            "/(failed|error)/i",
            'omg',
            $input
        );
    }
}
