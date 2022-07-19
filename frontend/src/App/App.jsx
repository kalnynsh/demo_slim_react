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

function App({ features }) {
  return (
    <FeaturesProvider features={features}>
      <BrowserRouter>
        <div className="app">
          <Routes>
            <Route path="/" element={<Home />} />
            {features.includes('JOIN_TO_US') ? (
              <Route path="/join" element={<Join />} />
            ) : null}
            {features.includes('JOIN_TO_US') ? (
              <Route path="/join/confirm" element={<Confirm />} />
            ) : null}
            {features.includes('JOIN_TO_US') ? (
              <Route path="/join/success" element={<Success />} />
            ) : null}
            <Route path="*" element={<NotFound />} />
          </Routes>
        </div>
      </BrowserRouter>
    </FeaturesProvider>
  )
}

App.propTypes = {
  features: PropTypes.array.isRequired,
}

export default App
