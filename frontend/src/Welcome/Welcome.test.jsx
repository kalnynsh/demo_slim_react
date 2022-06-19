import React from 'react'
import { render } from '@testing-library/react'
import Welcome from './Welcome'

test('renders welcome', () => {
  const { getByText } = render(<Welcome features={[]} />)
  const h1Element = getByText(/Auction/i)
  expect(h1Element).toBeInTheDocument()
})

test('renders old welcome', () => {
  const { getByText, queryByText } = render(<Welcome features={[]} />)

  expect(getByText(/We shall be here soon/i)).toBeInTheDocument()
  expect(queryByText(/We are here/i)).toBeNull()
})

test('renders new welcome', () => {
  const { getByText, queryByText } = render(
    <Welcome features={['WE_ARE_HERE']} />
  )

  expect(queryByText(/We shall be here soon/i)).toBeNull()
  expect(getByText(/We are here/i)).toBeInTheDocument()
})
