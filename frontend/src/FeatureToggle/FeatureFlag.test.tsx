import React from 'react'
import { render } from '@testing-library/react'
import FeatureFlag from './FeatureFlag'
import FeaturesProvider from './FeaturesProvider'

test('renders content if feature is active', () => {
  const { container } = render(
    <FeaturesProvider features={['FEATURE']}>
      <FeatureFlag name="FEATURE">
        <p>Content</p>
      </FeatureFlag>
    </FeaturesProvider>
  )

  expect(container).toHaveTextContent(/Content/)
})

test('does not render content if feature is not active', () => {
  const { container } = render(
    <FeaturesProvider features={[]}>
      <FeatureFlag name="FEATURE">
        <p>Content</p>
      </FeatureFlag>
    </FeaturesProvider>
  )

  expect(container).not.toHaveTextContent(/Content/)
})

test('does not render content in not mode. Is active', () => {
  const { container } = render(
    <FeaturesProvider features={['FEATURE']}>
      <FeatureFlag name="FEATURE" not>
        <p>Content</p>
      </FeatureFlag>
    </FeaturesProvider>
  )

  expect(container).not.toHaveTextContent(/Content/)
})

test('renders content in not mode. Is not active', () => {
  const { container } = render(
    <FeaturesProvider features={[]}>
      <FeatureFlag name="FEATURE" not>
        <p>Content</p>
      </FeatureFlag>
    </FeaturesProvider>
  )

  expect(container).toHaveTextContent(/Content/)
})
