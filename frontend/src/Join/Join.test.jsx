import React from 'react'
import { MemoryRouter, Routes, Route } from 'react-router-dom'
import { render } from '@testing-library/react'
import Join from './Join'
import Home from '../Home'

test('renders join page', () => {
  const { container } = render(
    <MemoryRouter initialEntries={['/join']}>
      <Routes>
        <Route path="/" element={<Home />} />
        <Route path="join" element={<Join />} />
      </Routes>
    </MemoryRouter>
  )

  expect(container).toHaveTextContent(/Join to us/i)
})
