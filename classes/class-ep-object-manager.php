<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // exit if accessed directly
}

class EP_Object_Manager {

	/** @var EP_Object_Index[] */
	private $objects = array();

	/**
	 * EP_Object_Manager constructor.
	 */
	public function __construct() {
	}

	/**
	 * Register an index
	 *
	 * If an index of the same name already exists, it will be unregistered and replaced by this object.
	 *
	 * @param EP_Object_Index $object
	 */
	public function register_object( $object ) {
		if ( isset( $this->objects[ $object->get_name() ] ) ) {
			$this->unregister_object( $object );
		}
		$this->objects[ $object->get_name() ] = $object;
		$object->sync_setup();
	}

	/**
	 * Unregister an index
	 *
	 * @param EP_Object_Index $object
	 */
	public function unregister_object( $object ) {
		$object->sync_teardown();
		unset( $this->objects[ $object->get_name() ] );
	}

	/**
	 * Get an index by name
	 *
	 * @param string $name
	 *
	 * @return null|EP_Object_Index
	 */
	public function get_object( $name ) {
		if ( isset( $this->objects[ $name ] ) ) {
			return $this->objects[ $name ];
		}

		return null;
	}

	/**
	 * Get a list of registed object names
	 *
	 * @return array
	 */
	public function get_registered_object_names() {
		return array_keys( $this->objects );
	}

	/**
	 * @return EP_Object_Manager
	 */
	public static function factory() {
		static $instance;
		if ( ! $instance ) {
			$instance = new self();
			$instance->register_object( new EP_Post_Index );
		}

		return $instance;
	}

}

EP_Object_Manager::factory();

function ep_register_object_type( $type ) {
	if ( ! $type instanceof EP_Object_Index ) {
		if ( is_string( $type ) && class_exists( $type ) ) {
			$type = new $type( null );
			ep_register_object_type( $type );
		}

		return;
	}
	EP_Object_Manager::factory()->register_object( $type );
}

function ep_get_object_type( $name ) {
	return EP_Object_Manager::factory()->get_object( $name );
}

function ep_get_registered_object_type_names() {
	return EP_Object_Manager::factory()->get_registered_object_names();
}
