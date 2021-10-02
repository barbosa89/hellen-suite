module.exports = {
    verbose: true,
    roots: [
        "<rootDir>/tests/Vue"
    ],
    moduleFileExtensions: ["js", "json", "vue"],
    transform: {
        "^.+\\.js$": "babel-jest",
        "^.+\\.vue$": "@vue/vue2-jest"
    }
};