<?php


use Phinx\Seed\AbstractSeed;

class TopologyNodeData extends AbstractSeed
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

        for ($i = 0; $i < 6; $i++) {
            $data[] = [
                'jmeno'             => $this->sanitizeString(
                                        $faker->slug(3, false)
                                    ),
                'adresa'            => $this->sanitizeString(
                                        $faker->words(3, true)
                                    ),
                'pozn'              => $this->sanitizeString(
                                        $faker->text(30)
                                    ),
                'ip_rozsah'         => $faker->localIpv4(), // https://fakerphp.org/formatters/internet/#localipv4
                'router_id'         => $faker->numberBetween(1, 5),
            ];
        }

        $this->insert('nod_list', $data);
    }

    private function sanitizeString(string $input): string
    {
        return preg_replace(
            "/(failed|chyba|error)/i",
            'omg',
            $input
        );
    }
}
