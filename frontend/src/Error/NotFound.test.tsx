import React from 'react'
import { MemoryRouter, Routes, Route } from 'react-router-dom'
import { render, screen } from '@testing-library/react'
import NotFound from './NotFound'
import Home from '../Home'

test('renders not found', () => {
  render(
    <MemoryRouter initialEntries={['/not-found']}>
      <Routes>
        <Route path="/" element={<Home />} />
        <Route path="not-found" element={<NotFound />} />
      </Routes>
    </MemoryRouter>
  )

  expect(screen.getByText(/Page is not found/i)).toBeInTheDocument()
})
