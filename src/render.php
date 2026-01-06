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

// Get block wrapper attributes.
$wrapper_attributes = WP_Block_Supports::get_instance()->apply_block_supports();
$wrapper_classes = isset( $wrapper_attributes['class'] ) ? $wrapper_attributes['class'] : '';

// Check for is-style-circle-solid or is-style-circle-outline to add additional class.
$is_circle_outline = strpos( $wrapper_classes, 'is-style-circle-outline' ) !== false;
$is_circle_solid = strpos( $wrapper_classes, 'is-style-circle-solid' ) !== false;
$is_circle = $is_circle_outline || $is_circle_solid;

// Extract attributes with defaults.
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

        // Circle calculations
$radius = 60;
$circumference = 2 * M_PI * $radius;
$stroke_dash_offset = $circumference - ($percentage / 100) * $circumference;


// echo '<pre>';
// var_dump( get_block_wrapper_attributes() );
// var_dump( esc_attr( get_block_wrapper_attributes() ));

?>
<div <?php echo get_block_wrapper_attributes() ?>>
    <div class="goal-progress-container">
      <?php if ( ! $is_circle ) : ?>
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
      <?php else : ?>
        <div class="goal-progress-circle-wrapper">
          <svg class="goal-progress-circle" viewBox="0 0 160 160">
            <?php if ( $is_circle_solid ) : ?>
                <defs>
                  <linearGradient id="gradient-<?php echo esc_attr( $goprog_gradientStart ); ?>-<?php echo esc_attr( $goprog_gradientEnd ); ?>" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="<?php echo esc_attr( $goprog_gradientStart ); ?>" />
                    <stop offset="100%" stop-color="<?php echo esc_attr( $goprog_gradientEnd ); ?>" />
                  </linearGradient>
                </defs>
                <circle
                  cx="80"
                  cy="80"
                  r="60"
                  fill="#f0f0f0"
                />
                <circle
                  cx="80"
                  cy="80"
                  r="60"
                  fill="url(#gradient-<?php echo esc_attr( $goprog_gradientStart ); ?>-<?php echo esc_attr( $goprog_gradientEnd ); ?>)"
                  style="clip-path: inset(<?php echo 100 - $percentage; ?>% 0 0 0);"
                />
            <?php else :  // Circle outline style ?>
                <defs>
                  <linearGradient id="gradient-<?php echo esc_attr( $goprog_gradientStart ); ?>-<?php echo esc_attr( $goprog_gradientEnd ); ?>" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="<?php echo esc_attr( $goprog_gradientStart ); ?>" />
                    <stop offset="100%" stop-color="<?php echo esc_attr( $goprog_gradientEnd ); ?>" />
                  </linearGradient>
                </defs>
                <circle
                  cx="80"
                  cy="80"
                  r="<?php echo esc_attr( $radius ); ?>"
                  stroke="#f0f0f0"
                  stroke-width="12"
                  fill="none"
                />
                <circle
                  cx="80"
                  cy="80"
                  r="<?php echo esc_attr( $radius ); ?>"
                  stroke="url(#gradient-<?php echo esc_attr( $goprog_gradientStart ); ?>-<?php echo esc_attr( $goprog_gradientEnd ); ?>)"
                  stroke-width="12"
                  stroke-linecap="round"
                  fill="none"
                  stroke-dasharray="<?php echo esc_attr( $circumference ); ?>"
                  stroke-dashoffset="<?php echo esc_attr( $stroke_dash_offset ); ?>"
                  style="transform: rotate(-90deg); transform-origin: 50% 50%; transition: stroke-dashoffset 0.6s ease-in-out;"
                />
            <?php endif; ?>
          </svg>
          <div class="goal-progress-circle-content">
            <div class="goal-progress-circle-value"><?php echo esc_html( $displayValue ); ?></div>
            <div class="goal-progress-circle-label"><?php echo esc_html( $goprog_goalLabel ); ?></div>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>