<?php

use Phinx\Seed\AbstractSeed;

class pageLevelData extends AbstractSeed
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

        $sql = <<<END
    INSERT INTO `leveling` (`id`, `level`, `popis`) VALUES
    (1,	6,	'a2: objekty'),
    (2,	10,	'a2: objekty-add'),
    (4,	10,	'topology-nod-list'),
    (5,	10,	'a2: topology-nod-list'),
    (6,	10,	'topolog-user-list'),
    (13,	10,	'vlastnici / vlastnici-gen-xml'),
    (14,	20,	'a2: platby'),
    (15,	20,	'maily'),
    (16,	10,	'a2: work'),
    (17,	10,	'a2: admin'),
    (20,	88,	'a2: admin-level-add'),
    (21,	82,	'a2: admin-level-list'),
    (22,	0,	'admin - change password (deleted)'),
    (23,	10,	'admin level action'),
    (25,	50,	'topology-nod-update'),
    (27,	30,	'objekty-vypis-ip'),
    (28,	30,	'soubory'),
    (29,	70,	'objekty update'),
    (30,	20,	'archiv-zmen, vlastnici update'),
    (31,	10,	'a2: automatika'),
    (32,	40,	'a2: automatika-sikana-odpocet'),
    (33,	70,	'objekty erase'),
    (34,	80,	'objekty garant akce'),
    (36,	40,	'a2: automatika-sikana-zakazani'),
    (37,	50,	'platby-akce'),
    (38,	10,	'a3: home.php, vlastnici2'),
    (40,	30,	'vlastnici2: pridani vlastnika'),
    (41,	50,	'platby-soucet'),
    (43,	40,	'stats-objekty'),
    (44,	48,	'platby hot vypis'),
    (45,	80,	'vlastnici erase'),
    (48,	40,	'vlastnici2-add-obj'),
    (49,	50,	'objekty - odendani od vlastnika'),
    (50,	50,	'platby-vypis'),
    (51,	30,	'vlastnici-add-fakt'),
    (53,	53,	'monitoring-control'),
    (55,	50,	'monitoring grafy'),
    (59,	70,	'objekty list - export'),
    (63,	40,	'vlastnici (2) export'),
    (64,	44,	'vlastnici export'),
    (67,	60,	'vlastnici erase fakturacni'),
    (68,	50,	'vlastnici2-change-fakt'),
    (75,	10,	'a2: partner-cat'),
    (76,	20,	'partner-vypis'),
    (77,	20,	'partner-vyrizeni'),
    (78,	10,	'a2: vypovedi'),
    (79,	30,	'a2: vypovedi vlozeni'),
    (80,	20,	'vypovedi plaintisk'),
    (81,	80,	'platby-hot-stats'),
    (82,	10,	'a3: vlastnici-archiv'),
    (84,	20,	'a2: opravy'),
    (85,	30,	'topology-router-list'),
    (86,	30,	'topology-router-add'),
    (87,	100,	'a2: board-header, others-board'),
    (90,	10,	'vlastnici-cat'),
    (91,	10,	'a2: admin-subcat'),
    (92,	210,	'a3: platby-cat'),
    (93,	20,	'a2: objekty-subcat'),
    (94,	99,	'objekty-lite'),
    (95,	10,	'a3: others-cat'),
    (96,	10,	'a2: about-map.php'),
    (99,	10,	'a2: vlastnici2-fakt-skupiny'),
    (101,	10,	'opravy a zavady vypis (homepage)'),
    (102,	10,	'a2: vlastnici hledani'),
    (103,	49,	'stats.php'),
    (104,	40,	'stats-vlastnici'),
    (105,	20,	'opravy-vlastnik'),
    (106,	20,	'opravy-zacit-resit'),
    (107,	10,	'fn.php'),
    (108,	33,	'faktury: fn-index'),
    (110,	20,	'faktury: fn-aut-sms'),
    (111,	10,	'partner-pripojeni'),
    (112,	40,	'admin-partner'),
    (115,	50,	'a2: automatika-fn-check-vlastnik'),
    (116,	40,	'topology-nod-erase'),
    (117,	20,	'voip index'),
    (118,	40,	'voip hovory'),
    (119,	20,	'partner-pozn-update'),
    (120,	50,	'voip cisla'),
    (125,	60,	'voip online-dial-cust-list'),
    (128,	60,	'topology-router-erase'),
    (131,	40,	'admin tarify list'),
    (132,	20,	'topology-router-mail'),
    (135,	20,	'a2: objekty-stb'),
    (136,	10,	'objekty-stb-add'),
    (137,	20,	'stb uprava'),
    (138,	44,	'vlastnici2-add-obj'),
    (139,	10,	'objekty test'),
    (140,	30,	'vlastnici2-fs-update'),
    (141,	20,	'vlastnici2-fs-erase'),
    (142,	2,	'about.php'),
    (143,	10,	'a2: archiv-zmen-cat.php'),
    (144,	10,	'a3: about-changes-old.php'),
    (145,	10,	'a3: about-changes.php'),
    (146,	10,	'a3: other-print'),
    (147,	10,	'a3: archiv-zmen-ucetni.php'),
    (148,	10,	'a3: archiv-zmen-ucetni.php : add'),
    (149,	10,	'a3: fn-kontrola-omezeni.php'),
    (150,	40,	'objekty stb unpair'),
    (151,	10,	'a3: others-web-simelon'),
    (301,	30,	'fakturacni-skupiny add'),
    (302,	44,	'vlastnici-cross'),
    (303,	49,	'admin-tarify action'),
    (304,	40,	'partner servis add'),
    (305,	50,	'partner servis list'),
    (306,	40,	'partner servis accept'),
    (307,	20,	'partner servis update pozn'),
    (308,	45,	'print redirect'),
    (309,	10,	'board rss'),
    (310,	99,	'objekty stb erase'),
    (311,	80,	'pohoda_sql - phd_list_fa');
END;
        $this->execute($sql);
    }


    public function down()
    {
        $this->execute('DELETE FROM leveling');
    }
}