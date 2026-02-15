module.exports = {
	extends: [ 'plugin:@poocommerce/eslint-plugin/recommended' ],
	globals: {
		jQuery: 'readonly',
	},
	settings: {},
	rules: {
		'poocommerce/feature-flag': 'off',
		'@wordpress/no-global-active-element': 'warn',
		camelcase: 'off',
		'@typescript-eslint/no-this-alias': 'off',
	},
	ignorePatterns: [ '**/*.min.js' ],
};
