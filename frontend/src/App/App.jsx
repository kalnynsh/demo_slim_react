import React from 'react'
import PropTypes from 'prop-types'
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

function App({ features }) {
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
              <Route exact path="/" element={<Home />} />
              {features.includes('OAUTH') ? (
                <Route exact path="/oauth" element={<OAuth />} />
              ) : null}
              <Route exact path="/join" element={<Join />} />
              <Route exact path="/join/confirm" element={<Confirm />} />
              <Route exact path="/join/success" element={<Success />} />
              <Route path="*" element={<NotFound />} />
            </Routes>
          </div>
        </BrowserRouter>
      </AuthProvider>
    </FeaturesProvider>
  )
}

App.propTypes = {
  features: PropTypes.array.isRequired,
}

export default App
