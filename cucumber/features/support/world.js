const { setWorldConstructor, setDefaultTimeout } = require('@cucumber/cucumber');

function CustomWorld ({ attach }) {
    this.attach = attach;
    this.browser = null;
    this.page = null;
};

setWorldConstructor(CustomWorld);

setDefaultTimeout(10 * 1000);
