<?php
/**
 * MINIORANGE OAuth Views
 *
 * @package    MoCognito\Views
 */

/**
 * MOCognito Pointer Manager
 */
class MoCognito_PointersManager {

	/**
	 * Pfile
	 *
	 * @var pfile $pfile for pfile
	 */
	private $pfile;

	/**
	 * Version
	 *
	 * @var Version $version for version
	 */
	private $version;

	/**
	 * Prefix
	 *
	 * @var prefix $prefix for prefix
	 */
	private $prefix;

	/**
	 * Pointers
	 *
	 * @var Pointers $pointers for pointers
	 */
	private $pointers = array();

	/**
	 * Constructor
	 *
	 * @param File    $file for file.
	 *
	 * @param Version $version for version.
	 *
	 * @param Prefix  $prefix for prefix.
	 */
	public function __construct( $file, $version, $prefix ) {
		$this->pfile   = file_exists( $file ) ? $file : false;
		$this->version = str_replace( '.', '_', $version );
		$this->prefix  = $prefix;
	}

	/**
	 * Parse
	 */
	public function parse() {
		if ( empty( $this->pfile ) ) {
			return;
		}
		$pointers = (array) require_once $this->pfile;
		if ( empty( $pointers ) ) {
			return;
		}
		foreach ( $pointers as $i => $pointer ) {
			$pointer['id']                    = "{$this->prefix}{$this->version}_{$i}";
			$this->pointers[ $pointer['id'] ] = (object) $pointer;
		}
	}

	/**
	 * Filter
	 *
	 * @param Page $page for page.
	 */
	public function filter( $page ) {

		if ( empty( $this->pointers ) ) {
			return array();
		}
		$uid        = get_current_user_id();
		$no         = explode( ',', (string) get_user_meta( $uid, 'dismissed_wp_pointers', true ) );
		$active_ids = array_diff( array_keys( $this->pointers ), $no );
		$good       = array();
		foreach ( $this->pointers as $i => $pointer ) {
			if ( in_array( $i, $active_ids, true ) // is active.
				&& isset( $pointer->where ) // has where.
				&& ( $pointer->where[0] === $page ) ) {
					$good[] = $pointer;
			}
		}
			$count = count( $good );
		if ( 0 === $good ) {
			return array();
		}
		foreach ( array_values( $good ) as $i => $pointer ) {
			$good[ $i ]->next = $i + 1 < $count ? $good[ $i + 1 ]->id : '';
		}

			return $good;
	}
}
