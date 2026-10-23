<?php
include 'config.php';
require_once 'functions.php';

date_default_timezone_set("$timezone");

// Load the service bodies before any output so a failed fetch shows a clear
// message instead of a blank 500.
$gsb_array = bmlt_get_json($bmlt_server . "/client_interface/json/?switcher=GetServiceBodies", true);
if ($gsb_array === null) {
    bmlt_fail($bmlt_server);
}

bmlt_sort_by_subkey($gsb_array, 'name');

// Decide which service bodies to list. When a parent is configured we show its
// children (optionally including the parent itself); otherwise we show them all.
// An empty config value counts as "not configured".
if (!empty($parent_service_body_id)) {
    $service_bodies = array_filter(
        $gsb_array,
        fn ($sb) => $sb['parent_id'] == $parent_service_body_id
    );
    if (!empty($include_parent_service_body_id)) {
        $key = array_search($parent_service_body_id, array_column($gsb_array, 'id'));
        if ($key !== false) {
            $service_bodies[] = $gsb_array[$key];
        }
        bmlt_sort_by_subkey($service_bodies, 'name');
    }
} else {
    $service_bodies = $gsb_array;
}
?>
<!DOCTYPE HTML>
<html>
<head>
    <title><?php echo bmlt_e($service_body_shortname); ?> Meeting List Docs</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <link rel="stylesheet" href="assets/css/main.css" />
    <!--[if lte IE 9]><link rel="stylesheet" href="assets/css/ie9.css" /><![endif]-->
    <noscript><link rel="stylesheet" href="assets/css/noscript.css" /></noscript>
</head>
<body>

<!-- Wrapper -->
<div id="wrapper">

    <!-- Header -->
    <header id="header">
        <div class="nalogo">
            <!--<span class="icon fa-file-text"></span>-->
            <img  src="images/NA_logo-white.png" alt="NA" height="100" width="100"/>
        </div>
        <div class="content">
            <div class="inner">
                <h1><?php echo bmlt_e($service_body_shortname); ?></h1>
                <p>Meeting List resources for<br />
                    the <?php echo bmlt_e($service_body_name); ?> of Narcotics Anonymous
                    <span class="rwd-break">
                                    <i class="fa fa-hand-o-right" aria-hidden="true"></i>
                                    <a href="<?php echo bmlt_e($service_body_website); ?>" target="_blank"><?php echo bmlt_e($service_body_website); ?></a>
                                </span>
                </p>
            </div>
        </div>
        <nav>
            <ul>
                <li><a href="#changes">Changes</a></li>
                <li><a href="#proofs">Proofs</a></li>
                <?php
                if (isset($GLOBALS['bmlt_instructional_video'])) {
                    ?>
                    <li><a href="<?php echo bmlt_e($bmlt_instructional_video); ?>" target="_new">INSTRUCTIONAL VIDEO</a></li>
                    <?php
                }
                if (isset($GLOBALS['bmlt_instructional_manual'])) {
                    ?>
                    <li><a href="<?php echo bmlt_e($bmlt_instructional_manual); ?>" target="_new">MANUAL</a></li>
                <?php } ?>
                <li><a href="<?php echo bmlt_e($bmlt_server); ?>" target="_blank">BMLT</a></li>
            </ul>
        </nav>
    </header>

    <!-- Main -->
    <div id="main">
        <!-- Intro -->
        <article id="changes">
            <h2 class="major">Changes</h2>
            <h3 class="major"><?php echo bmlt_e($service_body_shortname); ?></h3>
            <div class="table-wrapper">
                <table>
                    <thead>
                    <tr>
                        <th>Region/Area</th>
                        <th>Changes for previous...</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php echo bmlt_change_links_rows($service_bodies); ?>
                    </tbody>
                </table>
            </div>


        </article>

        <!-- PROOF REPORTS -->
        <article id="proofs">
            <h2 class="major">Proof Reports</h2>

            <h3 class="major"><?php echo bmlt_e($service_body_shortname); ?></h3>
            <div class="table-wrapper">
                <table>
                    <thead>
                    <tr>
                        <th>Area</th>
                        <th>Proof Report</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($service_bodies as $row_proofs) {
                        $recurse = in_array($row_proofs['type'], ['RS', 'ZF'], true) ? "&recurse=true" : "";
                        $query = "asc=" . urlencode($row_proofs['name']) . "&areanum=" . urlencode((string) $row_proofs['id']);
                        echo "<tr><td>" . bmlt_e($row_proofs['name']) . "</td><td>"
                            . "<a href=\"proofs.php?" . $query . $recurse
                            . "\" class=\"button special\" target=\"_blank\">Proof Report</a>"
                            . "</td></tr>";
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </article>
    </div>

    <!-- Footer -->
    <footer id="footer">
        <p class="copyright">&copy; <?php echo date("Y"); ?> - Meeting List Resources - <?php echo bmlt_e($service_body_shortname); ?></p>
    </footer>

</div>

<!-- BG -->
<div id="bg"></div>

<!-- Scripts -->
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/skel.min.js"></script>
<script src="assets/js/util.js"></script>
<script src="assets/js/main.js"></script>

</body>
</html>
