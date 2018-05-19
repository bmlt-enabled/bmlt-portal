<?php 
include 'config.php';

date_default_timezone_set("$timezone");
?>
<!DOCTYPE HTML>
<html>
	<head>
		<title><?php echo $service_body_shortname; ?> Meeting List Docs</title>
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
								<h1><?php echo $service_body_shortname; ?></h1>
								<p>Meeting List resources for<br />
								the <?php echo $service_body_name; ?> of Narcotics Anonymous
								<span class="rwd-break">
									<i class="fa fa-hand-o-right" aria-hidden="true"></i>
									<a href="<?php echo $service_body_website; ?>" target="_blank"><?php echo $service_body_website; ?></a>
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
        <li><a href="<?php echo $bmlt_instructional_video; ?>" target="_new">INSTRUCTIONAL VIDEO</a></li>
        <?php 
          } 
          if (isset($GLOBALS['bmlt_instructional_manual'])) {
        ?>
        <li><a href="<?php echo $bmlt_instructional_manual; ?>" target="_new">MANUAL</a></li>
        <?php } ?>
								<li><a href="<?php echo $bmlt_server; ?>" target="_blank">BMLT</a></li>
							</ul>
						</nav>
					</header>

				<!-- Main -->
					<div id="main">
						<!-- Intro -->
							<article id="changes">
								<h2 class="major">Changes</h2>
									<h3 class="major"><?php echo $service_body_shortname; ?></h3>
									<div class="table-wrapper">
										<table>
											<thead>
												<tr>
													<th>Region/Area</th>
													<th>Changes for previous...</th>
												</tr>
											</thead>
											<tbody>
           <?php
           function sortBySubkey(&$array, $subkey, $sortType = SORT_ASC) {
             if ( empty( $array ) ) { return; }
		             foreach ($array as $subarray) {
		               $keys[] = $subarray[$subkey];
		             }
				           array_multisort($keys, $sortType, $array);
           }
           $get_service_bodies = file_get_contents($bmlt_server . "/client_interface/json/?switcher=GetServiceBodies");
           $gsb_array = json_decode($get_service_bodies, true);
           sortBySubkey($gsb_array, 'name');
           $changes = "";
           $changes_psb = "";
           if (isset($parent_service_body_id)) {
 
             $filter_parent_service_body = array_filter( $gsb_array, function ($var) use ($parent_service_body_id) {
               return ($var['parent_id'] == $parent_service_body_id);
             });
 
             // loop begins
             foreach($filter_parent_service_body as $row_changes_psb) {   
               $changes_psb .= "<tr>";
               $changes_psb .= "<td>" .$row_changes_psb['name']. "</td>";
               $changes_psb .= "<td>";
              	$changes_psb .= "<a href=\"changes.php?asc=" .$row_changes_psb['name']. "&areanum=" .$row_changes_psb['id']. "&hdays=30\" class=\"button special\" target=\"_blank\">30 Days</a>";
               $changes_psb .= " | ";
               $changes_psb .= "<a href=\"changes.php?asc=" .$row_changes_psb['name']. "&areanum=" .$row_changes_psb['id']. "&hdays=90\" class=\"button special\" target=\"_blank\">90 Days</a>";
               $changes_psb .= "</td>";
               $changes_psb .= "</tr>";
             }
             // loop ends
            echo $changes_psb;
           }
           

           else {
             // loop begins
             foreach($gsb_array as $row_changes) {   
               $changes .= "<tr>";
               $changes .= "<td>" .$row_changes['name']. "</td>";
               $changes .= "<td>";
              	$changes .= "<a href=\"changes.php?asc=" .$row_changes['name']. "&areanum=" .$row_changes['id']. "&hdays=30\" class=\"button special\" target=\"_blank\">30 Days</a>";
               $changes .= " | ";
               $changes .= "<a href=\"changes.php?asc=" .$row_changes['name']. "&areanum=" .$row_changes['id']. "&hdays=90\" class=\"button special\" target=\"_blank\">90 Days</a>";
               $changes .= "</td>";
               $changes .= "</tr>";
             }
             // loop ends
           }
           echo $changes;
           ?>
											</tbody>
										</table>
									</div>
									
									
							</article>

						<!-- PROOF REPORTS -->
							<article id="proofs">
								<h2 class="major">Proof Reports</h2>

									<h3 class="major"><?php echo $service_body_shortname; ?></h3>
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
            if (isset($parent_service_body_id)) {
            $prf_array = $filter_parent_service_body;
            }
            else {
             $prf_array = $gsb_array;
            }
             
           $proofs = "";  
											// loop begins
											foreach($prf_array as $row_proofs) {
											  if ( $row_proofs['type'] == 'RS' || $row_proofs['type'] == 'ZF' ) {
													  // Do Nothing
 											 }
											  else {
											    $proofs .= "<tr>";
 											   $proofs .= "<td>" .$row_proofs['name']. "</td>";
 											   $proofs .= "<td>";
 											   $proofs .= "<a href=\"proofs.php?asc=" .$row_proofs['name']. "&areanum=" .$row_proofs['id']. "\" class=\"button special\" target=\"_blank\">Proof Report</a>";
 											   $proofs .= "</td>";
  											  $proofs .= "</tr>";
											  }
											}
											// loop ends
											echo $proofs;
											?>
											</tbody>
										</table>
									</div>
							</article>
					</div>

				<!-- Footer -->
					<footer id="footer">
						<p class="copyright">&copy; <?php echo date("Y"); ?> - Meeting List Resources - <?php echo $service_body_shortname; ?></p>
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
