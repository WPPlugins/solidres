<?php
/*------------------------------------------------------------------------
  Solidres - Hotel booking plugin for WordPress
  ------------------------------------------------------------------------
  @Author    Solidres Team
  @Website   http://www.solidres.com
  @Copyright Copyright (C) 2013 - 2016 Solidres. All Rights Reserved.
  @License   GNU General Public License version 3, or later
------------------------------------------------------------------------*/

if ( ! defined( 'ABSPATH' ) ) { exit; }

?>
<div style="display:none">
	<div id="inline_map"></div>
</div>

<script>
	var geocoder, map;
	function initialize() {
		var latlng = new google.maps.LatLng("<?php echo $asset->lat ?>", "<?php echo $asset->lng ?>");
		var options = {
			zoom: 15,
			center: latlng,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		}
		map = new google.maps.Map(document.getElementById("inline_map"), options);

		var image = new google.maps.MarkerImage( "<?php echo plugins_url( 'solidres/assets/images/icon-hotel-'. $asset->rating .'.png' ) ?>",
			new google.maps.Size(32, 37),
			new google.maps.Point(0, 0),
			new google.maps.Point(0, 32));

		var marker = new google.maps.Marker({
			map: map,
			position: latlng,
			icon: image
		});

		var windowContent = "<h4><?php echo apply_filters( 'solidres_asset_name', $asset->name ) ?></h4>" +
			<?php echo json_encode( apply_filters( 'solidres_asset_desc', $asset->description ) ) ?> +
				"<ul>" +
			"<li><?php echo $asset->address_1 . "  " . $asset->city ?></li>" +
			"<li><?php echo $asset->phone ?></li>" +
			"<li><?php echo $asset->email ?></li>" +
			"<li><?php echo $asset->website ?></li>" +
			"</ul>";

		var infowindow = new google.maps.InfoWindow({
			content: windowContent,
			maxWidth: 350
		});

		google.maps.event.addListener(marker, "click", function () {
			infowindow.open(map, marker);
		});
	}
	jQuery(function($) {
		$(".show_map").colorbox({iframe: false, inline: true, width:"95%", height:"90%", html: $('#inline_map').html(), onComplete: initialize});
	});

</script>