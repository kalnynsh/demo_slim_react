import React from 'react'
import System from '../Layout/System'
import { Link } from 'react-router-dom'

function OAuth() {
  return (
    <System>
      <h1>Authentication</h1>
      <p>
        <Link to="/">Back to Home</Link>
      </p>
    </System>
  )
}

export default OAuth
