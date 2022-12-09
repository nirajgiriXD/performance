<?php
/**
 * Server-Timing API: Perflab_Server_Timing_Metric class
 *
 * @package performance-lab
 * @since n.e.x.t
 */

/**
 * Class representing a single Server-Timing metric.
 *
 * @since n.e.x.t
 */
class Perflab_Server_Timing_Metric {

	/**
	 * The metric slug.
	 *
	 * @since n.e.x.t
	 * @var string
	 */
	private $slug;

	/**
	 * The metric value in milliseconds.
	 *
	 * @since n.e.x.t
	 * @var int|float|null
	 */
	private $value;

	/**
	 * The value measured before relevant execution logic in seconds, if used.
	 *
	 * @since n.e.x.t
	 * @var float|null
	 */
	private $before_value;

	/**
	 * Constructor.
	 *
	 * @since n.e.x.t
	 *
	 * @param string $slug The metric slug.
	 */
	public function __construct( $slug ) {
		$this->slug = $slug;
	}

	/**
	 * Gets the metric slug.
	 *
	 * @since n.e.x.t
	 *
	 * @return string The metric slug.
	 */
	public function get_slug() {
		return $this->slug;
	}

	/**
	 * Sets the metric value.
	 *
	 * Alternatively to setting the metric value directly, the {@see Perflab_Server_Timing_Metric::measure_before()}
	 * and {@see Perflab_Server_Timing_Metric::measure_after()} methods can be used to further simplify measuring.
	 *
	 * @since n.e.x.t
	 *
	 * @param int|float $value The metric value to set, in milliseconds.
	 */
	public function set_value( $value ) {
		if ( ! is_int( $value ) && ! is_float( $value ) ) {
			_doing_it_wrong(
				__METHOD__,
				/* translators: %s: PHP parameter name */
				sprintf( __( 'The %s parameter must be an integer or float.', 'performance-lab' ), '$value' ),
				''
			);
			return;
		}

		if ( did_action( 'perflab_server_timing_send_header' ) && ! doing_action( 'perflab_server_timing_send_header' ) ) {
			_doing_it_wrong(
				__METHOD__,
				/* translators: %s: WordPress action name */
				sprintf( __( 'The method must be called before or during the %s action.', 'performance-lab' ), 'perflab_server_timing_send_header' ),
				''
			);
			return;
		}

		$this->value = $value;
	}

	/**
	 * Gets the metric value.
	 *
	 * @since n.e.x.t
	 *
	 * @return int|float|null The metric value, or null if none set.
	 */
	public function get_value() {
		return $this->value;
	}

	/**
	 * Captures the current time, as a reference point to calculate the duration of a task afterwards.
	 *
	 * This should be used in combination with {@see Perflab_Server_Timing_Metric::measure_after()}. Alternatively,
	 * {@see Perflab_Server_Timing_Metric::set_value()} can be used to set a calculated value manually.
	 *
	 * @since n.e.x.t
	 */
	public function measure_before() {
		$this->before_value = microtime( true );
	}

	/**
	 * Captures the current time and compares it to the reference point to calculate a task's duration.
	 *
	 * This should be used in combination with {@see Perflab_Server_Timing_Metric::measure_before()}. Alternatively,
	 * {@see Perflab_Server_Timing_Metric::set_value()} can be used to set a calculated value manually.
	 *
	 * @since n.e.x.t
	 */
	public function measure_after() {
		if ( ! $this->before_value ) {
			_doing_it_wrong(
				__METHOD__,
				/* translators: %s: PHP method name */
				sprintf( __( 'The %s method must be called before.', 'performance-lab' ), __CLASS__ . '::measure_before()' ),
				''
			);
			return;
		}

		$this->set_value( ( microtime( true ) - $this->before_value ) * 1000.0 );
	}
}
