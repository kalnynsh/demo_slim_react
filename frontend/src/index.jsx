import React from 'react'
import './index.css'
import App from './App'
import * as serviceWorker from './serviceWorker'
import { createRoot } from 'react-dom/client'

const root = createRoot(document.getElementById('root'))
const features = []

root.render(
  <React.StrictMode>
    <App features={features} />
  </React.StrictMode>
)

serviceWorker.unregister()
