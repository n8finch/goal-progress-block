/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';

import {
  PanelBody,
  TextControl,
  RangeControl,
  ToggleControl,
  ColorPicker,
  SelectControl,
  Flex,
  FlexItem,
  __experimentalText as Text,
  __experimentalVStack as VStack,
} from '@wordpress/components';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

/**
 * Gradient presets for quick selection
 */
const GRADIENT_PRESETS = [
  { name: 'Ocean Blue', start: '#4facfe', end: '#00f2fe' },
  { name: 'Sunset', start: '#fa709a', end: '#fee140' },
  { name: 'Purple Dream', start: '#a18cd1', end: '#fbc2eb' },
  { name: 'Fresh Green', start: '#56ab2f', end: '#a8e063' },
  { name: 'Fire', start: '#f83600', end: '#f9d423' },
  { name: 'Cool Blues', start: '#2193b0', end: '#6dd5ed' },
];

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {Element} Element to render.
 */
export default function Edit({ attributes, setAttributes }) {
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

  const blockProps = useBlockProps();

  // Determine style variant from blockProps className
  const isCircleOutline =
    blockProps.className &&
    blockProps.className.includes('is-style-circle-outline');
  const isCircleSolid =
    blockProps.className &&
    blockProps.className.includes('is-style-circle-solid');
  const isCircle = isCircleOutline || isCircleSolid;

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

  // Circle calculations
  const radius = 60;
  const circumference = 2 * Math.PI * radius;
  const strokeDashoffset = circumference - (percentage / 100) * circumference;

  return (
    <>
      <InspectorControls>
        <PanelBody title={__('Goal Settings', 'goal-progress')}>
          <TextControl
            label={__('Goal Label', 'goal-progress')}
            value={goalLabel}
            onChange={(value) => setAttributes({ goalLabel: value })}
          />

          <RangeControl
            label={__('Current value', 'goal-progress')}
            value={currentValue}
            onChange={(value) => setAttributes({ currentValue: value })}
            min={minValue}
            max={maxValue}
          />

          <RangeControl
            label={__('Minimum value', 'goal-progress')}
            value={minValue}
            onChange={(value) => setAttributes({ minValue: value })}
            min={0}
            max={maxValue - 1}
          />

          <RangeControl
            label={__('Maximum value', 'goal-progress')}
            value={maxValue}
            onChange={(value) => setAttributes({ maxValue: value })}
            min={minValue + 1}
            max={10000}
          />

          <ToggleControl
            label={__('Show as percentage', 'goal-progress')}
            checked={showAsPercentage}
            onChange={(value) => setAttributes({ showAsPercentage: value })}
          />

          <ToggleControl
            label={__('Reverse direction (e.g. 100% to 0%)', 'goal-progress')}
            checked={reverseDirection}
            onChange={(value) => setAttributes({ reverseDirection: value })}
            help={__(
              'When enabled, the display will count down from 100% to 0% as the current value increases',
              'goal-progress'
            )}
          />
          {!showAsPercentage && (
            <TextControl
              label={__('Label connector', 'goal-progress')}
              value={labelConnector || ''}
              onChange={(value) => setAttributes({ labelConnector: value })}
              help={__(
                'Text to display between values if not a percentage, e.g. "of" "/", "to", "-", etc.',
                'goal-progress'
              )}
            />
          )}
        </PanelBody>

        <PanelBody
          title={__('Thermometer Colors', 'goal-progress')}
          initialOpen={false}>
          <SelectControl
            label={__('Gradient Preset', 'goal-progress')}
            value=''
            options={[
              { label: 'Choose a preset...', value: '' },
              ...GRADIENT_PRESETS.map((preset) => ({
                label: preset.name,
                value: preset.name,
              })),
            ]}
            onChange={(value) => {
              const preset = GRADIENT_PRESETS.find((p) => p.name === value);
              if (preset) {
                setAttributes({
                  gradientStart: preset.start,
                  gradientEnd: preset.end,
                });
              }
            }}
          />

          <VStack spacing={4}>
            <div>
              <Text>{__('Start Color', 'goal-progress')}</Text>
              <ColorPicker
                color={gradientStart}
                onChange={(value) => setAttributes({ gradientStart: value })}
                enableAlpha={false}
              />
            </div>

            <div>
              <Text>{__('End Color', 'goal-progress')}</Text>
              <ColorPicker
                color={gradientEnd}
                onChange={(value) => setAttributes({ gradientEnd: value })}
                enableAlpha={false}
              />
            </div>
          </VStack>
        </PanelBody>
      </InspectorControls>

      <div {...blockProps}>
        <div className='goal-progress-container'>
          {!isCircle ? (
            <>
              <div className='goal-progress-header'>
                <span className='goal-progress-label'>{goalLabel}</span>
                <span className='goal-progress-value'>{displayValue}</span>
              </div>

              <div className='goal-progress-thermometer'>
                <div className='goal-progress-track'>
                  <div
                    className='goal-progress-fill'
                    style={{
                      width: `${
                        reverseDirection ? 100 - percentage : percentage
                      }%`,
                      background: `linear-gradient(to right, ${gradientStart}, ${gradientEnd})`,
                    }}
                  />
                </div>
              </div>
            </>
          ) : (
            <div className='goal-progress-circle-wrapper'>
              <svg className='goal-progress-circle' viewBox='0 0 160 160'>
                {isCircleSolid ? (
                  <>
                    <defs>
                      <linearGradient
                        id={`gradient-${gradientStart}-${gradientEnd}`}
                        x1='0%'
                        y1='0%'
                        x2='100%'
                        y2='100%'>
                        <stop offset='0%' stopColor={gradientStart} />
                        <stop offset='100%' stopColor={gradientEnd} />
                      </linearGradient>
                    </defs>
                    <circle cx='80' cy='80' r='60' fill='#f0f0f0' />
                    <circle
                      cx='80'
                      cy='80'
                      r='60'
                      fill={`url(#gradient-${gradientStart}-${gradientEnd})`}
                      style={{
                        clipPath: `inset(${100 - percentage}% 0 0 0)`,
                      }}
                    />
                  </>
                ) : (
                  <>
                    <defs>
                      <linearGradient
                        id={`gradient-${gradientStart}-${gradientEnd}`}
                        x1='0%'
                        y1='0%'
                        x2='100%'
                        y2='100%'>
                        <stop offset='0%' stopColor={gradientStart} />
                        <stop offset='100%' stopColor={gradientEnd} />
                      </linearGradient>
                    </defs>
                    <circle
                      cx='80'
                      cy='80'
                      r={radius}
                      stroke='#f0f0f0'
                      strokeWidth='12'
                      fill='none'
                    />
                    <circle
                      cx='80'
                      cy='80'
                      r={radius}
                      stroke={`url(#gradient-${gradientStart}-${gradientEnd})`}
                      strokeWidth='12'
                      strokeLinecap='round'
                      fill='none'
                      strokeDasharray={circumference}
                      strokeDashoffset={strokeDashoffset}
                      style={{
                        transform: 'rotate(-90deg)',
                        transformOrigin: '50% 50%',
                        transition: 'stroke-dashoffset 0.6s ease-in-out',
                      }}
                    />
                  </>
                )}
              </svg>
              <div className='goal-progress-circle-content'>
                <div className='goal-progress-circle-value'>{displayValue}</div>
                <div className='goal-progress-circle-label'>{goalLabel}</div>
              </div>
            </div>
          )}
        </div>
      </div>
    </>
  );
}
