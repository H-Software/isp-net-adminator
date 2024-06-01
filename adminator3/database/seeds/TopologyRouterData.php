<?php


use Phinx\Seed\AbstractSeed;

class TopologyRouterData extends AbstractSeed
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
            // if($i == 0) {
            //     // first router has to have parent_router (id) = 0
            //     $parent_router = 0;
            // } else {
            //     $parent_router = $faker->numberBetween(1, 3);
            // }

            $data[] = [
                'nazev'        => $this->sanitizeString(
                                        $faker->slug(3, false)
                                    ),
                'ip_adresa'         => $faker->localIpv4(), // https://fakerphp.org/formatters/internet/#localipv4
                'parent_router'     => $i, // $parent_router,
                'mac'               => $faker->macAddress(),
                'poznamka'          => $this->sanitizeString(
                                        $faker->text(30)
                                    ),
            ];
        }

        $this->insert('router_list', $data);
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
