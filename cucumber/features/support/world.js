const { setWorldConstructor, setDefaultTimeout } = require('@cucumber/cucumber')

function CustomeWorld ({ attach }) {
  this.attach = attach
  this.browser = null
  this.page = null
};

setWorldConstructor(CustomeWorld)

setDefaultTimeout(10 * 3000)
