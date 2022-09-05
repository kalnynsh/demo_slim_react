import React from 'react'
import './index.css'
import App from './App'
import { createRoot } from 'react-dom/client'
import cookie from 'cookie'
import { mergeFeatures } from './FeatureToggle'
import defaultFeatures from './features'

const root = createRoot(document.getElementById('root'))
const cookies = cookie.parse(document.cookie)

const cookieFeatures = (cookies.features || '').split(/\s*,\s*/g).filter(Boolean)

const features = mergeFeatures(defaultFeatures, cookieFeatures)

root.render(
  <React.StrictMode>
    <App features={features} />
  </React.StrictMode>
)
