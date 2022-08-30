import React from 'react'
import styles from './Home.module.css'
import { Link } from 'react-router-dom'
import System from '../Layout/System'
import FeatureFlag from '../FeatureToggle'
import { useAuth } from '../OAuth/Provider'

function Home() {
  const { isAuthenticated, login, logout } = useAuth()

  return (
    <System>
      <h1>Auction</h1>
      <p>We are here</p>
      <p className={styles.links}>
        <FeatureFlag not name="OAUTH">
          <Link to="/join" data-testid="join-link">
            Join
          </Link>
        </FeatureFlag>

        <FeatureFlag name="OAUTH">
          {!isAuthenticated ? (
            <Link to="/join" data-testid="join-link">
              Join
            </Link>
          ) : null}
        </FeatureFlag>
        <FeatureFlag name="OAUTH">
          {!isAuthenticated ? (
            <button type="button" data-testid="login-button" onClick={login}>
              Log in
            </button>
          ) : null}
        </FeatureFlag>

        <FeatureFlag name="OAUTH">
          {isAuthenticated ? (
            <button type="button" data-testid="logout-button" onClick={logout}>
              Log out
            </button>
          ) : null}
        </FeatureFlag>
      </p>
    </System>
  )
}

export default Home
