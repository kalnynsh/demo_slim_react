import React from 'react'
import { render, screen } from '@testing-library/react'
import { MemoryRouter } from 'react-router-dom'
import Home from './Home'
import { FeaturesProvider } from '../FeatureToggle'

test('renders home', () => {
  render(
    <FeaturesProvider features={[]}>
      <MemoryRouter>
        <Home />
      </MemoryRouter>
    </FeaturesProvider>
  )

  expect(screen.queryByText(/We shall be here soon/i)).not.toBeInTheDocument()
  expect(screen.getByText(/We are here/i)).toBeInTheDocument()
})
