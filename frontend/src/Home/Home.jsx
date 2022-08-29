import React from 'react'
import { Link } from 'react-router-dom'
import System from '../Layout/System'

function Home() {
  return (
    <System>
      <h1>Auction</h1>
      <p>We are here</p>
      <p>
        <Link to="/join" data-testid="join-link">
          Join to us
        </Link>
      </p>
    </System>
  )
}

export default Home
