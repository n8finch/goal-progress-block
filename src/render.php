<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Server render template for the Goal Progress block.
 *
 * This file converts the client-side `save` output into an equivalent
 * server-rendered PHP template so the block can be rendered on the server.
 *
 * Receives an `$attributes` array and returns the HTML string.
 */

if ( ! isset( $attributes ) || ! is_array( $attributes ) ) {
    return '';
}

$goprog_goalLabel = isset( $attributes['goalLabel'] ) ? $attributes['goalLabel'] : '';
$goprog_currentValue = isset( $attributes['currentValue'] ) ? floatval( $attributes['currentValue'] ) : 0;
$goprog_minValue = isset( $attributes['minValue'] ) ? floatval( $attributes['minValue'] ) : 0;
$goprog_maxValue = isset( $attributes['maxValue'] ) ? floatval( $attributes['maxValue'] ) : 100;
$goprog_showAsPercentage = ! empty( $attributes['showAsPercentage'] );
$goprog_labelConnector = isset( $attributes['labelConnector'] ) ? $attributes['labelConnector'] : '';
$goprog_gradientStart = isset( $attributes['gradientStart'] ) ? $attributes['gradientStart'] : '';
$goprog_gradientEnd = isset( $attributes['gradientEnd'] ) ? $attributes['gradientEnd'] : '';
$goprog_reverseDirection = ! empty( $attributes['reverseDirection'] );

// Calculate percentage for the visual fill (always left to right)
$percentage = min(
    100,
    max(0, (($goprog_currentValue - $goprog_minValue) / ($goprog_maxValue - $goprog_minValue)) * 100)
);

// Calculate display percentage (handle reverse direction)
$displayPercentage = $goprog_reverseDirection ? 100 - $percentage : $percentage;
// Format the display value
$displayValue = $goprog_showAsPercentage
    ? round($displayPercentage) . '%'
    : ($goprog_reverseDirection 
        ? "{$goprog_maxValue} {$goprog_labelConnector} {$goprog_minValue}" 
        : "{$goprog_currentValue} {$goprog_labelConnector} {$goprog_maxValue}");

?>
<div <?php echo esc_attr( get_block_wrapper_attributes() ) ?>>
  <div class="goal-progress-container">
    <div class="goal-progress-header">
      <span class="goal-progress-label"><?php echo esc_html( $goprog_goalLabel ); ?></span>
      <span class="goal-progress-value"><?php echo esc_html( $displayValue ); ?></span>
    </div>

    <div class="goal-progress-thermometer">
      <div class="goal-progress-track">
        <div
          class="goal-progress-fill"
          style="width: <?php echo esc_attr( $goprog_reverseDirection ? 100 - $percentage : $percentage ); ?>%;
            background: linear-gradient(to right, <?php echo esc_attr( $goprog_gradientStart ); ?>, <?php echo esc_attr( $goprog_gradientEnd ); ?>);"
        />
      </div>
    </div>
  </div>
</div>
