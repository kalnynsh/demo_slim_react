import React from 'react'
import System from '../Layout/System'
import { Link } from 'react-router-dom'
import JoinForm from './JoinForm'

function Join(): JSX.Element {
  return (
    <System>
      <h1>Join to us</h1>
      <JoinForm />
      <p>
        <Link to="/">Back to home</Link>
      </p>
    </System>
  )
}

export default Join
