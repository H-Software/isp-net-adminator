<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property App\\\\Auth\\\\passwordHelper\\:\\:\\$container\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Auth/Password.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property auth_service\\:\\:\\$container\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Auth/serviceHelper.php',
];
$ignoreErrors[] = [
	'message' => '#^Function ereg not found\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Controllers/archivZmenController.php',
];
$ignoreErrors[] = [
	'message' => '#^Undefined variable\\: \\$sid$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Controllers/othersController.php',
];
$ignoreErrors[] = [
	'message' => '#^Function fix_link_to_another_adminator not found\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/app/Controllers/platbyController.php',
];
$ignoreErrors[] = [
	'message' => '#^Undefined variable\\: \\$body$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Controllers/platbyController.php',
];
$ignoreErrors[] = [
	'message' => '#^Function init_helper_base_html not found\\.$#',
	'count' => 4,
	'path' => __DIR__ . '/app/Core/Adminator/Adminator.php',
];
$ignoreErrors[] = [
	'message' => '#^Function fix_link_to_another_adminator not found\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/ArchivZmen.php',
];
$ignoreErrors[] = [
	'message' => '#^Undefined variable\\: \\$r$#',
	'count' => 2,
	'path' => __DIR__ . '/app/Core/ArchivZmen.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property zmeny_ucetni\\:\\:\\$logger\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/app/Core/ArchivZmenUcetni.php',
];
$ignoreErrors[] = [
	'message' => '#^Function init_helper_base_html not found\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/ArchivZmenUcetni.php',
];
$ignoreErrors[] = [
	'message' => '#^Undefined variable\\: \\$sql$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/ArchivZmenUcetni.php',
];
$ignoreErrors[] = [
	'message' => '#^Undefined variable\\: \\$error$#',
	'count' => 2,
	'path' => __DIR__ . '/app/Core/Customer/fakturacniSkupiny.class.php',
];
$ignoreErrors[] = [
	'message' => '#^Undefined variable\\: \\$fu_sql_select$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/Customer/fakturacniSkupiny.class.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property vlastnik2\\:\\:\\$listFindId\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/app/Core/Customer/vlastnik2.class.php',
];
$ignoreErrors[] = [
	'message' => '#^Undefined variable\\: \\$find$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/Customer/vlastnik2.class.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property App\\\\Core\\\\objekt\\:\\:\\$container\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/app/Core/Item/objekt.class.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property App\\\\Core\\\\objekt\\:\\:\\$validator\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/Item/objekt.class.php',
];
$ignoreErrors[] = [
	'message' => '#^Regex pattern is invalid\\: Unknown modifier \'\\)\' in pattern\\: /\\^\\(\\[\\[\\:digit\\:\\]\\]\\|\\\\\\.\\|/\\)\\+\\$/$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/Item/objekt.class.php',
];
$ignoreErrors[] = [
	'message' => '#^Undefined variable\\: \\$_get$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/Item/objekt.class.php',
];
$ignoreErrors[] = [
	'message' => '#^Undefined variable\\: \\$error$#',
	'count' => 4,
	'path' => __DIR__ . '/app/Core/Item/objekt.class.php',
];
$ignoreErrors[] = [
	'message' => '#^Undefined variable\\: \\$find_tarif$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/Item/objekt.class.php',
];
$ignoreErrors[] = [
	'message' => '#^Undefined variable\\: \\$garant$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/Item/objekt.class.php',
];
$ignoreErrors[] = [
	'message' => '#^Undefined variable\\: \\$id$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/Item/objekt.class.php',
];
$ignoreErrors[] = [
	'message' => '#^Undefined variable\\: \\$info$#',
	'count' => 4,
	'path' => __DIR__ . '/app/Core/Item/objekt.class.php',
];
$ignoreErrors[] = [
	'message' => '#^Undefined variable\\: \\$ip_rozsah$#',
	'count' => 2,
	'path' => __DIR__ . '/app/Core/Item/objekt.class.php',
];
$ignoreErrors[] = [
	'message' => '#^Undefined variable\\: \\$najdi$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/Item/objekt.class.php',
];
$ignoreErrors[] = [
	'message' => '#^Undefined variable\\: \\$output$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/Item/objekt.class.php',
];
$ignoreErrors[] = [
	'message' => '#^Undefined variable\\: \\$pole2$#',
	'count' => 4,
	'path' => __DIR__ . '/app/Core/Item/objekt.class.php',
];
$ignoreErrors[] = [
	'message' => '#^Undefined variable\\: \\$pole3$#',
	'count' => 2,
	'path' => __DIR__ . '/app/Core/Item/objekt.class.php',
];
$ignoreErrors[] = [
	'message' => '#^Undefined variable\\: \\$sql$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/Item/objekt.class.php',
];
$ignoreErrors[] = [
	'message' => '#^Undefined variable\\: \\$tarif$#',
	'count' => 2,
	'path' => __DIR__ . '/app/Core/Item/objekt.class.php',
];
$ignoreErrors[] = [
	'message' => '#^Undefined variable\\: \\$tunnel_pass$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/Item/objekt.class.php',
];
$ignoreErrors[] = [
	'message' => '#^Undefined variable\\: \\$tunnel_user$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/Item/objekt.class.php',
];
$ignoreErrors[] = [
	'message' => '#^Function mysql_fetch_array not found\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/Item/objektypridani.class.php',
];
$ignoreErrors[] = [
	'message' => '#^Function mysql_num_rows not found\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/Item/objektypridani.class.php',
];
$ignoreErrors[] = [
	'message' => '#^Function mysql_query not found\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/Item/objektypridani.class.php',
];
$ignoreErrors[] = [
	'message' => '#^Function split not found\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/app/Core/Item/objektypridani.class.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property App\\\\Core\\\\stb\\:\\:\\$validator\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/app/Core/Item/stb.class.php',
];
$ignoreErrors[] = [
	'message' => '#^Function fix_link_to_another_adminator not found\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/app/Core/Item/stb.class.php',
];
$ignoreErrors[] = [
	'message' => '#^Undefined variable\\: \\$output$#',
	'count' => 4,
	'path' => __DIR__ . '/app/Core/Item/stb.class.php',
];
$ignoreErrors[] = [
	'message' => '#^Undefined variable\\: \\$rs$#',
	'count' => 2,
	'path' => __DIR__ . '/app/Core/Item/stb.class.php',
];
$ignoreErrors[] = [
	'message' => '#^Deprecated in PHP 8\\.0\\: Required parameter \\$conAfter follows optional parameter \\$conList\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/PagingGlobal.php',
];
$ignoreErrors[] = [
	'message' => '#^Deprecated in PHP 8\\.0\\: Required parameter \\$conBefore follows optional parameter \\$conList\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/PagingGlobal.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property App\\\\Partner\\\\partner\\:\\:\\$container\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/Partner/partner.class.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property App\\\\Partner\\\\partner\\:\\:\\$validator\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/Partner/partner.class.php',
];
$ignoreErrors[] = [
	'message' => '#^Function hierarchy_vypis_router not found\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/Topology.php',
];
$ignoreErrors[] = [
	'message' => '#^Undefined variable\\: \\$colspan_stav$#',
	'count' => 4,
	'path' => __DIR__ . '/app/Core/Topology.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property admin\\:\\:\\$logger\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/app/Core/admin.php',
];
$ignoreErrors[] = [
	'message' => '#^Undefined variable\\: \\$error$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/admin.php',
];
$ignoreErrors[] = [
	'message' => '#^Undefined variable\\: \\$output$#',
	'count' => 4,
	'path' => __DIR__ . '/app/Core/admin.php',
];
$ignoreErrors[] = [
	'message' => '#^Function init_helper_base_html not found\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/board.php',
];
$ignoreErrors[] = [
	'message' => '#^Regex pattern is invalid\\: Unknown modifier \'&\' in pattern\\: /&lt;/ii&gt;$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/board.php',
];
$ignoreErrors[] = [
	'message' => '#^Regex pattern is invalid\\: Unknown modifier \'&\' in pattern\\: /&lt;/iu&gt;$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/board.php',
];
$ignoreErrors[] = [
	'message' => '#^Regex pattern is invalid\\: Unknown modifier \'&\' in pattern\\: /&lt;\\\\//ii&gt;$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/board.php',
];
$ignoreErrors[] = [
	'message' => '#^Regex pattern is invalid\\: Unknown modifier \'&\' in pattern\\: /&lt;\\\\//iu&gt;$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/board.php',
];
$ignoreErrors[] = [
	'message' => '#^Regex pattern is invalid\\: Unknown modifier \'/\' in pattern\\: /\\(http\\://\\[\\^ \\]\\+\\\\\\.\\[\\^ \\]\\+\\)/i$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/board.php',
];
$ignoreErrors[] = [
	'message' => '#^Regex pattern is invalid\\: Unknown modifier \'\\]\' in pattern\\: /\\[\\^/\\]\\(www\\\\\\.\\[\\^ \\]\\+\\\\\\.\\[\\^ \\]\\+\\)/i$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/board.php',
];
$ignoreErrors[] = [
	'message' => '#^Regex pattern is invalid\\: Unknown modifier \'b\' in pattern\\: /&lt;/ib&gt;$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/board.php',
];
$ignoreErrors[] = [
	'message' => '#^Regex pattern is invalid\\: Unknown modifier \'b\' in pattern\\: /&lt;\\\\//ib&gt;$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/board.php',
];
$ignoreErrors[] = [
	'message' => '#^Function fix_link_to_another_adminator not found\\.$#',
	'count' => 10,
	'path' => __DIR__ . '/app/Core/opravy.php',
];
$ignoreErrors[] = [
	'message' => '#^Function init_helper_base_html not found\\.$#',
	'count' => 4,
	'path' => __DIR__ . '/app/Core/opravy.php',
];
$ignoreErrors[] = [
	'message' => '#^Undefined variable\\: \\$class$#',
	'count' => 10,
	'path' => __DIR__ . '/app/Core/opravy.php',
];
$ignoreErrors[] = [
	'message' => '#^Function ereg not found\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/platby.php',
];
$ignoreErrors[] = [
	'message' => '#^Function ereg_replace not found\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/platby.php',
];
$ignoreErrors[] = [
	'message' => '#^Function init_helper_base_html not found\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/platby.php',
];
$ignoreErrors[] = [
	'message' => '#^Function split not found\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/app/Core/platby.php',
];
$ignoreErrors[] = [
	'message' => '#^Undefined variable\\: \\$poznamka2$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/print_reg_form.php',
];
$ignoreErrors[] = [
	'message' => '#^Function ereg not found\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/rss.php',
];
$ignoreErrors[] = [
	'message' => '#^Function ereg not found\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/shared/adminatorGlobal.class.php',
];
$ignoreErrors[] = [
	'message' => '#^Function mysql_num_rows not found\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/shared/adminatorGlobal.class.php',
];
$ignoreErrors[] = [
	'message' => '#^Function mysql_query not found\\.$#',
	'count' => 6,
	'path' => __DIR__ . '/app/Core/shared/adminatorGlobal.class.php',
];
$ignoreErrors[] = [
	'message' => '#^Function mysql_result not found\\.$#',
	'count' => 5,
	'path' => __DIR__ . '/app/Core/shared/adminatorGlobal.class.php',
];
$ignoreErrors[] = [
	'message' => '#^Instantiated class RouterOS not found\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/shared/adminatorGlobal.class.php',
];
$ignoreErrors[] = [
	'message' => '#^Undefined variable\\: \\$output$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/shared/adminatorGlobal.class.php',
];
$ignoreErrors[] = [
	'message' => '#^Undefined variable\\: \\$output$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/shared/objekt_a2.class.php',
];
$ignoreErrors[] = [
	'message' => '#^Undefined variable\\: \\$tarif_sql$#',
	'count' => 4,
	'path' => __DIR__ . '/app/Core/shared/objekt_a2.class.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property partner_servis\\:\\:\\$mod\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/shared/partner.servis.class.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property partner_servis\\:\\:\\$user\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/shared/partner.servis.class.php',
];
$ignoreErrors[] = [
	'message' => '#^Undefined variable\\: \\$mod$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/shared/partner.servis.class.php',
];
$ignoreErrors[] = [
	'message' => '#^Undefined variable\\: \\$user$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/shared/partner.servis.class.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property mk_synchro_qos\\:\\:\\$arr_global_diff_mis\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/app/Core/shared/ros_api_qos.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property mk_synchro_qos\\:\\:\\$chain\\.$#',
	'count' => 4,
	'path' => __DIR__ . '/app/Core/shared/ros_api_qos.php',
];
$ignoreErrors[] = [
	'message' => '#^Function ereg not found\\.$#',
	'count' => 15,
	'path' => __DIR__ . '/app/Core/shared/ros_api_qos.php',
];
$ignoreErrors[] = [
	'message' => '#^Function mysql_fetch_array not found\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/shared/ros_api_qos.php',
];
$ignoreErrors[] = [
	'message' => '#^Function mysql_query not found\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/shared/ros_api_qos.php',
];
$ignoreErrors[] = [
	'message' => '#^Undefined variable\\: \\$add_qt_q$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/shared/ros_api_qos.php',
];
$ignoreErrors[] = [
	'message' => '#^Undefined variable\\: \\$add_qt_q2$#',
	'count' => 2,
	'path' => __DIR__ . '/app/Core/shared/ros_api_qos.php',
];
$ignoreErrors[] = [
	'message' => '#^Undefined variable\\: \\$add_qt_q_dwn$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/shared/ros_api_qos.php',
];
$ignoreErrors[] = [
	'message' => '#^Undefined variable\\: \\$erase$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/shared/ros_api_qos.php',
];
$ignoreErrors[] = [
	'message' => '#^Undefined variable\\: \\$sql_obj_where$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/shared/ros_api_qos.php',
];
$ignoreErrors[] = [
	'message' => '#^Undefined variable\\: \\$sql_where$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/shared/ros_api_qos.php',
];
$ignoreErrors[] = [
	'message' => '#^Function ereg not found\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/shared/ros_api_restriction.php',
];
$ignoreErrors[] = [
	'message' => '#^Undefined variable\\: \\$sql_obj_where$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/shared/ros_api_restriction.php',
];
$ignoreErrors[] = [
	'message' => '#^Undefined variable\\: \\$sql_where$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/shared/ros_api_restriction.php',
];
$ignoreErrors[] = [
	'message' => '#^Static call to instance method fakturacni\\:\\:vypis\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/shared/vlastnik.class.php',
];
$ignoreErrors[] = [
	'message' => '#^Undefined variable\\: \\$db_ok2$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/shared/vlastnik.class.php',
];
$ignoreErrors[] = [
	'message' => '#^Function mysql_query not found\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/shared/vlastnik2_a2.class.php',
];
$ignoreErrors[] = [
	'message' => '#^Function mysql_result not found\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/shared/vlastnik2_a2.class.php',
];
$ignoreErrors[] = [
	'message' => '#^Undefined variable\\: \\$db_ok2$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/shared/vlastnikarchiv.class.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property voip\\:\\:\\$dotaz_rs\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/app/Core/shared/voip.class.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property voip\\:\\:\\$dotaz_rs_radku\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/shared/voip.class.php',
];
$ignoreErrors[] = [
	'message' => '#^Function mysql_fetch_array not found\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/shared/voip.class.php',
];
$ignoreErrors[] = [
	'message' => '#^Function mysql_num_rows not found\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/shared/voip.class.php',
];
$ignoreErrors[] = [
	'message' => '#^Function mysql_query not found\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Core/shared/voip.class.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Core\\\\adminator referenced with incorrect case\\: App\\\\Core\\\\Adminator\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/app/Models/PartnerOrder.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Models\\\\Email not found\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to static method createHelperSet\\(\\) on an unknown class Doctrine\\\\ORM\\\\Tools\\\\Console\\\\ConsoleRunner\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/cli-config.php',
];
$ignoreErrors[] = [
	'message' => '#^Class aboutController not found\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/app/routing.php',
];
$ignoreErrors[] = [
	'message' => '#^Class adminController not found\\.$#',
	'count' => 7,
	'path' => __DIR__ . '/app/routing.php',
];
$ignoreErrors[] = [
	'message' => '#^Class archivZmenController not found\\.$#',
	'count' => 4,
	'path' => __DIR__ . '/app/routing.php',
];
$ignoreErrors[] = [
	'message' => '#^Class objektyController not found\\.$#',
	'count' => 5,
	'path' => __DIR__ . '/app/routing.php',
];
$ignoreErrors[] = [
	'message' => '#^Class othersController not found\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/app/routing.php',
];
$ignoreErrors[] = [
	'message' => '#^Class platbyController not found\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/app/routing.php',
];
$ignoreErrors[] = [
	'message' => '#^Class topologyController not found\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/app/routing.php',
];
$ignoreErrors[] = [
	'message' => '#^Class vlastniciController not found\\.$#',
	'count' => 4,
	'path' => __DIR__ . '/app/routing.php',
];
$ignoreErrors[] = [
	'message' => '#^Class workController not found\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/routing.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
