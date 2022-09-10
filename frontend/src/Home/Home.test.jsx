import React from 'react'
import { render, screen } from '@testing-library/react'
import { MemoryRouter } from 'react-router-dom'
import Home from './Home'
import { FeaturesProvider } from '../FeatureToggle'
import { AuthProvider } from '../OAuth/Provider'

test('renders home', () => {
  render(
    <AuthProvider
      authorizeUrl="/api/authorize"
      tokenUrl="/api/token"
      clientId="frontend"
      scope="common"
      redirectPath="/oauth"
    >
      <FeaturesProvider features={[]}>
        <MemoryRouter>
          <Home />
        </MemoryRouter>
      </FeaturesProvider>
    </AuthProvider>
  )

  expect(screen.queryByText(/We shall be here soon/i)).not.toBeInTheDocument()
  expect(screen.getByText(/We are here/i)).toBeInTheDocument()
})
