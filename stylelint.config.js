/** @type {import('stylelint').Config} */
export default {
    extends: 'stylelint-config-standard',
    rules: {
        'alpha-value-notation': null,
        'color-function-notation': null,
        'color-hex-length': null,
        'comment-empty-line-before': null,
        'comment-whitespace-inside': null,
        'custom-property-empty-line-before': null,
        'custom-property-pattern': null,
        'declaration-block-no-duplicate-properties': [
            true,
            {'ignore': ['consecutive-duplicates']}
        ],
        'declaration-block-no-redundant-longhand-properties': null,
        'declaration-property-value-keyword-no-deprecated': null,
        'font-family-name-quotes': null,
        'media-feature-range-notation': null,
        'no-descending-specificity': null,
        'property-no-unknown': true,
        'property-no-vendor-prefix': null,
        'rule-empty-line-before': null,
        'selector-class-pattern': null,
        'selector-id-pattern': null,
        'selector-not-notation': null,
        'shorthand-property-no-redundant-values': null,
        'value-keyword-case': null,
    }
};
