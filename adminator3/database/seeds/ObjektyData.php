<?php


use Phinx\Seed\AbstractSeed;

class ObjektyData extends AbstractSeed
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

        // basic, without vlastnik
        for ($i = 0; $i < 2; $i++) {
            $data[] = [
                'dns_jmeno'         => $this->sanitizeString(
                                        $faker->domainName()
                                    ),
                'ip'                => $faker->localIpv4(),
                'mac'               => $faker->macAddress(),
                'typ'               => 1,
                'sikana_cas'        => 0,
            ];
        }

        // basic, with vlastnik
        for ($i = 0; $i < 2; $i++) {
            $data[] = [
                'dns_jmeno'         => $this->sanitizeString(
                                        $faker->domainName()
                                    ),
                'ip'                => $faker->localIpv4(),
                'mac'               => $faker->macAddress(),
                'typ'               => 1,
                'sikana_cas'        => 0,
                'id_cloveka'        => ($i + 1),
            ];
        }

        // TODO: add APcko

        // TODO: add other types (verejna_ip, ..)

        $this->insert('objekty', $data);
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
