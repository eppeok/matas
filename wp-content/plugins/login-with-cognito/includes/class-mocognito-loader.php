<?php
/**
 * MINIORANGE OAuth Includes
 *
 * @package    MoCognito\Includes
 */

/**
 * MOCognito Loader
 */
class MoCognito_Loader {

	/**
	 * Actions
	 *
	 * @var Actions $actions for actions
	 */
	protected $actions;

	/**
	 * Filters
	 *
	 * @var Filters $filters for filters
	 */
	protected $filters;

	/**
	 * Constructor
	 */
	public function __construct() {

		$this->actions = array();
		$this->filters = array();

	}

	/**
	 * Add Action
	 *
	 * @param Hook         $hook for hook.
	 *
	 * @param Component    $component for component.
	 *
	 * @param Callback     $callback for callback.
	 *
	 * @param Priority     $priority for priority.
	 *
	 * @param AcceptedArgs $accepted_args for accepted args.
	 */
	public function add_action( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->actions = $this->add( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
	}


	/**
	 * Add Filter
	 *
	 * @param Hook         $hook for hook.
	 *
	 * @param Component    $component for component.
	 *
	 * @param Callback     $callback for callback.
	 *
	 * @param Priority     $priority for priority.
	 *
	 * @param AcceptedArgs $accepted_args for accepted args.
	 */
	public function add_filter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->filters = $this->add( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Add
	 *
	 * @param Hooks        $hooks for hooks.
	 *
	 * @param Hook         $hook for hook.
	 *
	 * @param Component    $component for component.
	 *
	 * @param Callback     $callback for callback.
	 *
	 * @param Priority     $priority for priority.
	 *
	 * @param AcceptedArgs $accepted_args for accepted args.
	 */
	private function add( $hooks, $hook, $component, $callback, $priority, $accepted_args ) {

		$hooks[] = array(
			'hook'          => $hook,
			'component'     => $component,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args,
		);

		return $hooks;

	}

	/**
	 * Run
	 */
	public function run() {

		foreach ( $this->filters as $hook ) {
			add_filter( $hook['hook'], isset( $hook['component'] ) && ! empty( $hook['component'] ) ? array( $hook['component'], $hook['callback'] ) : $hook['callback'], $hook['priority'], $hook['accepted_args'] );
		}

		foreach ( $this->actions as $hook ) {
			add_action( $hook['hook'], isset( $hook['component'] ) && ! empty( $hook['component'] ) ? array( $hook['component'], $hook['callback'] ) : $hook['callback'], $hook['priority'], $hook['accepted_args'] );
		}

	}

}
