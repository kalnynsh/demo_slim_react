const { Then } = require('@cucumber/cucumber');
const { expect } = require('chai');

Then('I see welcome block', async function () {
  await this.page.waitForSelector('[data-testid=welcome]');
  const text = await this.page.$eval('[data-testid=welcome] h1', elem => elem.textContent);
  expect(text).to.eql('Auction');
});
