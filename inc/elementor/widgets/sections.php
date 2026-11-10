<?php
/**
 * Widgets do Elementor para as seções do layout Figma.
 *
 * Os controles saem do esquema em inc/sections/sections.php, e a saída usa os mesmos
 * templates de template-parts/sections/. Assim o visual no Elementor é idêntico ao do tema.
 *
 * @package ImobiliariaTema
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Repeater;

abstract class Imob_Section_Widget extends \Elementor\Widget_Base {

	/**
	 * Chave da seção no esquema.
	 *
	 * @var string
	 */
	protected $section = '';

	public function get_name() {
		return 'imob_sec_' . $this->section;
	}

	public function get_title() {
		$schema = imob_sections_schema();
		return $schema[ $this->section ]['title'];
	}

	public function get_icon() {
		$schema = imob_sections_schema();
		return $schema[ $this->section ]['icon'];
	}

	public function get_categories() {
		return array( 'imobiliaria-secoes' );
	}

	public function get_keywords() {
		return array( 'imobiliaria', 'seção', 'figma', $this->section );
	}

	public function get_style_depends() {
		return array( 'imob-home', 'imob-sections' );
	}

	public function get_script_depends() {
		return array( 'imob-home' );
	}

	/**
	 * Converte um campo do esquema em argumentos de controle do Elementor.
	 *
	 * @param array $field Campo [tipo, rótulo, padrão, extra].
	 * @return array
	 */
	protected function control_args( $field ) {
		list( $type, $label, $default ) = $field;
		$args = array( 'label' => $label );

		switch ( $type ) {
			case 'textarea':
				$args['type']    = Controls_Manager::TEXTAREA;
				$args['rows']    = 4;
				$args['default'] = $default;
				break;
			case 'html':
				$args['type']    = Controls_Manager::WYSIWYG;
				$args['default'] = $default;
				break;
			case 'image':
				$args['type']    = Controls_Manager::MEDIA;
				$id              = (int) $default;
				$args['default'] = array(
					'id'  => $id,
					'url' => $id ? (string) wp_get_attachment_image_url( $id, 'full' ) : '',
				);
				break;
			case 'url':
				$args['type']        = Controls_Manager::URL;
				$args['placeholder'] = 'https://';
				$args['default']     = array( 'url' => $default );
				break;
			case 'select':
				$args['type']    = Controls_Manager::SELECT;
				$args['options'] = $field[3];
				$args['default'] = $default;
				break;
			case 'switcher':
				$args['type']         = Controls_Manager::SWITCHER;
				$args['label_on']     = __( 'Sim', 'imobiliaria-tema' );
				$args['label_off']    = __( 'Não', 'imobiliaria-tema' );
				$args['return_value'] = 'yes';
				$args['default']      = $default;
				break;
			case 'number':
				$args['type']    = Controls_Manager::NUMBER;
				$args['min']     = 1;
				$args['default'] = (int) $default;
				break;
			default:
				$args['type']        = Controls_Manager::TEXT;
				$args['label_block'] = true;
				$args['default']     = $default;
		}

		if ( in_array( $type, array( 'text', 'textarea', 'html', 'url', 'image' ), true ) ) {
			$args['dynamic'] = array( 'active' => true );
		}
		return $args;
	}

	protected function register_controls() {
		$schema = imob_sections_schema();

		$this->start_controls_section(
			'content_section',
			array(
				'label' => __( 'Conteúdo', 'imobiliaria-tema' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		foreach ( $schema[ $this->section ]['fields'] as $key => $field ) {
			if ( 'repeater' !== $field[0] ) {
				$this->add_control( $key, $this->control_args( $field ) );
				continue;
			}

			$repeater    = new Repeater();
			$title_field = '';
			foreach ( $field[3] as $sub_key => $sub ) {
				$repeater->add_control( $sub_key, $this->control_args( $sub ) );
				if ( ! $title_field && in_array( $sub_key, array( 'title', 'value', 'text' ), true ) ) {
					$title_field = $sub_key;
				}
			}

			$rows = array();
			foreach ( $field[2] as $row ) {
				foreach ( $field[3] as $sub_key => $sub ) {
					if ( 'image' === $sub[0] ) {
						$id              = (int) ( $row[ $sub_key ] ?? 0 );
						$row[ $sub_key ] = array(
							'id'  => $id,
							'url' => $id ? (string) wp_get_attachment_image_url( $id, 'medium' ) : '',
						);
					}
				}
				$rows[] = $row;
			}

			$this->add_control(
				$key,
				array(
					'label'       => $field[1],
					'type'        => Controls_Manager::REPEATER,
					'fields'      => $repeater->get_controls(),
					'default'     => $rows,
					'title_field' => '{{{ ' . $title_field . ' }}}',
				)
			);
		}

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		imob_render_section( $this->section, imob_section_args_from_settings( $this->section, $settings ) );
	}
}

// phpcs:disable Generic.Files.OneObjectStructurePerFile, Squiz.Commenting
class Imob_Section_Widget_Hero extends Imob_Section_Widget { protected $section = 'hero'; }
class Imob_Section_Widget_Process extends Imob_Section_Widget { protected $section = 'process'; }
class Imob_Section_Widget_Properties extends Imob_Section_Widget { protected $section = 'properties'; }
class Imob_Section_Widget_Numbers extends Imob_Section_Widget { protected $section = 'numbers'; }
class Imob_Section_Widget_Lead extends Imob_Section_Widget { protected $section = 'lead'; }
class Imob_Section_Widget_Blog extends Imob_Section_Widget { protected $section = 'blog'; }
class Imob_Section_Widget_Testimonials extends Imob_Section_Widget { protected $section = 'testimonials'; }
class Imob_Section_Widget_Split extends Imob_Section_Widget { protected $section = 'split'; }
class Imob_Section_Widget_Pillars extends Imob_Section_Widget { protected $section = 'pillars'; }
class Imob_Section_Widget_Checklist extends Imob_Section_Widget { protected $section = 'checklist'; }
class Imob_Section_Widget_Features extends Imob_Section_Widget { protected $section = 'features'; }
class Imob_Section_Widget_Divider extends Imob_Section_Widget { protected $section = 'divider'; }
class Imob_Section_Widget_Cta extends Imob_Section_Widget { protected $section = 'cta'; }
// phpcs:enable
