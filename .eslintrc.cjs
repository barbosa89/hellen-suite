/* eslint-env node */
require("@rushstack/eslint-patch/modern-module-resolution")

module.exports = {
    root: true,
    ignorePatterns: ["resources/js/lang/locales.js"],
    extends: [
        "plugin:vue/essential",
        "eslint:recommended",
        "@vue/eslint-config-prettier"
    ],
    parserOptions: {
        ecmaVersion: "latest"
    },
    rules: {
        "vue/multi-word-component-names": "off",
        "no-undef": "off"
    }
}
