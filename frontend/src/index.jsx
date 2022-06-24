import React from 'react'
import './index.css'
import App from './App'
import * as serviceWorker from './serviceWorker'
import { createRoot } from 'react-dom/client'
import cookie from 'cookie'
import { mergeFeatures } from './FeatureToggle'

const root = createRoot(document.getElementById('root'))

const defaultFeatures = {
  WE_ARE_HERE: false,
}

const cookies = cookie.parse(document.cookie)
const cookieFeatures = (cookies.features || '').split(/\s*,\s*/g)
const features = mergeFeatures(defaultFeatures, cookieFeatures)

root.render(
  <React.StrictMode>
    <App features={features} />
  </React.StrictMode>
)

serviceWorker.unregister()
