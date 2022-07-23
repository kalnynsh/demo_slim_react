import React from 'react'
import { createMemoryHistory } from 'history'
import {
  unstable_HistoryRouter as HistoryRouter,
  MemoryRouter,
  Routes,
  Route,
} from 'react-router-dom'
import { render, waitFor, screen } from '@testing-library/react'
import Confirm from './Confirm'
import api from '../../Api'
import Success from '../Success'
import Home from '../../Home'

test('confirms without token', async () => {
  jest.spyOn(api, 'post')

  const history = createMemoryHistory({
    initialEntries: ['/join/confirm'],
  })

  render(
    <HistoryRouter history={history}>
      <Routes>
        <Route path="/" element={<Home />} />
        <Route path="/join/confirm" element={<Confirm />} />
      </Routes>
    </HistoryRouter>
  )

  expect(history.location.pathname).toBe('/')

  expect(api.post).not.toHaveBeenCalled()
})

test('confirms successfully', async () => {
  jest.spyOn(api, 'post').mockResolvedValue({
    ok: true,
    status: 201,
    headers: new Headers(),
    text: () => Promise.resolve(''),
  })

  const history = createMemoryHistory({
    initialEntries: ['/join/confirm?token=01'],
  })

  render(
    <HistoryRouter history={history}>
      <Routes>
        <Route path="/join/confirm" element={<Confirm />} />
        <Route path="/join/success" element={<Success />} />
      </Routes>
    </HistoryRouter>
  )

  await waitFor(() => {
    expect(api.post).toHaveBeenCalled()
  })

  // expect(history.location.pathname).toBe('/join/success')

  expect(api.post).toHaveBeenCalledWith('/v1/auth/join/confirm', {
    token: '01',
  })
})

test('shows error', async () => {
  jest.spyOn(api, 'post').mockRejectedValue({
    ok: false,
    status: 409,
    headers: new Headers({ 'content-type': 'application/json' }),
    json: () => Promise.resolve({ message: 'Incorrect token.' }),
  })

  render(
    <MemoryRouter initialEntries={['/join/confirm?token=02']}>
      <Routes>
        <Route path="/join/confirm" element={<Confirm />} />
      </Routes>
    </MemoryRouter>
  )

  const alert = await screen.findByTestId('alert-error')

  expect(alert).toHaveTextContent('Incorrect token.')
})
