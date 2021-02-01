<?php
use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use ElementorPro\Base\Base_Widget_Trait;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class BetterDocs_Elementor_Category_Box extends Widget_Base {

    use Base_Widget_Trait;

    public function get_name () {
        return 'betterdocs-category-box';
    }

    public function get_title () {
        return __('BetterDocs Category Box', 'betterdocs');
    }

    public function get_icon () {
        return 'betterdocs-icon-category-grid';
    }

    public function get_categories () {
        return ['docs-archive'];
    }

    public function get_keywords () {
        return ['betterdocs', 'docs', 'category-grid'];
    }

    public function get_custom_help_url() {
        return 'https://betterdocs.co/docs/';
    }

    protected function _register_controls() {
        $this->start_controls_section(
            'betterdocs_global_warning',
            [
                'label' => __('Warning!', 'betterdocs'),
            ]
        );

        $this->add_control(
            'betterdocs_global_warning_text',
            [
                'type'            => Controls_Manager::RAW_HTML,
                'raw'             => __("Seems like you don't have Essential Addons on your site. Please make sure to install or activate <a href='plugin-install.php?s=essential-addons-for-elementor-lite&tab=search&type=term' target='_blank'>Essential Addons for Elementor</a> on your website to use this widget.", 'betterdocs'),
                'content_classes' => 'elementor-control-raw-html betterdocs-elementor-note elementor-panel-alert elementor-panel-alert-info',
            ]
        );

        $this->end_controls_section();
	}


	protected function render() {
	    return;
	}
}
