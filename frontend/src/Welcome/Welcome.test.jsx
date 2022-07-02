import React from 'react'
import { render, screen } from '@testing-library/react'
import Welcome from './Welcome'
import { FeaturesProvider } from '../FeatureToggle'

test('renders welcome', () => {
  render(
    <FeaturesProvider features={[]}>
      <Welcome />
    </FeaturesProvider>
  )

  const resultGetByText = screen.getByText(/We shall be here soon/i)
  const resultQueryByText = screen.queryByText(/We are here/i)

  expect(resultGetByText).toBeInTheDocument()
  expect(resultQueryByText).not.toBeInTheDocument()
})

test('renders new welcome', () => {
  render(
    <FeaturesProvider features={['WE_ARE_HERE']}>
      <Welcome />
    </FeaturesProvider>
  )

  const resultGetByText = screen.getByText(/We are here/i)
  const resultQueryByText = screen.queryByText(/We shall be here soon/i)

  expect(resultGetByText).toBeInTheDocument()
  expect(resultQueryByText).not.toBeInTheDocument()
})
