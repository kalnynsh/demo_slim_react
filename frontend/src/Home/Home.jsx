import React from 'react'
import System from '../Layout/System'
import FeatureFlag from '../FeatureToggle'

function Home() {
  return (
    <System>
      <h1>Auction</h1>

      <FeatureFlag not name="WE_ARE_HERE">
        <p>We shall be here soon</p>
      </FeatureFlag>

      <FeatureFlag name="WE_ARE_HERE">
        <p>We are here</p>
      </FeatureFlag>
    </System>
  )
}

export default Home
