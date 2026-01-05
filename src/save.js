/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps } from '@wordpress/block-editor';

/**
 * The save function defines the way in which the different attributes should
 * be combined into the final markup, which is then serialized by the block
 * editor into `post_content`.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#save
 *
 * @return {Element} Element to render.
 */
export default function save({ attributes }) {
  const {
    goalLabel,
    currentValue,
    minValue,
    maxValue,
    showAsPercentage,
    labelConnector,
    gradientStart,
    gradientEnd,
    reverseDirection,
  } = attributes;

  const blockProps = useBlockProps.save();

  // Calculate percentage for the visual fill (always left to right)
  const percentage = Math.min(
    100,
    Math.max(0, ((currentValue - minValue) / (maxValue - minValue)) * 100)
  );

  // Calculate display percentage
  const displayPercentage = reverseDirection ? 100 - percentage : percentage;

  // Format display value
  const displayValue = showAsPercentage
    ? `${Math.round(displayPercentage)}%`
    : reverseDirection
    ? `${maxValue} ${labelConnector} ${minValue}`
    : `${currentValue} ${labelConnector} ${maxValue}`;

  return (
    <div {...blockProps}>
      <div className='goal-progress-container'>
        <div className='goal-progress-header'>
          <span className='goal-progress-label'>{goalLabel}</span>
          <span className='goal-progress-value'>{displayValue}</span>
        </div>

        <div className='goal-progress-thermometer'>
          <div className='goal-progress-track'>
            <div
              className='goal-progress-fill'
              style={{
                width: `${reverseDirection ? 100 - percentage : percentage}%`,
                background: `linear-gradient(to right, ${gradientStart}, ${gradientEnd})`,
              }}
            />
          </div>
        </div>
      </div>
    </div>
  );
}
