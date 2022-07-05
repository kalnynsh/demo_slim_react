import React from 'react'
import { Link } from 'react-router-dom'
import System from '../Layout/System'
import FeatureFlag from '../FeatureToggle'

function Home() {
  return (
    <System>
      <h1>Auction</h1>

      <FeatureFlag not name="JOIN_TO_US">
        <p>We shall be here soon</p>
      </FeatureFlag>

      <FeatureFlag name="JOIN_TO_US">
        <p>We are here</p>
        <p>
          <Link to="/join" data-testid="join-link">
            Join to us
          </Link>
        </p>
      </FeatureFlag>
    </System>
  )
}

export default Home
