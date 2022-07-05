import React from 'react'
import System from '../Layout/System'
import { Link } from 'react-router-dom'

function Join() {
  return (
    <System>
      <h1>Join to us</h1>
      <p>We are here</p>
      <p>
        <Link to="/">Back to home</Link>
      </p>
    </System>
  )
}

export default Join
