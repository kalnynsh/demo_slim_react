import React from 'react'
import { render, screen } from '@testing-library/react'
import Home from './Home'
import { FeaturesProvider } from '../FeatureToggle'

test('renders home', () => {
  render(
    <FeaturesProvider features={[]}>
      <Home />
    </FeaturesProvider>
  )

  const resultGetByText = screen.getByText(/We shall be here soon/i)
  const resultQueryByText = screen.queryByText(/We are here/i)

  expect(resultGetByText).toBeInTheDocument()
  expect(resultQueryByText).not.toBeInTheDocument()
})

test('renders new home', () => {
  render(
    <FeaturesProvider features={['JOIN_TO_US']}>
      <Home />
    </FeaturesProvider>
  )

  const resultGetByText = screen.getByText(/We are here/i)
  const resultQueryByText = screen.queryByText(/We shall be here soon/i)

  expect(resultGetByText).toBeInTheDocument()
  expect(resultQueryByText).not.toBeInTheDocument()
})
