import React from 'react'
import { MemoryRouter, Routes, Route } from 'react-router-dom'
import Success from './Success'
import { render, screen } from '@testing-library/react'

test('renders success join page', () => {
  render(
    <MemoryRouter initialEntries={['/join/success']}>
      <Routes>
        <Route path="/join/success" element={<Success />} />
      </Routes>
    </MemoryRouter>
  )

  expect(screen.getByTestId('join-success')).toBeInTheDocument()
})
