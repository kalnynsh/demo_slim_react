import React from 'react'
import PropTypes from 'prop-types'
import { BrowserRouter, Route, Routes } from 'react-router-dom'
import { FeaturesProvider } from '../FeatureToggle'
import Home from '../Home'
import Join from '../Join'
import { NotFound } from '../Error'
import './App.css'

function App({ features }) {
  return (
    <FeaturesProvider features={features}>
      <BrowserRouter>
        <div className="app">
          <Routes>
            <Route path="/" element={<Home />} />
            {features.includes('JOIN_TO_US') ? (
              <Route path="join" element={<Join />} />
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
