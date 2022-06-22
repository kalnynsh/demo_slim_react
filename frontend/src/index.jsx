import React from 'react'
import './index.css'
import App from './App'
import * as serviceWorker from './serviceWorker'
import { createRoot } from 'react-dom/client'
import cookie from 'cookie'

const root = createRoot(document.getElementById('root'))
const defaultFeatures = []

const cookies = cookie.parse(document.cookie)
const cookieFeatures = (cookies.features || '').split(/\s*,\s*/g)

const features = [...defaultFeatures, ...cookieFeatures]

root.render(
  <React.StrictMode>
    <App features={features} />
  </React.StrictMode>
)

serviceWorker.unregister()
