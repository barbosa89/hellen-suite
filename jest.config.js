module.exports = {
    "roots": [
        "<rootDir>/tests/Vue"
    ],
    "moduleFileExtensions": ["js", "json", "vue"],
    "transform": {
        ".*\\.(vue)$": "<rootDir>/node_modules/vue-jest",
        "^.+\\.js$": "<rootDir>/node_modules/babel-jest"
    },
    "setupFilesAfterEnv": [
        "<rootDir>tests/Vue/setup.js"
    ],
    "testEnvironment": "jsdom",
    "collectCoverage": true,
    "coverageDirectory": "<rootDir>/tests/coverage/js"
};
