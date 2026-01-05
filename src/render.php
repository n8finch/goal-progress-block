<?php
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

$goalLabel = isset( $attributes['goalLabel'] ) ? $attributes['goalLabel'] : '';
$currentValue = isset( $attributes['currentValue'] ) ? floatval( $attributes['currentValue'] ) : 0;
$minValue = isset( $attributes['minValue'] ) ? floatval( $attributes['minValue'] ) : 0;
$maxValue = isset( $attributes['maxValue'] ) ? floatval( $attributes['maxValue'] ) : 100;
$showAsPercentage = ! empty( $attributes['showAsPercentage'] );
$labelConnector = isset( $attributes['labelConnector'] ) ? $attributes['labelConnector'] : '';
$gradientStart = isset( $attributes['gradientStart'] ) ? $attributes['gradientStart'] : '';
$gradientEnd = isset( $attributes['gradientEnd'] ) ? $attributes['gradientEnd'] : '';
$reverseDirection = ! empty( $attributes['reverseDirection'] );

// Calculate percentage for the visual fill (always left to right)
$percentage = min(
    100,
    max(0, (($currentValue - $minValue) / ($maxValue - $minValue)) * 100)
);

// Calculate display percentage (handle reverse direction)
$displayPercentage = $reverseDirection ? 100 - $percentage : $percentage;

// Format the display value
$displayValue = $showAsPercentage
    ? round($displayPercentage) . '%'
    : ($reverseDirection 
        ? "{$maxValue} {$labelConnector} {$minValue}" 
        : "{$currentValue} {$labelConnector} {$maxValue}");

?>
<div <?php echo esc_attr( get_block_wrapper_attributes() ) ?>>
  <div class="goal-progress-container">
    <div class="goal-progress-header">
      <span class="goal-progress-label"><?php echo esc_html( $goalLabel ); ?></span>
      <span class="goal-progress-value"><?php echo esc_html( $displayValue ); ?></span>
    </div>

    <div class="goal-progress-thermometer">
      <div class="goal-progress-track">
        <div
          class="goal-progress-fill"
          style="width: <?php echo esc_attr( $reverseDirection ? 100 - $percentage : $percentage ); ?>%;
            background: linear-gradient(to right, <?php echo esc_attr( $gradientStart ); ?>, <?php echo esc_attr( $gradientEnd ); ?>);"
        />
      </div>
    </div>
  </div>
</div>
