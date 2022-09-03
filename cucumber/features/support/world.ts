import { Browser, Page } from 'puppeteer'
import { setWorldConstructor, setDefaultTimeout, World } from '@cucumber/cucumber'

export class CustomWorld extends World {
  browser: Browser | null = null
  page: Page | null = null
}

setWorldConstructor(CustomWorld)

setDefaultTimeout(10 * 3000)
