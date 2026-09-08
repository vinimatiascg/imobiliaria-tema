<?php
/**
 * Elementor Widget: Mapa Global de Imóveis
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Imob_Elementor_Widget_imovel_map extends \Elementor\Widget_Base {

	public function get_name() {
		return 'imob_imovel_map';
	}

	public function get_title() {
		return __( 'Mapa de Imóveis', 'imobiliaria-tema' );
	}

	public function get_icon() {
		return 'eicon-google-maps';
	}

	public function get_categories() {
		return [ 'imobiliaria-tema' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Configurações do Mapa', 'imobiliaria-tema' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'map_height',
			[
				'label' => __( 'Altura do Mapa', 'imobiliaria-tema' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'vh' ],
				'range' => [
					'px' => [
						'min' => 200,
						'max' => 1000,
						'step' => 10,
					],
					'vh' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 500,
				],
				'selectors' => [
					'{{WRAPPER}} .imob-global-map' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'map_zoom',
			[
				'label' => __( 'Zoom Inicial', 'imobiliaria-tema' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 20,
				'default' => 12,
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$gmaps_key = get_option( 'imob_gmaps_key' );

		if ( ! $gmaps_key ) {
			echo '<p>' . __( 'Configure a API Key do Google Maps nas Opções do Tema para renderizar este mapa.', 'imobiliaria-tema' ) . '</p>';
			return;
		}

		// Query properties with location
		$query = new \WP_Query( array(
			'post_type' => 'imovel',
			'posts_per_page' => -1,
			'post_status' => 'publish',
			'meta_query' => array(
				array(
					'key' => '_imob_mapa',
					'compare' => 'EXISTS'
				)
			)
		) );

		$locations = [];
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$mapa = get_post_meta( get_the_ID(), '_imob_mapa', true );
				if ( $mapa ) {
					list($lat, $lng) = explode(',', $mapa);
					$lat = trim($lat);
					$lng = trim(str_replace(',18', '', $lng)); // Clean RH zooms
					$lng = trim(str_replace(',16', '', $lng));
					$lng = trim(str_replace(',14', '', $lng));
					
					$preco_fmt = imob_get_formatted_price( get_the_ID(), false );

					$locations[] = [
						'title' => get_the_title(),
						'lat' => floatval($lat),
						'lng' => floatval($lng),
						'url' => get_permalink(),
						'price' => $preco_fmt,
						'image' => get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' )
					];
				}
			}
			wp_reset_postdata();
		}

		$map_id = 'imob-global-map-' . uniqid();
		$zoom = intval( $settings['map_zoom'] );
		?>
		
		<div id="<?php echo esc_attr($map_id); ?>" class="imob-global-map" style="width: 100%; border-radius: 8px; overflow: hidden;"></div>

		<script>
		document.addEventListener("DOMContentLoaded", function() {
			var imobLocations = <?php echo wp_json_encode($locations); ?>;
			var mapElement = document.getElementById('<?php echo esc_js($map_id); ?>');
			
			if (typeof google === 'undefined') {
				// Script might be loaded asynchronously, wait for it
				window.initGlobalMap = function() {
					renderMap();
				};
				var script = document.createElement('script');
				script.src = "https://maps.googleapis.com/maps/api/js?key=<?php echo esc_js($gmaps_key); ?>&callback=initGlobalMap";
				document.head.appendChild(script);
			} else {
				renderMap();
			}

			function renderMap() {
				var bounds = new google.maps.LatLngBounds();
				var mapOptions = {
					zoom: <?php echo esc_js($zoom); ?>,
					mapTypeId: google.maps.MapTypeId.SATELLITE
				};
				
				var map = new google.maps.Map(mapElement, mapOptions);
				var infoWindow = new google.maps.InfoWindow();

				if (imobLocations.length === 0) {
					// Default to Brazil if no properties
					map.setCenter({lat: -14.235, lng: -51.925});
					map.setZoom(4);
					return;
				}

				for (var i = 0; i < imobLocations.length; i++) {
					var loc = imobLocations[i];
					var position = new google.maps.LatLng(loc.lat, loc.lng);
					bounds.extend(position);

					var marker = new google.maps.Marker({
						position: position,
						map: map,
						title: loc.title
					});

					// Create closure for the event listener
					(function(marker, loc) {
						google.maps.event.addListener(marker, 'click', function() {
							var content = '<div style="max-width: 200px;">';
							if ( loc.image ) {
								content += '<img src="' + loc.image + '" style="width: 100%; height: auto; border-radius: 4px; margin-bottom: 10px;">';
							}
							content += '<h4 style="margin: 0 0 5px; font-size: 14px;">' + loc.title + '</h4>';
							if ( loc.price ) {
								content += '<div style="margin: 0 0 10px; background: #0E1A2B; color: #fff; padding: 5px; border-radius: 4px;">' + loc.price + '</div>';
							}
							content += '<a href="' + loc.url + '" style="display: inline-block; background: #0073aa; color: #fff; padding: 5px 10px; text-decoration: none; border-radius: 3px; font-size: 12px;">Ver Imóvel</a>';
							content += '</div>';
							
							infoWindow.setContent(content);
							infoWindow.open(map, marker);
						});
					})(marker, loc);
				}

				// Only fit bounds if there's more than one property to avoid extreme zoom
				if (imobLocations.length > 1) {
					map.fitBounds(bounds);
				} else {
					map.setCenter(bounds.getCenter());
				}
			}
		});
		</script>
		<?php
	}
}
