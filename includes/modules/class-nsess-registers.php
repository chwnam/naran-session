<?php
/**
 * NSESS: Registers module
 *
 * Manage all registers
 */

/* ABSPATH check */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'NSESS_Registers' ) ) {
	/**
	 * You can remove unused registers.
	 *
	 * @property-read NSESS_Register_Activation    $activation
	 * @property-read NSESS_Register_Ajax          $ajax
	 * @property-read NSESS_Register_Comment_Meta  $comment_meta
	 * @property-read NSESS_Register_Cron          $cron
	 * @property-read NSESS_Register_Cron_Schedule $cron_schedule
	 * @property-read NSESS_Register_Deactivation  $deactivation
	 * @property-read NSESS_Register_Option        $option
	 * @property-read NSESS_Register_Post_Meta     $post_meta
	 * @property-read NSESS_Register_Post_Type     $post_type
	 * @property-read NSESS_Register_Script        $script
	 * @property-read NSESS_Register_Style         $style
	 * @property-read NSESS_Register_Submit        $submit
	 * @property-read NSESS_Register_Taxonomy      $taxonomy
	 * @property-read NSESS_Register_Term_Meta     $term_meta
	 * @property-read NSESS_Register_User_Meta     $user_meta
	 */
	class NSESS_Registers implements NSESS_Module {
		use NSESS_Submodule_Impl;

		public function __construct() {
			/**
			 * You can remove unused registers.
			 */
			$this->assign_modules(
				[
					'activation'    => NSESS_Register_Activation::class,
					'ajax'          => NSESS_Register_Ajax::class,
					'comment_meta'  => NSESS_Register_Comment_Meta::class,
					'cron'          => NSESS_Register_Cron::class,
					'cron_schedule' => NSESS_Register_Cron_Schedule::class,
					'deactivation'  => NSESS_Register_Deactivation::class,
					'option'        => NSESS_Register_Option::class,
					'post_meta'     => NSESS_Register_Post_Meta::class,
					'post_type'     => NSESS_Register_Post_Type::class,
					'script'        => NSESS_Register_Script::class,
					'style'         => NSESS_Register_Style::class,
					'submit'        => NSESS_Register_Submit::class,
					'taxonomy'      => NSESS_Register_Taxonomy::class,
					'term_meta'     => NSESS_Register_Term_Meta::class,
					// NOTE: 'uninstall' is not a part of registers submodules.
					//       Because it 'uninstall' hook requires static method callback.
					'user_meta'     => NSESS_Register_User_Meta::class,
				]
			);
		}
	}
}
