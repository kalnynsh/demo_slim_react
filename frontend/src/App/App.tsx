import React from 'react'
import { BrowserRouter, Route, Routes } from 'react-router-dom'
import { FeaturesProvider } from '../FeatureToggle'
import Home from '../Home'
import Join from '../Join'
import { NotFound } from '../Error'
import Confirm from '../Join/Confirm'
import './App.css'
import Success from '../Join/Success'
import { AuthProvider } from '../OAuth/Provider'
import OAuth from '../OAuth'

function App({ features }: { features: string[] }) {
  return (
    <FeaturesProvider features={features}>
      <AuthProvider
        authorizeUrl={process.env.REACT_APP_AUTH_URL + '/authorize'}
        tokenUrl={process.env.REACT_APP_AUTH_URL + '/token'}
        clientId="frontend"
        scope="common"
        redirectPath="/oauth"
      >
        <BrowserRouter>
          <div className="app">
            <Routes>
              <Route path="/" element={<Home />} />
              {features.includes('OAUTH') ? <Route path="/oauth" element={<OAuth />} /> : null}
              <Route path="/join" element={<Join />} />
              <Route path="/join/confirm" element={<Confirm />} />
              <Route path="/join/success" element={<Success />} />
              <Route path="*" element={<NotFound />} />
            </Routes>
          </div>
        </BrowserRouter>
      </AuthProvider>
    </FeaturesProvider>
  )
}

export default App
