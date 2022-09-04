import { CustomWorld } from '../world'
import { Given } from '@cucumber/cucumber'

Given('I am a guest user', () => null)

Given('I am the user', async function (this: CustomWorld) {
  if (!this.page) {
    throw new Error('Page is undefined')
  }

  await this.page.evaluateOnNewDocument(() => {
    localStorage.setItem('auth.tokens', JSON.stringify({
      accessToken:
        'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiJmcm9udGVuZCIsImp0a' +
        'SI6Ijg4MjMwNjdhYjU4OTFiYTMyMTBhMTY4ZWZiOGE4ZDE2OTQwMWU5ZWZiMjEyMzV' +
        'iOWEzZTI0NzYzNzY3MjFiNWQ5MjdhZThiNzM3MDk3MzFkIiwiaWF0IjoxNjYyMDI1N' +
        'DIwLjMzNzIzLCJuYmYiOjE2NjIwMjU0MjAuMzM3MjQzLCJleHAiOjMzMjE4OTM0MjI' +
        'wLjA1OTU4Niwic3ViIjoiMDAwMDAwMDAtMDAwMC0wMDAwLTAwMDAtMDAwMDAwMDAwM' +
        'DAxIiwic2NvcGVzIjpbImNvbW1vbiJdLCJyb2xlIjoidXNlciJ9.nosoPElQSOmD5s' +
        'ZjvtgNxruHYotvlKW75DkGIRJOHCmmgbEeI9yvO8jKhXKlBgR7vwCYr1mLMTSV46rk' +
        'Cb84hQxfOgV3XNsS24NMyNdRbhnXlBHP-rkFe756KnD4mb3zwDrxy76-KnLgI0-IED' +
        'QHQfeyvb0sBOji9S_BoaBKydA_qZGZCJMc_mATqCo00zElaa8LykQeOX59L74-vtal' +
        'Als5GciVCyU2vAg_G8kV9dtvKre5kOmACFrtiUvF4dUnkE1L86HUduucY-z_kwXA8N' +
        'PycoDB_hYAa4cSdEQ_4rjhSn5rh1ifylxlnAl8yL0YQ75mqOy_aVbbx_eqX54RaQ',
      expires: new Date().getTime() + 36000000,
      refreshToken: ''
    }))
  })
})
