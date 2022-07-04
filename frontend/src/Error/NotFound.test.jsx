import React from 'react'
import { MemoryRouter, Routes, Route } from 'react-router-dom'
import { render } from '@testing-library/react'
import NotFound from './NotFound'
import Home from '../Home'

test('renders not found', () => {
  const { container } = render(
    <MemoryRouter initialEntries={['/not-found']}>
      <Routes>
        <Route path="/" element={<Home />} />
        <Route path="not-found" element={<NotFound />} />
      </Routes>
    </MemoryRouter>
  )

  expect(container).toHaveTextContent(/Page is not found/i)
})
