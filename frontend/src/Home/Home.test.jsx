import React from 'react'
import { render, screen } from '@testing-library/react'
import { MemoryRouter } from 'react-router-dom'
import Home from './Home'
import { FeaturesProvider } from '../FeatureToggle'
import DummyAuthProvider from '../OAuth/Provider/DummyAuthProvider'

test('renders home', () => {
  render(
    <DummyAuthProvider isAuthenticated={false}>
      <FeaturesProvider features={[]}>
        <MemoryRouter>
          <Home />
        </MemoryRouter>
      </FeaturesProvider>
    </DummyAuthProvider>
  )

  expect(screen.queryByText(/We shall be here soon/i)).not.toBeInTheDocument()
  expect(screen.getByText(/We are here/i)).toBeInTheDocument()
})
