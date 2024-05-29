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

        // generate "fresh" posts
        // for ($i = 0; $i < 6; $i++) {
        //     $data[] = [
        //         'nazev'        => $this->sanitizeString(
        //                                 $faker->slug(3, false)
        //                             ),
        //         'ip_adresa'         => $faker->localIpv4(), // https://fakerphp.org/formatters/internet/#localipv4
        //         'parent_router'     => 1,
        //         'mac'               => $faker->macAddress(),
        //         'poznamka'          => $this->sanitizeString(
        //                                 $faker->text(30)
        //                             ),
        //     ];
        // }

        // // This is a cool short-hand method
        // $this->insert('router_list', $data);
    }

    private function sanitizeString(string $input): string
    {
        return preg_replace(
            "/(failed|chyba|error)/",
            '',
            $input
        );
    }
}
